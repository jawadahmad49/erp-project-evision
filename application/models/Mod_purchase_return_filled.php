<?php

class Mod_purchase_return_filled extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }
 
	
	public function add_purchase_return_filled($data){

		$cheque_no= '';
		$cheque_dt= date('Y-m-d');
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $cash_code=$fix_code['cash_code'];

        $trans_id = $this->db->query("select max(trans_id) as trans_id from tbl_issue_goods where sale_point_id='$sale_point_id'")->row_array()['trans_id'];

      if($trans_id==''){
      	 $trans_id=1;
      	}else{
      		 $trans_id=$trans_id+1;
      	}

		$net_payable=$data['net_payable'];
		$discount_amt=$data['discount'];
$this->db->trans_start();

		$ins_array = array(
		    "issuedto" =>$data['vendor'],
		    "issuedate" =>$data['date'],
		    "sale_point_id" =>$sale_point_id,
		    "trans_id" =>$trans_id,
		    "remarks" =>$data['remarks'],
		    "gas_amt" =>$data['total_bill'],
		    "type" =>'Fill',
		    "11_kg_price"=>$data['kg_11_price']
		   // "created_date" =>date('Y-m-d'),
		    //"created_by" =>$this->session->userdata('id')      
		);
		//pm($ins_array);exit();

		#----------- add record---------------#
		$table = "tbl_issue_goods";
		$add_goods = $this->mod_common->insert_into_table($table, $ins_array);
		$insert_id = $add_goods;
			if($add_goods){
				$query=$this->multipleitems_againstid($data,$insert_id,$trans_id,'tbl_issue_goods_detail');
				if($query){
					return $add_goods;
				}else{
					return false;
				}
			}else{
				return false;
		}
	}

	public function multipleitems_againstid($data,$goodsid,$trans_id,$table){

	    $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();

	    $remarks=$data['remarks'];
	    $issuedate = $data['date'];

		
		$datas = array();
		foreach($data['item'] as $key=>$value) {

			$batch_status ="open"; 
			$Batch_stock = $data['qty'][$key];

			$datas[] = array(
				'ig_detail_id' => $goodsid,
				'sale_point_id' =>$sale_point_id,
		        'trans_id' =>$trans_id, 
		 	    'itemid' => $data['item'][$key],
		   		'qty' => $data['qty'][$key],
		    	'sprice' => $data['unitcost'][$key],
		    	'type' => $data['type'][$key],
		    	'amount' => $data['amount'][$key],
		    	'Posted_Date' =>$issuedate,
		   	);

			$netamount+=$data['amount'][$key];

			$gstAmt+=$data['gst_amount'][$key];
			$gst=$data['gst'][$key];

			$naritem = $value['item'];
			$narqty=$data['qty'][$key];
			$narprice=$data['unitcost'][$key];
			
		}
			$this->db->insert_batch($table, $datas);




foreach ($data['item'] as $key => $value) {
	$itemid = $data['item'][$key];
	$amount =  $data['amount'][$key];
	$qty =  $data['qty'][$key];

	$amount_open = 0;
	$qty_open = 0;
	$t_amount_open = 0;

	$getOpening = $this->db->get_where("tbl_shop_opening",array("materialcode"=>$itemid))->row_array();
	if(count($getOpening) != 0){
		$amount_open = $getOpening['cost_price'];
		$qty_open = $getOpening['qty'];

		$t_amount_open = $amount_open*$qty_open;
	}
	

			$arr=(explode("-",$recvd_date));
			$from_date_for=$arr[0].'-'.$arr[1];
		 
				$sql_in="SELECT d.* FROM tbl_issue_goods_detail d, tbl_issue_goods m where  d.itemid = '$itemid' 
				and d.ig_detail_id= m.issuenos
				and m.issuedate like '$from_date_for%'
				order by recvd_date   ";

				
				$resul = $this->db->query($sql_in);
				$val = $resul->result_array();
				foreach($val as $key=>$value) {
					

				$quantity = $value['quantity'];
				$amt = $value['inc_vat_amount'];

				$new_quantity+=$quantity ;
				$total_amt+=$amt;
				}



				$total_amt+=$amount;
				$new_quantity+=$qty;
				$new_rate=round($total_amt/$new_quantity,2);
				$udata['cost_price'] = round($new_rate,0);

				$this->db->where("materialcode",$itemid);
				$this->db->update("tblmaterial_coding",$udata);




 
			$receiptdate=$data['date'];
			$vendorcode=$data['vendor'];
			$user = $this->session->userdata('id');
			//$goodsid=$goodsid."-G";
			$goodsids=$sale_point_id."-PRF-".$trans_id;
			//$new_goodsids=$sale_point_id."-Purchase Payment-".$trans_id;
		/////////////////////////// here is code//////////////////
			 
		  $check_exists="SELECT * FROM `tbltrans_master` WHERE `vno` = '$goodsids' and `vtype`='SV'";
		  $query = $this->db->query($check_exists);

		  if($query->num_rows()!= 0)
		  {
		    $sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsids' and `vtype`='SV'";
		    $this->db->query($sqld);
		    $sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsids' and `vtype`='SV'";
		    $this->db->query($sqlm);
		    $sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsids' ";
		    $this->db->query($sqld);
		    $sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsids' ";
		    $this->db->query($sqlm);
		  }

		 $items_detail='';
		 


				 $sql_in="SELECT  d.itemid,d.qty,i.itemname,d.amount,d.sprice FROM  tbl_issue_goods m,tbl_issue_goods_detail d ,tblmaterial_coding i where m.issuenos=d.ig_detail_id and
				d.itemid=i.materialcode and m.issuenos ='$goodsid' ";

				
				$resul = $this->db->query($sql_in);
				$rw = $resul->result_array();
				foreach($rw as $key=>$value) {

					$item_amount=0;
					$items_detail="";
					$nar1="";
					$gate_pas=$value['ref1'];
					$item_amount=$value['inc_vat_amount'];
					
					//$items_detail_m.=$value['itemname'].' ,  '.$value['quantity'].'@'.$value['rate'].':';
					$items_detail_m.=$value['itemname'].' ,  '.$value['quantity'].'@'.$value['rate'];
			 
			}
		 $nar='Purchase Return Filled #:'.$trans_id.',  '.$items_detail_m.'('.$remarks.')';
				
			$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date,sale_point_id,trans_id)
			values
			('$goodsids' , 'SV' , '$netamount' , '$netamount' ,'No' ,'No' ,'$user','PRF' ,'$receiptdate','$sale_point_id','$trans_id')";
			$this->db->query($querys);
			$master_id = $this->db->query("select masterid from tbltrans_master where vno='$goodsids'")->row_array()['masterid'];

		   	$ex_total = $netamount - $gstAmt;

		     //insert into transaction details debit entry
		    $sr++;
				$stock_code=$fix_code['stock_code'];
				 
				 $sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id) 
			   values('$goodsids','$sr','$vendorcode','$vendorname','$amount','0','$nar','SV','PRF','$receiptdate','$sale_point_id','$trans_id','$master_id')";
				$this->db->query($queryd);
				$debit+=$amount;
				
				 $sr++;
			    $resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id)
				values('$goodsids','$sr','$stock_code','$stock_name','0','$amount','$nar','SV','PRF','$receiptdate','$sale_point_id','$trans_id','$master_id')";
				$this->db->query($resultdd);
				$credit+=$amount;
			   
					
		  	
			 
				 

		
		   		$updates ="UPDATE `tbl_issue_goods` set `post_gl`=1 where `issuenos`='$goodsid'";
		   		$q =  $this->db->query($updates);

		   		if ($debit!=$credit) {
			    $this->session->set_flashdata('err_message', 'Debit Sides And Credit Sides Are Not Equal!');
				redirect(SURL . 'Purchase_return_filled/');}

		   		$this->db->trans_complete();
		   		return $q;
		   		//echo "<pre>"; print_r($this->db->queries);exit;
		 
		}
	}


	public function manage_return_filled($from,$to,$sale_point_id){
		$this->db->select('tbl_issue_goods.*,tblacode.*');    
		$this->db->from('tbl_issue_goods');
		$this->db->join('tblacode', 'tbl_issue_goods.issuedto = tblacode.acode');
		$this->db->join('tbl_issue_goods_detail', 'tbl_issue_goods.issuenos = tbl_issue_goods_detail.ig_detail_id');
		$this->db->where('tbl_issue_goods_detail.type','Filled');
		$this->db->where('tbl_issue_goods.type=','Fill');
		
		$this->db->where('tbl_issue_goods.issuedate >=', $from);
		$this->db->where('tbl_issue_goods.issuedate <=', $to);
		$this->db->where('tbl_issue_goods.sale_point_id =', $sale_point_id);	

		$this->db->group_by('ig_detail_id');
		$this->db->order_by("issuenos", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function edit_purchase_return($id){
		$this->db->select('tbl_issue_goods.*,tbl_issue_goods_detail.*,tblacode.*');
		$this->db->from('tbl_issue_goods');
		$this->db->join('tbl_issue_goods_detail', 'tbl_issue_goods.issuenos = tbl_issue_goods_detail.ig_detail_id');
		$this->db->join('tblacode', 'tbl_issue_goods.issuedto = tblacode.acode');
		$this->db->where('tbl_issue_goods.issuenos=',$id);
		$this->db->order_by("issuenos", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}
	

	public function update_purchase_return($data){

        $cheque_no= '';
		$cheque_dt= date('Y-m-d');
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $cash_code=$fix_code['cash_code'];
		$cheque_no= '';
		$cheque_dt= date('Y-m-d');
		$trans_id = $data['trans_id'];
		$net_payable=$data['net_payable'];
		$discount_amt=$data['discount'];


$this->db->trans_start();

		$ins_array = array(
		    "issuedto" =>$data['vendor'],
		    "issuedate" =>$data['date'],
		    "sale_point_id" =>$sale_point_id,
		    "trans_id" =>$trans_id,
		    "remarks" =>$data['remarks'],
		    "gas_amt" =>$data['total_bill'],
		    "type" =>'Fill',
		    "11_kg_price"=>$data['kg_11_price']    
		);
         //echo $trans_id;exit();
		#----------- add record---------------#
		$id = $_POST['id'];
		$table = "tbl_issue_goods";
		$where = "issuenos= '$id'";
		$update_goods=$this->mod_common->update_table($table,$where,$ins_array);
		
			if($update_goods){
				return $this->updatemultiple_againstid($data,$id,'tbl_issue_goods_detail');
			}else{
				return false;
			}
	}

	public function updatemultiple_againstid($data,$goodsid,$table){

		$datas = array();
		$datai = array();

	    $remarks=$data['remarks'];
	    $trans_id=$data['trans_id'];
		$issuedate = $data['date'];
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
     



		foreach($data['item'] as $key=>$value) {
			$datas[] = array(
				'srno' => $data['items_detailid'][$key],
				'ig_detail_id' => $goodsid,
				'sale_point_id' =>$sale_point_id,
		        'trans_id' =>$data['trans_id'],
			    'itemid' => $data['item'][$key],
			    'qty' => $data['qty'][$key],
			    'sprice' => $data['unitcost'][$key],
			    'type' => $data['type'][$key],
			    'Posted_Date'=>$issuedate,
			    'amount'=>$data['amount'][$key],
			   );
			$netamount+=$data['amount'][$key];
			$gstAmt+= $data['gst_amount'][$key];
			$gst= $data['gst'][$key];

			$naritem = $value['item'];
			$narqty=$data['qty'][$key];
			$narprice=$data['unitcost'][$key];
		}
			
		foreach($datas as $key=>$value) {
			if($value['srno']){
				$datau[] = $value;
			}else{ 
				$datai[] = $value;
			}
		}

foreach ($data['item'] as $key => $value) {
	$itemid = $data['item'][$key];
	$amount =  $data['amount'][$key];
	$qty =  $data['qty'][$key];

	$amount_open = 0;
	$qty_open = 0;
	$t_amount_open = 0;
		$cheque_no= '';
		$cheque_date= '';
		$bank_name= '';

		$net_payment=$data['net_payable'];
		$discount=$data['discount'];

		if($datau){ $this->db->update_batch($table, $datau,'srno');}
		if($datai){ $this->db->insert_batch($table, $datai); }

		
		
		
		
		
		
				$new_quantity=0;
				$total_amt=0;
				$arr=(explode("-",$recvd_date));
				$from_date_for=$arr[0].'-'.$arr[1];
		 
			 	$sql_in="SELECT d.* FROM tbl_issue_goods_detail d, tbl_issue_goods m where  d.itemid = '$itemid' 
				and d.ig_detail_id= m.issuenos
				and m.issuedate like '$from_date_for%'
				order by issuedate   ";
 
				
				$resul = $this->db->query($sql_in);
				$val = $resul->result_array();
				foreach($val as $key=>$value) {
					

				$quantity = $value['quantity'];
				$amt = $value['inc_vat_amount'];

				$new_quantity+=$quantity ;
				$total_amt+=$amt;
				}


 
				$new_rate=round($total_amt/$new_quantity,2);
				$udata['cost_price'] = round($new_rate,0);

				$this->db->where("materialcode",$itemid);
				$this->db->update("tblmaterial_coding",$udata);

		
		
		
		
		
		
		
		
			$receiptdate=$data['date'];
			$vendorcode=$data['vendor'];
			$user = $this->session->userdata('id');
			$goodsids=$sale_point_id."-PRF-".$trans_id;
			
		/////////////////////////// here is code//////////////////
			 
		  $check_exists="SELECT * FROM `tbltrans_master` WHERE `vno` = '$goodsids' ";
		  $query = $this->db->query($check_exists);

		    $check_exists_bank="SELECT * FROM `tbltrans_master` WHERE `vno` = '$goodsids' ";
		   
		  $query_bank = $this->db->query($check_exists_bank);

		  if($query_bank->num_rows()!=0)
		  {
		  		$sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsids' ";
		    	$this->db->query($sqld);
		    	$sqld ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsids' ";
		    	$this->db->query($sqld);
		  }
//echo "<pre>";print_r($this->db->queries); echo $query->num_rows();exit;
		  if($query->num_rows()!=0)
		  {
		    $sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsids' ";
		    $this->db->query($sqld);
		    $sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsids'  ";
		    $this->db->query($sqld);

		    $sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsids' ";
		    $this->db->query($sqlm);
		    $sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsids' ";
		    $this->db->query($sqlm);
		  }
		  
		 $inv_num='';
		 $items_detail='';
		
 $sql_in="SELECT  d.itemid,d.qty,i.itemname,d.amount,d.sprice FROM  tbl_issue_goods m,tbl_issue_goods_detail d ,tblmaterial_coding i where m.issuenos=d.ig_detail_id and
				d.itemid=i.materialcode and m.issuenos ='$goodsid' ";

				
				$resul = $this->db->query($sql_in);
				$rw = $resul->result_array();
				foreach($rw as $key=>$value) {

					$item_amount=0;
					$items_detail="";
					$nar1="";
					$gate_pas=$value['ref1'];
					$item_amount=$value['inc_vat_amount'];
					
					//$items_detail_m.=$value['itemname'].' ,  '.$value['quantity'].'@'.$value['rate'].':';
					$items_detail_m.=$value['itemname'].' ,  '.$value['quantity'].'@'.$value['rate'];
			 
			}
		 $nar='Purchase Return Filled #:'.$trans_id.',  '.$items_detail_m.'('.$remarks.')';
				
			$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date,sale_point_id,trans_id)
			values
			('$goodsids' , 'SV' , '$netamount' , '$netamount' ,'No' ,'No' ,'$user','PRF' ,'$receiptdate','$sale_point_id','$trans_id')";
			$this->db->query($querys);
			$master_id = $this->db->query("select masterid from tbltrans_master where vno='$goodsids'")->row_array()['masterid'];

		

		     //insert into transaction details debit entry
		    $sr++;
				$stock_code=$fix_code['stock_code'];
				 
				 $sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id) 
			   values('$goodsids','$sr','$vendorcode','$vendorname','$netamount','0','$nar','SV','PRF','$receiptdate','$sale_point_id','$trans_id','$master_id')";
				$this->db->query($queryd);
				$debit+=$netamount;
				
				 $sr++;
			    $resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id)
				values('$goodsids','$sr','$stock_code','$stock_name','0','$netamount','$nar','SV','PRF','$receiptdate','$sale_point_id','$trans_id','$master_id')";
				$this->db->query($resultdd);
				$credit+=$netamount;
			   
			 
				 

		
		  
 
		   	 
		   		$updates ="UPDATE `tbl_issue_goods` set `post_gl`=1 where `issuenos`='$goodsid'";
		   		$q = $this->db->query($updates);

		   		if ($debit!=$credit) {
			    $this->session->set_flashdata('err_message', 'Debit Sides And Credit Sides Are Not Equal!');
				redirect(SURL . 'Purchase_return_filled/');
		   		}

		   		$this->db->trans_complete();
		   		return $q;
		   		
		 
		}
		
	}
 
}


?>