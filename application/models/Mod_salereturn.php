<?php

class Mod_salereturn extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }
	       public function get_report_small($data){

        $id=$data['id'];
        $fdate=$data['from_date'];
        $tdate=$data['to_date'];

         $sort=$data['sort'];
        $query1 = "";
        $query2 = "";
        $query3 = "";
         $filter = $data['filter'];
        $filter1 = $data['filter1'];
        $filter2= $data['filter2'];

       $acode = $data['acode'];
 
 
 

              $query1 = "SELECT opngbl,optype,aname ,acode FROM `tblacode` WHERE  acode='$acode'";

            $result_main = $this->db->query($query1);
            $result_main->result_array();


            foreach($result_main->result_array() as $key=>$line){
             
            $opngbl = ''; $optype = '';
            
            $opngbl = $line['opngbl'];
            $optype = $line['optype'];
            $aname = $line['aname'];
            $acode = $line['acode'];

            $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and vdate<='$tdate' ";

            $result = $this->db->query($query2);
            foreach($result->result_array() as $key=>$line){
                $op_damount = $line['op_damount'];
                $op_camount = $line['op_camount'];
            }
                         $total_opngbl = '';
            if($optype=='Credit'){
                $opngbl = -1*($opngbl) ;
            }
              $total_opngbl =    ($op_damount    -$op_camount )+($opngbl);

           
                    number_format($total_opngbl);


					

				////////////////////////////// PURCHASE Filled /////////////////////////////////
				
				$id_sale=$id.'-Sale';
				$query = "SELECT COALESCE(SUM(damount),0) AS total_amount
				FROM tbltrans_detail where vno='$id_sale'";
				$result = $this->db->query($query);
				$recv_from_vendor_f_row = $result->row_array();
				$final=$recv_from_vendor_f_row['total_amount'];
				$id_recv=$id.'-Receive';
				$query = "SELECT COALESCE(SUM(damount),0) AS total_rec
				FROM tbltrans_detail where vno='$id_recv'";
				$result = $this->db->query($query);
				$recv_from_vendor_f_row = $result->row_array();
				$total_rec=$recv_from_vendor_f_row['total_rec'];
            
			// print $total_rec.'<br>';
			// print $final.'<br>';
			// print $total_opngbl.'<br>';
			// exit;
			
			 $now_bal=$total_opngbl;
			
                    $datas[] = array(
                        "accountcode" =>$acode,
                        "accountname" =>$aname,
                       
                        "tbalance" => $now_bal,
                     
                    );   
        }
		//  pm($datas);exit;
		return $datas;

	   }
	
	public function add_sale_return($data){
		//pm($data['makenew']);
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $trans_id = $this->db->query("select max(trans_id) as trans_id from tbl_issue_return where sale_point_id='$sale_point_id'")->row_array()['trans_id'];

      if($trans_id==''){
      	 $trans_id=1;
      	}else{
      		 $trans_id=$trans_id+1;
      	}
      	$this->db->trans_start();
		$ins_array = array(
		    "scode" =>$data['customer'],
		    "irdate" =>$data['date'],
		    "remarks" =>$data['remarks'],
			"raiseddate" =>date('Y-m-d'),
			"raisedby" =>$this->session->userdata('id'),
			"type" =>"salereturn",
			"total_received" =>$data['totalrecv'],
			"pay_mode" =>$data['pay_mode'],     
		    "bank_code" =>$data['bank_code'],     
		    "cheque_no" =>$data['cheque_no'],
		    "branch_code" =>$data['scode'],
		    "sale_point_id" =>$sale_point_id,
		    "trans_id" =>$trans_id,     
		    "cheque_date" =>$data['cheque_date'],
		    "return_gas" =>$data['return_gas'], 
		    "return_rate" =>$data['return_rate'], 
		    "return_amount" =>$data['return_amount'],  
		);
		#----------- add record---------------#
		$table = "tbl_issue_return";
		$add_goods = $this->mod_common->insert_into_table($table, $ins_array);
		$insert_id = $add_goods;
			if($add_goods){
				return $this->multipleitems_againstid($data,$insert_id,$trans_id,'tbl_issue_return_detail');
			}else{
				return false;
		}
	}

	public function multipleitems_againstid($data,$goodsid,$trans_id,$table){
		//echo "<pre>";print_r($data);exit;
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $cash_code=$fix_code['cash_code'];
        $stock_code=$fix_code['stock_code'];
        $return_amount=$data['return_amount'];
		$datas = array();
		foreach($data['item'] as $key=>$value) {
		$datas[] = array(
			'irnos' => $goodsid,
			'branch_code' =>$data['scode'],
			'sale_point_id' =>$sale_point_id,
		    'trans_id' =>$trans_id,
		    'itemid' => $data['item'][$key],
		    'type' => $data['type'][$key],
		    'qty' => $data['qty'][$key],
		    'gas_amount' => $data['gasamt'][$key],
		    'wrate' => $data['security'][$key],
		    'total_amount' => $data['amounttotal'][$key],
		    
		   );
			$netamount+=$data['amounttotal'][$key];
			//$netamountr+=$data['amountreceived'][$key];

			$naritem = $value['item'];
			$narqty=$data['qty'][$key];
			$narprice=$data['gasamt'][$key];
			 $remarks =$data['remarks'];

			

		}
		//$this->mod_common->insert_into_table($table, $datas);

		 	$this->db->insert_batch($table, $datas);

			
		/////////////////////////// here is code//////////////////
		 	$receiptdate=$data['date'];
			$vendorcode=$data['customer'];
			$user = $this->session->userdata('id');
			$goodsidt=$sale_point_id."-Return-".$trans_id;
			//$goodsidtt=$sale_point_id."-Return Payment-".$trans_id;
			
			 
		  $check_exists="SELECT * FROM `tbltrans_master` WHERE `vno` = '$goodsidt'";
		  $query = $this->db->query($check_exists);

		  if($query->num_rows()!=0)
		  {
		    $sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsidt' ";
		    $this->db->query($sqld);
		    $sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsidt' ";
		    $this->db->query($sqlm);
			 $sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsidt' ";
		    $this->db->query($sqld);
		    $sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsidt' ";
		    $this->db->query($sqlm);
		  }
		  
		  
			$items_detail_m='';
			$stock_amount=0;
			$security_amount=0;

			$sql_in="
			SELECT m.irnos, d.itemid,d.qty,i.itemname,d.total_amount,d.wrate,d.gas_amount 
			FROM  tbl_issue_return m,tbl_issue_return_detail d ,tblmaterial_coding i
			where m.irnos=d.irnos and
			d.itemid=i.materialcode and m.irnos ='$goodsid' ";


			$resul = $this->db->query($sql_in);
			$rw = $resul->result_array();
			foreach($rw as $key=>$value) {



			$item_amount=0;
			$items_detail="";
			$nar1="";
			$inv_num=$value['sino'];
			$gate_pas=$value['ref1'];
			$item_amount=$value['inc_vat_amount'];

			$stock_amount+=$value['gas_amount'];
			$security_amount+=$value['wrate'];
			$total_receive=$data['totalrecv'];

			
			$items_detail_m.=$value['itemname'].' , Qty: '.$value['qty'].', Return Amount:'.$value['gas_amount'].' -';

			}
			$nar='Return against #: '.$trans_id.', '.$items_detail_m.','.$remarks;
			$nar_return_gas='Return Gas against #: '.$trans_id.', '.$items_detail_m.','.$remarks;
			$branch_code=$data['scode'];

			$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date,sale_point_id,trans_id)
			values
			('$goodsidt' , 'SV' , '$netamount' , '$netamount' ,'No' ,'No' ,'$user','SP' ,'$receiptdate','$sale_point_id','$trans_id')";
			$this->db->query($querys);
			$master_id = $this->db->query("select masterid from tbltrans_master where vno='$goodsidt'")->row_array()['masterid'];
		   
		

		     //insert into transaction details debit entry
			//echo $stock_amount;exit();
			if($stock_amount>0){
            $sr++;	
            
			$appliances_code=$fix_code['appliances_code'];	 
			
			$nar='Gass Amount Paid against #: '.$trans_id.', '.$items_detail_m.','.$remarks;
			$nar_new='Return against #: '.$trans_id.', '.$items_detail_m.','.$remarks;
				 
			$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode,ig_detail_id)
			values('$goodsidt','$sr','$appliances_code','$stock_name','$netamount','0','$nar_new','SV','SP','$receiptdate','$sale_point_id','$trans_id','$branch_code','$master_id')";
			$this->db->query($resultdd);
			$debit+=$netamount;
			 $sr++;				
			$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode,ig_detail_id)
			values('$goodsidt','$sr','$vendorcode','$stock_name','0','$netamount','$nar_new','SV','SP','$receiptdate','$sale_point_id','$trans_id','$branch_code','$master_id')";
			$this->db->query($resultdd);
			$credit+=$netamount;

			
			}
			if($return_amount>0){
			$sr++;
				$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode) 
				values('$goodsidt','$master_id','$sr','$stock_code','','0','$return_amount','$nar_return_gas','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
				$this->db->query($queryd);
				$credit+=$return_amount;
				

			$sr++;
				$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode) 
				values('$goodsidt','$master_id','$sr','$vendorcode','$vendorname','$return_amount','0','$nar_return_gas','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
				$this->db->query($queryd);
				$debit+=$return_amount;
			
			}
			if($total_receive>0) {
					
					$chequedate=''; 
					$chequeno='';
					if($data['pay_mode']=='Bank'){
					$cash_inhand=$data['bank_code'];
					$chequedate=$data['cheque_date'];
					$type='BR';
					$chequeno=$data['cheque_no'];
					$nar='Bank Paid against #: '.$trans_id.', '.$items_detail_m.','.$remarks;
					}else{
						$cash_inhand=$fix_code['cash_code'];
						$chequedate='';
						 $chequeno='';
						 $type='CR';
						 $nar='Cash Paid against #: '.$trans_id.', '.$items_detail_m.','.$remarks;

					}

				
				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequedate,chequeno,sale_point_id,trans_id,scode,ig_detail_id) 
			   values('$goodsidt','$sr','$vendorcode','','$total_receive','0','$nar','SV','$type','$receiptdate','$chequedate','$chequeno','$sale_point_id','$trans_id','$branch_code','$master_id')";
				$this->db->query($queryd);
				$debit+=$total_receive;

				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequedate,chequeno,sale_point_id,trans_id,scode,ig_detail_id) 
			   values('$goodsidt','$sr','$cash_inhand','$stock_name','0','$total_receive','$nar','SV','$type','$receiptdate','$chequedate','$chequeno','$sale_point_id','$trans_id','$branch_code','$master_id')";
				$this->db->query($queryd);
				$credit+=$total_receive;
				
		
			}
			
	 

		   	 
			$updates ="UPDATE `tbl_issue_return` set `post_gl`=1 where `irnos`='$goodsidt'";
			if ($debit!=$credit) {
			$this->session->set_flashdata('err_message', 'Debit Sides And Credit Sides Are Not Equal!');
				redirect(SURL . 'Salereturn/');
		   			
		   	}
		   	$this->db->trans_complete();
			return $this->db->query($updates);
	}

// SELECT `tbl_issue_return`.*, `tblacode`.*, SUM(`tbl_issue_return_detail`.`total_amount`) FROM `tbl_issue_return` JOIN `tblacode` ON `tbl_issue_return`.`scode` = `tblacode`.`acode` JOIN `tbl_issue_return_detail` ON `tbl_issue_return_detail`.`irnos`= `tbl_issue_return`.`irnos` GROUP BY `irnos` ORDER BY `irnos` DESC
	public function manage_salereturn($from,$to,$sale_point_id){
		$this->db->select('tbl_issue_return.*,tblacode.*,SUM(tbl_issue_return_detail.total_amount) as amounttotal, tbl_issue_return_detail.type as empty_filled');    //,SUM(tbl_issue_return_detail.total_amount)
		$this->db->from('tbl_issue_return');
		$this->db->join('tblacode', 'tbl_issue_return.scode = tblacode.acode');
		$this->db->join('tbl_issue_return_detail', ' tbl_issue_return_detail.irnos= tbl_issue_return.irnos');
		$this->db->where('tbl_issue_return.type','salereturn');
		$this->db->where('tbl_issue_return.irdate >=', $from);
		$this->db->where('tbl_issue_return.irdate <=', $to);
		$this->db->where('tbl_issue_return.sale_point_id =', $sale_point_id);	
		$this->db->group_by('irnos');
		$this->db->order_by("irnos", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function edit_salereturn($id){
		$this->db->select('tbl_issue_return.*,tbl_issue_return_detail.*,tblacode.*');
		$this->db->from('tbl_issue_return');
		$this->db->join('tbl_issue_return_detail', 'tbl_issue_return.irnos = tbl_issue_return_detail.irnos');
		$this->db->join('tblacode', 'tbl_issue_return.scode = tblacode.acode');
		$this->db->where('tbl_issue_return.irnos=',$id);
		$this->db->order_by("tbl_issue_return.irnos", "desc");
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

	public function update_sale_return($data){
		//pm($data);
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $this->db->trans_start();
		$ins_array = array(
		   "scode" =>$data['customer'],
		    "irdate" =>$data['date'],
		    "remarks" =>$data['remarks'],
			"modifieddate" =>date('Y-m-d'),
			"modifiedby" =>$this->session->userdata('id'),
			"type" =>"salereturn",
			"total_received" =>$data['totalrecv'],
			 "pay_mode" =>$data['pay_mode'],     
		    "bank_code" =>$data['bank_code'],     
		    "cheque_no" =>$data['cheque_no'],
		     "sale_point_id" =>$sale_point_id,
		    "trans_id" =>$data['trans_id'], 
		    "branch_code" =>$data['scode'],    
		    "cheque_date" =>$data['cheque_date'],
		    "return_gas" =>$data['return_gas'], 
		    "return_rate" =>$data['return_rate'], 
		    "return_amount" =>$data['return_amount'],
		);
		#----------- add record---------------#
		$id = $_POST['id'];
		$table = "tbl_issue_return";
		$where = "irnos= '$id'";
		$update_goods=$this->mod_common->update_table($table,$where,$ins_array);
		
			if($update_goods){
				return $this->updatemultiple_againstid($data,$id,'tbl_issue_return_detail');
			}else{
				return false;
			}
	}

	public function updatemultiple_againstid($data,$goodsid,$table){
		//pm($data);exit();
		$datas = array();
		$datai = array();
		$trans_id=$data['trans_id'];
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $cash_code=$fix_code['cash_code'];
        $stock_code=$fix_code['stock_code'];
        $return_amount=$data['return_amount'];
		foreach($data['item'] as $key=>$value) {
			$datas[] = array(
			'sr_no' => $data['items_detailid'][$key],
			'irnos' => $goodsid,
			'branch_code' =>$data['scode'],
			'sale_point_id' =>$sale_point_id,
		    'trans_id' =>$data['trans_id'],
		    'itemid' => $data['item'][$key],
		    'type' => $data['type'][$key],
		    'qty' => $data['qty'][$key],
		    'gas_amount' => $data['gasamt'][$key],
		    'wrate' => $data['security'][$key],
		    'total_amount' => $data['amounttotal'][$key],

			   );
			 $netamount+=$data['amounttotal'][$key];
			//$netamountr+=$data['amountreceived'][$key];

			$naritem = $value['item'];
			$narqty=$data['qty'][$key];
			$narprice=$data['gasamt'][$key];
			$remarks =$data['remarks'];
		}
			
		foreach($datas as $key=>$value) {
			if($value['sr_no']){
				$datau[] = $value;
			}else{ 
				$datai[] = $value;
			}
		}
		
		if($datau){ $this->db->update_batch($table, $datau,'sr_no');}
		if($datai){ $this->db->insert_batch($table, $datai); }

			$receiptdate=$data['date'];
			$vendorcode=$data['customer'];
			$user = $this->session->userdata('id');
			$goodsidt=$sale_point_id."-Return-".$trans_id;
			//$goodsidtt=$sale_point_id."-Return Payment-".$trans_id;
			
		/////////////////////////// here is code//////////////////
			 
		  $check_exists="SELECT * FROM `tbltrans_master` WHERE `vno` = '$goodsidt' ";
		  $query = $this->db->query($check_exists);

		  if($query->num_rows()!=0)
		  {
			   $sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsidt' ";
		    $this->db->query($sqld);
		    $sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsidt' ";
		    $this->db->query($sqlm);
		    $sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsidt' ";
		    $this->db->query($sqld);
		    $sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsidt'  ";
		    $this->db->query($sqlm);
		  }
		  

			$items_detail_m='';
			$stock_amount=0;
			$security_amount=0;

			$sql_in="
			SELECT m.irnos, d.itemid,d.qty,i.itemname,d.total_amount,d.wrate,d.gas_amount 
			FROM  tbl_issue_return m,tbl_issue_return_detail d ,tblmaterial_coding i
			where m.irnos=d.irnos and
			d.itemid=i.materialcode and m.irnos ='$goodsid' ";


			$resul = $this->db->query($sql_in);
			$rw = $resul->result_array();
			foreach($rw as $key=>$value) {



			$item_amount=0;
			$items_detail="";
			$nar1="";
			$inv_num=$value['sino'];
			$gate_pas=$value['ref1'];
			$item_amount=$value['inc_vat_amount'];

			$stock_amount+=$value['gas_amount'];
			$security_amount+=$value['wrate'];
			$total_receive=$data['totalrecv'];

			
			$items_detail_m.=$value['itemname'].' , Qty: '.$value['qty'].', Return Amount:'.$value['gas_amount'].' -';

			}

			$nar='Return against #: '.$trans_id.', '.$items_detail_m.','.$remarks;
			$nar_return_gas='Return Gas against #: '.$trans_id.', '.$items_detail_m.','.$remarks;
			 $branch_code=$data['scode'];  



			$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date,sale_point_id,trans_id)
			values
			('$goodsidt' , 'SV' , '$netamount' , '$netamount' ,'No' ,'No' ,'$user','SP' ,'$receiptdate','$sale_point_id','$trans_id')";
			$this->db->query($querys);
			$master_id = $this->db->query("select masterid from tbltrans_master where vno='$goodsidt'")->row_array()['masterid'];
		   
			if($stock_amount>0){
                     $sr++;	
            
			$appliances_code=$fix_code['appliances_code'];	 
			
			$nar='Gass Amount Paid against #: '.$trans_id.', '.$items_detail_m.','.$remarks;
			$nar_new='Return against #: '.$trans_id.', '.$items_detail_m.','.$remarks;
				 
			$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode,ig_detail_id)
			values('$goodsidt','$sr','$appliances_code','$stock_name','$netamount','0','$nar_new','SV','SP','$receiptdate','$sale_point_id','$trans_id','$branch_code','$master_id')";
			$this->db->query($resultdd);
			$debit+=$netamount;
			 $sr++;				
			$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode,ig_detail_id)
			values('$goodsidt','$sr','$vendorcode','$stock_name','0','$netamount','$nar_new','SV','SP','$receiptdate','$sale_point_id','$trans_id','$branch_code','$master_id')";
			$this->db->query($resultdd);
			$credit+=$netamount;
			
			}
			if($return_amount>0){
			$sr++;
				$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode) 
				values('$goodsidt','$master_id','$sr','$stock_code','','0','$return_amount','$nar_return_gas','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
				$this->db->query($queryd);
				$credit+=$return_amount;
				

			$sr++;
				$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode) 
				values('$goodsidt','$master_id','$sr','$vendorcode','$vendorname','$return_amount','0','$nar_return_gas','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
				$this->db->query($queryd);
				$debit+=$return_amount;
			
			}
			if($total_receive>0) {
					
					$chequedate=''; 
					$chequeno='';
					if($data['pay_mode']=='Bank'){
					$cash_inhand=$data['bank_code'];
					$chequedate=$data['cheque_date'];
					$chequeno=$data['cheque_no'];
					$type="BR";
					$nar='Bank Paid against #: '.$trans_id.', '.$items_detail_m.','.$remarks;
					}else{
						$cash_inhand=$fix_code['cash_code'];
						$chequedate='';
						 $chequeno='';
						 $type="CR";
						 $nar='Cash Paid against #: '.$trans_id.', '.$items_detail_m.','.$remarks;

					}

				
				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequedate,chequeno,sale_point_id,trans_id,scode,ig_detail_id) 
			   values('$goodsidt','$sr','$vendorcode','','$total_receive','0','$nar','SV','$type','$receiptdate','$chequedate','$chequeno','$sale_point_id','$trans_id','$branch_code','$master_id')";
				$this->db->query($queryd);
				$debit+=$total_receive;

				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequedate,chequeno,sale_point_id,trans_id,scode,ig_detail_id) 
			   values('$goodsidt','$sr','$cash_inhand','$stock_name','0','$total_receive','$nar','SV','$type','$receiptdate','$chequedate','$chequeno','$sale_point_id','$trans_id','$branch_code','$master_id')";
				$this->db->query($queryd);
				$credit+=$total_receive;
				
		
			}
			
	 

		   	 
			$updates ="UPDATE `tbl_issue_return` set `post_gl`=1 where `irnos`='$goodsidt'";
			if ($debit!=$credit) {
			$this->session->set_flashdata('err_message', 'Debit Sides And Credit Sides Are Not Equal!');
				redirect(SURL . 'Salereturn/');
		   			
		   	}

		   	$this->db->trans_complete();
			return $this->db->query($updates);
 
		
	}

	public function get_details($data){
		
		$fromdate=$data['date'];
		$itemid = $data['item_id'];

        $sql="SELECT * from `tblmaterial_coding` WHERE `materialcode`=$itemid";
        $query = $this->db->query($sql);
         
        if($query->num_rows()>0){
            foreach($query->result_array() as $key => $value) {
                $itemname = $value['itemname'];
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

                //$sqlsc ="SELECT SUM(`qty`) as igsumq,SUM(`returns`) as rfcustomer from `tbl_issue_return_detail` WHERE `returns`!='' AND `itemid`=$itemid";

                $sqlsc = "SELECT COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as igsumq,COALESCE(SUM(`tbl_issue_return_detail`.`returns`),0) as rfcustomer  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `issuedate`<='$fromdate' AND  `tbl_issue_return_detail`.`returns`!='' AND `tbl_issue_return_detail`.`itemid`=$itemid";
                $querysc = $this->db->query($sqlsc);
                $saltcusf = $querysc->row_array();
//echo $querys['qty']."<br>"; 
/*echo $recfrmvenf['Dgsumq']."<br>";
echo $saltcusf['igsumq'];

exit;*/
//echo $querys['qty'];
//echo $recfrmvenf['Dgsumq'];
//echo $saltcusf['igsumq'];

                $opgbalfilled = $querys['qty']+$recfrmvenf['Dgsumq']-$saltcusf['igsumq'];



               /* $sqls ="SELECT  * from `tbl_shop_opening` WHERE `date`<='$fromdate' AND `type`='Filled' AND `materialcode`=$itemid";
                $querys = $this->db->query($sqls)->row_array();

                $sqlv = "SELECT tbl_goodsreceiving.*,SUM(`tbl_goodsreceiving_detail`.`quantity`) as Dgsumq,SUM(`tbl_goodsreceiving_detail`.`ereturn`) as otvendor  FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<='$fromdate' AND `tbl_goodsreceiving_detail`.`type`='Filled' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryv = $this->db->query($sqlv);
                $recfrmvenf = $queryv->row_array();
                
// just this query less and equal
                $sqlsc = "SELECT tbl_issue_return.*,SUM(`tbl_issue_return_detail`.`qty`) as igsumq,SUM(`tbl_issue_return_detail`.`returns`) as rfcustomer  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `issuedate`<='$fromdate' AND `tbl_issue_return_detail`.`returns`!='' AND `tbl_issue_return_detail`.`itemid`=$itemid";
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

                 $sqlscc = "SELECT tbl_issue_return.*,SUM(`tbl_issue_return_detail`.`qty`) as igsumq,SUM(`tbl_issue_return_detail`.`returns`) as rfcustomer  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `issuedate` BETWEEN '$fromdate' AND '$todate' AND `tbl_issue_return_detail`.`returns`!='' AND `tbl_issue_return_detail`.`itemid`=$itemid";
                $queryscc = $this->db->query($sqlscc);
                $saltcusff = $queryscc->row_array();

                /*   end rest four columns b/w date for filled   */
                /* end here is code for filled */
                /* here is code for empty */

                $sqls ="SELECT  * from `tbl_shop_opening` WHERE `date`<='$fromdate' AND  `type`='Empty' AND `materialcode`=$itemid";
                $querys = $this->db->query($sqls)->row_array();

                //$sqlsc ="SELECT SUM(`qty`) as igsumq,SUM(`returns`) as rfcustomer from `tbl_issue_return_detail` WHERE `returns`='' AND `itemid`=$itemid";
                 $sqlsc = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`returns`),0) as rfcustomer  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `issuedate`<='$fromdate'   AND `tbl_issue_return_detail`.`itemid`=$itemid";
                $querysc = $this->db->query($sqlsc);
                $saltcuse = $querysc->row_array();

                //$sqlv ="SELECT SUM(`quantity`) as Dgsumq,SUM(`ereturn`) as otvendor from `tbl_goodsreceiving_detail` WHERE `type`='Empty' AND `itemid`=$itemid";
                 $sqlv = "SELECT COALESCE(SUM(`tbl_goodsreceiving_detail`.`quantity`),0) as Dgsumq   FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<='$fromdate' AND  `tbl_goodsreceiving_detail`.`type`='Empty' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryv = $this->db->query($sqlv);
                $recfrmvene=$queryv->row_array();



                 $sqlv_e = "SELECT  COALESCE(SUM(`tbl_goodsreceiving_detail`.`ereturn`),0) as otvendor  FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<='$fromdate'   AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryv_e = $this->db->query($sqlv_e);
                $recfrmvene_e=$queryv_e->row_array();


//echo $querys['qty'];
//echo $saltcuse['igsumq'];
//echo $recfrmvene['otvendor'];
//echo $recfrmvene['Dgsumq'];
//exit;
                $opgbalempty = $querys['qty']+$saltcuse['rfcustomer']+$recfrmvene['Dgsumq']-$recfrmvene_e['otvendor'];
                //$opgbalempty = $querys['qty'];


                /*$sqls ="SELECT  * from `tbl_shop_opening` WHERE `date`<='$fromdate' AND `type`='Empty' AND `materialcode`=$itemid";
                $querys = $this->db->query($sqls)->row_array();

                //$sqlsc ="SELECT SUM(`qty`) as igsumq,SUM(`returns`) as rfcustomer from `tbl_issue_return_detail` WHERE `returns`='' AND `itemid`=$itemid";
                $sqlsc = "SELECT tbl_issue_return.*,SUM(`tbl_issue_return_detail`.`qty`) as igsumq,SUM(`tbl_issue_return_detail`.`returns`) as rfcustomer  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `issuedate`<='$fromdate' AND `tbl_issue_return_detail`.`returns`='' AND `tbl_issue_return_detail`.`itemid`=$itemid";
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

                $sqlsccc = "SELECT tbl_issue_return.*,SUM(`tbl_issue_return_detail`.`qty`) as igsumq,SUM(`tbl_issue_return_detail`.`returns`) as rfcustomer  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `issuedate` BETWEEN '$fromdate' AND '$todate' AND `tbl_issue_return_detail`.`returns`='' AND `tbl_issue_return_detail`.`itemid`=$itemid";
                $querysccc = $this->db->query($sqlsccc);
                $saltcusee = $querysccc->row_array();



                 $sqlvvv = "SELECT tbl_goodsreceiving.*,SUM(`tbl_goodsreceiving_detail`.`quantity`) as Dgsumq,SUM(`tbl_goodsreceiving_detail`.`ereturn`) as otvendor  FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate` BETWEEN '$fromdate' AND '$todate' AND `tbl_goodsreceiving_detail`.`type`='Empty' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryvvv = $this->db->query($sqlvvv);
                $recfrmvenee=$queryvvv->row_array();


                /*   end rest four columns b/w date for empty    */
                /* end here is code for empty */

                $datas[] = array(
                    'itemid' => $itemname,
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