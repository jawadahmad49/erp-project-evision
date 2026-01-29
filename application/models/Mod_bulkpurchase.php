<?php

class Mod_bulkpurchase extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }

	
	public function add_bulkpurchase($data){

		$cheque_no= '';
		$cheque_dt= date('Y-m-d');

		if($data['pay_mode']=='bank')
		{
			$trans_type_new='BP';
			$trans_type_new='BP';

			$cheque_no= $data['cheque_no'];
			$cheque_dt= $data['cheque_date'];
			$total_paid= $data['enter_amount_bank'];
			$band_cash_code= $data['bank_name'];
			$goodsids=$goodsid."-Purchase";
		}
		else
		{
			$trans_type_new='CP';
			$trans_type_new='CP';
			$total_paid= $data['enter_amount_cash'];
			$band_cash_code= '2003013001';
			$goodsids=$goodsid."-Purchase";
		}
		$net_payable=$data['net_payable'];
		$discount_amt=$data['discount'];

		$ins_array = array(
		    "suppliercode" =>$data['vendor'],
		    "receiptdate" =>$data['date'],
		    "remarks" =>$data['remarks'],
		    "pay_mode" =>$data['pay_mode'],
		    "total_bill" =>$data['total_bill'],
		    "net_payable" =>$data['net_payable'],
		    "discount_amt" =>$data['discount'],
		    "total_paid" =>$total_paid,
		    "bank_code" =>$band_cash_code,
		    "cheque_no" =>$cheque_no,
		    "cheque_dt" =>$cheque_dt,
		    "purchasetype" =>$data['purchasetype'],  
		    "hospitality_comp" =>$data['hospitality_comp'],
		    "trans_typ" =>'bulkpurchase',
		    'price11kg' =>$data['price11kg'],
		    'vehicle' =>$data['vehicle']
		);
 
		$table = "tbl_goodsreceiving";
		$add_goods = $this->mod_common->insert_into_table($table, $ins_array);
		$insert_id = $add_goods;
			if($add_goods){
				return $this->multipleitems_againstid($data,$insert_id,'tbl_goodsreceiving_detail');
			}else{
				return false;
		}
	}

	public function multipleitems_againstid($data,$goodsid,$table){

	 $tax_acode='2005001001';
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
			$goodsids=$goodsid."-Purchase";
		}
		else
		{
			$trans_type_new='CP';
			$trans_type_new='CP';
			$enter_amount= $data['enter_amount_cash'];
			$band_cash_code= '2003013001';
			$goodsids=$goodsid."-Purchase";
		}
		$net_payment=$data['net_payable'];
		$discount=$data['discount'];

		//$this->db->trans_start();

		//echo "<pre>";print_r($data);//exit;
		$iDs_for_trans_detail = array();

		$datas = array();
		foreach($data['item'] as $key=>$value) {
			$datas = array(
				'receipt_detail_id' => $goodsid,
		 	    'itemid' => $data['item'][$key],
		   		'quantity' => $data['qty'][$key],
		    	'rate' => $data['unitcost'][$key],
		    	'gstp' => $data['gst'][$key],
		    	'vat_amount' => $data['gst_amount'][$key],
		    	'inc_vat_amount' => $data['amount'][$key],
		    	'ex_vat_amount' => $data['examount'][$key],
		    	'type' => 'Filled',
		    	'category_id' =>1,
		    	'ereturn' => $data['ereturn'][$key],
		   	);
			$netamount+=$data['amount'][$key];
			$gstAmt+=$data['gst_amount'][$key];
			$total_qty +=$data['qty'][$key];

			$naritem = $value['item'];
			$narqty=$data['qty'][$key];
			$narprice=$data['unitcost'][$key];

			$this->db->insert($table, $datas);
			array_push($iDs_for_trans_detail,$this->db->insert_id());
			
		} 
			$receiptdate=$data['date'];
			$vendorcode=$data['vendor'];
			$vehicle=$data['vehicle'];
			$user = $this->session->userdata('id');
			//$goodsid=$goodsid."-G";
			$goodsids=$goodsid."-Purchase";
			$new_goodsids=$goodsid."-Purchase";
		/////////////////////////// here is code//////////////////
			 
		  $check_exists="SELECT * FROM `tbltrans_master` WHERE `vno` = '$goodsids' and `vtype`='PV'";
		  $query = $this->db->query($check_exists);

		  if($query->num_rows()!= 0)
		  {
		    $sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsids' and `vtype`='PV'";
		    $this->db->query($sqld);
		    $sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsids' and `vtype`='PV'";
		    $this->db->query($sqlm);
		    $sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$new_goodsids' ";
		    $this->db->query($sqld);
		    $sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$new_goodsids' ";
		    $this->db->query($sqlm);
		  }
		  
		  
		  
		  
		  	  if($data['purchasetype']=='Own'){
		  
				 $inv_num='';
				 $items_detail='';
		 
				$res="SELECT `sino` from `tbl_goodsreceiving` where `receiptnos`='$goodsids'";
				$query = $this->db->query($res);
				$res_=$query->result_array();
				$inv_num=$res_['sino'];
				 

				 $sql_in="SELECT m.sino, d.itemid,d.quantity,i.tank_name,d.amount,d.rate,d.inc_vat_amount 
				 FROM  tbl_goodsreceiving m,tbl_goodsreceiving_detail d ,tbl_tank i where m.receiptnos=d.receipt_detail_id and
				d.itemid=i.tank_id and m.receiptnos ='$goodsids' ";

				
				$resul = $this->db->query($sql_in);
				$rw = $resul->result_array();
				foreach($rw as $key=>$value) {

					$item_amount=0;
					$items_detail="";
					$nar1="";
					$inv_num=$value['sino'];
					$gate_pas=$value['ref1'];
					$item_amount=$value['inc_vat_amount'];
					
					$items_detail_m.=$value['quantity'].'@'.$value['rate'].':';
			 
			}
			$nar='Bulk Purchase #:'.$goodsid.', Vehicle no.'.$vehicle.' '.$items_detail_m;
			$new_nar='Payment against Purchase #:'.$goodsid.', Vehicle no.'.$vehicle.',  '.$items_detail_m;
	 

			$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date)
			values
			('$goodsids' , 'PV' , '$netamount' , '$netamount' ,'No' ,'No' ,'$user','GP' ,'$receiptdate')";
			$this->db->query($querys);
		   
		   //{
$netamount = $netamount-$gstAmt;
		     //insert into transaction details debit entry
				$sr++;
		      $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate) 
			   values('$goodsids','$sr','$vendorcode','$vendorname','0','$netamount','$nar','PV','GP','$receiptdate')";
				$this->db->query($queryd);
if($gstAmt){

				//insert into transaction details crebit entry
				$sr++;
		      $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate) 
			   values('$goodsids','$sr','$vendorcode','$vendorname','0','$gstAmt','Tax: $nar','PV','GP','$receiptdate')";
				$this->db->query($queryd);
}
		 
		   //exit;


// new entries start
		if ($enter_amount>0) {
		   	# code...
			$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date,net_payment,discount)
			values
			('$new_goodsids' ,'$trans_type_new' , '$enter_amount' , '$enter_amount' ,'No' ,'No' ,'$user','$trans_type_new' ,'$receiptdate','$net_payment','$discount')";
			$this->db->query($querys);
		   
		   //{

		   	$stock_code='2003001002';

		     //insert into transaction details debit entry
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
 
		   $sql_in="SELECT m.sino, d.itemid,d.quantity,i.tank_name,d.amount,d.rate,d.inc_vat_amount,d.vat_amount FROM  tbl_goodsreceiving m,
		   tbl_goodsreceiving_detail d ,tbl_tank i where m.receiptnos=d.receipt_detail_id and
				d.itemid=i.tank_id and m.receiptnos ='$goodsid' ";

				$resul = $this->db->query($sql_in);
				$rw = $resul->result_array();

				foreach($rw as $key=>$value) {
					$vat_amount=0;
					$item_amount=0;
					$items_detail="";
					$nar1="";
					$inv_num=$value['sino'];
					$gate_pas=$value['ref1'];
					$item_amount=$value['inc_vat_amount'];
					$vat_amount=$value['vat_amount'];
					
					$items_detail.=$value['quantity'].'@'.$value['rate'];
 
		 
					$nar1='Bulk Purchase #:'.$goodsid.', Vehicle no.'.$vehicle.',  '.$items_detail;
				
					$resultm= "SELECT `stock_code`,`materialcode` ,(select `aname` from `tblacode` where `acode`=`stock_code`) as 'aname'  
					from `tblmaterial_coding` where `materialcode`='".$value['itemid']."'";
					$m = $this->db->query($resultm);

					 $res=$m->result_array();
					 foreach($res as $keys=>$values) {
						$stock_code=$values['stock_code'];
						$itemid=$values['materialcode'];
						$stock_name=$values['aname'];
					}
						
					$stock_code='2003001002';

					$qty = $data["qty"][$key];
				 
				  $item_amount-=$vat_amount;
				$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,ig_detail_id,acode,aname,damount,camount,remarks,vtype,svtype,vdate,qty)
					values('$goodsids','$sr','$iDs_for_trans_detail[$key]','$stock_code','$stock_name','$item_amount','0','$nar1','PV','GP','$receiptdate','$qty')";
					$this->db->query($resultdd);
					$sr++;
					
					
				 if($vat_amount>0){
					 $resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,ig_detail_id,acode,aname,damount,camount,remarks,vtype,svtype,vdate)
					values('$goodsids','$sr','$iDs_for_trans_detail[$key]','$tax_acode','','$vat_amount','0','$nar1','PV','GP','$receiptdate')";
					$this->db->query($resultdd);
						$sr++;
						
				 }
					
				} 
		   }
		   		$updates ="UPDATE `tbl_goodsreceiving` set `post_gl`=1 where `receiptnos`='$goodsids'";
		   		$up =  $this->db->query($updates); 

		   		//$this->db->trans_complete();
		   		//exit;
		   		return $up;
	 
	}

	public function select_single_date($where){
		$this->db->select('tbl_goodsreceiving.*');    
		$this->db->from('tbl_goodsreceiving');
		$this->db->join('tbl_goodsreceiving_detail', 'tbl_goodsreceiving.receiptnos = tbl_goodsreceiving_detail.receipt_detail_id');
		$this->db->where($where);

		$query = $this->db->get();
		return $query->row_array();
	}

	public function manage_bulkpurchase($from,$to){
		$this->db->select('tbl_goodsreceiving.*,tblacode.*');    
		$this->db->from('tbl_goodsreceiving');
		$this->db->join('tblacode', 'tbl_goodsreceiving.suppliercode = tblacode.acode');
		$this->db->join('tbl_goodsreceiving_detail', 'tbl_goodsreceiving.receiptnos = tbl_goodsreceiving_detail.receipt_detail_id');
		$this->db->where('tbl_goodsreceiving_detail.type','Filled');
		$this->db->where('tbl_goodsreceiving.trans_typ','bulkpurchase');
		$this->db->where('tbl_goodsreceiving_detail.category_id=','1');

		$this->db->where('tbl_goodsreceiving.receiptdate >=', $from);
		$this->db->where('tbl_goodsreceiving.receiptdate <=', $to);	
		
		$this->db->group_by('receipt_detail_id');
		$this->db->order_by("receiptnos", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function manage_bulkpurchase_transit($from,$to){
		$this->db->select('tbl_goodsreceiving.*,tblacode.*');    
		$this->db->from('tbl_goodsreceiving');
		$this->db->join('tblacode', 'tbl_goodsreceiving.suppliercode = tblacode.acode');
		$this->db->join('tbl_goodsreceiving_detail', 'tbl_goodsreceiving.receiptnos = tbl_goodsreceiving_detail.receipt_detail_id');
		$this->db->where('tbl_goodsreceiving_detail.type','Filled');
		$this->db->where('tbl_goodsreceiving.trans_typ','bulkpurchase');
		$this->db->where('tbl_goodsreceiving_detail.category_id=','1');

		$this->db->where('tbl_goodsreceiving.receiptdate >=', $from);
		$this->db->where('tbl_goodsreceiving.receiptdate <=', $to);	
		$this->db->where('tbl_goodsreceiving.status =', 'Intransit');	
		
		$this->db->group_by('receipt_detail_id');
		$this->db->order_by("receiptnos", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}


	public function edit_bulkpurchase($id){
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
	

	

	public function get_brand_item($where,$brand_array){
		//error_reporting(E_ALL);
		//pm($brand_array);

		$this->db->select('*');
		$this->db->where_in('brandname', $brand_array);
		$this->db->where($where);
        $query = $this->db->get('tblmaterial_coding');
		return $query->result_array();
	}	

	public function update_bulkpurchase($data){


		$cheque_no= '';
		$cheque_dt= date('Y-m-d');

		if($data['pay_mode']=='bank')
		{
			$trans_type_new='BP';
			$trans_type_new='BP';

			$cheque_no= $data['cheque_no'];
			$cheque_dt= $data['cheque_date'];
			$total_paid= $data['enter_amount_bank'];
			$band_cash_code= $data['bank_name'];
			$goodsids=$goodsid."-Purchase";
		}
		else
		{
			$trans_type_new='CP';
			$trans_type_new='CP';
			$total_paid= $data['enter_amount_cash'];
			$band_cash_code= '2003013001';
			$goodsids=$goodsid."-Purchase";
		}
		$net_payable=$data['net_payable'];
		$discount_amt=$data['discount'];




		$ins_array = array(
		    "suppliercode" =>$data['vendor'],
		    "receiptdate" =>$data['date'],
		    "remarks" =>$data['remarks'],
		   	"pay_mode" =>$data['pay_mode'],
		    "total_bill" =>$data['total_bill'],
		    "net_payable" =>$data['net_payable'],
		    "discount_amt" =>$data['discount'],
		    "total_paid" =>$total_paid,
		    "bank_code" =>$band_cash_code,
		    "cheque_no" =>$cheque_no,
		    "cheque_dt" =>$cheque_dt,
			"purchasetype" =>$data['purchasetype'], 		
		    "hospitality_comp" =>$data['hospitality_comp'],
		    "trans_typ" =>'bulkpurchase',
		    'vehicle' =>$data['vehicle']
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
 
		$this->db->trans_start();

		$iDs_for_trans_detail = array();

		foreach($data['item'] as $key=>$value) {
			$datas[] = array(
				'receipt_id' => $data['items_detailid'][$key],
				'receipt_detail_id' => $goodsid,
			    'itemid' => $data['item'][$key],
			    'quantity' => $data['qty'][$key],
			    'rate' => $data['unitcost'][$key],
			    'gstp' => $data['gst'][$key],
			    'vat_amount' => $data['gst_amount'][$key],
			    'inc_vat_amount' => $data['amount'][$key],
			    'ex_vat_amount' => $data['examount'][$key],
			    'type' => "Filled",
		    	'category_id' =>1,
			    'ereturn' => $data['ereturn'][$key],
			   );
			$netamount+=$data['amount'][$key];
			$gstAmt+=$data['gst_amount'][$key];
			$total_qty +=$data['qty'][$key];

			$naritem = $value['item'];
			$narqty=$data['qty'][$key];
			$narprice=$data['unitcost'][$key];
			//pm($datas[$key]);

			if($data['items_detailid'][$key]==""){ 
				$this->db->insert($table, $datas[$key]); 
				array_push($iDs_for_trans_detail,$this->db->insert_id());
			} else{
				array_push($iDs_for_trans_detail,$data['items_detailid'][$key]);
			}
			
		}

		//print_r($iDs_for_trans_detail);
			
		foreach($datas as $key=>$value) {
			if($value['receipt_id']){

				$datau[] = $value;
			}else{ 
				$datai[] = $value;
			} 
		}

		//pm($iDs_for_trans_detail);

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
			$band_cash_code= '2003013001';
		}

		$net_payment=$data['net_payable'];
		$discount=$data['discount'];

		if($datau){ $this->db->update_batch($table, $datau,'receipt_id');}
		

			$receiptdate=$data['date'];
			$vendorcode=$data['vendor'];
			$vehicle=$data['vehicle'];
			$user = $this->session->userdata('id');
			//$goodsid=$goodsid."-G";
			$goodsids=$goodsid."-Purchase";
			$new_goodsids=$goodsid."-Purchase";
		/////////////////////////// here is code//////////////////
			 
		  $check_exists="SELECT * FROM `tbltrans_master` WHERE `vno` = '$goodsids' and `vtype`='PV'";
		  $query = $this->db->query($check_exists);

		    $check_exists_bank="SELECT * FROM `tbltrans_master` WHERE `vno` = '$goodsids' and (`svtype`='BP' OR `svtype`='CP') and (`vtype`='BP' OR `vtype`='CP')";
		   
		  $query_bank = $this->db->query($check_exists_bank);

		  if($query_bank->num_rows()!=0)
		  {
		  		$sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsids' and (`svtype`='BP' OR `svtype`='CP')and (`vtype`='BP' OR `vtype`='CP')";
		    	$this->db->query($sqld);
		    	$sqld ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsids' and (`svtype`='BP' OR `svtype`='CP')and (`vtype`='BP' OR `vtype`='CP')";
		    	$this->db->query($sqld);
		  }
//echo "<pre>";print_r($this->db->queries); echo $query->num_rows();exit;
		  if($query->num_rows()!=0)
		  {
		    $sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsids' and `vtype`='PV'";
		    $this->db->query($sqld);
		    $sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$new_goodsids'  ";
		    $this->db->query($sqld);

		    $sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsids' and `vtype`='PV'";
		    $this->db->query($sqlm);
		    $sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$new_goodsids' ";
		    $this->db->query($sqlm);
		  }
		   
		  
		  if($data['purchasetype']=='Own'){
		  
		  
		  
		 $inv_num='';
		 $items_detail='';
		 
				$res="SELECT `sino` from `tbl_goodsreceiving` where `receiptnos`='$goodsids'";
				$query = $this->db->query($res);
				$res_=$query->result_array();
				$inv_num=$res_['sino'];
				 


		   $sql_in="SELECT m.sino, d.itemid,d.quantity,i.tank_name,d.amount,d.rate,d.inc_vat_amount FROM  tbl_goodsreceiving m,
		   tbl_goodsreceiving_detail d ,tbl_tank i where m.receiptnos=d.receipt_detail_id and
				d.itemid=i.tank_id and m.receiptnos ='$goodsids' ";

				
				$resul = $this->db->query($sql_in);
				$rw = $resul->result_array();
				foreach($rw as $key=>$value) {

					

					$item_amount=0;
					$items_detail="";
					$nar1="";
					$inv_num=$value['sino'];
					$gate_pas=$value['ref1'];
					$item_amount=$value['inc_vat_amount'];
					
					$items_detail_m.=' Qty '.$value['quantity'].'@'.$value['rate'].':';
			 
			}
				 
				$nar='Bulk Purchase #:'.$goodsid.', Vehicle no.'.$vehicle.',  '.$items_detail_m;
				$new_nar='Payment against Purchase #:'.$goodsid.', Vehicle no.'.$vehicle.',  '.$items_detail_m;



			$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date)
			values
			('$goodsids' , 'PV' , '$netamount' , '$netamount' ,'No' ,'No' ,'$user','GP' ,'$receiptdate')";
			$this->db->query($querys);
		   
		   //{
$netamount = $netamount - $gstAmt;
$nar1='Bulk Purchase #:'.$goodsid.',  '.$items_detail;
		     //insert into transaction details debit entry
				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate) 
			   values('$goodsids','$sr','$vendorcode','$vendorname','0','$netamount','$nar','PV','GP','$receiptdate')";
				$this->db->query($queryd);

if($gstAmt > 0){
				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate) 
			   values('$goodsids','$sr','$vendorcode','$vendorname','0','$gstAmt','Tax: $nar1','PV','GP','$receiptdate')";
				$this->db->query($queryd);
			}


// New insetion 
			if ($enter_amount>0) {

			$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date,net_payment,discount)
			values
			('$new_goodsids' ,'$trans_type_new' , '$enter_amount' , '$enter_amount' ,'No' ,'No' ,'$user','$trans_type_new' ,'$receiptdate','$net_payment','$discount')";
			$this->db->query($querys);
		   

		   //{
		     //insert into transaction details debit entry
				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequeno,chequedate) 
			   values('$new_goodsids','$sr','$band_cash_code','$vendorname','0','$enter_amount','$new_nar','$trans_type_new','$trans_type_new','$receiptdate','$cheque_no','$cheque_date')";
				$this->db->query($queryd);

				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequeno,chequedate) 
			   values('$new_goodsids','$sr','$vendorcode','$vendorname','$enter_amount','0','$new_nar','$trans_type_new','$trans_type_new','$receiptdate','$cheque_no','$cheque_date')";
				$this->db->query($queryd);
		  
			}
// 
				// end
		 
		   $sr++;
		 
		   $sql_in="SELECT m.sino, d.itemid,d.quantity,i.tank_name,d.amount,d.rate,d.inc_vat_amount,d.vat_amount FROM
		   tbl_goodsreceiving m,tbl_goodsreceiving_detail d ,tbl_tank i where m.receiptnos=d.receipt_detail_id and
				d.itemid=i.tank_id and m.receiptnos ='$goodsid' ";

				
				$resul = $this->db->query($sql_in);
				$rw = $resul->result_array();
				foreach($rw as $key=>$value) {

					

					$item_amount=0;
					$vat_amount=0;
					$items_detail="";
					$nar1="";
					$inv_num=$value['sino'];
					$gate_pas=$value['ref1'];
					$item_amount=$value['inc_vat_amount'];
					$vat_amount=$value['vat_amount'];
					
					$items_detail.=' Qty '.$value['quantity'].'@'.$value['rate'];
					 
		 $nar1='Bulk Purchase #:'.$goodsid.', Vehicle no.'.$vehicle.',  '.$items_detail;

				
					$resultm= "SELECT `stock_code`,`materialcode` ,(select `aname` from `tblacode` where `acode`=`stock_code`) as 'aname'  
					from `tblmaterial_coding` where `materialcode`='".$value['itemid']."'";
					$m = $this->db->query($resultm);

					 $res=$m->result_array();
					 foreach($res as $keys=>$values) {
						$stock_code=$values['stock_code'];
						$itemid=$values['materialcode'];
						$stock_name=$values['aname'];
					}
						

					
				 $stock_code='2003001002';

				 $qty = $data["qty"][$key];
				  
						 $tax_acode='2005001001';
					  $item_amount-=$vat_amount;
					$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,ig_detail_id,acode,aname,damount,camount,remarks,vtype,svtype,vdate,qty)
					values('$goodsids','$sr','$iDs_for_trans_detail[$key]','$stock_code','$stock_name','$item_amount','0','$nar1','PV','GP','$receiptdate','$qty')";
					$this->db->query($resultdd);
					$sr++;
					
					
					
				 if($vat_amount>0){
					$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,ig_detail_id,acode,aname,damount,camount,remarks,vtype,svtype,vdate)
					values('$goodsids','$sr','$iDs_for_trans_detail[$key]','$tax_acode','','$vat_amount','0','Tax: $nar1','PV','GP','$receiptdate')";
					$this->db->query($resultdd);
						$sr++;
				 }
					
				}

		  // }

		   }
		   		$updates ="UPDATE `tbl_goodsreceiving` set `post_gl`=1 where `receiptnos`='$goodsids'";
		   		$up = $this->db->query($updates); 

		   		$this->db->trans_complete();

		   		return $up;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function repost_trans($goodsid){
		 
		 
		$cheque_no= '';
		$cheque_date= '';
		$bank_name= '';
	
	$sql_in_m="SELECT * FROM  tbl_goodsreceiving where receiptnos ='$goodsid' ";


	$resul_m = $this->db->query($sql_in_m);
	$rw_m = $resul_m->result_array();
	foreach($rw_m as $key=>$value_m) {
	$remarks=$value_m['remarks'];
	$pay_mode=$value_m['pay_mode'];
	$cheque_no=$value_m['cheque_no'];
	$cheque_date=$value_m['cheque_dt'];
	$enter_amount=$value_m['total_paid'];
	$band_cash_code=$value_m['bank_code']; 
	
	$net_payment=$value_m['net_payable']; 
	$discount=$value_m['discount_amt']; 
	$receiptdate=$value_m['receiptdate']; 
	$vendorcode=$value_m['suppliercode']; 
	$purchasetype=$value_m['purchasetype']; 
	}


	
		 
		 
		 
	   
		 
			$user = $this->session->userdata('id');
		 
			$goodsids=$goodsid."-Purchase";
			$new_goodsids=$goodsid."-Purchase";
			
		$taxamount=0;
		$tax_acode='2005001001';
		 

		if($pay_mode=='bank')
		{
			$trans_type_new='BP';

		  
		}
		else
		{
			$trans_type_new='CP';
			 
		}
 
		$this->db->trans_start();
 
			$user = $this->session->userdata('id');
	 
			$goodsids=$goodsid."-Purchase";


  
		 
		 
		 
		 
		 
		/////////////////////////// here is code//////////////////
			 
		  $check_exists="SELECT * FROM `tbltrans_master` WHERE `vno` = '$goodsids' and `vtype`='PV'";
		  $query = $this->db->query($check_exists);

		    $check_exists_bank="SELECT * FROM `tbltrans_master` WHERE `vno` = '$goodsids' and (`svtype`='BP' OR `svtype`='CP') and (`vtype`='BP' OR `vtype`='CP')";
		   
		  $query_bank = $this->db->query($check_exists_bank);

		  if($query_bank->num_rows()!=0)
		  {
		  		$sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsids' and (`svtype`='BP' OR `svtype`='CP')and (`vtype`='BP' OR `vtype`='CP')";
		    	$this->db->query($sqld);
		    	$sqld ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsids' and (`svtype`='BP' OR `svtype`='CP')and (`vtype`='BP' OR `vtype`='CP')";
		    	$this->db->query($sqld);
		  }
//echo "<pre>";print_r($this->db->queries); echo $query->num_rows();exit;
		  if($query->num_rows()!=0)
		  {
		    $sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsids' and `vtype`='PV'";
		    $this->db->query($sqld);
		    $sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$new_goodsids'  ";
		    $this->db->query($sqld);

		    $sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsids' and `vtype`='PV'";
		    $this->db->query($sqlm);
		    $sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$new_goodsids' ";
		    $this->db->query($sqlm);
		  }
		   
		  
		  if($purchasetype=='Own'){
		  
		  
		  
		 $inv_num='';
		 $items_detail='';
		 
				$res="SELECT `sino` from `tbl_goodsreceiving` where `receiptnos`='$goodsids'";
				$query = $this->db->query($res);
				$res_=$query->result_array();
				$inv_num=$res_['sino'];
				 


		   $sql_in="SELECT m.sino, d.itemid,d.quantity,i.tank_name,d.amount,d.rate,d.inc_vat_amount,d.vat_amount FROM  tbl_goodsreceiving m,
		   tbl_goodsreceiving_detail d ,tbl_tank i where m.receiptnos=d.receipt_detail_id and
				d.itemid=i.tank_id and m.receiptnos ='$goodsids' ";

				
				$resul = $this->db->query($sql_in);
				$rw = $resul->result_array();
				foreach($rw as $key=>$value) {

					

					$item_amount=0;
					$items_detail="";
					$nar1="";
					$inv_num=$value['sino'];
					$gate_pas=$value['ref1'];
					$item_amount=$value['inc_vat_amount'];
						$gstAmt+=$value['vat_amount'];
						$netamount+=$value['inc_vat_amount'];
					$items_detail_m.=' Qty '.$value['quantity'].'@'.$value['rate'].':';
			 
			}
				 
				$nar='Bulk Purchase #:'.$goodsid.',  '.$items_detail_m;
				$new_nar='Payment against Purchase #:'.$goodsid.',  '.$items_detail_m;



			$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date)
			values
			('$goodsids' , 'PV' , '$netamount' , '$netamount' ,'No' ,'No' ,'$user','GP' ,'$receiptdate')";
			$this->db->query($querys);
		   
		   //{
$netamount = $netamount - $gstAmt;
$nar1='Bulk Purchase #:'.$goodsid.',  '.$items_detail;
		     //insert into transaction details debit entry
				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate) 
			   values('$goodsids','$sr','$vendorcode','$vendorname','0','$netamount','$nar','PV','GP','$receiptdate')";
				$this->db->query($queryd);

if($gstAmt > 0){
				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate) 
			   values('$goodsids','$sr','$vendorcode','$vendorname','0','$gstAmt','Tax: $nar1','PV','GP','$receiptdate')";
				$this->db->query($queryd);
			}


// New insetion 
			if ($enter_amount>0) {

			$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date,net_payment,discount)
			values
			('$new_goodsids' ,'$trans_type_new' , '$enter_amount' , '$enter_amount' ,'No' ,'No' ,'$user','$trans_type_new' ,'$receiptdate','$net_payment','$discount')";
			$this->db->query($querys);
		   

		   //{
		     //insert into transaction details debit entry
				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequeno,chequedate) 
			   values('$new_goodsids','$sr','$band_cash_code','$vendorname','0','$enter_amount','$new_nar','$trans_type_new','$trans_type_new','$receiptdate','$cheque_no','$cheque_date')";
				$this->db->query($queryd);

				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequeno,chequedate) 
			   values('$new_goodsids','$sr','$vendorcode','$vendorname','$enter_amount','0','$new_nar','$trans_type_new','$trans_type_new','$receiptdate','$cheque_no','$cheque_date')";
				$this->db->query($queryd);
		  
			}
// 
				// end
		 
		   $sr++;
		 
		   $sql_in="SELECT m.sino, d.itemid,d.quantity,i.tank_name,d.amount,d.rate,d.inc_vat_amount,d.vat_amount FROM
		   tbl_goodsreceiving m,tbl_goodsreceiving_detail d ,tbl_tank i where m.receiptnos=d.receipt_detail_id and
				d.itemid=i.tank_id and m.receiptnos ='$goodsid' ";

				
				$resul = $this->db->query($sql_in);
				$rw = $resul->result_array();
				foreach($rw as $key=>$value) {

					

					$item_amount=0;
					$vat_amount=0;
					$items_detail="";
					$nar1="";
					$inv_num=$value['sino'];
					$gate_pas=$value['ref1'];
					$item_amount=$value['inc_vat_amount'];
					$vat_amount=$value['vat_amount'];
					
					$items_detail.=' Qty '.$value['quantity'].'@'.$value['rate'];
					 
		 $nar1='Bulk Purchase #:'.$goodsid.',  '.$items_detail;

				
					$resultm= "SELECT `stock_code`,`materialcode` ,(select `aname` from `tblacode` where `acode`=`stock_code`) as 'aname'  
					from `tblmaterial_coding` where `materialcode`='".$value['itemid']."'";
					$m = $this->db->query($resultm);

					 $res=$m->result_array();
					 foreach($res as $keys=>$values) {
						$stock_code=$values['stock_code'];
						$itemid=$values['materialcode'];
						$stock_name=$values['aname'];
					}
						

					
				 $stock_code='2003001002';

				 $qty = $data["qty"][$key];
				  
						 $tax_acode='2005001001';
					  $item_amount-=$vat_amount;
					$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,ig_detail_id,acode,aname,damount,camount,remarks,vtype,svtype,vdate,qty)
					values('$goodsids','$sr','0','$stock_code','$stock_name','$item_amount','0','$nar1','PV','GP','$receiptdate','$qty')";
					$this->db->query($resultdd);
					$sr++;
					
					
					
				 if($vat_amount>0){
					$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,ig_detail_id,acode,aname,damount,camount,remarks,vtype,svtype,vdate)
					values('$goodsids','$sr','0','$tax_acode','','$vat_amount','0','Tax: $nar1','PV','GP','$receiptdate')";
					$this->db->query($resultdd);
						$sr++;
				 }
					
				}

		  // }

		   }
		   		$updates ="UPDATE `tbl_goodsreceiving` set `post_gl`=1 where `receiptnos`='$goodsids'";
		   		$up = $this->db->query($updates); 

		   		$this->db->trans_complete();

		   		return $up;
		 
	}

	
	
	
	
	
	
	
 
}


?>