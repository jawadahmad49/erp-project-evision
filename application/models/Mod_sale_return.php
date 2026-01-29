<?php

class Mod_sale_return extends CI_Model {

    function __construct() { 

        parent::__construct();
        error_reporting(0);
    
    }
	
	public function add_sale_return($data){
		// pm($data);exit();
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $cash_code=$fix_code['cash_code'];
        $trans_id=$data['trans_id'];
        if ($trans_id=='') {

        $trans_id = $this->db->query("select max(trans_id) as trans_id from tbl_goodsreceiving where sale_point_id='$sale_point_id'")->row_array()['trans_id'];

      if($trans_id==''){
      	 $trans_id=1;
      	}else{
      		 $trans_id=$trans_id+1;
      	}

}

        $this->db->trans_start();
		$ins_array = array(
		    "suppliercode" =>$data['customer'],
		    "receiptdate" =>$data['date'],
		    "remarks" =>$data['remarks'],
		    "scode" =>$data['scode'],
		    "gstp" =>$data['gstp'],
		    "vat_amount" =>$data['vat_amount'],
		    "inc_vat_amount" =>$data['inc_vat_amount'],
		    "net_payable" =>$data['total_payable'],
		    "total_bill" =>$data['total_bill'],
			"pay_mode" =>$data['pay_mode'],     
		    "bank_code" =>$data['bank_code'],     
		    "cheque_no" =>$data['cheque_no'],     
		    "cheque_dt" =>$data['cheque_date'],
		    "sale_point_id" =>$sale_point_id,
		    "trans_id" =>$trans_id,
		    "trans_typ" =>'salereturn',
		    "Purchase_type" =>'salereturn',
		    "total_paid" =>$data['totalrecv'],
		    "11_kg_price" => $data['kg_11_price'], 
		    "return_gas" =>$data['return_gas'],
		    "return_rate" =>$data['return_rate'],
		    "return_amount" => $data['return_amount'] 
		); 
if(!empty($this->input->post("id"))){

	    $trans_id=$data['trans_id'];
		$id = $_POST['id'];
		$table = "tbl_goodsreceiving";
		$where = "receiptnos= '$id'";
		$update_goods=$this->mod_common->update_table($table,$where,$ins_array);
		$lastqqf=$this->db->last_query();

		
			if($update_goods){
				return $this->multipleitems_againstid($data,$id,$trans_id,'tbl_goodsreceiving_detail','34');

			}else{
				return false;
			}

}else{
		$table = "tbl_goodsreceiving";
		//echo "<pre>";var_dump($ins_array); exit();
		$add_goods = $this->mod_common->insert_into_table($table, $ins_array);
		
	
$lastqqf=$this->db->last_query();

			
		$insert_id = $add_goods;
			if($add_goods){
				return $this->multipleitems_againstid($data,$insert_id,$trans_id,'tbl_goodsreceiving_detail');
			}else{
				return false;
		}
}
	
	}
	public function closemonthwithscript($id){

		set_time_limit(0);
		ini_set('memory_limit', '-1');
		

		//$sales = $this->db->query("select tbl_issue_goods_detail.*,tbl_issue_goods.issuedto from tbl_issue_goods_detail inner join tblmaterial_coding on tbl_issue_goods_detail.itemid=tblmaterial_coding.materialcode  where tbl_issue_goods_detail.ig_detail_id='$id' order by issuedate asc")->result_array();
		echo "select * from tbl_issue_goods_detail  where tbl_issue_goods_detail.ig_detail_id='$id'";exit;
		//pm($sales);exit;
		
		if(!empty($sales)){
			foreach($sales as $key => $value){
					$totalsaledamt = $value['qty']*$value['sprice'];
					$returnqty=0;
					
					
					$purchasequery = $this->db->query("
						select * from
							(
								SELECT receiptdate,Batch_stock,receipt_id,rate,case
									when receipt_id > 0 then 'issuegoods'
									end as
									tablename
									FROM `tbl_goodsreceiving_detail` 
									inner join tbl_goodsreceiving on 
									tbl_goodsreceiving.receiptnos=tbl_goodsreceiving_detail.receipt_detail_id 
									where 
									batch_status='0' and itemid='".$value['itemid']."' and 
									type='Filled' 
								
								union
								
								SELECT irdate as receiptdate,Batch_stock,sr_no as receipt_id,(total_amount/qty) as rate,case
								when tbl_issue_return_detail.sr_no > 0 then 'return'
								end as
								tablename
								 FROM `tbl_issue_return` inner join tbl_issue_return_detail on tbl_issue_return.irnos=tbl_issue_return_detail.irnos where tbl_issue_return_detail.itemid='".$value['itemid']."' and batch_status='0' and tbl_issue_return_detail.type='Filled') as newtable order by receiptdate asc limit 1 
						")->result_array()[0];

					if($purchasequery['tablename']=="issuegoods" || $purchasequery['tablename'] =="return"){
							
								
					}else{
						//pm($purchasequery);
					}


					if($purchasequery['Batch_stock']>$value['qty']){
						$batch_stock_left = $purchasequery['Batch_stock']-$value['qty'];
						$stocktaken = $value['qty'];
						
						

						if($purchasequery['tablename']=="issuegoods"){

							$this->db->query("update tbl_goodsreceiving_detail set Batch_stock='$batch_stock_left' where receipt_id='".$purchasequery['receipt_id']."'");
							$purchase_batch_no = "IG-".$purchasequery['receipt_id'];

						}else{
							$this->db->query("update tbl_issue_return_detail set Batch_stock='$batch_stock_left' where sr_no='".$purchasequery['receipt_id']."'");
							$purchase_batch_no = "R-".$purchasequery['receipt_id'];
						}
						

						$totalpurchasedamt = $value['qty']*$purchasequery['rate'];

					}else if($purchasequery['Batch_stock']==$value['qty']){
						
						if($purchasequery['tablename']=="issuegoods"){

							$this->db->query("update tbl_goodsreceiving_detail set batch_status='1',Batch_stock='0' where receipt_id='".$purchasequery['receipt_id']."'");

							$purchase_batch_no = "IG-".$purchasequery['receipt_id'];

						}else{
							$this->db->query("update tbl_issue_return_detail set batch_status='1',Batch_stock='0' where sr_no='".$purchasequery['receipt_id']."'");

							$purchase_batch_no = "R-".$purchasequery['receipt_id'];
						}

						$stocktaken = $value['qty'];

						$totalpurchasedamt = $value['qty']*$purchasequery['rate'];

					}else{
						$halfamt=0;
						$sale_Qty_left = $value['qty']-$purchasequery['Batch_stock'];

						if($purchasequery['tablename']=="issuegoods"){

							$this->db->query("update tbl_goodsreceiving_detail set batch_status='1',Batch_stock='0' where receipt_id='".$purchasequery['receipt_id']."'");
							$purchase_batch_no = "IG-".$purchasequery['receipt_id'];

						}else{
							$this->db->query("update tbl_issue_return_detail set batch_status='1',Batch_stock='0' where sr_no='".$purchasequery['receipt_id']."'");

							$purchase_batch_no = "R-".$purchasequery['receipt_id'];
						}

						$halfamt = $purchasequery['Batch_stock']*$purchasequery['rate'];

						$stocktaken = $purchasequery['Batch_stock'];

						$loop=2;
						while(1<$loop){
								
							//starts here

							$purchasequery = $this->db->query("
							select * from
							(
								SELECT receiptdate,Batch_stock,receipt_id,rate,case
									when receipt_id > 0 then 'issuegoods'
									end as
									tablename
									FROM `tbl_goodsreceiving_detail` 
									inner join tbl_goodsreceiving on 
									tbl_goodsreceiving.receiptnos=tbl_goodsreceiving_detail.receipt_detail_id 
									where 
									batch_status='0' and itemid='".$value['itemid']."' and 
									type='Filled' 
								
								union
								
								SELECT irdate as receiptdate,Batch_stock,sr_no as receipt_id,(total_amount/qty) as rate,case
								when tbl_issue_return_detail.sr_no > 0 then 'return'
								end as
								tablename
								 FROM `tbl_issue_return` inner join tbl_issue_return_detail on tbl_issue_return.irnos=tbl_issue_return_detail.irnos where tbl_issue_return_detail.itemid='".$value['itemid']."' and batch_status='0' and tbl_issue_return_detail.type='Filled') as newtable order by receiptdate asc limit 1 
							")->result_array()[0];

							if($purchasequery['tablename']=="issuegoods" || $purchasequery['tablename'] =="return"){
							
								
							}else{
								//pm($purchasequery);
							}

							//ends here

							if($sale_Qty_left>$purchasequery['Batch_stock']){

								$sale_Qty_left = $sale_Qty_left - $purchasequery['Batch_stock'];

								$stocktaken = $stocktaken.",".$purchasequery['Batch_stock'];

								if($purchasequery['tablename']=="issuegoods"){

									$this->db->query("update tbl_goodsreceiving_detail set batch_status='1',Batch_stock='0' where receipt_id='".$purchasequery['receipt_id']."'");

									$purchase_batch_no = $purchase_batch_no.",IG-".$purchasequery['receipt_id'];

								}else{
									$this->db->query("update tbl_issue_return_detail set batch_status='1',Batch_stock='0' where sr_no='".$purchasequery['receipt_id']."'");

									$purchase_batch_no = $purchase_batch_no.",R-".$purchasequery['receipt_id'];
								}

								

								$halfamt = $halfamt + ($purchasequery['Batch_stock']*$purchasequery['rate']);
								

							}else if($sale_Qty_left==$purchasequery['Batch_stock']){


								if($purchasequery['tablename']=="issuegoods"){

									$this->db->query("update tbl_goodsreceiving_detail set batch_status='1',Batch_stock='0' where receipt_id='".$purchasequery['receipt_id']."'");

									$purchase_batch_no = $purchase_batch_no.",IG-".$purchasequery['receipt_id'];

								}else{
									$this->db->query("update tbl_issue_return_detail set batch_status='1',Batch_stock='0' where sr_no='".$purchasequery['receipt_id']."'");

									$purchase_batch_no = $purchase_batch_no.",R-".$purchasequery['receipt_id'];
								}

								

								$halfamt = $halfamt + ($purchasequery['Batch_stock']*$purchasequery['rate']);
								$loop=0;

								$stocktaken = $stocktaken.",".$purchasequery['Batch_stock'];

							}else{
								
								$Batch_stock_left = $purchasequery['Batch_stock'] - $sale_Qty_left;

								if($purchasequery['tablename']=="issuegoods"){

									$this->db->query("update tbl_goodsreceiving_detail set batch_status='0',Batch_stock='$Batch_stock_left' where receipt_id='".$purchasequery['receipt_id']."'");

									$purchase_batch_no = $purchase_batch_no.",IG-".$purchasequery['receipt_id'];

								}else{
									$this->db->query("update tbl_issue_return_detail set batch_status='0',Batch_stock='$Batch_stock_left' where sr_no='".$purchasequery['receipt_id']."'");

									$purchase_batch_no = $purchase_batch_no.",R-".$purchasequery['receipt_id'];
								}

								$halfamt = $halfamt + ($sale_Qty_left*$purchasequery['rate']);
								
								$loop=0;

								$stocktaken = $stocktaken.",".$sale_Qty_left;
							}

						}

						$totalpurchasedamt = $halfamt;
					}

					$this->db->query("update tbl_issue_goods_detail set purchase_batch_no='$purchase_batch_no',purchase_amt='$totalpurchasedamt',qty_taken='$stocktaken' where srno='".$value['srno']."'");
					$totalprofit = $totalprofit + ($totalsaledamt-$totalpurchasedamt);

			}

		
		}
		

	}


	public function multipleitems_againstid($data,$goodsid,$trans_id,$table,$updated_value=''){
 //pm($data);exit();
		//echo $cylinder_sale_amt=$data['sale_security_amt'];exit();
			 $tax_amount=0;
			 $netamount_ex=0;
			 $return_rate=0;
			 $return_gas=0;
			 $return_amount=0;
			 $total_discount=0;
			 $vat_amountt=$data['vat_amount'];
			 $amount_paid=$data['totalrecv'];
			 $total_gass_amount=$data['total_gass_amount'];
			 $recvd_date = $data['date'];
			 $return_amount = $data['return_amount'];

			 $gstp=$data['gstp'];
             date_default_timezone_set("Asia/Karachi");
	         $today=date('Y-m-d h:i:sa');
             $uid=$this->session->userdata('id');
             $login_user=$this->session->userdata('id');
             $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
             $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
             $sale_code=$fix_code['sales_code'];
		     $cost_code=$fix_code['cost_of_goods_code'];
		     $stock_code=$fix_code['stock_code'];
		     $cash_inhand=$fix_code['cash_code'];
		     $gas_return_acc=$fix_code['gas_return_code'];
		     $security_code=$fix_code['security_code'];
		     $items_detail='';
 		    $tax_acode=$fix_code['tax_receive'];
			
			if($updated_value=='')
			{

			 $login_user=$this->session->userdata('id');
             $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
             $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
			 $sale_code=$fix_code['sales_code'];
			 $cash_inhand=$fix_code['cash_code'];
			 $tax_acode=$fix_code['tax_receive'];
			 $security_code=$fix_code['security_code'];
	 
		$datas = array();
		foreach($data['item'] as $key=>$value) {
		$batch_status ="open"; 
		$type=$data['type'][$key];
		if ($type=='wo_sec') {
			$type='Empty';
			$sub_type='wo_sec_return';
		}else if ($type=='security') {
			$type='Empty';
			$sub_type='security_return';
		}else{
			$type='Filled';
			$sub_type='filled_return';
		}
		
		$datas[] = array(
			'receipt_detail_id' => $goodsid,
			'sale_point_id' =>$sale_point_id,
		    'trans_id' =>$trans_id,
		    'itemid' => $data['item'][$key],
		    'quantity' => $data['qty'][$key],
		    'gstp' => $data['gst'][$key],
		    'vat_amount' => $data['gst_amounttotal'][$key],
		    'rate' => $data['price'][$key],
		    'wrate' => $data['security'][$key],
		    'scode' => $data['scode'],
		    'category_id' =>1,
		    'type' => $type,
		    'sub_type' => $sub_type,
		    'batch_status' => $batch_status,
		    'Batch_stock'=>$data['qty'][$key],
		    'amount'=>$data['amounttotal'][$key],
		    'recvd_date'=>$recvd_date,
		   
		    
		   );
		   
		   $type=$data['type'][$key];
		if ($type=='Filled') {
				$gas_amount+=$data['price'][$key]*$data['qty'][$key];
				$sec_amount+=$data['security'][$key]*$data['qty'][$key];
				$total_amount=$gas_amount+$sec_amount;
			}

		if ($type=='security') {
				$security_amount+=$data['security'][$key]*$data['qty'][$key]-$return_amount;
				
			}
		if ($type=='wo_sec') {
				$wo_sec_amount+=$data['price'][$key]*$data['qty'][$key];
				
			}
 		 	

		}
		

		
		
		 $this->db->insert_batch($table, $datas);
		 
		  foreach($data['item'] as $key=>$value) {
			   $insertIds[$key]  = $this->db->last_query();
			   $insdte.=$insertIds[$key]; 
			   
		
		  }

		 
		 }
		 else
		 {
//pm($data);exit();
		$datas = array();
		$datai = array();
		foreach($data['item'] as $key=>$value) {
			$recvd_date = $data['date'];
				$type=$data['type'][$key];
		if ($type=='wo_sec') {
			$type='Empty';
			$sub_type='wo_sec_return';
		}else if ($type=='security') {
			$type='Empty';
			$sub_type='security_return';
		}else{
			$type='Filled';
			$sub_type='filled_return';
		}
			$datas[] = array(
				'receipt_id' => $data['items_detailid'][$key],
				'itemid' => $data['item'][$key],
				'receipt_detail_id' => $goodsid,
				'scode' => $data['scode'],
				'sale_point_id' =>$sale_point_id,
		        'trans_id' =>$trans_id,
			    'quantity' => $data['qty'][$key],
				'gstp' => $data['gst'][$key],
		        'vat_amount' => $data['gst_amounttotal'][$key],
			    'rate' => $data['price'][$key],
			    'wrate' => $data['security'][$key],
			    'type' => $type,
			    'recvd_date'=>$recvd_date,
			    'category_id' =>1,
			    'sub_type' => $sub_type,
			    
			   
			   );

			
			
		   $type=$data['type'][$key];
		if ($type=='Filled') {
				$gas_amount+=$data['price'][$key]*$data['qty'][$key];
				$sec_amount+=$data['security'][$key]*$data['qty'][$key];
				$total_amount=$gas_amount+$sec_amount;
			}

		if ($type=='security') {
				$security_amount+=$data['security'][$key]*$data['qty'][$key];
				
			}
		if ($type=='wo_sec') {
				$wo_sec_amount+=$data['price'][$key]*$data['qty'][$key];
				
			}
 		 	

 
		}

		foreach($datas as $key=>$value) {
			if($value['receipt_id']){
				$datau[] = $value;
			}else{ 
				$datai[] = $value;
			}
		}
			if($datau){ $this->db->update_batch($table, $datau,'receipt_id');
			$tdsd=$this->db->last_query();
			
		   // print_r($tdsd);exit;
		
			
			
			}
			if($datai){ $this->db->insert_batch($table, $datai);
			  foreach($data['item'] as $key=>$value) {
			   $insertIds[$key]  = $this->db->last_query();
			   $insdtedd.=$insertIds[$key]; 
			   
		
		  }

			}
		 }
				
			
			
			
			
			
		/////////////////////////// here is code//////////////////
		 	$receiptdate=$data['date'];
			$vendorcode=$data['customer'];
			$user = $this->session->userdata('id');
		    $goodsidt=$sale_point_id."-Return-".$trans_id;
			
			
			
			
			
		$nar_return='Gas Return '.$return_gas.'KG@'.$return_rate;


		$check_exists="SELECT * FROM `tbltrans_master` WHERE `vno` = '$goodsidt'";

		  $query = $this->db->query($check_exists);

	if($query->num_rows()!=0)
	{
		$sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsidt'"; $this->db->query($sqld);
    	$sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsidt'"; $this->db->query($sqlm);

	}
 
 	    $sale_code=$fix_code['sales_code'];
		$cost_code=$fix_code['cost_of_goods_code'];
		$stock_code=$fix_code['stock_code'];
		$cash_inhand=$fix_code['cash_code'];
		$sale_cylinder_code=$fix_code['sale_cylinder_code'];
		$cash_type='CR';
		$gas_return_acc=$fix_code['gas_return_code'];
		$security_code=$fix_code['security_code'];
		$items_detail='';
 		$tax_acode=$fix_code['tax_receive'];
 		$cylinder_sec_code=$fix_code['cylinder_sec_code'];
 		$cylinder_wo_sec_code=$fix_code['cylinder_wo_sec_code'];
 		$empty_stock_code=$fix_code['empty_stock_code'];
 		$stock_code=$fix_code['stock_code'];



		   $sql_in="SELECT m.sino, d.itemid,d.quantity,i.itemname,d.amount,d.rate,d.inc_vat_amount,d.gstp FROM  tbl_goodsreceiving m,tbl_goodsreceiving_detail d ,tblmaterial_coding i where m.receiptnos=d.receipt_detail_id and
				d.itemid=i.materialcode and m.receiptnos ='$goodsid' ";

				
				$resul = $this->db->query($sql_in);
				$rw = $resul->result_array();
				foreach($rw as $key=>$value) {

					$item_amount=0;
					$items_detail="";
					$nar1="";
					$inv_num=$value['sino'];
					$gate_pas=$value['ref1'];
					$item_amount=$value['inc_vat_amount'];
					
					//$items_detail_m.=$value['itemname'].' ,  '.$value['quantity'].'@'.$value['rate'].':';
					$items_detail_m.=$value['itemname'].' ,  '.$value['quantity'].'@'.$value['rate'];
			 
			}
				
		$nar_filled='Return Filled Cylinder against #:'.$goodsid.',  '.$items_detail_m.'('.$data['remarks'].')';
		$nar_return_gas='Return Gas Against Return Sale #:'.$goodsid.',  '.$items_detail_m.'('.$data['remarks'].')';
		$nar_tax='Against #:'.$goodsid.',  '.$items_detail_m.'('.$data['remarks'].')';
		$scode=$data['scode'];
		 
		$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date,sale_point_id,trans_id)
			values
			('$goodsidt' , 'SV' , '$total_gass_amount' , '$total_gass_amount' ,'No' ,'No' ,'$user','SP' ,'$receiptdate','$sale_point_id','$trans_id')";
			$this->db->query($querys);
			$master_id = $this->db->query("select masterid from tbltrans_master where vno='$goodsidt'")->row_array()['masterid'];
   if ($gas_amount>0) {  

				$sr++;
		        $queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode) 
			   values('$goodsidt','$master_id','$sr','$stock_code','','0','$gas_amount','$nar_filled','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
				$this->db->query($queryd);
				$debit+=$gas_amount;
				$sr++;
				if ($sec_amount>0) {
					$nar_filled_sec='Cylinders Against Security Return against #:'.$goodsid.',  '.$items_detail_m.'('.$data['remarks'].')';
					 $queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode) 
			   values('$goodsidt','$master_id','$sr','$cylinder_sec_code','','0','$sec_amount','$nar_filled_sec','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
				$this->db->query($queryd);
				$debit+=$sec_amount;
				}
		       
				$sr++;
		        $queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode) 
			   values('$goodsidt','$master_id','$sr','$vendorcode','$vendorname','$total_amount','0','$nar_filled','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
				$this->db->query($queryd);
				$credit+=$total_amount;
				}
				

   
if ($security_amount>0) { 
$nar_sec='Cylinders Against Security Return against #:'.$goodsid.',  '.$items_detail_m.'('.$data['remarks'].')';     
				$sr++;
		        $queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode) 
			   values('$goodsidt','$master_id','$sr','$empty_stock_code','','0','$security_amount','$nar_sec','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
				$this->db->query($queryd);
				$debit+=$security_amount;

				$sr++;
		        $queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode) 
			   values('$goodsidt','$master_id','$sr','$vendorcode','','$security_amount','0','$nar_sec','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
				$this->db->query($queryd);
				$credit+=$security_amount;

}
if ($wo_sec_amount>0) { 
$nar_wo_sec='Cylinders Without Security Return against #:'.$goodsid.',  '.$items_detail_m.'('.$data['remarks'].')';     
				$sr++;
		        $queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode) 
			   values('$goodsidt','$master_id','$sr','$empty_stock_code','','0','$wo_sec_amount','$nar_wo_sec','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
				$this->db->query($queryd);
				$debit+=$wo_sec_amount;

				$sr++;
		        $queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode) 
			   values('$goodsidt','$master_id','$sr','$cylinder_wo_sec_code','','$wo_sec_amount','0','$nar_wo_sec','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
				$this->db->query($queryd);
				$credit+=$wo_sec_amount;

}

			if($return_amount>0){
				
				$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id)
					values('$goodsidt','$sr','$stock_code','','$return_amount','0','$nar_return_gas','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
					$this->db->query($resultdd);
					$debit+=$return_amount;
					

						$sr++;
			       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id) 
				   values('$goodsidt','$sr','$vendorcode','$vendorname','0','$return_amount','$nar_return_gas','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
					$this->db->query($queryd);
					$credit+=$return_amount;

				
			}
	

			if($vat_amountt>0){
				
				$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id)
					values('$goodsidt','$sr','$tax_acode','','$vat_amountt','0','Tax $gstp %: $nar_tax','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
					$this->db->query($resultdd);
					$debit+=$vat_amountt;
					

						$sr++;
			       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id) 
				   values('$goodsidt','$sr','$vendorcode','$vendorname','0','$vat_amountt','Tax $gstp %: $nar_tax','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
					$this->db->query($queryd);
					$credit+=$vat_amountt;

				
			}


					if($amount_paid>0) {
					
					$recv_nar='Amount Paid against #:'.$goodsid.',  '.$items_detail_m.'('.$data['remarks'].')';	
					// if($return_amount>0) {
					// 	$recv_nar=$nar.','.$nar_return;
					// }
					
					
					$chequedate=''; $chequeno='';
				if($data['pay_mode']=='Bank'){
					$cash_inhand=	$data['bank_code'];
					$cash_type='BR';

					$chequedate=$data['cheque_date'];
					$chequeno=$data['cheque_no'];
				} 

			$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequedate,chequeno,sale_point_id,trans_id,scode) 
			   values('$goodsidt','$master_id','$sr','$vendorcode','$vendorname','$amount_paid','0','$recv_nar','SV','$cash_type','$receiptdate','$chequedate','$chequeno','$sale_point_id','$trans_id','$scode')";
				$this->db->query($queryd);
				$debit+=$amount_paid;

				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequedate,chequeno,sale_point_id,trans_id,scode) 
			   values('$goodsidt','$master_id','$sr','$cash_inhand','','0','$amount_paid','$recv_nar','SV','$cash_type','$receiptdate','$chequedate','$chequeno','$sale_point_id','$trans_id','$scode')";
				$this->db->query($queryd);
				$credit+=$amount_paid;

				 
				
		
			}


		  

	
		   	 
		   		$updates ="UPDATE `tbl_goodsreceiving` set `post_gl`=1 where `receiptnos`='$goodsidt'";
		   		if($data['makenew']){ $makenew = $data['makenew'];
		   			
		   		}
				

		   		$q = $this->db->query($updates);
		   		if ($debit!=$credit) {
			    $this->session->set_flashdata('err_message', 'Debit Sides And Credit Sides Are Not Equal!');
				redirect(SURL . 'Sale_return/');
		   			}
		   		$this->db->trans_complete();
		   		return $q;
				
				
	}



	public function repost_sale($goodsid){
		$tax_acode='2004003001';
		$sale_code='2003001003';

		$user = $this->session->userdata('id');
		$goodsidt=$goodsid."-Sale";
		$goodsecurity=$goodsid."-Receive Security";
		$goodsidr=$goodsid."-Receive";
		$goodsidss=$goodsid."-Sale Security";
		$goodsidgasreturn=$goodsid."-Returned Gas";

		$netamount = 0;
		$gstAmt = 0;
 
 
 
 
		$check_exists="SELECT * FROM `tbltrans_master` WHERE `vno` = '$goodsidt' and `vtype`='SV'";

		  $query = $this->db->query($check_exists);

	if($query->num_rows()!=0)
	{
		
		$sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsidt' and `vtype`='SV'"; $this->db->query($sqld);
    	$sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsidt' and `vtype`='SV'"; $this->db->query($sqlm);
	  
    	$sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsecurity' and `vtype`='SV'"; $this->db->query($sqld);
		$sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsecurity' and `vtype`='SV'"; $this->db->query($sqlm);

    	$sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsidr' and `vtype`='SV'"; $this->db->query($sqld);
		$sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsidr' and `vtype`='SV'"; $this->db->query($sqlm);
	

    	$sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsidss' and `vtype`='SV'"; $this->db->query($sqld);
		$sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsidss' and `vtype`='SV'"; $this->db->query($sqlm);


    	$sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsidgasreturn' and `vtype`='SV'"; $this->db->query($sqld);
		$sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsidgasreturn' and `vtype`='SV'"; $this->db->query($sqlm);	}
 
 
 
 
 
 	$check_exists="SELECT * FROM `tbl_issue_goods_detail` WHERE `ig_detail_id` = '$goodsid' ";

		  $query = $this->db->query($check_exists);

	if($query->num_rows()!=0)
	{
 
 
 
 
 
 
 
 
 
 
		$goodsItemsData = $this->db->get_where("tbl_issue_goods_detail",array("ig_detail_id"=>$goodsid))->result();

		foreach ($goodsItemsData as $key => $value) {
			$netamount += $value->total_amount;
			$tax_amount += $value->vat_amount;
			$ex_vat_total_amount +=$value->ex_vat_total_amount;
			$returns=$value->returns;  

			$wrate = $value->wrate;
			if($wrate>0){
				$items_detail_m.=$value->itemname.' ,  '.$value->qty.'@'.$value->sprice.',security '.$wrate ;
			}else{
				$items_detail_m.=$value->itemname.' ,  '.$value->qty.'@'.$value->sprice ;
			}	
			$items_detail_m.=', empty returned '.$returns.':';
		}
		$items_detail_m= substr_replace($items_detail_m, "", -1);
		$nar='Sale against #:'.$goodsid.',  '.$items_detail_m.'('.$data->remarks.')';

		$uData['total_amount'] = $netamount;
		$uData['after_discount_amt'] = $netamount - $goodsData->total_discount;

		$this->db->where("issuenos",$goodsid);
		$this->db->update("tbl_issue_goods",$uData);

		$goodsData = $this->db->get_where("tbl_issue_goods",array("issuenos"=>$goodsid))->row();

		$security_amt = $goodsData->security_amt;
		$total_discount = $goodsData->total_discount;
		$vendorcode = $goodsData->issuedto;
		$receiptdate = $goodsData->issuedate;
		$netamountr = $goodsData->gas_amt;
		$return_amount = $goodsData->return_amount;

		$net_payable = $netamount;
		$vendorname = "";

				
		$sale_code='3001001001';
		$cash_inhand='2003013001';
		$gas_return_acc='2003001002';
		$security_code='1001002001';
		$items_detail='';
 		$tax_acode='1001003001';

 		if($goodsData->pay_mode=='Bank'){
			$cash_inhand = $goodsData->bank_code;
			$chequedate = $goodsData->cheque_date;
			$chequeno = $goodsData->cheque_no;
		}

 		$netamount = $netamount - $total_discount - $security_amt - $tax_amount;

		$nar_return = "";
				$sr++;
		        $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate) 
			   values('$goodsidt','$sr','$vendorcode','$vendorname','0','$netamount','$nar','SV','SP','$receiptdate')";
				$this->db->query($queryd);	

				$sr++;
		        $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate) 
			   values('$goodsidt','$sr','$sale_code','','$netamount','0','$nar','SV','SP','$receiptdate')";
				$this->db->query($queryd);	


			if($tax_amount>0){
				$sr++;
				$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate) 
				values('$goodsidt','$sr','$tax_acode','','$tax_amount','0','Tax:$nar','SV','SP','$receiptdate' )";
				$this->db->query($queryd);
			}

			if($tax_amount>0){
				$sr++;
				$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate) 
				values('$goodsidt','$sr','$vendorcode','$vendorname','0','$tax_amount','Tax:$nar','SV','SP','$receiptdate' )";
				$this->db->query($queryd);
			}
				if($netamountr>0) {
					
					$recv_nar=$nar;
					if($return_amount>0) {
						$recv_nar=$nar.','.$nar_return;
					}
					$chequedate=''; $chequeno='';
				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequedate,chequeno) 
			   values('$goodsidr','$sr','$vendorcode','$vendorname','0','$netamountr','$recv_nar','SV','SP','$receiptdate','$chequedate','$chequeno')";
				$this->db->query($queryd);


				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequedate,chequeno) 
			   values('$goodsidr','$sr','$cash_inhand','','$netamountr','0','$recv_nar','SV','SP','$receiptdate','$chequedate','$chequeno')";
				$this->db->query($queryd);
				
		
			} 
				if($return_amount>0 && $netamountr>0) {
					
					$recv_nar='';
					if($return_amount>0) {
						$recv_nar=$nar_return;
					}
				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate) 
			   values('$goodsidgasreturn','$sr','$vendorcode','$vendorname','0','$return_amount','$recv_nar','SV','SP','$receiptdate')";
				$this->db->query($queryd);


				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate) 
			   values('$goodsidgasreturn','$sr','$gas_return_acc','','$return_amount','0','$recv_nar','SV','SP','$receiptdate')";
				$this->db->query($queryd);
			}

			
			if($netamountr==0 && $return_amount>0) {
				
					$sr++;
					$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate) 
					values('$goodsidr','$sr','$vendorcode','$vendorname','0','$return_amount','$nar_return','SV','SP','$receiptdate')";
					$this->db->query($queryd);


					$sr++;
					$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate) 
					values('$goodsidr','$sr','$cash_inhand','','$return_amount','0','$nar_return','SV','SP','$receiptdate')";
					$this->db->query($queryd);
			}

			$sale_security = $security_amt;
			$securityamts = $security_amt;
			$nar_security = $nar;

				if($sale_security>0){
					
					$nar_security=$nar;

					$sr++;
					$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate) 
					values('$goodsidss','$sr','$security_code','$vendorname','0','$sale_security','$nar_security','SV','SP','$receiptdate')";
					$this->db->query($queryd);
					$sr++;
					$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate) 
					values('$goodsidss','$sr','$vendorcode','$vendorname','$sale_security','0','$nar_security','SV','SP','$receiptdate')";
					$this->db->query($queryd);

$sr++;

		        $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate) 
			   values('$goodsecurity','$sr','$cash_inhand','$vendorname','$securityamts','0','$nar','SV','SP','$receiptdate')";
				$this->db->query($queryd);

$sr++;
		        $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate) 
			   values('$goodsecurity','$sr','$vendorcode','$vendorname','0','$securityamts','$nar','SV','SP','$receiptdate')";
				$this->db->query($queryd);				

			}
			
			$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date)
			values
			('$goodsidt' , 'SV' , '$netamount' , '$netamount' ,'No' ,'No' ,'$user','SP' ,'$receiptdate')";
			$this->db->query($querys);

			 $sale_code='3001001001';
			 $cash_inhand='2003013001';


			if($security_amt!=0){

				 $querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date)
				values
				('$goodsecurity' , 'SV' , '$securityamts' , '$securityamts' ,'No' ,'No' ,'$user','SP' ,'$receiptdate')";
				$this->db->query($querys);

				$sr++;

			}

		   $sr++;

	   		$updates ="UPDATE `tbl_issue_goods` set `post_gl`=1 where `issuenos`='$goodsidt'";
	   		$this->db->query($updates);

		if($security_amt!=0){
			 

			$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date)
			values
			('$goodsidr' , 'SV' , '$netamountr' , '$netamountr' ,'No' ,'No' ,'$user','SP' ,'$receiptdate')";
			$this->db->query($querys);
		   
		   
 			$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date)
			values
			('$goodsidss' , 'SV' , '$netamountr' , '$netamountr' ,'No' ,'No' ,'$user','SP' ,'$receiptdate')";
			$this->db->query($querys);
		 }
	}
	}



	public function today_amount_recv($dt){
		$dt=date('Y-m-d');
		$this->db->select('tbl_issue_goods.total_received');    //,SUM(tbl_issue_goods_detail.total_amount)
		$this->db->from('tbl_issue_goods');
 
		$this->db->where('tbl_issue_goods.issuedate=', $dt);
 	
 
		$query = $this->db->get();
		 
		return $query->result_array();
	}
	public function manage_sale_return($from,$to,$sale_point_id){
	$this->db->select('tbl_goodsreceiving.*,tblacode.*');    
		$this->db->from('tbl_goodsreceiving');
		$this->db->join('tblacode', 'tbl_goodsreceiving.suppliercode = tblacode.acode');
		$this->db->join('tbl_goodsreceiving_detail', 'tbl_goodsreceiving.receiptnos = tbl_goodsreceiving_detail.receipt_detail_id');
		$this->db->where('tbl_goodsreceiving_detail.sub_type','filled_return');
		$this->db->where('tbl_goodsreceiving_detail.category_id=','1');
		$this->db->where('tbl_goodsreceiving.Purchase_type=','purchasereturn');
		
		$this->db->where('tbl_goodsreceiving.receiptdate >=', $from);
		$this->db->where('tbl_goodsreceiving.receiptdate <=', $to);
		$this->db->where('tbl_goodsreceiving.sale_point_id =', $sale_point_id);	

		$this->db->group_by('receipt_detail_id');
		$this->db->order_by("receiptnos", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function edit_salelpg($id){
		$this->db->select('tbl_goodsreceiving.*,tbl_goodsreceiving_detail.*,tblcategory.catname,tblacode.*');
		$this->db->from('tbl_goodsreceiving');
		$this->db->join('tbl_goodsreceiving_detail', 'tbl_goodsreceiving.receiptnos = tbl_goodsreceiving_detail.receipt_detail_id');
		$this->db->join('tblacode', 'tbl_goodsreceiving.suppliercode = tblacode.acode');
		$this->db->join('tblcategory', 'tbl_goodsreceiving_detail.category_id = tblcategory.id');
		$this->db->where('tbl_goodsreceiving.receiptnos=',$id);
		$this->db->order_by("receiptnos", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function edit_makeneworder($id){
		$this->db->select('tbl_orderbooking.*,tbl_orderbooking_detail.*,tblacode.*');
		$this->db->from('tbl_orderbooking');
		$this->db->join('tbl_orderbooking_detail', 'tbl_orderbooking.id = tbl_orderbooking_detail.orderid');
		$this->db->join('tblacode', 'tbl_orderbooking.acode = tblacode.acode');
		$this->db->where('tbl_orderbooking.id=',$id);
		$this->db->order_by("tbl_orderbooking.id", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function update_sale_lpg($data){
		//pm($data);
		date_default_timezone_set("Asia/Karachi");
	    $today=date('Y-m-d h:i:sa');
        $uid=$this->session->userdata('id');
        $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $this->db->trans_start();
		$ins_array = array(
		    "issuedto" =>$data['customer'],
		    "issuedate" =>$data['date'],
		    "remarks" =>$data['remarks'],
		    "sale_type" =>$data['saletype'],
		    "return_gas" =>$data['return_gas'],
		    "return_rate" =>$data['return_rate'],
		    "return_amount" =>$data['return_amount'],
		    "security_amt" =>$data['securityamt'],
		    "gas_amt" =>$data['gasamt'],
		    "total_received" =>$data['totalrecv'],
			 "total_discount" =>$data['total_discount'],
		    "after_discount_amt" =>$data['after_discount_amt'],
		     "vat_percentage" =>$data['gstp'],
		    "vat_amount" =>$data['vat_amount'],
		    "inc_vat_amount" =>$data['inc_vat_amount'],
		    "cylinder_sale_amt" =>$data['sale_security_amt'],
		    "delivery_charges" =>$data['delivery_charges'],
			 "type" =>'Fill',
			 "scode" =>$data['scode'],
			 "pay_mode" =>$data['pay_mode'],     
		    "bank_code" =>$data['bank_code'],     
		    "cheque_no" =>$data['cheque_no'],     
		    "cheque_date" =>$data['cheque_date'],
		    "sale_point_id" =>$sale_point_id,
		    "trans_id" =>$data['trans_id'],  
		    "11_kg_price" => $data['kg_11_price']   
		);
		#----------- add record---------------#`
		$trans_id=$data['trans_id'];
		$id = $_POST['id'];
		$table = "tbl_issue_goods";
		$where = "issuenos= '$id'";
		$update_goods=$this->mod_common->update_table($table,$where,$ins_array);
		$lastqqf=$this->db->last_query();

			 $query="insert into tbl_user_log (user_id,trans_reference,dt,trans_type,form_name,query_exec,trans_dt )
				values
			('$uid' , '$id' , now() , 'UPDATE tbl_issue_goods' ,'SaleLPg.php' ,\"$lastqqf\",'$today')";
			$this->db->query($query);
			if($update_goods){
				return $this->multipleitems_againstid($data,$id,$trans_id,'tbl_issue_goods_detail','34');

			}else{
				return false;
			}
	}

 
	public function get_details($data){
		//pm()
		
		$fromdate=$data['date'];
		$itemid = $data['item_id'];

        $sql="SELECT * from `tblmaterial_coding` WHERE `materialcode`=$itemid";
        $query = $this->db->query($sql);
         
        if($query->num_rows()>0){
            foreach($query->result_array() as $key => $value) {
                $itemname = $value['itemname'];
                $catcode = $value['catcode'];
                //$itemid = $data['item_id'];

                /* here is code for filled */
                /*   opening balnace start     */


                 $sqls ="SELECT  * from `tbl_shop_opening` WHERE `date`<='$fromdate' AND `type`='Filled' AND `materialcode`=$itemid";
                $querys = $this->db->query($sqls)->row_array();


                $sqlv = "SELECT  COALESCE(SUM(`tbl_goodsreceiving_detail`.`quantity`),0) as Dgsumq, COALESCE(SUM(`tbl_goodsreceiving_detail`.`ereturn`),0) as otvendor FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<='$fromdate' AND `tbl_goodsreceiving_detail`.`type`='Filled' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryv = $this->db->query($sqlv);
                $recfrmvenf = $queryv->row_array();
                
                //$sqlv ="SELECT SUM(`quantity`) as Dgsumq,SUM(`ereturn`) as otvendor from `tbl_goodsreceiving_detail` WHERE `type`='Filled' AND `itemid`=$itemid";
                //$queryv = $this->db->query($sqlv);
                //$recfrmvenf = $queryv->row_array();

                //$sqlsc ="SELECT SUM(`qty`) as igsumq,SUM(`returns`) as rfcustomer from `tbl_issue_goods_detail` WHERE `returns`!='' AND `itemid`=$itemid";

               /* $sqlsc = "SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as igsumq,COALESCE(SUM(`tbl_issue_goods_detail`.`returns`),0) as rfcustomer  FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE `issuedate`<='$fromdate' AND  `tbl_issue_goods_detail`.`returns`!='' AND `tbl_issue_goods_detail`.`itemid`=$itemid";
                $querysc = $this->db->query($sqlsc);
                $saltcusf = $querysc->row_array();*/


                $sql_con = "SELECT  COALESCE(SUM(`tbl_cylinderconversion_detail`.`qty`),0) as from_qty FROM `tbl_cylinderconversion_master` INNER JOIN `tbl_cylinderconversion_detail` ON `tbl_cylinderconversion_master`.`trans_id` = `tbl_cylinderconversion_detail`.`trans_id` WHERE `trans_date`<='$fromdate' AND `tbl_cylinderconversion_detail`.`type`='from' AND `tbl_cylinderconversion_detail`.`itemcode`=$itemid";
                $query_con = $this->db->query($sql_con);
                $recfrmvenf_con = $query_con->row_array();
                


                $sql_con_to = "SELECT  COALESCE(SUM(`tbl_cylinderconversion_detail`.`qty`),0) as to_qty FROM `tbl_cylinderconversion_master` INNER JOIN `tbl_cylinderconversion_detail` ON `tbl_cylinderconversion_master`.`trans_id` = `tbl_cylinderconversion_detail`.`trans_id` WHERE `trans_date`<='$fromdate' AND `tbl_cylinderconversion_detail`.`type`='to' AND `tbl_cylinderconversion_detail`.`itemcode`=$itemid";
                $query_con_to = $this->db->query($sql_con_to);
                $recfrmvenf_con_to = $query_con_to->row_array();




                $sqlsc = "SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as igsumq   FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE `issuedate`<='$fromdate'   AND `tbl_issue_goods_detail`.`itemid`=$itemid";
                $querysc = $this->db->query($sqlsc);
                $saltcusf = $querysc->row_array();

               

					if($catcode!=1){
                	
				  $sqlreturnf = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returnqtyf  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `irdate`<='$fromdate' AND `tbl_issue_return_detail`.`type`='Filled' AND `tbl_issue_return`.`type`='purchasereturnother' AND `tbl_issue_return_detail`.`itemid`=$itemid";
                $queryreturnf = $this->db->query($sqlreturnf);
                $return_qtyf = $queryreturnf->row_array();
				
				}else{
				 
			 $sqlreturnf = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returnqtyf  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `irdate`<='$fromdate' AND `tbl_issue_return_detail`.`type`='Filled' AND `tbl_issue_return`.`type`='purchasereturn' AND `tbl_issue_return_detail`.`itemid`=$itemid";
                $queryreturnf = $this->db->query($sqlreturnf);
                $return_qtyf = $queryreturnf->row_array();

				}
				
			
            
                $sqlreturnf_sale = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returnqtyf  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `irdate`<='$fromdate' AND `tbl_issue_return_detail`.`type`='Filled' AND `tbl_issue_return`.`type`='salereturn' AND `tbl_issue_return_detail`.`itemid`=$itemid";
                $queryreturnf_sale = $this->db->query($sqlreturnf_sale);
                $return_qtyf_sale = $queryreturnf_sale->row_array();    

//echo $querys['qty']."<br>"; 
/*echo $recfrmvenf['Dgsumq']."<br>";
echo $saltcusf['igsumq'];

exit;*/
//echo $querys['qty'];
//echo $recfrmvenf['Dgsumq'];
//echo $saltcusf['igsumq'];


                $opgbalfilled = $querys['qty']-$return_qtyf['returnqtyf']+$return_qtyf_sale['returnqtyf']+$recfrmvenf['Dgsumq']-$saltcusf['igsumq']-$recfrmvenf_con['from_qty']+$recfrmvenf_con_to['to_qty'];



               /* $sqls ="SELECT  * from `tbl_shop_opening` WHERE `date`<='$fromdate' AND `type`='Filled' AND `materialcode`=$itemid";
                $querys = $this->db->query($sqls)->row_array();

                $sqlv = "SELECT tbl_goodsreceiving.*,SUM(`tbl_goodsreceiving_detail`.`quantity`) as Dgsumq,SUM(`tbl_goodsreceiving_detail`.`ereturn`) as otvendor  FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<='$fromdate' AND `tbl_goodsreceiving_detail`.`type`='Filled' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryv = $this->db->query($sqlv);
                $recfrmvenf = $queryv->row_array();
                
// just this query less and equal
                $sqlsc = "SELECT tbl_issue_goods.*,SUM(`tbl_issue_goods_detail`.`qty`) as igsumq,SUM(`tbl_issue_goods_detail`.`returns`) as rfcustomer  FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE `issuedate`<='$fromdate' AND `tbl_issue_goods_detail`.`returns`!='' AND `tbl_issue_goods_detail`.`itemid`=$itemid";
                $querysc = $this->db->query($sqlsc);
                $saltcusf = $querysc->row_array();
/*echo $querys['qty']."<br>";
echo $recfrmvenf['Dgsumq']."<br>";
echo $saltcusf['igsumq'];
exit;*/
               // $opgbalfilled = $querys['qty']+$recfrmvenf['Dgsumq']-$saltcusf['igsumq'];
                //$filledstock = $querys['qty'];*/
                
                /*   opening balnace end     */
                /*   rest four columns b/w date for filled     */

                $sqlbdf ="SELECT * from `tbl_shop_opening` WHERE `date` BETWEEN '$fromdate' AND '$todate' AND `type`='Filled' AND `materialcode`=$itemid";
                $querybdf= $this->db->query($sqlbdf)->row_array();

                $sqlvv = "SELECT tbl_goodsreceiving.*,SUM(`tbl_goodsreceiving_detail`.`quantity`) as Dgsumq,SUM(`tbl_goodsreceiving_detail`.`ereturn`) as otvendor  FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate` BETWEEN '$fromdate' AND '$todate' AND `tbl_goodsreceiving_detail`.`type`='Filled' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryvv = $this->db->query($sqlvv);
                $recfrmvenff = $queryvv->row_array();

                 $sqlscc = "SELECT tbl_issue_goods.*,SUM(`tbl_issue_goods_detail`.`qty`) as igsumq,SUM(`tbl_issue_goods_detail`.`returns`) as rfcustomer  FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE `issuedate` BETWEEN '$fromdate' AND '$todate' AND `tbl_issue_goods_detail`.`returns`!='' AND `tbl_issue_goods_detail`.`itemid`=$itemid";
                $queryscc = $this->db->query($sqlscc);
                $saltcusff = $queryscc->row_array();

                /*   end rest four columns b/w date for filled   */
                /* end here is code for filled */
                /* here is code for empty */

                $sqls ="SELECT  * from `tbl_shop_opening` WHERE `date`<='$fromdate' AND  `type`='Empty' AND `materialcode`=$itemid";
                $querys = $this->db->query($sqls)->row_array();

                //$sqlsc ="SELECT SUM(`qty`) as igsumq,SUM(`returns`) as rfcustomer from `tbl_issue_goods_detail` WHERE `returns`='' AND `itemid`=$itemid";
                 $sqlsc = "SELECT  COALESCE(SUM(`tbl_issue_goods_detail`.`returns`),0) as rfcustomer  FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE `issuedate`<='$fromdate'   AND `tbl_issue_goods_detail`.`itemid`=$itemid";
                $querysc = $this->db->query($sqlsc);
                $saltcuse = $querysc->row_array();

                //$sqlv ="SELECT SUM(`quantity`) as Dgsumq,SUM(`ereturn`) as otvendor from `tbl_goodsreceiving_detail` WHERE `type`='Empty' AND `itemid`=$itemid";
                 $sqlv = "SELECT COALESCE(SUM(`tbl_goodsreceiving_detail`.`quantity`),0) as Dgsumq   FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<='$fromdate' AND  `tbl_goodsreceiving_detail`.`type`='Empty' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryv = $this->db->query($sqlv);
                $recfrmvene=$queryv->row_array();



                 $sqlv_e = "SELECT  COALESCE(SUM(`tbl_goodsreceiving_detail`.`ereturn`),0) as otvendor  FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<='$fromdate'   AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryv_e = $this->db->query($sqlv_e);
                $recfrmvene_e=$queryv_e->row_array();

				 
					$sqlreturn = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returnqty  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `irdate`<='$fromdate' AND `tbl_issue_return_detail`.`type`='Empty' AND `tbl_issue_return`.`type`='purchasereturn' AND `tbl_issue_return_detail`.`itemid`=$itemid";
                $queryreturn = $this->db->query($sqlreturn);
                $return_qty = $queryreturn->row_array();
			 

                $sqlreturn_sale = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returnqty  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `irdate`<='$fromdate' AND `tbl_issue_return_detail`.`type`='Empty' AND `tbl_issue_return`.`type`='salereturn' AND `tbl_issue_return_detail`.`itemid`=$itemid";
                $queryreturn_sale = $this->db->query($sqlreturn_sale);
                $return_qty_sale = $queryreturn_sale->row_array();


//echo $querys['qty'];
//echo $saltcuse['igsumq'];
//echo $recfrmvene['otvendor'];
//echo $recfrmvene['Dgsumq'];
//exit;
                //pm($return_qty['returnqty']);
                //pm($recfrmvene_e['otvendor']);
                //pm($return_qty['returnqty']);
                $opgbalempty = $querys['qty']+$saltcuse['rfcustomer']-$return_qty['returnqty']+$return_qty_sale['returnqty']+$recfrmvene['Dgsumq']-$recfrmvene_e['otvendor']+$recfrmvenf_con['from_qty']-$recfrmvenf_con_to['to_qty'];
                //$opgbalempty = $querys['qty'];



                /*$sqls ="SELECT  * from `tbl_shop_opening` WHERE `date`<='$fromdate' AND `type`='Empty' AND `materialcode`=$itemid";
                $querys = $this->db->query($sqls)->row_array();

                //$sqlsc ="SELECT SUM(`qty`) as igsumq,SUM(`returns`) as rfcustomer from `tbl_issue_goods_detail` WHERE `returns`='' AND `itemid`=$itemid";
                $sqlsc = "SELECT tbl_issue_goods.*,SUM(`tbl_issue_goods_detail`.`qty`) as igsumq,SUM(`tbl_issue_goods_detail`.`returns`) as rfcustomer  FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE `issuedate`<='$fromdate' AND `tbl_issue_goods_detail`.`returns`='' AND `tbl_issue_goods_detail`.`itemid`=$itemid";
                $querysc = $this->db->query($sqlsc);
                $saltcuse = $querysc->row_array();

                //$sqlv ="SELECT SUM(`quantity`) as Dgsumq,SUM(`ereturn`) as otvendor from `tbl_goodsreceiving_detail` WHERE `type`='Empty' AND `itemid`=$itemid";
                $sqlv = "SELECT tbl_goodsreceiving.*,SUM(`tbl_goodsreceiving_detail`.`quantity`) as Dgsumq,SUM(`tbl_goodsreceiving_detail`.`ereturn`) as otvendor  FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<='$fromdate' AND `tbl_goodsreceiving_detail`.`type`='Empty' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryv = $this->db->query($sqlv);
                $recfrmvene=$queryv->row_array();

                //$opgbalempty = $querys['qty']+$saltcuse['igsumq']-$recfrmvene['Dgsumq'];
                $opgbalempty = $querys['qty']+$saltcuse['igsumq']+$recfrmvene['Dgsumq']-$recfrmvene['otvendor'];
                */
                
                /* end here is code for empty */
                /*   rest four columns b/w date for empty    */

                $sqlbdf ="SELECT * from `tbl_shop_opening` WHERE `date` BETWEEN '$fromdate' AND '$todate' AND `type`='Empty' AND `materialcode`=$itemid";
                $querybdf= $this->db->query($sqlbdf)->row_array();

                $sqlsccc = "SELECT tbl_issue_goods.*,SUM(`tbl_issue_goods_detail`.`qty`) as igsumq,SUM(`tbl_issue_goods_detail`.`returns`) as rfcustomer  FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE `issuedate` BETWEEN '$fromdate' AND '$todate' AND `tbl_issue_goods_detail`.`returns`='' AND `tbl_issue_goods_detail`.`itemid`=$itemid";
                $querysccc = $this->db->query($sqlsccc);
                $saltcusee = $querysccc->row_array();



                 $sqlvvv = "SELECT tbl_goodsreceiving.*,SUM(`tbl_goodsreceiving_detail`.`quantity`) as Dgsumq,SUM(`tbl_goodsreceiving_detail`.`ereturn`) as otvendor  FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate` BETWEEN '$fromdate' AND '$todate' AND `tbl_goodsreceiving_detail`.`type`='Empty' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryvvv = $this->db->query($sqlvvv);
                $recfrmvenee=$queryvvv->row_array();


                /*   end rest four columns b/w date for empty    */
                /* end here is code for empty */

                $datas[] = array(
                    'itemid' => $itemname,
                    'catcode' => $catcode,
                    'filled' => $opgbalfilled,
                    'empty' => $opgbalempty,
                    'RFVF'=>$recfrmvenff['Dgsumq'],
                    'otvendorf'=>$recfrmvenff['otvendor'],
                    'saleoutf'=>$saltcusff['igsumq'],
                    'rfcustomerf'=>$saltcusff['rfcustomer'],
                    'RFVE'=>$recfrmvenee['Dgsumq'],
                    'otvendore'=>$recfrmvenee['otvendor'],
                    'saleoute'=>$saltcusee['igsumq'],
                    'rfcustomere'=>$saltcusee['rfcustomer'],
                    'fromdate'=>$fromdate,
                    'todate'=>$todate,
                    //'filledstock'=>$filledstock,
                );
            
            }
        }
        
        return $datas;
    }
 
}

?>