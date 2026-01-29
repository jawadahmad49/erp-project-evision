<?php

class Mod_purchasereturn extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }

	
	public function add_purchase_return($data){

		//pm($data);
		$ins_array = array(
		    "scode" =>$data['acode'],
		    "irdate" =>$data['date'],
		    "remarks" =>$data['remarks'],
			"raiseddate" =>date('Y-m-d'),
			"raisedby" =>$this->session->userdata('id'),
			"type" =>"purchasereturn",   
		);
		#----------- add record---------------#
		$table = "tbl_issue_return";
		$add_goods = $this->mod_common->insert_into_table($table, $ins_array);
		$insert_id = $add_goods;
			if($add_goods){
				return $this->multipleitems_againstid($data,$insert_id,'tbl_issue_return_detail');
			}else{
				return false;
		}
	}

	public function multipleitems_againstid($data,$goodsid,$table){
		//echo "<pre>";print_r($data);//exit;
		$remarks = $data['remarks'];

		$datas = array();
		foreach($data['item'] as $key=>$value) {
		$datas[] = array(
			'irnos' => $goodsid,
		    'itemid' => $data['item'][$key],
		    'type' => $data['type'][$key],
		    'qty' => $data['qty'][$key],
		    'gas_amount' => $data['gasamt'][$key],
		    //'wrate' => $data['security'][$key],
		    'total_amount' => $data['amounttotal'][$key],
		    
		   );
			$netamount+=$data['amounttotal'][$key];
			//$netamountr+=$data['amountreceived'][$key];

			$naritem = $value['item'];
			$narqty=$data['qty'][$key];
			$narprice=$data['gasamt'][$key];

			

		}
		//$this->mod_common->insert_into_table($table, $datas);

		 	$this->db->insert_batch($table, $datas);

			
		/////////////////////////// here is code//////////////////
		 	$receiptdate=$data['date'];
			$vendorcode=$data['acode'];
			$user = $this->session->userdata('id');
			$goodsidt=$goodsid."-Return";
			
			 
		  $check_exists="SELECT * FROM `tbltrans_master` WHERE `vno` = '$goodsidt' and `vtype`='SV'";
		  $query = $this->db->query($check_exists);

		  if($query->num_rows()!=0)
		  {
		    $sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsidt' and `vtype`='SV'";
		    $this->db->query($sqld);
		    $sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsidt' and `vtype`='SV'";
		    $this->db->query($sqlm);
		  }
		  
		 
		 $items_detail='';
		 
				
			
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

			
			$items_detail_m.=$value['itemname'].' , Qty: '.$value['qty'].', Gas Amount:'.$value['gas_amount'].' -';

			}

			$nar='Return against #: '.$goodsid.', '.$items_detail_m.' , ('.$remarks.')';
	 
	 
	 
	 
			$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date)
			values
			('$goodsidt' , 'SV' , '$netamount' , '$netamount' ,'No' ,'No' ,'$user','SP' ,'$receiptdate')";
			$this->db->query($querys);
		   
		   

		     //insert into transaction details debit entry
				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate) 
			   values('$goodsidt','$sr','$vendorcode','$vendorname','0','$netamount','$nar','SV','SP','$receiptdate')";
				$this->db->query($queryd);

		 
		   $sr++;
		 
		   $sql_in="SELECT  d.itemid,d.qty,i.itemname,d.gas_amount,d.total_amount FROM  tbl_issue_return m,tbl_issue_return_detail d ,tblmaterial_coding i where m.irnos=d.irnos and
				d.itemid=i.materialcode and m.irnos ='$goodsidt' ";

				
				$resul = $this->db->query($sql_in);
				$rw = $resul->result_array();
				foreach($rw as $key=>$value) {
					$item_amount=0;
					$items_detail="";
					$nar1="";
					
					$gate_pas=$value['ref1'];
					$item_amount=$value['total_amount'];
					
					$nar1.='Return against # '.$goodsidt.''. $value['itemname'].' ,  '.$value['qty'].'@'.$value['gas_amount'].' , ('.$remarks.')';
					
					 
				
					$resultm= "SELECT `stock_code`,`materialcode` ,(select `aname` from `tblacode` where `acode`=`stock_code`) as 'aname'  
					from `tblmaterial_coding` where `materialcode`='".$value['itemid']."'";
					$m = $this->db->query($resultm);

					 $res=$m->result_array();
					 foreach($res as $keys=>$values) {
						$stock_code=$values['stock_code'];
						$itemid=$values['materialcode'];
						$stock_name=$values['aname'];
					}
						
					
		 
 
					$stock_code='2003001001';
				 
				 
				 
					$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate)
					values('$goodsidt','$sr','$stock_code','$stock_name','$item_amount','0','$nar1','SV','SP','$receiptdate')";
					$this->db->query($resultdd);
					$sr++;
					
				}

		   	 
		   		$updates ="UPDATE `tbl_issue_return` set `post_gl`=1 where `irnos`='$goodsidt'";
		   		//echo "<pre>";print_r($this->db->queries);exit;
		   	return	$this->db->query($updates);

 
		   	
	}


	public function repost_return($goodsid){

		$user = $this->session->userdata('id');
		$goodsidt=$goodsid."-Return";
 
		$goodsItemsData = $this->db->get_where("tbl_issue_return_detail",array("irnos"=>$goodsid))->result();

		foreach ($goodsItemsData as $key => $value) {
			$stock_amount+=$value->gas_amount;
			$netamount+=$value->total_amount;
			$security_amount+=$value->wrate;		
				
			$items_detail_m.='Qty: '.$value->qty.', Gas Amount:'.$value->gas_amount.' -';
		}

		$goodsData = $this->db->get_where("tbl_issue_return",array("irnos"=>$goodsid))->row();
		$remarks = $goodsData->remarks;
		$receiptdate=$goodsData->irdate;
		$vendorcode=$goodsData->scode; 
 
		$vendorname = "";
		$stock_name = "";

		$nar='Return against #: '.$goodsid.', '.$items_detail_m.' , ('.$remarks.')';

		$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date)
			values
			('$goodsidt' , 'SV' , '$netamount' , '$netamount' ,'No' ,'No' ,'$user','SP' ,'$receiptdate')";
			$this->db->query($querys);
		    
		$sr++;
		$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate) 
		values('$goodsidt','$sr','$vendorcode','$vendorname','0','$netamount','$nar','SV','SP','$receiptdate')";
		$this->db->query($queryd);

		$sr++;						
		$stock_code='2003001001';	 
		$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate)
		values('$goodsidt','$sr','$stock_code','$stock_name','$stock_amount','0','$nar','SV','SP','$receiptdate')";
		$this->db->query($resultdd);		
	}

// SELECT `tbl_issue_return`.*, `tblacode`.*, SUM(`tbl_issue_return_detail`.`total_amount`) FROM `tbl_issue_return` JOIN `tblacode` ON `tbl_issue_return`.`scode` = `tblacode`.`acode` JOIN `tbl_issue_return_detail` ON `tbl_issue_return_detail`.`irnos`= `tbl_issue_return`.`irnos` GROUP BY `irnos` ORDER BY `irnos` DESC
	public function manage_purchasereturn($from,$to){
		$this->db->select('tbl_issue_return.*,tblacode.*,SUM(tbl_issue_return_detail.total_amount) as amounttotal');    //,SUM(tbl_issue_return_detail.total_amount)
		$this->db->from('tbl_issue_return');
		$this->db->join('tblacode', 'tbl_issue_return.scode = tblacode.acode');
		$this->db->join('tbl_issue_return_detail', ' tbl_issue_return_detail.irnos= tbl_issue_return.irnos');
		$this->db->where('tbl_issue_return.type','purchasereturn');
		
		$this->db->where('tbl_issue_return.irdate >=', $from);
		$this->db->where('tbl_issue_return.irdate <=', $to);	
		
		$this->db->group_by('irnos');
		$this->db->order_by("irnos", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function edit_purchasereturn($id){
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

	public function update_purchase_return($data){
	// pm($data);
		$ins_array = array(
		   "scode" =>$data['acode'],
		    "irdate" =>$data['date'],
		    "remarks" =>$data['remarks'],
			"modifieddate" =>date('Y-m-d'),
			"modifiedby" =>$this->session->userdata('id'),
			"type" =>"purchasereturn",
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
		$datas = array();
		$datai = array();

		$remarks = $data['remarks'];
		
		foreach($data['item'] as $key=>$value) {
			$datas[] = array(
			'sr_no' => $data['items_detailid'][$key],
			'irnos' => $goodsid,
		    'itemid' => $data['item'][$key],
		    'type' => $data['type'][$key],
		    'qty' => $data['qty'][$key],
		    'gas_amount' => $data['gasamt'][$key],
		    //'wrate' => $data['security'][$key],
		    'total_amount' => $data['amounttotal'][$key],
			   );
			$netamount+=$data['amounttotal'][$key];
			//$netamountr+=$data['amountreceived'][$key];

			$naritem = $value['item'];
			$narqty=$data['qty'][$key];
			$narprice=$data['gasamt'][$key];
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
			$vendorcode=$data['acode'];
			$user = $this->session->userdata('id');
			$goodsidt=$goodsid."-Return";
			
		/////////////////////////// here is code//////////////////
			 
		  $check_exists="SELECT * FROM `tbltrans_master` WHERE `vno` = '$goodsidt' and `vtype`='SV'";
		  $query = $this->db->query($check_exists);

		  if($query->num_rows()!=0)
		  {
		    $sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsidt' and `vtype`='SV'";
		    $this->db->query($sqld);
		    $sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsidt' and `vtype`='SV'";
		    $this->db->query($sqlm);
		  }
		  

			$items_detail='';
			 $items_detail='';
		 
				
			
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

			
			$items_detail_m.=$value['itemname'].' , Qty: '.$value['qty'].', Gas Amount:'.$value['gas_amount'].' -';

			}

			$nar='Return against #: '.$goodsid.', '.$items_detail_m.' , ('.$remarks.')';
	 
			$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date)
			values
			('$goodsidt' , 'SV' , '$netamount' , '$netamount' ,'No' ,'No' ,'$user','SP' ,'$receiptdate')";
			$this->db->query($querys);
		   
		   //{

		     //insert into transaction details debit entry
				$sr++;
		       $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate) 
			   values('$goodsidt','$sr','$vendorcode','$vendorname','0','$netamount','$nar','SV','SP','$receiptdate')";
				$this->db->query($queryd);

		 
		   $sr++;
		 
		   $sql_in="SELECT d.itemid,d.qty,i.itemname,d.gas_amount,d.total_amount FROM  tbl_issue_return m,tbl_issue_return_detail d ,tblmaterial_coding i where m.irnos=d.irnos and
				d.itemid=i.materialcode and m.irnos ='$goodsidt' ";

				
				$resul = $this->db->query($sql_in);
				$rw = $resul->result_array();
				foreach($rw as $key=>$value) {
					$item_amount=0;
					$items_detail="";
					$nar1="";
			
					$gate_pas=$value['ref1'];
					$item_amount=$value['total_amount'];
					
					$nar1.='Return against # '.$goodsidt.''. $value['itemname'].' ,  '.$value['qty'].'@'.$value['gas_amount'].' , ('.$remarks.')';
					
					 
		 

				
					$resultm= "SELECT `stock_code`,`materialcode` ,(select `aname` from `tblacode` where `acode`=`stock_code`) as 'aname'  
					from `tblmaterial_coding` where `materialcode`='".$value['itemid']."'";
					$m = $this->db->query($resultm);

					 $res=$m->result_array();
					 foreach($res as $keys=>$values) {
						$stock_code=$values['stock_code'];
						$itemid=$values['materialcode'];
						$stock_name=$values['aname'];
					}
						
					$stock_code='2003001001';
				 
				 
					$resultdd ="INSERT INTO `tbltrans_detail`(vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate)
					values('$goodsidt','$sr','$stock_code','$stock_name','$item_amount','0','$nar1','SV','SP','$receiptdate')";
					$this->db->query($resultdd);
					$sr++;
					
				}

		  // }

		   	 
		   		$updates ="UPDATE `tbl_issue_return` set `post_gl`=1 where `irnos`='$goodsidt'";
		   	return	$this->db->query($updates);

		   	 
		
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