<?php

class Mod_girndirect extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }
 
	
	public function add_direct_girn($data){

		$cheque_no= '';
		$cheque_dt= date('Y-m-d');
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
		    "sale_point_id" =>$sale_point_id,
		    "trans_id" =>$trans_id,
		    "remarks" =>$data['remarks'],
		    "pay_mode" =>$data['pay_mode'],
		    "total_bill" =>$data['total_bill'],
		    "net_payable" =>$data['netpayable'],
		    "gstp" =>$data['gstp'],
		    "vat_amount" =>$data['vat_amount'],
		    "inc_vat_amount" =>$data['inc_vat_amount'],
		    "discount_amt" =>$data['discount'],
		    "total_paid" =>$total_paid,
		    "bank_code" =>$band_cash_code,
		    "cheque_no" =>$cheque_no,
		    "cheque_dt" =>$cheque_dt,
		    "trans_typ" =>'purchasefilled',
		    "Purchase_type" =>'purchasefilled',
		    "return_rate" =>$data['return_rate'],
		    "return_gas" =>$data['return_gas'],
		    "return_amount" =>$data['return_amount'],
		    "11_kg_price"=>$data['kg_11_price']
		   // "created_date" =>date('Y-m-d'),
		    //"created_by" =>$this->session->userdata('id')      
		);
		//pm($ins_array);exit();

		#----------- add record---------------#
		$table = "tbl_goodsreceiving";
		$add_goods = $this->mod_common->insert_into_table($table, $ins_array);
		$insert_id = $add_goods;
			if($add_goods){
				$query=$this->multipleitems_againstid($data,$insert_id,$trans_id,'tbl_goodsreceiving_detail');
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
        $tax_acode=$fix_code['tax_receive'];
        $cash_code=$fix_code['cash_code'];
	 //$tax_acode='2004003001';
	    $remarks=$data['remarks'];
	    $vat_amountt = $data['vat_amount'];
	    $return_amountt=$data['return_amount'];
		$gstp = $data['gstp'];

		$cheque_no= '';
		$cheque_date= '';
		$bank_name= '';

		$recvd_date = $data['date'];
		if($data['pay_mode']=='bank')
		{
			$trans_type_new='BP';
			$trans_type_new='BP';

			$cheque_no= $data['cheque_no'];
			$cheque_date= $data['cheque_date'];
			$enter_amount= $data['enter_amount_bank'];
			$band_cash_code= $data['bank_name'];
			$goodsids=$sale_point_id."-Purchase-".$trans_id;
		}
		else
		{
			$trans_type_new='CP';
			$trans_type_new='CP';
			$enter_amount= $data['enter_amount_cash'];
			$band_cash_code= $cash_code;
			$goodsids=$sale_point_id."-Purchase-".$trans_id;
		}
		$net_payment=$data['net_payable'];
		$discount=$data['discount'];

		
		$datas = array();
		foreach($data['item'] as $key=>$value) {

			$batch_status ="open"; 
			$Batch_stock = $data['qty'][$key];

			$datas[] = array(
				'receipt_detail_id' => $goodsid,
				'sale_point_id' =>$sale_point_id,
		        'trans_id' =>$trans_id, 
		 	    'itemid' => $data['item'][$key],
		   		'quantity' => $data['qty'][$key],
		    	'rate' => $data['unitcost'][$key],
		    	'gstp' => $data['gst'][$key],
		    	'vat_amount' => $data['gst_amount'][$key],
		    	'inc_vat_amount' => $data['amount'][$key],
		    	'ex_vat_amount' => $data['examount'][$key],
		    	'type' => $data['type'][$key],
		    	'category_id' =>1,
		    	'ereturn' => $data['ereturn'][$key],
		    	'recvd_date'=>$recvd_date,
		    	'batch_status' => $batch_status,
		    	'Batch_stock'=>$data['qty'][$key],
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
		 
				$sql_in="SELECT d.* FROM tbl_goodsreceiving_detail d, tbl_goodsreceiving m where  d.itemid = '$itemid' 
				and d.receipt_detail_id= m.receiptnos
				and m.receiptdate like '$from_date_for%'
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
			$goodsids=$sale_point_id."-Purchase-".$trans_id;
			//$new_goodsids=$sale_point_id."-Purchase Payment-".$trans_id;
		/////////////////////////// here is code//////////////////
			 
		  $check_exists="SELECT * FROM `tbltrans_master` WHERE `vno` = '$goodsids' and `vtype`='PV'";
		  $query = $this->db->query($check_exists);

		  if($query->num_rows()!= 0)
		  {
		    $sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsids' and `vtype`='PV'";
		    $this->db->query($sqld);
		    $sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsids' and `vtype`='PV'";
		    $this->db->query($sqlm);
		    $sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsids' ";
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
				
					//$nar='P #: '.$goodsid.' DATE : {'.$receiptdate.'}';

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
		 $nar='Purchase filled #:'.$trans_id.',  '.$items_detail_m.'('.$remarks.')';
		 $nar_return_gas='Return Gas Against Purchase #:'.$trans_id.',  '.$items_detail_m.'('.$remarks.')';
		 $new_nar='Payment against purchase #:'.$trans_id.'('.$remarks.')';
				
			$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date,sale_point_id,trans_id)
			values
			('$goodsids' , 'PV' , '$netamount' , '$netamount' ,'No' ,'No' ,'$user','GP' ,'$receiptdate','$sale_point_id','$trans_id')";
			$this->db->query($querys);
			$master_id = $this->db->query("select masterid from tbltrans_master where vno='$goodsids'")->row_array()['masterid'];
		   
		   //{

		   	$ex_total = $netamount - $gstAmt;

		     //insert into transaction details debit entry
			


			

		 
		   


// new entries start
		if ($enter_amount>0) {
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
		   $sr++;
		}

// new entries End 
		$sr++;

		 $nar1.='Purchase filled #:'.$goodsid;
		   $sql_in="SELECT m.sino, d.itemid,d.quantity,i.itemname,d.amount,d.rate,d.inc_vat_amount,d.vat_amount,d.gstp
		   FROM  tbl_goodsreceiving m,tbl_goodsreceiving_detail d ,tblmaterial_coding i where m.receiptnos=d.receipt_detail_id and
				d.itemid=i.materialcode and m.receiptnos ='$goodsid' ";

					$item_amount=0;
					$vat_amount=0;
				
				$resul = $this->db->query($sql_in);
				$rw = $resul->result_array();
				foreach($rw as $key=>$value) {
					$items_detail="";
					 
					$inv_num=$value['sino'];
					$gate_pas=$value['ref1'];
					$vat_amount+=$value['vat_amount'];
					$item_amount+=$value['inc_vat_amount'];
					
					$items_detail.=$value['itemname'].' ,  '.$value['quantity'].'@'.$value['rate'];		 
					$nar1.=',  '.$items_detail;
			 
					
				}
				
				
				$nar1.=',  ('.$remarks.')';
				//$stock_code='2003001001';
				$stock_code=$fix_code['stock_code'];
				 
				 $item_amount-=$vat_amount;
				 //echo $item_amount;exit();
				
					$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id)
					values('$goodsids','$sr','$stock_code','$stock_name','$item_amount','0','$nar1','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
					$this->db->query($resultdd);
					$debit+=$item_amount;
					 $sr++;
					
		  	$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id) 
			   values('$goodsids','$sr','$vendorcode','$vendorname','0','$ex_total','$nar','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
				$this->db->query($queryd);
				$credit+=$ex_total;
			 
				 
				 if($vat_amountt>0){

					$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id)
					values('$goodsids','$sr','$tax_acode','','$vat_amountt','0','Tax $gstp %: $nar1','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
					$this->db->query($resultdd);
					$debit+=$vat_amountt;

						$sr++;
			       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id) 
				   values('$goodsids','$sr','$vendorcode','$vendorname','0','$vat_amountt','Tax $gstp %: $nar','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
					$this->db->query($queryd);
					$credit+=$vat_amountt;

				 }
					if($return_amountt>0){
				 	$sr++;
			       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id) 
				   values('$goodsids','$sr','$vendorcode','$vendorname','$return_amountt','0','$nar_return_gas','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
					$this->db->query($queryd);
					$credit+=$return_amountt;

				 	$sr++;
				 	$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id)
					values('$goodsids','$sr','$stock_code','','0','$return_amountt','$nar_return_gas','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
					$this->db->query($resultdd);
					$debit+=$return_amountt;

					
			}
		
		   		$updates ="UPDATE `tbl_goodsreceiving` set `post_gl`=1 where `receiptnos`='$goodsid'";
		   		$q =  $this->db->query($updates);

		   		if ($debit!=$credit) {
			    $this->session->set_flashdata('err_message', 'Debit Sides And Credit Sides Are Not Equal!');
				redirect(SURL . 'DirectGIRN/');}

		   		$this->db->trans_complete();
		   		return $q;
		   		//echo "<pre>"; print_r($this->db->queries);exit;
		 
		}
	}


	public function repost_purchase($goodsid){

	
		$tax_acode='2004003001';
		$stock_code='2003001001';

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

		 
		$nar='Purchase filled #:'.$goodsid.',  '.$items_detail_m.'('.$remarks.')'; 

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


	public function select_single_date($where){
		$this->db->select('tbl_goodsreceiving.*');    
		$this->db->from('tbl_goodsreceiving');
		$this->db->join('tbl_goodsreceiving_detail', 'tbl_goodsreceiving.receiptnos = tbl_goodsreceiving_detail.receipt_detail_id');
		$this->db->where($where);

		$query = $this->db->get();
		return $query->row_array();
	}

	public function manage_directgirn($from,$to,$sale_point_id){
		$this->db->select('tbl_goodsreceiving.*,tblacode.*');    
		$this->db->from('tbl_goodsreceiving');
		$this->db->join('tblacode', 'tbl_goodsreceiving.suppliercode = tblacode.acode');
		$this->db->join('tbl_goodsreceiving_detail', 'tbl_goodsreceiving.receiptnos = tbl_goodsreceiving_detail.receipt_detail_id');
		$this->db->where('tbl_goodsreceiving_detail.type','Filled');
		$this->db->where('tbl_goodsreceiving_detail.category_id=','1');
		$this->db->where('tbl_goodsreceiving.Purchase_type=','purchasefilled');
		
		$this->db->where('tbl_goodsreceiving.receiptdate >=', $from);
		$this->db->where('tbl_goodsreceiving.receiptdate <=', $to);
		$this->db->where('tbl_goodsreceiving.sale_point_id =', $sale_point_id);	

		$this->db->group_by('receipt_detail_id');
		$this->db->order_by("receiptnos", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function edit_directgirn($id){
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
	public function select_single_trans($id_tars){

		$res = "SELECT * FROM `tbltrans_master` WHERE `vno` = '$id_tars' AND (`vtype` = 'BP'  OR `vtype` = 'CP') LIMIT 1";

		$query = $this->db->query($res);
		return $res_=$query->row_array();


		// $this->db->select('*');
		// $this->db->from('tbltrans_master');
		// $this->db->where($where);
		// $this->db->where("vtype = 'BP'");
		// $this->db->or_where("vtype = 'CP'");
		// $query = $this->db->get();
		// return $query->row_array();
	}

	public function select_single_trans_detail($id_tars){


		$res = "SELECT * FROM `tbltrans_detail` WHERE `vno` = '$id_tars' AND (`vtype` = 'BP'  OR `vtype` = 'CP') ORDER BY `testid` ASC LIMIT 1";

		$query = $this->db->query($res);
		return $res_=$query->row_array();


		// return $this->db->query($updates);

		// $this->db->select('*');
		// $this->db->from('tbltrans_detail');
		// $this->db->where($where);
		// $this->db->where("vtype = 'BP'");
		// $this->db->or_where("vtype = 'CP'");
		// $this->db->order_by("testid", "ASC");
		// $query = $this->db->get();
		// return $query->row_array();
	}
	
	public function select_single_trans_bank($where){
		$this->db->select('*');
		$this->db->from('tbltrans_detail');
		$this->db->where($where);
		$this->db->where("vtype = 'BP'");
		$query = $this->db->get();
		return $query->result_array();
	}
	

	

	public function get_brand_item($where,$brand_array){
		//error_reporting(E_ALL);
		//pm($brand_array);

		$this->db->select('*');
		$this->db->where_in('brandname', $brand_array);
		$this->db->where($where);
        $query = $this->db->get('tblmaterial_coding');
		return $query->result_array();
	}	

	public function update_direct_girn($data){

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
		    "net_payable" =>$data['netpayable'],
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
		    "trans_typ" =>'purchasefilled',
		    "Purchase_type" =>'purchasefilled',
		    "return_rate" =>$data['return_rate'],
		    "return_gas" =>$data['return_gas'],
		    "return_amount" =>$data['return_amount'],
		    "11_kg_price"=>$data['kg_11_price']
		   // "created_date" =>date('Y-m-d'),
		    //"created_by" =>$this->session->userdata('id')      
		);
         //echo $trans_id;exit();
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
	    $vat_amountt=$data['vat_amount'];
	    $return_amountt=$data['return_amount'];
	    $gstp=$data['gstp'];
	    $trans_id=$data['trans_id'];
		$recvd_date = $data['date'];
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $tax_acode=$fix_code['tax_receive'];
        $cash_code=$fix_code['cash_code'];
	    //$tax_acode='2004003001';
		//  echo count($data['item']);
		// echo "<br>";
		 //pm($data);exit();

		    	$recvd_date = $data['date'];



		foreach($data['item'] as $key=>$value) {
			$datas[] = array(
				'receipt_id' => $data['items_detailid'][$key],
				'receipt_detail_id' => $goodsid,
				'sale_point_id' =>$sale_point_id,
		        'trans_id' =>$data['trans_id'],
			    'itemid' => $data['item'][$key],
			    'batch_status' => 'open',
		    	'Batch_stock'=>$data['qty'][$key],
			    'quantity' => $data['qty'][$key],
			    'rate' => $data['unitcost'][$key],
			    'gstp' => $data['gst'][$key],
			    'vat_amount' => $data['gst_amount'][$key],
			    'inc_vat_amount' => $data['amount'][$key],
			    'ex_vat_amount' => $data['examount'][$key],
			    'type' => $data['type'][$key],
			    'recvd_date'=>$recvd_date,
		    	'category_id' =>1,
			    'ereturn' => $data['ereturn'][$key],
			   );
			$netamount+=$data['amount'][$key];
			$gstAmt+= $data['gst_amount'][$key];
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

	
	
	
		    
		$cheque_no= '';
		$cheque_date= '';
		$bank_name= '';
		if($data['pay_mode']=='bank')
		{
			$trans_type_new='BP';
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

		
		
		
		
		
		
				$new_quantity=0;
				$total_amt=0;
				$arr=(explode("-",$recvd_date));
				$from_date_for=$arr[0].'-'.$arr[1];
		 
			 	$sql_in="SELECT d.* FROM tbl_goodsreceiving_detail d, tbl_goodsreceiving m where  d.itemid = '$itemid' 
				and d.receipt_detail_id= m.receiptnos
				and m.receiptdate like '$from_date_for%'
				order by recvd_date   ";
 
				
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
			$goodsids=$sale_point_id."-Purchase-".$trans_id;
			
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
				$nar='Purchase filled #:'.$trans_id.',  '.$items_detail_m.'('.$remarks.')';
				$nar_return_gas='Return Gas Against Purchase #:'.$trans_id.',  '.$items_detail_m.'('.$remarks.')';
				$new_nar='Payment against purchase #:'.$trans_id.'('.$remarks.')';



			$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date,sale_point_id,trans_id)
			values
			('$goodsids' , 'PV' , '$netamount' , '$netamount' ,'No' ,'No' ,'$user','GP' ,'$receiptdate','$sale_point_id','$trans_id')";
			$this->db->query($querys);
			$master_id = $this->db->query("select masterid from tbltrans_master where vno='$goodsids'")->row_array()['masterid'];
		   
		   //{

		     //insert into transaction details debit entry
		


// New insetion 
			if ($enter_amount>0) { 
				$sr++;
		    
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequeno,chequedate,sale_point_id,trans_id,ig_detail_id) 
			   values('$goodsids','$sr','$vendorcode','$vendorname','$enter_amount','0','$new_nar','$trans_type_new','$trans_type_new','$receiptdate','$cheque_no','$cheque_date','$sale_point_id','$trans_id','$master_id')";
				$this->db->query($queryd);
				$debit+=$enter_amount;
				   $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequeno,chequedate,sale_point_id,trans_id,ig_detail_id) 
			   values('$goodsids','$sr','$band_cash_code','$vendorname','0','$enter_amount','$new_nar','$trans_type_new','$trans_type_new','$receiptdate','$cheque_no','$cheque_date','$sale_point_id','$trans_id','$master_id')";
				$this->db->query($queryd);
				$credit+=$enter_amount;

				$sr++;
		  
			}
// 
				// end
		 
		   $sr++;
		  
			 $nar1.='Purchase filled #:'.$trans_id;
			 
	 
		   $sql_in="SELECT m.sino, d.itemid,d.quantity,i.itemname,d.amount,d.rate,d.inc_vat_amount,d.vat_amount FROM  tbl_goodsreceiving m,tbl_goodsreceiving_detail d ,tblmaterial_coding i where m.receiptnos=d.receipt_detail_id and
				d.itemid=i.materialcode and m.receiptnos ='$goodsid' ";

					$item_amount=0;
					$vat_amount=0;
				
				$resul = $this->db->query($sql_in);
				$rw = $resul->result_array();
				foreach($rw as $key=>$value) {
					$items_detail="";
					 
					$inv_num=$value['sino'];
					$gate_pas=$value['ref1'];
					$vat_amount+=$value['vat_amount'];
					$item_amount+=$value['inc_vat_amount'];
					
					$items_detail.=$value['itemname'].' ,  '.$value['quantity'].'@'.$value['rate'];
 
					$nar1.=',  '.$items_detail;
				
			 
				
					
				}
				
				
				$nar1.='('.$remarks.')';
				//$stock_code='2003001001';
				$stock_code=$fix_code['stock_code'];
				 
					$item_amount-=$vat_amount;
					$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id)
					values('$goodsids','$sr','$stock_code','$stock_name','$item_amount','0','$nar1','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
					$this->db->query($resultdd);
					$debit+=$item_amount;
					$sr++;
		  	   $ex_total = $netamount - $gstAmt;
				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id) 
			   values('$goodsids','$sr','$vendorcode','$vendorname','0','$ex_total','$nar','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
				$this->db->query($queryd);
				$credit+=$ex_total;
			 
				 
				 if($vat_amountt>0){
					$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id)
					values('$goodsids','$sr','$tax_acode','','$vat_amountt','0','Tax $gstp %: $nar1','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
					$this->db->query($resultdd);
					$debit+=$vat_amountt;
						$sr++;
			       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id) 
				   values('$goodsids','$sr','$vendorcode','$vendorname','0','$vat_amountt','Tax $gstp %: $nar','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
					$this->db->query($queryd);
					$credit+=$vat_amountt;
				 }
				 	if($return_amountt>0){
				 	$sr++;
			       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id) 
				   values('$goodsids','$sr','$vendorcode','$vendorname','$return_amountt','0','$nar_return_gas','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
					$this->db->query($queryd);
					$credit+=$return_amountt;

				 	$sr++;
				 	$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,ig_detail_id)
					values('$goodsids','$sr','$stock_code','','0','$return_amountt','$nar_return_gas','PV','GP','$receiptdate','$sale_point_id','$trans_id','$master_id')";
					$this->db->query($resultdd);
					$debit+=$return_amountt;

					
			}
		  
 
		   	 
		   		$updates ="UPDATE `tbl_goodsreceiving` set `post_gl`=1 where `receiptnos`='$goodsid'";
		   		$q = $this->db->query($updates);

		   		if ($debit!=$credit) {
			    $this->session->set_flashdata('err_message', 'Debit Sides And Credit Sides Are Not Equal!');
				redirect(SURL . 'DirectGIRN/');
		   		}

		   		$this->db->trans_complete();
		   		return $q;
		   		
		 
		}
		
	}
 
}


?>