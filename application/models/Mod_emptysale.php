<?php

class Mod_emptysale extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }

	///customer taxes  1001003001
	//// vendor taxes 4001002001
	
	public function add_sale_lpg($data){
		 //pm($data); exit();

		 if(isset($data['condition'])){
		 	if($data['condition'] == "Filled"){
		 		$newtypee = "Fill";
		 	}else{
		 		$newtypee = $data['condition'];
		 	}
		 	
		 }else{
		 	$newtypee = "Empty";
		 }
		 $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $trans_id = $this->db->query("select max(trans_id) as trans_id from tbl_issue_goods where sale_point_id='$sale_point_id'")->row_array()['trans_id'];

      if($trans_id==''){
      	 $trans_id=1;
      	}else{
      		 $trans_id=$trans_id+1;
      	}
        $this->db->trans_start();
		$ins_array = array(
		    "issuedto" =>$data['customer'],
		    "issuedate" =>$data['date'],
		    "remarks" =>$data['remarks'],
		    "sale_type" =>$data['saletype'],
		    "return_gas" =>$data['returngas'],
		    "return_rate" =>$data['returnrate'],
		    "return_amount" =>$data['returntotal'],
		    "security_amt" =>$data['securityamt'],
		    "vat_percentage" =>$data['gstp'],
		    "vat_amount" =>$data['vat_amount'],
		    "inc_vat_amount" =>$data['inc_vat_amount'],
		    "type" => $newtypee,
		    "sale_point_id" =>$sale_point_id,
		    "trans_id" =>$trans_id,
		    "gas_amt" =>$data['gasamt'],
		    "scode" =>$data['scode'],
		    "total_received" =>$data['totalrecv'],
		    "gas_amt" =>$data['gasamt'],
		    "total_amount" =>$data['total_bill'],
			 "pay_mode" =>$data['pay_mode'],     
		    "bank_code" =>$data['bank_code'],     
		    "cheque_no" =>$data['cheque_no'],     
		    "cheque_date" =>$data['cheque_date'],     
		   // "created_date" =>date('Y-m-d'),
		    //"created_by" =>$this->session->userdata('id')      
		);

		//pm($this->input->post());

		#----------- add record---------------#
		$table = "tbl_issue_goods";
		$add_goods = $this->mod_common->insert_into_table($table, $ins_array);
		$insert_id = $add_goods;
			if($add_goods){
				return $this->multipleitems_againstid($data,$insert_id,$trans_id,'tbl_issue_goods_detail');
			}else{
				return false;
		}
	}
	public function multipleitems_againstid($data,$goodsid,$trans_id,$table,$updated_value=''){

		$remarks = $data['remarks'];
		$vat_amount = $data['vat_amount'];
		$Posted_Date = $data['date'];
		$gstp = $data['gstp'];
		$tax_amount=0;
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
		
		if($updated_value=='')
		{
		 $stock_code=$fix_code['stock_code'];
		 $cash_inhand=$fix_code['cash_code'];
		 $tax_acode=$fix_code['tax_receive'];
		 $security_code=$fix_code['security_code'];
	     $cash_type='CR';
		$datas = array();
		foreach($data['item'] as $key=>$value) {
		$datas[] = array(
			'ig_detail_id' => $goodsid,
			'sale_point_id' =>$sale_point_id,
		    'trans_id' =>$trans_id,
		    'itemid' => $data['item'][$key],
		    'item_return' => $data['item_return'][$key],
		    'qty' => $data['qty'][$key],
		    'vat_percentage' => $data['gst'][$key],
		    'vat_amount' => $data['gst_amounttotal'][$key],
		    'ex_vat_total_amount' => $data['ex_amounttotal'][$key],
		    'sprice' => $data['price'][$key],
		    'total_amount' => $data['amounttotal'][$key], 
		    'scode' => $data['scode'],
		    'Posted_Date' => $Posted_Date,
		    'type' =>'Empty',
		    'wrate' => $data['price'][$key],
		    'catcode' => '1',

		 );
		   
		   $tax_amount+=$data['gst_amounttotal'][$key];
			$netamount+=$data['price'][$key]*$data['qty'][$key];
 		 	$sale_security+=$data['security'][$key]*$data['qty'][$key];
 		 	$gst=$data['gst'][$key];
 
			 
			$netamountr=$data['totalrecv'];

			$securityamts =$data['securityamt'];

			 
			$naritem = $value['item'];
			$narqty=$data['qty'][$key];
			$narprice=$data['price'][$key];

		}
		
		
			 if($tax_amount){
	  $netamount_ex=$netamount+$tax_amount ;
	}else{
	  $netamount_ex=$netamount;
		
	}
		
		
		 	$this->db->insert_batch($table, $datas);
		 }
		 else
		 {

		$datas = array();
		$datai = array();
		foreach($data['item'] as $key=>$value) {
			$datas[] = array(
				'srno' => $data['items_detailid'][$key],
				'ig_detail_id' => $goodsid,
				'sale_point_id' =>$sale_point_id,
		        'trans_id' =>$trans_id,
			    'itemid' => $data['item'][$key],
			    'item_return' => $data['item_return'][$key],
			    'qty' => $data['qty'][$key],
				'vat_percentage' => $data['gst'][$key],
		        'vat_amount' => $data['gst_amounttotal'][$key],
		        'ex_vat_total_amount' => $data['ex_amounttotal'][$key],
			    'sprice' => $data['price'][$key],
			    'scode' => $data['scode'],
			    'total_amount' => $data['amounttotal'][$key],
			    'Posted_Date' => $Posted_Date,
		        'type' =>'Empty',
		        'wrate' => $data['price'][$key],
		        'catcode' => '1',

			   
			   );
			    $tax_amount+=$data['gst_amounttotal'][$key];
			$netamount+=$data['price'][$key]*$data['qty'][$key];
		 $sale_security+=$data['security'][$key]*$data['qty'][$key];
		 $gst=$data['gst'][$key];
 
			$netamountr=$data['totalrecv'];
			 
			$securityamts =$data['securityamt'];

			$naritem = $value['item'];
			$narqty=$data['qty'][$key];
			$narprice=$data['price'][$key];
		}
		
		 if($tax_amount){
	  $netamount_ex=$netamount+$tax_amount ;
	}else{
	  $netamount_ex=$netamount;
		
	}
		  
	 
		//	print $sale_security;
		foreach($datas as $key=>$value) {
			if($value['srno']){
				$datau[] = $value;
			}else{ 
				$datai[] = $value;
			}
		}
			if($datau){ $this->db->update_batch($table, $datau,'srno');}
			if($datai){ $this->db->insert_batch($table, $datai); }
		 }
			
			
			
			
			
			
		/////////////////////////// here is code//////////////////
		 	$receiptdate=$data['date'];
			$vendorcode=$data['customer'];
			$user = $this->session->userdata('id');
			$goodsidt=$sale_point_id."-Sale-".$trans_id;
		
			 

 			$return_rate=0;
			$return_gas=0;
			$return_amount=0;

			$sql_in_m="SELECT  return_rate,return_gas,return_amount FROM  tbl_issue_goods 
			where issuenos ='$goodsid' ";


			$resul_m = $this->db->query($sql_in_m);
			$rw_m = $resul_m->result_array();
			foreach($rw_m as $key=>$value_m) {
			$return_rate=$value_m['return_rate'];
			$return_gas=$value_m['return_gas'];
			$return_amount=$value_m['return_amount'];

			}
		$nar_return='Gas Return '.$return_gas.'KG@'.$return_rate.' , ('.$remarks.')';


		$check_exists="SELECT * FROM `tbltrans_master` WHERE `vno` = '$goodsidt'";

		  $query = $this->db->query($check_exists);

	if($query->num_rows()!=0)
	{
		$sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsidt'"; $this->db->query($sqld);
    	$sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsidt'"; $this->db->query($sqlm);
	  
    
	}

 	    $stock_code=$fix_code['stock_code'];
		$cash_inhand=$fix_code['cash_code'];
		$gas_return_acc=$fix_code['empty_stock_code'];
		$security_code=$fix_code['security_code'];
		$items_detail='';
 		$tax_acode=$fix_code['tax_receive'];

		 $sql_in="SELECT  m.security_amt,d.itemid,d.qty,i.itemname,d.amount,d.sprice,d.wrate,d.total_amount,d.returns,d.vat_percentage
			FROM  tbl_issue_goods m,tbl_issue_goods_detail d ,tblmaterial_coding i where m.issuenos=d.ig_detail_id and
				d.itemid=i.materialcode and m.issuenos ='$goodsid' ";

				
				$resul = $this->db->query($sql_in);
				$rw = $resul->result_array();
				foreach($rw as $key=>$value) {
					$item_amount=0;
					$items_detail="";
					$nar1="";
					
					$returns=$value['returns'];
					$gate_pas=$value['ref1'];
					$item_amount=$value['total_amount'];
					$security_amts=$value['security_amt'];
					$wrate=$value['wrate'];
					$gst=$value['vat_percentage'];
					
				 if($wrate>0){
					 
					$items_detail_m.=$value['itemname'].' ,  '.$value['qty'].'@'.$value['sprice'].',security '.$wrate;
				 }else{
					$items_detail_m.=$value['itemname'].' ,  '.$value['qty'].'@'.$value['sprice'] ;
					 
				 }	
				  
				 $items_detail_m.=', empty returned '.$returns.':';
				 

				}
		$items_detail_m= substr_replace($items_detail_m, "", -1);
				
		$nar='Empty Sale against #:'.$trans_id.',  '.$items_detail_m.' , ('.$remarks.')';
		$nar_tax=$gstp.' % Empty Sale against #:'.$trans_id.',  '.$items_detail_m.' , ('.$remarks.')';	
		$scode=$data['scode'];
		$total_bill=$data['total_bill'];
		
///////////////////////// sale entry for gas
     $querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date,sale_point_id,trans_id)
			values
			('$goodsidt' , 'SV' , '$total_bill' , '$total_bill' ,'No' ,'No' ,'$user','SP' ,'$receiptdate','$sale_point_id','$trans_id')";
			$this->db->query($querys);
			$master_id = $this->db->query("select masterid from tbltrans_master where vno='$goodsidt'")->row_array()['masterid'];
				$sr++;
		        $queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode) 
			   values('$goodsidt','$master_id','$sr','$vendorcode','$vendorname','0','$netamount','$nar','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
				$this->db->query($queryd);
				$debit+=$netamount;

				$empty_stock_code=$fix_code['sale_cylinder_code'];
	// 			// $empty_sale_code=$fix_code['empty_sale_code'];
	// 			// $empty_sale_code=$this->db->query("select acode from tblacode where general='$empty_sale_code' and atype='Child'")->result_array();
	// 			// //pm($empty_sale_code);exit();
	// 			//  $cylinder_11=$empty_sale_code[0]['acode'];
	// 			//  $cylinder_45kg=$empty_sale_code[1]['acode'];
	// 			//  $cylinder_15kg=$empty_sale_code[2]['acode'];
	// 			//  $cylinder_6kg=$empty_sale_code[3]['acode'];
	// 			//  $cylinder_4kg=$empty_sale_code[4]['acode'];
	// 			//  $cylinder_10kg=$empty_sale_code[5]['acode'];
	// 			//  $cylinder_5kg=$empty_sale_code[6]['acode'];
	// 			//  $cylinder_6kgcom=$empty_sale_code[7]['acode'];

	// 			//  foreach($data['item'] as $key=>$value) {
	//    //          $item_code=$data['item'][$key];
	// 	  //       if ($item_code=='1') {
	// 			// 	$stock_code=$cylinder_4kg;
	// 			// }else if ($item_code=='2') {
	// 			// 	$stock_code=$cylinder_5kg;
	// 			// }else if ($item_code=='3') {
	// 			// 	$stock_code=$cylinder_6kg;
	// 			// }else if ($item_code=='4') {
	// 			// 	$stock_code=$cylinder_10kg;
	// 			// }else if ($item_code=='5') {
	// 			// 	$stock_code=$cylinder_11;
	// 			// }else if ($item_code=='6') {
	// 			// 	$stock_code=$cylinder_15kg;
	// 			// }else if ($item_code=='7') {
	// 			// 	$stock_code=$cylinder_45kg;
	// 			// }else if ($item_code=='8') {
	// 			// 	$stock_code=$cylinder_6kgcom;
	// 			// }

	// $amounttotal=$data['ex_amounttotal'][$key];
	// $gst=$data['gst'][$key];
	// $itemname=$this->db->query("select itemname from tblmaterial_coding where materialcode='$item_code' ")->row_array()['itemname'];
	// $nar_new='Empty Sale against #:'.$trans_id.',  '.$itemname.'('.$remarks.')';
	

				$sr++;
		        $queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode) 
			   values('$goodsidt','$master_id','$sr','$empty_stock_code','','$netamount','0','$nar','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
				$this->db->query($queryd);	
				$credit+=$netamount;
			//}


				if($vat_amount>0){
					$sr++;
					$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode) 
					values('$goodsidt','$master_id','$sr','$vendorcode','$vendorname','0','$vat_amount','Tax : $nar_tax','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode' )";
					$this->db->query($queryd);
					$debit+=$vat_amount;
					$sr++;
					$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode) 
					values('$goodsidt','$master_id','$sr','$tax_acode','','$vat_amount','0','Tax : $nar_tax','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
					$this->db->query($queryd);
					$credit+=$vat_amount;
				}
///////////////////////// recv entry for gas if amount recv>0

				if($netamountr>0) {
					
					$recv_nar='Receive against #:'.$trans_id.',  '.$items_detail_m.' , ('.$remarks.')';	
					if($return_amount>0) {
						$recv_nar=$nar.','.$nar_return;
					}
					
					
					$chequedate=''; $chequeno='';
					if($data['pay_mode']=='Bank'){
					$cash_inhand=	$data['bank_code'];
					$cash_type='BR';

					$chequedate=$data['cheque_date'];
					$chequeno=$data['cheque_no'];
					}
					
					
				$sr++;
		      


				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequedate,chequeno,sale_point_id,trans_id,scode) 
			   values('$goodsidt','$master_id','$sr','$cash_inhand','','$netamountr','0','$recv_nar','SV','$cash_type','$receiptdate','$chequedate','$chequeno','$sale_point_id','$trans_id','$scode')";
				$this->db->query($queryd);
				$debit+=$netamountr;

				 $queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequedate,chequeno,sale_point_id,trans_id,scode) 
			   values('$goodsidt','$master_id','$sr','$vendorcode','$vendorname','0','$netamountr','$recv_nar','SV','$cash_type','$receiptdate','$chequedate','$chequeno','$sale_point_id','$trans_id','$scode')";
				$this->db->query($queryd);
				$credit+=$netamountr;
				
		
			} 

				if($return_amount>0 && $netamountr>0) {
					
					//$recv_nar='';
					if($return_amount>0) {
						$recv_nar=$nar_return;
					}
				$sr++;
		      


				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode) 
			   values('$goodsidt','$master_id','$sr','$gas_return_acc','','$return_amount','0','$recv_nar','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
				$this->db->query($queryd);
				$debit+=$return_amount;
				 $queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode) 
			   values('$goodsidt','$master_id','$sr','$vendorcode','$vendorname','0','$return_amount','$recv_nar','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
				$this->db->query($queryd);
				$credit+=$return_amount;
			}

			
			if($netamountr==0 && $return_amount>0) {
				
					$sr++;
					$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode) 
					values('$goodsidt','$master_id','$sr','$cash_inhand','','$return_amount','0','$nar_return','SV','$cash_type','$receiptdate','$sale_point_id','$trans_id','$scode')";
					$this->db->query($queryd);
					$debit+=$return_amount;
					$sr++;
					$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode) 
					values('$goodsidt','$master_id','$sr','$vendorcode','$vendorname','0','$return_amount','$nar_return','SV','$cash_type','$receiptdate','$sale_point_id','$trans_id','$scode')";
					$this->db->query($queryd);
					$credit+=$return_amount;
			}
  
			

					 //$stock_code='2003001001';
					 $stock_code=$fix_code['stock_code'];

					 //$cash_inhand='2003013001';
					 $cash_inhand=$fix_code['cash_code'];
 
		   $sr++;
		 
		   $sql_in="SELECT  m.security_amt,d.itemid,d.qty,i.itemname,d.amount,d.sprice,d.total_amount FROM  tbl_issue_goods m,tbl_issue_goods_detail d ,tblmaterial_coding i where m.issuenos=d.ig_detail_id and
				d.itemid=i.materialcode and m.issuenos ='$goodsid' ";

				
				$resul = $this->db->query($sql_in);
				$rw = $resul->result_array();
				foreach($rw as $key=>$value) {
					$item_amount=0;
					$items_detail="";
					$nar1="";
					
					$gate_pas=$value['ref1'];
					$item_amount=$value['total_amount'];
					$security_amts=$value['security_amt'];
					$items_detail.=$value['itemname'].' ,  '.$value['qty'].'@'.$value['sprice'];
					
				 
		 			$nar1='Empty Sale against #:'.$trans_id.',  '.$items_detail.' , ('.$remarks.')';
					 

			 
				}

 
		   	 
		   		$updates ="UPDATE `tbl_issue_goods` set `post_gl`=1 where `issuenos`='$goodsid'";
		   		//echo "<pre>";print_r($this->db->queries);exit;
		   		$this->db->query($updates);


	   		/////////////////////////// here is code FOR AMOUNT RECEIVED//////////////////
  

		  $sql_ins="SELECT  m.security_amt,d.itemid,d.qty,i.itemname,d.amount,d.sprice,d.total_amount FROM  tbl_issue_goods m,tbl_issue_goods_detail d ,tblmaterial_coding i where m.issuenos=d.ig_detail_id and
				d.itemid=i.materialcode and m.issuenos ='$goodsid' ";

				
				$resuls = $this->db->query($sql_ins);
				$rws = $resuls->result_array();
				foreach($rws as $key=>$value) {
					$item_amount=0;
					$items_detail="";
					$nar1="";
					
					$gate_pas=$value['ref1'];
				 
					$item_amount=$value['total_amount'];

					$security_amts=$value['security_amt'];
					
				 
					
					$items_detail_ms.=$value['itemname'].' ,  '.$value['qty'].'@'.$value['sprice'].':';
				}
				
		$nar='Receive against Empty Sale #:'.$trans_id.',  '.$items_detail_ms.' , ('.$remarks.')';	
		  

	
 
		   		$updates ="UPDATE `tbl_issue_goods` set `post_gl`=1 where `issuenos`='$goodsid'";
		   		if($data['makenew']){ $makenew = $data['makenew'];
		   			$updates ="UPDATE `tbl_orderbooking` set `status`='delivered' where `id`='$makenew'";
		   		}
		   		//exit();
		   		//echo "<pre>";print_r($this->db->queries);exit;
		   		$q =  $this->db->query($updates);
		   		if ($debit!=$credit) {
			    $this->session->set_flashdata('err_message', 'Debit Sides And Credit Sides Are Not Equal!');
				redirect(SURL . 'Emptysale/');
		   				}
		   		$this->db->trans_complete();
	}



public function repost_sale($goodsid){
		$tax_acode='2004003001';
		$stock_code='2003001003';

		$user = $this->session->userdata('id');
		$goodsidt=$goodsid."-Sale";
		$goodsecurity=$goodsid."-Receive Security";
		$goodsidr=$goodsid."-Receive";
		$goodsidss=$goodsid."-Sale Security";
		$goodsidgasreturn=$goodsid."-Returned Gas";

		$netamount = 0;
		$gstAmt = 0;
 
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
		$nar='Empty Sale against #:'.$goodsid.',  '.$items_detail_m.'('.$data->remarks.')';

		$uData['total_amount'] = $netamount;
		$uData['after_discount_amt'] = $netamount - $goodsData->total_discount;

		$this->db->where("issuenos",$goodsid);
		$this->db->update("tbl_issue_goods",$uData);

		$goodsData = $this->db->get_where("tbl_issue_goods",array("issuenos"=>$goodsid))->row();

		$security_amt = $goodsData->security_amt; 
		$vendorcode = $goodsData->issuedto;
		$receiptdate = $goodsData->issuedate;
		$netamountr = $goodsData->total_received;
		$return_amount = $goodsData->return_amount;

		$net_payable = $netamount;
		$vendorname = "";

				
		$stock_code='2003001001';
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

 		$netamount = $netamount - $tax_amount;

		$nar_return = "";
				$sr++;
		        $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate) 
			   values('$goodsidt','$sr','$vendorcode','$vendorname','0','$netamount','$nar','SV','SP','$receiptdate')";
				$this->db->query($queryd);	

				$sr++;
		        $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate) 
			   values('$goodsidt','$sr','$stock_code','','$netamount','0','$nar','SV','SP','$receiptdate')";
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
 
				
			
			$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date)
			values
			('$goodsidt' , 'SV' , '$netamount' , '$netamount' ,'No' ,'No' ,'$user','SP' ,'$receiptdate')";
			$this->db->query($querys);



	   		$updates ="UPDATE `tbl_issue_goods` set `post_gl`=1 where `issuenos`='$goodsidt'";
	   		$this->db->query($updates);

	}


// SELECT `tbl_issue_goods`.*, `tblacode`.*, SUM(`tbl_issue_goods_detail`.`total_amount`) FROM `tbl_issue_goods` JOIN `tblacode` ON `tbl_issue_goods`.`issuedto` = `tblacode`.`acode` JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods_detail`.`ig_detail_id`= `tbl_issue_goods`.`issuenos` GROUP BY `ig_detail_id` ORDER BY `issuenos` DESC
	public function manage_salelpg($from,$to,$sale_point_id){
		$this->db->select('tbl_issue_goods.*,tblacode.*,SUM(tbl_issue_goods_detail.total_amount) as amounttotal');    //,SUM(tbl_issue_goods_detail.total_amount)
		$this->db->from('tbl_issue_goods');
		$this->db->join('tblacode', 'tbl_issue_goods.issuedto = tblacode.acode');
		$this->db->join('tbl_issue_goods_detail', ' tbl_issue_goods_detail.ig_detail_id= tbl_issue_goods.issuenos');
		$this->db->where('tbl_issue_goods.decanting=','');
		$this->db->where('tbl_issue_goods.type=','Empty');
		
		$this->db->where('tbl_issue_goods.issuedate >=', $from);
		$this->db->where('tbl_issue_goods.issuedate <=', $to);
		$this->db->where('tbl_issue_goods.sale_point_id =', $sale_point_id);	
		
		$this->db->group_by('ig_detail_id');
		$this->db->order_by("issuenos", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function manage_damagesalelpg($from,$to){
		$this->db->select('tbl_issue_goods.*,tblacode.*,SUM(tbl_issue_goods_detail.total_amount) as amounttotal');    //,SUM(tbl_issue_goods_detail.total_amount)
		$this->db->from('tbl_issue_goods');
		$this->db->join('tblacode', 'tbl_issue_goods.issuedto = tblacode.acode');
		$this->db->join('tbl_issue_goods_detail', ' tbl_issue_goods_detail.ig_detail_id= tbl_issue_goods.issuenos');
		$this->db->where('tbl_issue_goods.decanting=','');
		$this->db->where('tbl_issue_goods_detail.salestatus=','Damage');
		
		$this->db->where('tbl_issue_goods.issuedate >=', $from);
		$this->db->where('tbl_issue_goods.issuedate <=', $to);		
		
		$this->db->group_by('ig_detail_id');
		$this->db->order_by("issuenos", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}


	public function edit_salelpg($id){
		$this->db->select('tbl_issue_goods.*,tbl_issue_goods_detail.*,tblacode.*');
		$this->db->from('tbl_issue_goods');
		$this->db->join('tbl_issue_goods_detail', 'tbl_issue_goods.issuenos = tbl_issue_goods_detail.ig_detail_id');
		$this->db->join('tblacode', 'tbl_issue_goods.issuedto = tblacode.acode');
		$this->db->where('tbl_issue_goods.issuenos=',$id);
		$this->db->order_by("issuenos", "desc");
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
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $this->db->trans_start();
		$ins_array = array(
		    "issuedto" =>$data['customer'],
		    "issuedate" =>$data['date'],
		    "remarks" =>$data['remarks'],
		    "sale_type" =>$data['saletype'],
		    "return_gas" =>$data['returngas'],
		    "return_rate" =>$data['returnrate'],
		    "return_amount" =>$data['returntotal'],
		    "security_amt" =>$data['securityamt'],
		    "vat_percentage" =>$data['gstp'],
		    "vat_amount" =>$data['vat_amount'],
		    "inc_vat_amount" =>$data['inc_vat_amount'],
		    "gas_amt" =>$data['gasamt'],
		    "total_amount" =>$data['total_bill'],
		    "total_received" =>$data['totalrecv'],
			"type" =>'Empty',
			"sale_point_id" =>$sale_point_id,
		    "trans_id" =>$data['trans_id'],
		    "scode" =>$data['scode'],
			"pay_mode" =>$data['pay_mode'],     
		    "bank_code" =>$data['bank_code'],     
		    "cheque_no" =>$data['cheque_no'],     
		    "cheque_date" =>$data['cheque_date'],   
		   // "created_date" =>date('Y-m-d'),
		    //"created_by" =>$this->session->userdata('id')      
		);
		#----------- add record---------------#
		$id = $_POST['id'];
		$trans_id = $data['trans_id'];
		$table = "tbl_issue_goods";
		$where = "issuenos= '$id'";
		$update_goods=$this->mod_common->update_table($table,$where,$ins_array);
		
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