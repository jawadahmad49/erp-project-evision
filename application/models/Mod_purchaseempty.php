<?php

class Mod_purchaseempty extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    } 

	public function add_purchase_empty($data){
		$cheque_no= '';
		$cheque_dt= '';
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $cash_code=$fix_code['cash_code'];

        $trans_id = $this->db->query("select max(trans_id) as trans_id from tbl_goodsreceiving where sale_point_id='$sale_point_id'")->row_array()['trans_id'];

      if($trans_id==''){
      	 $trans_id=1;
      	}else{
      		 $trans_id=$trans_id+1;
      	}

		if($data['pay_mode']=='bank')
		{
			$trans_type_new='BP';
			$trans_type_new='BP';

			$cheque_no= $data['cheque_no'];
			$cheque_dt= $data['cheque_date'];
			$total_paid= $data['enter_amount_bank'];
			$band_cash_code= $data['bank_name'];
			$goodsids=$sale_point_id."-Purchase-".$trans_id;
		}
		else
		{
			$trans_type_new='CP';
			$trans_type_new='CP';
			$total_paid= $data['enter_amount_cash'];
			$band_cash_code= $cash_code;
			$goodsids=$sale_point_id."-Purchase-".$trans_id;
		}
		$net_payable=$data['net_payable'];
		$discount_amt=$data['discount'];
$this->db->trans_start();

		$ins_array = array(
		    "suppliercode" =>$data['vendor'],
		    "receiptdate" =>$data['date'],
		    "remarks" =>$data['remarks'],		    
		    "pay_mode" =>$data['pay_mode'],
		    "total_bill" =>$data['total_bill'],
		    "net_payable" =>$data['net_payable'],
		    "gstp" =>$data['gstp'],
		    "vat_amount" =>$data['vat_amount'],
		    "inc_vat_amount" =>$data['inc_vat_amount'],
		    "discount_amt" =>$data['discount'],
		    "total_paid" =>$total_paid,
		    "bank_code" =>$band_cash_code,
		    "cheque_no" =>$cheque_no,
		    "cheque_dt" =>$cheque_dt,
		    "sale_point_id" =>$sale_point_id,
		    "trans_id" =>$trans_id,
		    "trans_typ" =>'purchaseempty',
		    "Purchase_type" =>'purchaseempty',
		    
	     
		);

		#----------- add record---------------#
		$table = "tbl_goodsreceiving";
		$add_goods = $this->mod_common->insert_into_table($table, $ins_array);
		$insert_id = $add_goods;
			if($add_goods){
				return $this->multipleitems_againstid($data,$insert_id,$trans_id,'tbl_goodsreceiving_detail');
			}else{
				return false;
		}
	}


	public function repost_purchase($goodsid){

	
		$tax_acode='2004003001';
		$stock_code='2003001002';

		$user = $this->session->userdata('id');
		$goodsids=$goodsid."-Purchase";
		$new_goodsids=$goodsid."-Purchase Payment";

		$netamount = 0;
		$gstAmt = 0;
 
		$goodsItemsData = $this->db->get_where("tbl_goodsreceiving_detail",array("receipt_detail_id"=>$goodsid))->result();

		foreach ($goodsItemsData as $key => $value) {
			$netamount += $value->inc_vat_amount;
			$gstAmt += $value->vat_amount;
			$items_detail_m.=$value->itemname.' ,  '.$value->quantity.'@'.$value->rate.':';
		}

		$uData['total_bill'] = $netamount;
		$uData['net_payable'] = $netamount;

		$this->db->where("receiptnos",$goodsid);
		$this->db->update("tbl_goodsreceiving",$uData);

		$goodsData = $this->db->get_where("tbl_goodsreceiving",array("receiptnos"=>$goodsid))->row();
		$remarks = $goodsData->remarks;
		$receiptdate=$goodsData->receiptdate;
		$vendorcode=$goodsData->suppliercode;
		$enter_amount = $goodsData->total_paid;
		$discount = $goodsData->discount_amt;
		$band_cash_code = $goodsData->bank_code;
		$cheque_no = $goodsData->cheque_no;
		$cheque_date = $goodsData->cheque_date;

		$net_payable = $netamount;
		$vendorname = "";

		 
		$nar='Purchase empty #:'.$goodsid.',  '.$items_detail_m.'('.$remarks.')'; 

		$new_nar = $nar;
				
		$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date)
			values
			('$goodsids' , 'PV' , '$netamount' , '$netamount' ,'No' ,'No' ,'$user','GP' ,'$receiptdate')";
			$this->db->query($querys);
		   
		   	$ex_total = $netamount - $gstAmt;

			$sr++;
		    $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate) values('$goodsids','$sr','$vendorcode','$vendorname','0','$ex_total','$nar','PV','GP','$receiptdate')";
			$this->db->query($queryd);

			$sr++;
			$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate)values('$goodsids','$sr','$stock_code','','$ex_total','0','$nar','PV','GP','$receiptdate')";
			$this->db->query($resultdd);

			if($gstAmt > 0){
				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate) 
			   values('$goodsids','$sr','$vendorcode','$vendorname','0','$gstAmt','Tax: $nar','PV','GP','$receiptdate')";
				$this->db->query($queryd);
			}

			if($gstAmt>0){
				$sr++;
				$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate)
				values('$goodsids','$sr','$tax_acode','','$gstAmt','0','Tax: $nar','PV','GP','$receiptdate')";
				$this->db->query($resultdd);
			}
		 
		if ($enter_amount>0) {
			$trans_type_new = "CP";
			$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date,net_payment,discount)
			values
			('$new_goodsids' ,'$trans_type_new' , '$enter_amount' , '$enter_amount' ,'No' ,'No' ,'$user','$trans_type_new' ,'$receiptdate','$net_payment','$discount')";
			$this->db->query($querys);

			$sr++;
	       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequeno,chequedate) 
		   values('$new_goodsids','$sr','$band_cash_code','$vendorname','0','$enter_amount','$new_nar','$trans_type_new','$trans_type_new','$receiptdate','$cheque_no','$cheque_date')";
			$this->db->query($queryd);

			$sr++;
	       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequeno,chequedate) 
		   values('$new_goodsids','$sr','$vendorcode','$vendorname','$enter_amount','0','$new_nar','$trans_type_new','$trans_type_new','$receiptdate','$cheque_no','$cheque_date')";
			$this->db->query($queryd);
		   $sr++;
		}
	   		$updates ="UPDATE `tbl_goodsreceiving` set `post_gl`=1 where `receiptnos`='$goodsids'";
	   		$q =  $this->db->query($updates);
	   		$this->db->trans_complete();
	   		return $q;
	}


	public function multipleitems_againstid($data,$goodsid,$trans_id,$table){
        $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $tax_acode=$fix_code['tax_receive'];
        $cash_code=$fix_code['cash_code'];
		$remarks = $data['remarks'];
		$vat_amount = $data['vat_amount'];
		$gstp = $data['gstp'];
		

		$cheque_no= '';
		$cheque_date= '';
		$bank_name= '';
		if($data['pay_mode']=='bank')
		{
			$trans_type_new='BP';

			$cheque_no= $data['cheque_no'];
			$cheque_date= $data['cheque_date'];
			$enter_amount= $data['enter_amount_bank'];
			$band_cash_code= $data['bank_name'];
		}
		else
		{
			$trans_type_new='CP';
			$enter_amount= $data['enter_amount_cash'];
			$band_cash_code= $cash_code;

		}

		$net_payment=$data['net_payable'];
		$discount=$data['discount'];

		$datas = array();
		foreach($data['item'] as $key=>$value) {
			$datas[] = array(
				'receipt_detail_id' => $goodsid,
				'sale_point_id' =>$sale_point_id,
		        'trans_id' =>$trans_id,
		 	    'itemid' => $data['item'][$key],
		   		'quantity' => $data['qty'][$key],
		   		 'batch_status' => 'open',
		    	'Batch_stock'=>$data['qty'][$key],
		    	'recvd_date'=>$data['date'],
		    	'rate' => $data['unitcost'][$key],
		    	'gstp' => $data['gst'][$key],
		    	'vat_amount' => $data['gst_amount'][$key],
		    	'inc_vat_amount' => $data['amount'][$key],
		    	'ex_vat_amount' => $data['examount'][$key],
		    	'type' => $data['type'][$key],
		    	'category_id' =>1
		   	);
			$netamount+=$data['amount'][$key];
			$taxAmount+= $data['gst_amount'][$key];
			//$gst=$data['gst'][$key];

			$naritem = $value['item'];
			$narqty=$data['qty'][$key];
			$narprice=$data['unitcost'][$key];
			
		}
			$this->db->insert_batch($table, $datas);
			$receiptdate=$data['date'];
			$vendorcode=$data['vendor'];
			$user = $this->session->userdata('id');
			
			$goodsids=$sale_point_id."-Purchase-".$trans_id;
			//$new_goodsids=$sale_point_id."-Purchase Payment-".$trans_id;

            $ex_amount = $netamount - $taxAmount;


		/////////////////////////// here is code//////////////////
			 
		  $check_exists="SELECT * FROM `tbltrans_master` WHERE `vno` = '$goodsids'";
		  $query = $this->db->query($check_exists);

		  if($query->num_rows()!= 0)
		  {
		    $sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsids'";
		    $this->db->query($sqld);
		    $sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsids'";
		    $this->db->query($sqlm);
			
			
		    $sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsids'  ";
		    $this->db->query($sqld);
		    $sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsids' ";
		    $this->db->query($sqlm);
			
		  }
		  
		 $inv_num='';
		 $items_detail='';
		 
				$res="SELECT `sino` from `tbl_goodsreceiving` where `receiptnos`='$goodsid'";
				$query = $this->db->query($res);
				$res_=$query->result_array();
				$inv_num=$res_['sino'];
				
		

			$sql_in="SELECT m.sino, d.itemid,d.quantity,i.itemname,d.amount,d.rate,d.inc_vat_amount FROM  tbl_goodsreceiving m,tbl_goodsreceiving_detail d ,tblmaterial_coding i where m.receiptnos=d.receipt_detail_id and
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
					
					
					$items_detail_m.=$value['itemname'].' ,  '.$value['quantity'].'@'.$value['rate'].':';
					
				}
		 
		 $nar='Purchase empty #:'.$trans_id.',  '.$items_detail_m.'('.$remarks.')';
		 $new_nar='Payment against purchase #:'.$trans_id.'('.$remarks.')';


		   

			$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date,sale_point_id,trans_id)
			values
			('$goodsids' , 'PV' , '$netamount' , '$netamount' ,'No' ,'No' ,'$user','GP' ,'$receiptdate','$sale_point_id','$trans_id')";
			$this->db->query($querys);
			$master_id = $this->db->query("select masterid from tbltrans_master where vno='$goodsids'")->row_array()['masterid'];
		   
	

				$empty_stock_code=$fix_code['empty_stock_code'];
// 				$item_stock_code=$this->db->query("select acode from tblacode where general='$empty_stock_code' and atype='Child'")->result_array();
		
// 				 $cylinder_11=$item_stock_code[0]['acode'];
// 				 $cylinder_45kg=$item_stock_code[1]['acode'];
// 				 $cylinder_15kg=$item_stock_code[2]['acode'];
// 				 $cylinder_6kg=$item_stock_code[3]['acode'];
// 				 $cylinder_4kg=$item_stock_code[4]['acode'];
// 				 $cylinder_10kg=$item_stock_code[5]['acode'];
// 				 $cylinder_5kg=$item_stock_code[6]['acode'];
// 				 $cylinder_6kgcom=$item_stock_code[7]['acode'];

			
			
// foreach($data['item'] as $key=>$value) {
// 	$item_code=$data['item'][$key];
// 		        if ($item_code=='1') {
// 					$stock_code=$cylinder_4kg;
// 				}else if ($item_code=='2') {
// 					$stock_code=$cylinder_5kg;
// 				}else if ($item_code=='3') {
// 					$stock_code=$cylinder_6kg;
// 				}else if ($item_code=='4') {
// 					$stock_code=$cylinder_10kg;
// 				}else if ($item_code=='5') {
// 					$stock_code=$cylinder_11;
// 				}else if ($item_code=='6') {
// 					$stock_code=$cylinder_15kg;
// 				}else if ($item_code=='7') {
// 					$stock_code=$cylinder_45kg;
// 				}else if ($item_code=='8') {
// 					$stock_code=$cylinder_6kgcom;
// 				}

// 	$amount=$data['examount'][$key];
// 	$rate=$data['unitcost'][$key];
// 	$quantity=$data['qty'][$key];
// 	$gst=$data['gst'][$key];
// 	$itemname=$this->db->query("select itemname from tblmaterial_coding where materialcode='$item_code' ")->row_array()['itemname'];
// 	$nar_new='Purchase empty #:'.$trans_id.',  '.$itemname.' '.$quantity.' @'.$rate.' ('.$remarks.')';
				 $sr++;
					$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id)
					values('$goodsids','$sr','$empty_stock_code','$stock_name','$ex_amount','0','$nar','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
					$this->db->query($resultdd);
					$debit+=$ex_amount;
				//}
				
	$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id) 
			   values('$goodsids','$sr','$vendorcode','$vendorname','0','$ex_amount','$nar','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
				$this->db->query($queryd);
				$credit+=$ex_amount;

				if($vat_amount>0){

					 $nar_tax=$gstp .' % Purchase empty #:'.$trans_id.',  '.$items_detail_m.'('.$remarks.')';
					$sr++;
					$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id)
					values('$goodsids','$sr','$tax_acode','','$vat_amount','0','Tax: $nar_tax','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
					$this->db->query($resultdd);
					$debit+=$vat_amount;
					$sr++;
			       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id) 
				   values('$goodsids','$sr','$vendorcode','$vendorname','0','$vat_amount','Tax: $nar_tax','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
					$this->db->query($queryd);
					$credit+=$vat_amount;
				 }

		 
		   
		 
	 // new entries start

	 
	 if($enter_amount>0){
		

	   
	   	$stock_code=$fix_code['stock_code'];

	     //insert into transaction details debit entry
		

			$sr++;
	       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequeno,chequedate,sale_point_id,trans_id,ig_detail_id) 
		   values('$goodsids','$sr','$vendorcode','$vendorname','$enter_amount','0','$new_nar','$trans_type_new','$trans_type_new','$receiptdate','$cheque_no','$cheque_date','$sale_point_id','$trans_id','$master_id')";
			$this->db->query($queryd);
			$debit+=$enter_amount;
				$sr++;
	       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequeno,chequedate,sale_point_id,trans_id,ig_detail_id) 
		   values('$goodsids','$sr','$band_cash_code','$vendorname','0','$enter_amount','$new_nar','$trans_type_new','$trans_type_new','$receiptdate','$cheque_no','$cheque_date','$sale_point_id','$trans_id','$master_id')";
			$this->db->query($queryd);
			$credit+=$enter_amount;
	
	   }
	//$sr++;

// new entries End 
		   $sql_in="SELECT m.sino, d.itemid,d.quantity,i.itemname,d.amount,d.rate,d.inc_vat_amount FROM  tbl_goodsreceiving m,tbl_goodsreceiving_detail d ,tblmaterial_coding i where m.receiptnos=d.receipt_detail_id and
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
					
					$items_detail.=$value['itemname'].' ,  '.$value['quantity'].'@'.$value['rate'];
					
				
		 
		 $nar1='Purchase empty #:'.$trans_id.',  '.$items_detail;

		 $nar1.=', ('.$remarks.')';

				
					$resultm= "SELECT `stock_code`,`materialcode` ,(select `aname` from `tblacode` where `acode`=`stock_code`) as 'aname'  
					from `tblmaterial_coding` where `materialcode`='".$value['itemid']."'";
					$m = $this->db->query($resultm);

					 $res=$m->result_array();
					 foreach($res as $keys=>$values) {
						$stock_code=$values['stock_code'];
						$itemid=$values['materialcode'];
						$stock_name=$values['aname'];
					}
					
				
					
				}

		  // }

		   	 
		   		$updates ="UPDATE `tbl_goodsreceiving` set `post_gl`=1 where `receiptnos`='$goodsid'";
		   		$q= $this->db->query($updates);

		   
		   	

		   			
		   	if ($debit!=$credit) {
			$this->session->set_flashdata('err_message', 'Debit Sides And Credit Sides Are Not Equal!');
				redirect(SURL . 'Purchaseempty/');
		   			
		   		}
		   		$this->db->trans_complete();
		   		//echo "<pre>"; print_r($this->db->queries);exit;
		 
//}
	}

	public function manage_purchaseempty($from,$to,$sale_point_id){
		$this->db->select('tbl_goodsreceiving.*,tblacode.*,tbl_goodsreceiving_detail.*');    
		$this->db->from('tbl_goodsreceiving');
		$this->db->join('tblacode', 'tbl_goodsreceiving.suppliercode = tblacode.acode');
		$this->db->join('tbl_goodsreceiving_detail', 'tbl_goodsreceiving.receiptnos = tbl_goodsreceiving_detail.receipt_detail_id');
		$this->db->where('tbl_goodsreceiving_detail.type','Empty');
		$this->db->where('tbl_goodsreceiving_detail.category_id=','1');
		$this->db->where('tbl_goodsreceiving.Purchase_type=','purchaseempty');
		
		$this->db->where('tbl_goodsreceiving.receiptdate >=', $from);
		$this->db->where('tbl_goodsreceiving.receiptdate <=', $to);
		$this->db->where('tbl_goodsreceiving.sale_point_id =', $sale_point_id);	
		
		$this->db->group_by('receipt_detail_id');
		$this->db->order_by("receiptnos", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function edit_purchaseempty($id){
		$this->db->select('tbl_goodsreceiving.*,tbl_goodsreceiving_detail.*,tblacode.*');
		$this->db->from('tbl_goodsreceiving');
		$this->db->join('tbl_goodsreceiving_detail', 'tbl_goodsreceiving.receiptnos = tbl_goodsreceiving_detail.receipt_detail_id');
		$this->db->join('tblacode', 'tbl_goodsreceiving.suppliercode = tblacode.acode');
		$this->db->where('tbl_goodsreceiving.receiptnos=',$id);
		$this->db->order_by("receiptnos", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function select_single_trans($id_tars){

		$res = "SELECT * FROM `tbltrans_master` WHERE `vno` = '$id_tars' AND (`vtype` = 'BP'  OR `vtype` = 'CP') LIMIT 1";

		$query = $this->db->query($res);
		return $res_=$query->row_array();
	}

	public function select_single_trans_detail($id_tars){

		$res = "SELECT * FROM `tbltrans_detail` WHERE `vno` = '$id_tars' AND (`vtype` = 'BP'  OR `vtype` = 'CP') ORDER BY `testid` ASC LIMIT 1";

		$query = $this->db->query($res);
		return $res_=$query->row_array();
	}
	
	public function select_single_trans_bank($where){
		$this->db->select('*');
		$this->db->from('tbltrans_detail');
		$this->db->where($where);
		$this->db->where("vtype = 'BP'");
		$query = $this->db->get();
		return $query->result_array();
	}
	

	public function update_purchase_empty($data){

		$cheque_no= '';
		$cheque_dt= date('Y-m-d');
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $cash_code=$fix_code['cash_code'];
		$cheque_no= '';
		$cheque_dt= date('Y-m-d');
		$trans_id = $data['trans_id'];

		if($data['pay_mode']=='bank')
		{
			$trans_type_new='BP';
			$trans_type_new='BP';

			$cheque_no= $data['cheque_no'];
			$cheque_dt= $data['cheque_date'];
			$total_paid= $data['enter_amount_bank'];
			$band_cash_code= $data['bank_name'];
			$goodsids=$sale_point_id."-Purchase-".$trans_id;
		}
		else
		{
			$trans_type_new='CP';
			$trans_type_new='CP';
			$total_paid= $data['enter_amount_cash'];
			$band_cash_code= $cash_code;
			$goodsids=$sale_point_id."-Purchase-".$trans_id;
		}
		$net_payable=$data['net_payable'];
		$discount_amt=$data['discount'];

$this->db->trans_start();

		$ins_array = array(
		    "suppliercode" =>$data['vendor'],
		    "receiptdate" =>$data['date'],
		    "remarks" =>$data['remarks'],
		   	"pay_mode" =>$data['pay_mode'],
		    "total_bill" =>$data['total_bill'],
		    "net_payable" =>$data['net_payable'],
		    "gstp" =>$data['gstp'],
		    "vat_amount" =>$data['vat_amount'],
		    "inc_vat_amount" =>$data['inc_vat_amount'],
		    "discount_amt" =>$data['discount'],
		    "total_paid" =>$total_paid,
		    "bank_code" =>$band_cash_code,
		    "cheque_no" =>$cheque_no,
		    "cheque_dt" =>$cheque_dt,
		    "sale_point_id" =>$sale_point_id,
		    "trans_id" =>$data['trans_id'],
		    "trans_typ" =>'purchaseempty',
		     "Purchase_type" =>'purchaseempty',
		   // "created_date" =>date('Y-m-d'),
		    //"created_by" =>$this->session->userdata('id')      
		);
		#----------- add record---------------#
		$id = $_POST['id'];
		$table = "tbl_goodsreceiving";
		$where = "receiptnos= '$id'";
		$update_goods=$this->mod_common->update_table($table,$where,$ins_array);
		
			if($update_goods){
				return $this->updatemultiple_againstid($data,$id,'tbl_goodsreceiving_detail');
			}else{
				return false;
			}
	}
	public function updatemultiple_againstid($data,$goodsid,$table){


		$datas = array();
		$datai = array();
		$remarks=$data['remarks'];
	    $trans_id=$data['trans_id'];
		$recvd_date = $data['date'];
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $tax_acode=$fix_code['tax_receive'];
        $cash_code=$fix_code['cash_code'];

		$remarks = $data['remarks'];
		$vat_amount = $data['vat_amount'];
		$gstp = $data['gstp'];
		//$tax_acode='2004003001';


		foreach($data['item'] as $key=>$value) {
			$datas[] = array(
				'receipt_id' => $data['items_detailid'][$key],
				'receipt_detail_id' => $goodsid,
				'sale_point_id' =>$sale_point_id,
		        'trans_id' =>$data['trans_id'],
			    'itemid' => $data['item'][$key],
			    'quantity' => $data['qty'][$key],
			    'batch_status' => 'open',
		    	'Batch_stock'=>$data['qty'][$key],
			    'rate' => $data['unitcost'][$key],
			    'gstp' => $data['gst'][$key],
			    'recvd_date'=>$data['date'],
			    'vat_amount' => $data['gst_amount'][$key],
			    'inc_vat_amount' => $data['amount'][$key],
			    'ex_vat_amount' => $data['examount'][$key],
		    	'category_id' =>1,
			    'type' => $data['type'][$key],
			
			   );
			$netamount+=$data['amount'][$key];
			$gstAmt += $data['gst_amount'][$key];
			$gst= $data['gst'][$key];

			$naritem = $value['item'];
			$narqty=$data['qty'][$key];
			$narprice=$data['unitcost'][$key];
		}
			
	

		foreach($datas as $key=>$value) {
			if($value['receipt_id']){
				$datau[] = $value;
			}else{ 
				$datai[] = $value;
			}
		}

		$cheque_no= '';
		$cheque_date= date('Y-m-d');
		$bank_name= '';
		if($data['pay_mode']=='bank')
		{
			$trans_type_new='BP';

			$cheque_no= $data['cheque_no'];
			$cheque_date= $data['cheque_date'];
			$enter_amount= $data['enter_amount_bank'];
			$band_cash_code= $data['bank_name'];
		}
		else
		{
			$trans_type_new='CP';
			$enter_amount= $data['enter_amount_cash'];
			$band_cash_code= $cash_code;
		}

		$net_payment=$data['net_payable'];
		$discount=$data['discount'];
		
		if($datau){ $this->db->update_batch($table, $datau,'receipt_id');}
		if($datai){ $this->db->insert_batch($table, $datai); }

			$receiptdate=$data['date'];
			$vendorcode=$data['vendor'];
			$user = $this->session->userdata('id');
			

			$goodsids=$sale_point_id."-Purchase-".$trans_id;
			// $new_goodsids=$sale_point_id."-Purchase Payment-".$trans_id;
		/////////////////////////// here is code//////////////////


		    $check_exists_bank="SELECT * FROM `tbltrans_master` WHERE `vno` = '$goodsids' and (`svtype`='BP' OR `svtype`='CP') and (`vtype`='BP' OR `vtype`='CP')";

		  $query_bank = $this->db->query($check_exists_bank);
		 

		  if($query_bank->num_rows()!=0)
		  {
		  		$sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsids' and (`svtype`='BP' OR `svtype`='CP') and (`vtype`='BP' OR `vtype`='CP')";
		    	$this->db->query($sqld);
		    	$sqld ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsids' and (`svtype`='BP' OR `svtype`='CP') and (`vtype`='BP' OR `vtype`='CP')";
		    	$this->db->query($sqld);
		  }
		  $check_exists="SELECT * FROM `tbltrans_master` WHERE `vno` = '$goodsids' and `vtype`='PV'";
		  $query = $this->db->query($check_exists);

		  if($query->num_rows()!=0)
		  {
		    $sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsids' and `vtype`='PV'";
		    $this->db->query($sqld);
		    $sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsids' and `vtype`='PV'";
		    $this->db->query($sqlm);

		    $sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsids'  and (`vtype`='BP' OR `vtype`='CP')";
		    $this->db->query($sqld);
		    $sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsids'  and (`vtype`='BP' OR `vtype`='CP')";
		    $this->db->query($sqlm);
		  }
		  
		 $inv_num='';
		 $items_detail='';
		 
				$res="SELECT `sino` from `tbl_goodsreceiving` where `receiptnos`='$goodsids'";
				$query = $this->db->query($res);
				$res_=$query->result_array();
				$inv_num=$res_['sino'];
				

				$new_nar='Payment against purchase #:'.$trans_id.'('.$remarks.')';
				$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date,sale_point_id,trans_id)
			values
			('$goodsids' , 'PV' , '$netamount' , '$netamount' ,'No' ,'No' ,'$user','GP' ,'$receiptdate','$sale_point_id','$trans_id')";
			$this->db->query($querys);
			$master_id = $this->db->query("select masterid from tbltrans_master where vno='$goodsids'")->row_array()['masterid'];



if ($enter_amount>0) {

		     //insert into transaction details debit entry
			

				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequeno,chequedate,sale_point_id,trans_id,ig_detail_id) 
			   values('$goodsids','$sr','$vendorcode','$vendorname','$enter_amount','0','$new_nar','$trans_type_new','$trans_type_new','$receiptdate','$cheque_no','$cheque_date','$sale_point_id','$trans_id','$master_id')";
				$this->db->query($queryd);
				$debit+=$enter_amount;
					$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequeno,chequedate,sale_point_id,trans_id,ig_detail_id) 
			   values('$goodsids','$sr','$band_cash_code','$vendorname','0','$enter_amount','$new_nar','$trans_type_new','$trans_type_new','$receiptdate','$cheque_no','$cheque_date','$sale_point_id','$trans_id','$master_id')";
				$this->db->query($queryd);
				$credit+=$enter_amount;

				
// End new Query
}

//$sr++;

			

			$sql_in="SELECT m.sino, d.itemid,d.quantity,i.itemname,d.amount,d.rate,d.inc_vat_amount FROM  tbl_goodsreceiving m,tbl_goodsreceiving_detail d ,tblmaterial_coding i where m.receiptnos=d.receipt_detail_id and
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
					
					
					$items_detail_m.=$value['itemname'].' ,  '.$value['quantity'].'@'.$value['rate'].':';
					
				}
				$nar='Purchase empty #:'.$trans_id.',  '.$items_detail_m.'('.$remarks.')';
		 
		$ex_amount = $netamount - $gstAmt;
		   

			
				$sr++;
				 
				 	//$stock_code=$fix_code['stock_code'];
				 	$empty_stock_code=$fix_code['empty_stock_code'];
// 				$item_stock_code=$this->db->query("select acode from tblacode where general='$empty_stock_code' and atype='Child'")->result_array();
				
// 				 $cylinder_11=$item_stock_code[0]['acode'];
// 				 $cylinder_45kg=$item_stock_code[1]['acode'];
// 				 $cylinder_15kg=$item_stock_code[2]['acode'];
// 				 $cylinder_6kg=$item_stock_code[3]['acode'];
// 				 $cylinder_4kg=$item_stock_code[4]['acode'];
// 				 $cylinder_10kg=$item_stock_code[5]['acode'];
// 				 $cylinder_5kg=$item_stock_code[6]['acode'];
// 				 $cylinder_6kgcom=$item_stock_code[7]['acode'];

			
			
// foreach($data['item'] as $key=>$value) {
// 	$item_code=$data['item'][$key];
// 		        if ($item_code=='1') {
// 					$stock_code=$cylinder_4kg;
// 				}else if ($item_code=='2') {
// 					$stock_code=$cylinder_5kg;
// 				}else if ($item_code=='3') {
// 					$stock_code=$cylinder_6kg;
// 				}else if ($item_code=='4') {
// 					$stock_code=$cylinder_10kg;
// 				}else if ($item_code=='5') {
// 					$stock_code=$cylinder_11;
// 				}else if ($item_code=='6') {
// 					$stock_code=$cylinder_15kg;
// 				}else if ($item_code=='7') {
// 					$stock_code=$cylinder_45kg;
// 				}else if ($item_code=='8') {
// 					$stock_code=$cylinder_6kgcom;
// 				}

// 	$amount=$data['examount'][$key];
// 	$itemname=$this->db->query("select itemname from tblmaterial_coding where materialcode='$item_code' ")->row_array()['itemname'];
// 	$rate=$data['unitcost'][$key];
// 	$quantity=$data['qty'][$key];
// 	$gst=$data['gst'][$key];
// 	$itemname=$this->db->query("select itemname from tblmaterial_coding where materialcode='$item_code' ")->row_array()['itemname'];
// 	$nar_new='Purchase empty #:'.$trans_id.',  '.$itemname.' '.$quantity.' @'.$rate.' ('.$remarks.')';
				 $sr++;
				 
					$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id)
					values('$goodsids','$sr','$empty_stock_code','$stock_name','$ex_amount','0','$nar','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
					$this->db->query($resultdd);
					$debit+=$ex_amount;
				//}
				 $sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id) 
			   values('$goodsids','$sr','$vendorcode','$vendorname','0','$ex_amount','$nar','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
				$this->db->query($queryd);
				$credit+=$ex_amount;

				if($vat_amount>0){
					 $nar_tax=$gstp .' % Purchase empty #:'.$trans_id.',  '.$items_detail_m.'('.$remarks.')';
					$sr++;
					$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id)
					values('$goodsids','$sr','$tax_acode','','$vat_amount','0','Tax: $nar_tax','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
					$this->db->query($resultdd);
					$debit+=$vat_amount;
						$sr++;
			       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id) 
				   values('$goodsids','$sr','$vendorcode','$vendorname','0','$vat_amount','Tax: $nar_tax','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
					$this->db->query($queryd);
					$credit+=$vat_amount;
				 }

		 
		   //$sr++;
		 
		   $sql_in="SELECT m.sino, d.itemid,d.quantity,i.itemname,d.amount,d.rate,d.inc_vat_amount FROM  tbl_goodsreceiving m,tbl_goodsreceiving_detail d ,tblmaterial_coding i where m.receiptnos=d.receipt_detail_id and
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
					
					$items_detail.=$value['itemname'].' ,  '.$value['quantity'].'@'.$value['rate'];
					
		 
					$nar1='Purchase empty #:'.$trans_id.',  '.$items_detail.'('.$remarks.')';

				
					$resultm= "SELECT `stock_code`,`materialcode` ,(select `aname` from `tblacode` where `acode`=`stock_code`) as 'aname'  
					from `tblmaterial_coding` where `materialcode`='".$value['itemid']."'";
					$m = $this->db->query($resultm);

					 $res=$m->result_array();
					 foreach($res as $keys=>$values) {
						$stock_code=$values['stock_code'];
						$itemid=$values['materialcode'];
						$stock_name=$values['aname'];
					}
						
			
					
				}


		   	 
		   		$updates ="UPDATE `tbl_goodsreceiving` set `post_gl`=1 where `receiptnos`='$goodsid'";
		   		$q=  $this->db->query($updates);

		   	if ($debit!=$credit) {
			$this->session->set_flashdata('err_message', 'Debit Sides And Credit Sides Are Not Equal!');
				redirect(SURL . 'Purchaseempty/');
		   			
		   	}

		   		$this->db->trans_complete();
		   		return $q;
	
		 

		
	}
 
}


?>