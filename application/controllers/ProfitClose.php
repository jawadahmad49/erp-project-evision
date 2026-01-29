<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProfitClose extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_common"
        ));
        error_reporting(0);
        
    }
	public function index()
	{ 
		$data["title"] = "Manage Closing Profit";

		$myyear = $this->db->query("select * from close_profit order by id desc limit 1")->result_array()[0];

		if(empty($myyear['year'])){
			$data['year'] = "2018";
		}else if($myyear['month']=="12"){
			$data['year'] = $myyear['year']+1;
		}else{
			$data['year'] = $myyear['year'];
		}

		$data['monthsclose'] = $this->db->query("select * from close_profit where year='".$data['year']."' GROUP by month")->result_array();
		//pm($data['monthsclose']);

		$this->db->query("update tbl_goodsreceiving_detail set Batch_stock=quantity where batch_status='0' and Batch_stock='0'");
		
		$this->load->view($this->session->userdata('language')."/ProfitClose/sale1",$data);
	}

	public function unclosemnth(){

		$query= $this->db->query("select * from close_profit order by id desc limit 1")->result_array()[0];
		$year = $query['year']; 
		$mnth = $query['month']; 

		

		$this->db->trans_start();
		$this->db->query("delete from close_profit where month='$mnth'");

		if($mnth==1){
			$mymnth = "January";
		}else if($mnth==2){
			$mymnth = "February";
		}
		else if($mnth==3){
			$mymnth = "March";
		}
		else if($mnth==4){
			$mymnth = "April";
		}
		else if($mnth==5){
			$mymnth = "May";
		}
		else if($mnth==6){
			$mymnth = "June";
		}
		else if($mnth==7){
			$mymnth = "July";
		}
		else if($mnth==8){
			$mymnth = "August";
		}
		else if($mnth==9){
			$mymnth = "September";
		}
		else if($mnth==10){
			$mymnth = "October";
		}
		else if($mnth==11){
			$mymnth = "November";
		}
		else if($mnth==12){
			$mymnth = "December";
		}

		if($mnth < 10){
			$mnth = "0".$mnth;
		}
		$from = $year."-".$mnth."-"."01"; 
		$to = date("Y-m-t",strtotime($from));

		$this->uncloselogic($from,$to);

		$this->db->trans_complete();

		 $this->session->set_flashdata('ok_message', "$mymnth Unclosed");
		     redirect(SURL."ProfitClose");
	}

	public function closemnth(){

		$mnth = $this->input->post("mnth"); 
		$year = $this->input->post("year");

		if(empty($mnth)){ 
			 $this->session->set_flashdata('err_message', 'please select month');
		     redirect(SURL."ProfitClose");
		}

		error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

		if($mnth > 1){
			$lastmnth = $mnth-1;
			$chklastclosing = $this->db->query("select * from close_profit where month='$lastmnth' and year='$year'");

			if($chklastclosing->num_rows()>0){

			}else{
				$this->session->set_flashdata('err_message', 'please Close previous month first');
			     redirect(SURL."ProfitClose");
			}

		}

		$chklastclosing = $this->db->query("select * from close_profit where month='$mnth' and year='$year'");

		if($chklastclosing->num_rows()>0){
			$this->session->set_flashdata('err_message', 'This month has already been closed');
		     redirect(SURL."ProfitClose");
		}

		

		$from = $this->input->post("year")."-".$mnth."-"."01"; 
		$to = date("Y-m-t",strtotime($from));

		$this->db->trans_start();

		$materialcode = $this->db->query("select * from tblmaterial_coding where catcode='1'")->result_array();

		$chk = $this->db->query("select * from close_profit where month='$mnth' and year='$year'");
		if($chk->num_rows() > 0){
				$this->db->query("delete from close_profit where month='$mnth' and year='$year'");
		}else{
				$closignstock =  $this->closemonthwithscript($from,$to);
				$batchesleft = $this->db->query("select receipt_id from tbl_goodsreceiving_detail inner join tbl_goodsreceiving on tbl_goodsreceiving.receiptnos=tbl_goodsreceiving_detail.receipt_detail_id inner join tblmaterial_coding on materialcode=tbl_goodsreceiving_detail.itemid where batch_status='0' and catcode='1' and tbl_goodsreceiving_detail.type='Filled' and receiptdate <= '$to'")->result_array();
				//pm($batchesleft);
				$batches_left="";
				foreach ($batchesleft as $key => $value) {
					$batches_left .= "-".$value['receipt_id'];
				}
				

				$array = array(
								"month"=>$mnth,
								"year"=>$year,
								"closingstock"=>$closignstock['qty'],
								"closingstockamt"=>$closignstock['amt'],
								"batches_left"=>$batches_left,
							  );
				$this->mod_common->insert_into_table("close_profit",$array);
		}

		$this->db->trans_complete();

		 $this->session->set_flashdata('ok_message', "Month Closed");
		 redirect(SURL."ProfitClose");
		
	}

	public function closemonthwithscript($from,$to){

		set_time_limit(0);
		ini_set('memory_limit', '-1');
		

		$sales = $this->db->query("select tbl_issue_goods_detail.*,tbl_issue_goods.issuedto from tbl_issue_goods_detail inner join tblmaterial_coding on tbl_issue_goods_detail.itemid=tblmaterial_coding.materialcode inner join tbl_issue_goods on tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id where tblmaterial_coding.catcode='1' and tbl_issue_goods.type='Fill' and tbl_issue_goods.issuedate between '$from' and '$to' order by issuedate asc")->result_array();
		
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

			return $Closingstock = $this->db->query("SELECT sum(tbl_goodsreceiving_detail.Batch_stock *itemnameint/1000) as qty,sum(tbl_goodsreceiving_detail.rate *Batch_stock) as amt FROM `tbl_goodsreceiving` inner join tbl_goodsreceiving_detail on tbl_goodsreceiving.receiptnos=tbl_goodsreceiving_detail.receipt_detail_id INNER join tblmaterial_coding on materialcode=tbl_goodsreceiving_detail.itemid WHERE catcode='1' and trans_typ='purchasefilled' and batch_status='0' and receiptdate <= '$to'
				union
				select sum(Batch_stock*itemnameint/1000) as qty,sum((tbl_issue_return_detail.total_amount/qty) *Batch_stock) as amt from tbl_issue_return_detail inner join tbl_issue_return on tbl_issue_return.irnos=tbl_issue_return_detail.irnos inner join tblmaterial_coding on materialcode=tbl_issue_return_detail.itemid where catcode='1' and tbl_issue_return_detail.type='Filled' and irdate <= '$to'
				")->result_array()[0];
		}
		

	}


	public function uncloselogic($from,$to){

		set_time_limit(0);

		$sales = $this->db->query("select * from tbl_issue_goods_detail inner join tblmaterial_coding on tbl_issue_goods_detail.itemid=tblmaterial_coding.materialcode inner join tbl_issue_goods on tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id where tblmaterial_coding.catcode='1' and tbl_issue_goods.type='Fill' and tbl_issue_goods.issuedate between '$from' and '$to' order by issuedate desc")->result_array();
		
		//pm($sales);
		if(!empty($sales)){
			foreach ($sales as $key => $value){

					$qtytaken = explode(",",$value['qty_taken']);
					$Purchseno = explode(",",$value['purchase_batch_no']);
					
					$i=0;
					foreach ($Purchseno as $key => $Pvalue){

						$newexplode = explode("-",$Pvalue);
						$newqtytaken = $qtytaken[$i];

						if($newexplode[0]=="IG"){

							$this->db->query("update tbl_goodsreceiving_detail set batch_status='0',Batch_stock=Batch_stock+$newqtytaken where receipt_id='".$newexplode[1]."'");

						}else{

							$this->db->query("update tbl_issue_return_detail set batch_status='0',Batch_stock=Batch_stock+$newqtytaken where sr_no='".$newexplode[1]."'");

						}

						$i++;
					}
				$this->db->query("update tbl_issue_goods_detail set purchase_batch_no='0',purchase_amt='0',qty_taken='0' where srno='".$value['srno']."'");
			}
		}
		
		return true;

	}

	public function uncloselogicold($from,$to){

		set_time_limit(0);

		$sales = $this->db->query("select * from tbl_issue_goods_detail inner join tblmaterial_coding on tbl_issue_goods_detail.itemid=tblmaterial_coding.materialcode inner join tbl_issue_goods on tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id where tblmaterial_coding.catcode='1' and tbl_issue_goods.type='Fill' and tbl_issue_goods.issuedate between '$from' and '$to' order by issuedate desc")->result_array();
		
		pm($sales);
		if(!empty($sales)){
			foreach ($sales as $key => $value){

					$Purchseno = array_reverse(explode(",",$value['purchase_batch_no']));
					//pm($Purchseno);
					foreach ($Purchseno as $key => $Pvalue) {

						$newexplode = explode("-",$Pvalue);
						
						if($newexplode[0]=="IG"){

							$purchasequery = $this->db->query("SELECT * FROM `tbl_goodsreceiving_detail` where receipt_id='".$newexplode[1]."'")->result_array()[0];

							if($purchasequery['quantity']>$value['qty']){
								$this->db->query("update tbl_goodsreceiving_detail set batch_status='0',Batch_stock=quantity where receipt_id='".$newexplode[1]."'");
							}else if($purchasequery['quantity']==$value['qty']){
								$this->db->query("update tbl_goodsreceiving_detail set batch_status='0',Batch_stock=quantity where receipt_id='".$newexplode[1]."'");
							}else{
								$stockleft = $value['qty']-$purchasequery['quantity'];
								$this->db->query("update tbl_goodsreceiving_detail set batch_status='0',Batch_stock=quantity where receipt_id='".$newexplode[1]."'"); 
							}

						}else{

							$purchasequery = $this->db->query("SELECT * FROM `tbl_issue_return_detail` where sr_no='".$newexplode[1]."'")->result_array()[0];

							if($purchasequery['qty']>$value['qty']){
								$this->db->query("update tbl_issue_return_detail set batch_status='0',Batch_stock=qty where sr_no='".$newexplode[1]."'");
							}else if($purchasequery['qty']==$value['qty']){
								$this->db->query("update tbl_issue_return_detail set batch_status='0',Batch_stock=qty where sr_no='".$newexplode[1]."'");
							}else{
								$stockleft = $value['qty']-$purchasequery['qty'];
								$this->db->query("update tbl_goodsreceiving_detail set batch_status='0',Batch_stock=qty where sr_no='".$newexplode[1]."'"); 
							}

						}
					}
				$this->db->query("update tbl_issue_goods_detail set purchase_batch_no='0',purchase_amt='0',qty_taken='0' where srno='".$value['srno']."'");
			}
		}
		
		return true;

	}

}
