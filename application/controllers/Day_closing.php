<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Day_closing extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_common"
        ));
        error_reporting(0);
    }

	public function index()
	{ 

 $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        
		$data['title']='Last Posted Date is ';
		
		//$last_date=$this->mod_common->select_last_records('tbl_posting_stock')['post_date'];
		 $last_date =$this->db->query("SELECT `post_date` FROM `tbl_posting_stock` where sale_point_id='$sale_point_id' ORDER BY `post_date` DESC LIMIT 1")->row_array()['post_date'];
		 // echo "SELECT `post_date` FROM `tbl_posting_stock` where sale_point_id='$sale_point_id' ORDER BY `post_date` ASC LIMIT 1";exit();
		$data['last_date']= date('Y-m-d', strtotime('+1 day', strtotime($last_date)));
		$data['unclose_date']=$last_date;
		 if ($last_date=='') {
	    $data['last_date'] =$this->db->query("SELECT `issuedate` FROM `tbl_issue_goods` where sale_point_id='$sale_point_id' ORDER BY `issuedate` ASC LIMIT 1")->row_array()['issuedate'];

		  $data['message']=1;

		 }

		$this->load->view($this->session->userdata('language')."/day_closing",$data);
		}
	
 
 
 
 
public function add()
	{
 	 $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
         if ($sale_point_id=='0') {
	  	$this->session->set_flashdata('err_message', '- Admin Has No Rights To Close Day!');
			redirect(SURL . 'Day_closing');
			exit();
	  }
       
		$date=$this->input->post('from_date_1');
		//echo $date;exit();
		$data =$this->db->query("select * from tblmaterial_coding inner join tbl_issue_goods_detail on tblmaterial_coding.materialcode = tbl_issue_goods_detail.itemid where tblmaterial_coding.catcode='1' and Posted_Date='$date' and sale_point_id='$sale_point_id' and tbl_issue_goods_detail.type not in ('Filled','Empty')")->result_array();



		
		//pm($data);exit();
		foreach ($data as $key => $value) {


						$stocktaken = $value['stocktaken'];
						$con['conditions']=array(
						"srno"=> $value['srno'],
										);

$qty = $value['qty'];
$id = $value['itemid'];


	$p_q = $this->db->query("select * from tbl_goodsreceiving_detail where itemid='$id' and batch_status='open' and type='Filled' and sale_point_id='$sale_point_id' ORDER BY recvd_date asc  LIMIT 1")->row_array();


				$s_qan =$qty ;
	$pur_qty =$this->db->query("select sum(Batch_stock) as qan from  tbl_goodsreceiving_detail where itemid='$id' and batch_status='open' and sale_point_id='$sale_point_id' and type='Filled' and recvd_date<='$date'")->row_array()['qan'];
 
//echo $s_qan;exit();
//echo $pur_qty;exit();
 if ($s_qan>$pur_qty) {
			
		 	$this->session->set_flashdata('error', 'Some thing is Wrong In Stock!');
		 	$itemname =$this->db->query("select itemname from  tblmaterial_coding where materialcode='$id'")->row_array()['itemname'];
		 	//echo $s_qan;
	             echo "Stock Is Not Avaiable for this item no ".$itemname." Plz Enter Purchase for this Item ";
	             $date=$this->input->post('from_date_1');
	             $this->unclose_again($date);
	             exit();
		 }



			
				if($p_q['Batch_stock']>$s_qan){

						$Batch_stock_left = $p_q['Batch_stock']-$s_qan;
						$stocktaken = $s_qan;
						$purchase_batch_no = $p_q['receipt_id'];
						$id = $p_q['receipt_id'];
						$this->db->query("update tbl_goodsreceiving_detail set Batch_stock='$Batch_stock_left' where receipt_id='".$p_q['receipt_id']."'");

						$totalpurchasedamt = $s_qan*$p_q['rate'];
						
					

					}else if($p_q['Batch_stock']==$s_qan){
						$purchase_batch_no = $p_q['receipt_id'];
						$this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

						$stocktaken = $s_qan;

						$totalpurchasedamt = $s_qan*$p_q['rate'];

					}else{
						$halfamt=0;
						$sale_Qty_left = $s_qan-$p_q['Batch_stock'];
						$this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

						$halfamt = $p_q['Batch_stock']*$p_q['rate'];
						$purchase_batch_no = $p_q['receipt_id'];

						$stocktaken = $p_q['Batch_stock'];

						$loop=2;
						$end = 0;
						while(1<$loop){
								

							$p_q = $this->db->query("select * from tbl_goodsreceiving_detail where batch_status='open' and itemid='$id' and sale_point_id='$sale_point_id' and type='Filled' order by recvd_date asc limit 1")->result_array()[0];

							if($sale_Qty_left>$p_q['Batch_stock']){

								$sale_Qty_left = $sale_Qty_left - $p_q['Batch_stock'];

								$stocktaken = $stocktaken.",".$p_q['Batch_stock'];

								$this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

								$halfamt = $halfamt + ($p_q['Batch_stock']*$p_q['rate']);
								$purchase_batch_no = $purchase_batch_no.",".$p_q['receipt_id'];

							}else if($sale_Qty_left==$p_q['Batch_stock']){

								$this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

								$halfamt = $halfamt + ($p_q['Batch_stock']*$p_q['rate']);
								$loop=0;
								$purchase_batch_no = $purchase_batch_no.",".$p_q['receipt_id'];

								$stocktaken = $stocktaken.",".$p_q['Batch_stock'];

							}else{
								
								$Batch_stock_left = $p_q['Batch_stock'] - $sale_Qty_left;
									$this->db->query("update tbl_goodsreceiving_detail set batch_status='open',Batch_stock='$Batch_stock_left' where receipt_id='".$p_q['receipt_id']."'");

								$halfamt = $halfamt + ($sale_Qty_left*$p_q['rate']);
								
								$loop=0;

								$purchase_batch_no = $purchase_batch_no.",".$p_q['receipt_id'];

								$stocktaken = $stocktaken.",".$sale_Qty_left;
							}
							if ($end==60) {
								$this->session->set_flashdata('error', 'Some Thing wrong happend!');
								redirect(SURL . 'Day_closing/');
								exit();
							}
							$end=$end+1;
}
$totalpurchasedamt = $halfamt;
}

//pm($totalpurchasedamt);
$this->db->query("update tbl_issue_goods_detail set purchase_amt='$totalpurchasedamt',purchase_batch_no='$purchase_batch_no',stocktaken='$stocktaken' where srno='".$value['srno']."'");



		}
		$date=$this->input->post('from_date_1'); 
		$table='tblmaterial_coding';
			//$all_item=$this->mod_common->select_array_records($table);
			$all_item=$this->db->query("select * from tblmaterial_coding where catcode='1'")->result_array();

			$last_day_filled=0;
			$last_day_empty=0;

			foreach ($all_item as $key => $value) {
				$id=$value['materialcode'];

				$today_empty_filled=$this->mod_common->stock($id,'empty',date('Y-m-d', strtotime('+1 day', strtotime($date))),1);
				$today_stock=explode('_', $today_empty_filled);

				$closing_empty_filled=$this->mod_common->stock($id,'empty',date('Y-m-d', strtotime('+1 day', strtotime($date))),1);
				$closing_stock=explode('_', $closing_empty_filled);

				$last_date= date('Y-m-d', strtotime('-1 day', strtotime($date)));


				$where_last = "post_date = '" . $last_date . "' AND itemcode = '" . $id . "'";

				$last_day=$this->mod_common->select_single_records('tbl_posting_stock',$where_last);

				//pm($last_day); exit();

				$last_day_filled=$last_day['today_filled'];
				$last_day_empty=$last_day['today_empty'];

				$ins_array = array(
				    "post_date" =>$date,
				    "itemcode" =>$id,
				    "last_day_filled" =>$last_day_filled,
				    "last_day_empty" =>$last_day_empty,
				    "today_filled" =>$today_stock[0],
				    "today_empty" =>$today_stock[1],
				   	"closing_filled" =>$closing_stock[0],
				    "closing_empty" =>$closing_stock[1],
					"created_by" =>$this->session->userdata('id'), 
					"created_dt" =>date('Y-m-d'), 
					"sale_point_id" =>$sale_point_id,
				);
				#----------- add record---------------#
				$table = "tbl_posting_stock";
				$this->mod_common->insert_into_table($table, $ins_array);
			}

$date=$this->input->post('from_date_1');
		$data =$this->db->query("select * from tbl_issue_goods where issuedate='$date' and sale_point_id='$sale_point_id'")->result_array();
		//pm($data);
		
		foreach ($data as $key => $value)
		 {
			$purchase_amt=0;
			$id = $value['issuenos'];

		$dataa =$this->db->query("select * from tbl_issue_goods_detail where ig_detail_id='$id' and sale_point_id='$sale_point_id'")->result_array();
		foreach ($dataa as $key => $valuee) 
		{
			$purchase_amt =$purchase_amt +$valuee['purchase_amt'];

		}
 $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
 $cost_code=$fix_code['cost_of_goods_code'];
 $stock_code=$fix_code['stock_code'];
				$vno = $id."-SV-CV";
				$nar = "Cost of good sold for sale= ".$id;
				 $array = array(
	    				"vno"=>$vno,
	    				"vtype"=>"CV",
	    				"damount"=>$purchase_amt,
	    				"camount"=>$purchase_amt,
	    				"svtype"=>"",
	    				"sale_point_id" =>$sale_point_id,
	    				"created_date"=>$date
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_master",$array);
	
	    
	    $array = array(
	    				"vno"=>$vno,
	    				"acode"=>$stock_code,//stock code will come here
	    				"damount"=>"0",
	    				"camount"=>$purchase_amt,
	    				"remarks"=>$nar,
	    				"vtype"=>"CV",
	    				"svtype"=>"",
	    				"vdate"=>$date,
	    				"sale_point_id" =>$sale_point_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);

	    $array = array(
	    				"vno"=>$vno,
	    				"acode"=>$cost_code,
	    				"damount"=>$purchase_amt,
	    				"camount"=>"0",
	    				"remarks"=>$nar,
	    				"vtype"=>"CV",
	    				"svtype"=>"",
	    				"vdate"=>$date,
	    				"sale_point_id" =>$sale_point_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);





		}

$this->sale_procedure();

		// $this->session->set_flashdata('ok_message', 'Successfully close');
		// redirect(SURL . 'Day_closing');	
	}

public function sale_procedure(){
	$login_user=$this->session->userdata('id');
    $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

		$date=$this->input->post('from_date_1');
		
		$data =$this->db->query("select * from tblmaterial_coding inner join tbl_issue_goods_detail on tblmaterial_coding.materialcode = tbl_issue_goods_detail.itemid where tblmaterial_coding.catcode='1' and Posted_Date='$date' and sale_point_id='$sale_point_id' and tbl_issue_goods_detail.type='sale'")->result_array();


		//pm($data);exit();
		foreach ($data as $key => $value) {


						$stocktaken = $value['stocktaken'];
						$con['conditions']=array(
						"srno"=> $value['srno'],
										);

                         $qty = $value['qty'];
                         $id = $value['itemid'];


	$p_q = $this->db->query("select * from tbl_goodsreceiving_detail where itemid='$id' and batch_status='open' and type='Filled' and sale_point_id='$sale_point_id' ORDER BY recvd_date asc  LIMIT 1")->row_array();

	$rate_empty = $this->db->query("select * from tbl_goodsreceiving_detail where itemid='$id' and batch_status='open' and type='Empty' and sale_point_id='$sale_point_id' and rate>0 ORDER BY recvd_date asc  LIMIT 1")->row_array();

				$s_qan =$qty ;
	// $pur_qty =$this->db->query("select sum(Batch_stock) as qan from  tbl_goodsreceiving_detail where itemid='$id' and batch_status='open' and sale_point_id='$sale_point_id' and type='Filled'")->row_array()['qan'];
	// //pm($pur_qty);exit();
 // if ($s_qan>$pur_qty) {
			
	// 	 	$this->session->set_flashdata('error', 'Some thing is Wrong In Stock!');
	// 	 	$itemname =$this->db->query("select itemname from  tblmaterial_coding where materialcode='$id'")->row_array()['itemname'];
	// 	 	//echo $s_qan;
	//              echo "Stock Is Not Avaiable for this item no ".$itemname." Plz Enter Purchase for this Item ";
	//              $date=$this->input->post('from_date_1');
	//              $this->unclose_again_empty($date);
	//              exit();
	// 	 }



			
				if($p_q['Batch_stock']>$s_qan){

						//$Batch_stock_left = $p_q['Batch_stock']-$s_qan;
						//$stocktaken = $s_qan;
						 //$purchase_batch_no = $p_q['receipt_id'];
						// $id = $p_q['receipt_id'];
						// $this->db->query("update tbl_goodsreceiving_detail set Batch_stock='$Batch_stock_left' where receipt_id='".$p_q['receipt_id']."'");

						$totalpurchasedamt = $s_qan*$rate_empty['rate'];
						
					

					}else if($p_q['Batch_stock']==$s_qan){
						//$purchase_batch_no = $p_q['receipt_id'];
						// $this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

						//$stocktaken = $s_qan;

						$totalpurchasedamt = $s_qan*$rate_empty['rate'];

					}else{
						$halfamt=0;
						$sale_Qty_left = $s_qan-$p_q['Batch_stock'];
						// $this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

						$halfamt = $p_q['Batch_stock']*$rate_empty['rate'];
						$purchase_batch_no = $p_q['receipt_id'];

						$stocktaken = $p_q['Batch_stock'];

						$loop=2;
						$end = 0;
						while(1<$loop){
								

							$p_q = $this->db->query("select * from tbl_goodsreceiving_detail where batch_status='open' and itemid='$id' and sale_point_id='$sale_point_id' and type='Filled' order by recvd_date asc limit 1")->result_array()[0];

							$rate_empty = $this->db->query("select * from tbl_goodsreceiving_detail where batch_status='open' and itemid='$id' and sale_point_id='$sale_point_id' and type='Empty' order by recvd_date asc limit 1")->result_array()[0];

							if($sale_Qty_left>$p_q['Batch_stock']){

								$sale_Qty_left = $sale_Qty_left - $p_q['Batch_stock'];

								$stocktaken = $stocktaken.",".$p_q['Batch_stock'];

								// $this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

								$halfamt = $halfamt + ($p_q['Batch_stock']*$rate_empty['rate']);
								$purchase_batch_no = $purchase_batch_no.",".$p_q['receipt_id'];

							}else if($sale_Qty_left==$p_q['Batch_stock']){

								// $this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

								$halfamt = $halfamt + ($p_q['Batch_stock']*$rate_empty['rate']);
								$loop=0;
								$purchase_batch_no = $purchase_batch_no.",".$p_q['receipt_id'];

								$stocktaken = $stocktaken.",".$p_q['Batch_stock'];

							}else{
								
								$Batch_stock_left = $p_q['Batch_stock'] - $sale_Qty_left;
									// $this->db->query("update tbl_goodsreceiving_detail set batch_status='open',Batch_stock='$Batch_stock_left' where receipt_id='".$p_q['receipt_id']."'");

								$halfamt = $halfamt + ($sale_Qty_left*$rate_empty['rate']);
								
								$loop=0;

								$purchase_batch_no = $purchase_batch_no.",".$rate_empty['receipt_id'];

								$stocktaken = $stocktaken.",".$sale_Qty_left;
							}
							if ($end==60) {
								$this->session->set_flashdata('error', 'Some Thing wrong happend!');
								redirect(SURL . 'Day_closing/');
								exit();
							}
							$end=$end+1;
}
$totalpurchasedamt = $halfamt;
}

//pm($totalpurchasedamt);
$this->db->query("update tbl_issue_goods_detail set purchase_amt_empty='$totalpurchasedamt' where srno='".$value['srno']."' and type='sale'");



		}
		

        $date=$this->input->post('from_date_1');
		$data =$this->db->query("select * from tbl_issue_goods where issuedate='$date' and sale_point_id='$sale_point_id'")->result_array();
		//pm($data);
		
		foreach ($data as $key => $value)
		 {
			$purchase_amt=0;
			$id = $value['issuenos'];

		$dataa =$this->db->query("select * from tbl_issue_goods_detail where ig_detail_id='$id' and sale_point_id='$sale_point_id' and type='sale'")->result_array();
		foreach ($dataa as $key => $valuee) 
		{
			$purchase_amt =$purchase_amt +$valuee['purchase_amt_empty'];

		}
 $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
 $cost_code_cylinder=$fix_code['cost_of_goods_cylinder_code'];
 $sale_cylinder_code=$fix_code['empty_stock_code'];
				$vno = $id."-SV-CV";
				$nar = "Cost of good sold for empty cylinder sale = ".$id;
				 $array = array(
	    				"vno"=>$vno,
	    				"vtype"=>"CV",
	    				"damount"=>$purchase_amt,
	    				"camount"=>$purchase_amt,
	    				"svtype"=>"",
	    				"sale_point_id" =>$sale_point_id,
	    				"created_date"=>$date
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_master",$array);
	
	    
	    $array = array(
	    				"vno"=>$vno,
	    				"acode"=>$sale_cylinder_code,//stock code will come here
	    				"damount"=>"0",
	    				"camount"=>$purchase_amt,
	    				"remarks"=>$nar,
	    				"vtype"=>"CV",
	    				"svtype"=>"",
	    				"vdate"=>$date,
	    				"sale_point_id" =>$sale_point_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);

	    $array = array(
	    				"vno"=>$vno,
	    				"acode"=>$cost_code_cylinder,//cost of goods cylinder code will come here
	    				"damount"=>$purchase_amt,
	    				"camount"=>"0",
	    				"remarks"=>$nar,
	    				"vtype"=>"CV",
	    				"svtype"=>"",
	    				"vdate"=>$date,
	    				"sale_point_id" =>$sale_point_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);





		}
		$this->wo_sec_procedure();
		 // $this->session->set_flashdata('ok_message', 'Successfully close');
		 // redirect(SURL . 'Day_closing');

}
public function wo_sec_procedure(){
	$login_user=$this->session->userdata('id');
    $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

		$date=$this->input->post('from_date_1');
		
		$data =$this->db->query("select * from tblmaterial_coding inner join tbl_issue_goods_detail on tblmaterial_coding.materialcode = tbl_issue_goods_detail.itemid where tblmaterial_coding.catcode='1' and Posted_Date='$date' and sale_point_id='$sale_point_id' and tbl_issue_goods_detail.type='wo_sec'")->result_array();
		//pm($data);exit();
		foreach ($data as $key => $value) {


						$stocktaken = $value['stocktaken'];
						$con['conditions']=array(
						"srno"=> $value['srno'],
										);

                         $qty = $value['qty'];
                         $id = $value['itemid'];


	$p_q = $this->db->query("select * from tbl_goodsreceiving_detail where itemid='$id' and batch_status='open' and type='Filled' and sale_point_id='$sale_point_id' ORDER BY recvd_date asc  LIMIT 1")->row_array();

	$rate_empty = $this->db->query("select * from tbl_goodsreceiving_detail where itemid='$id' and batch_status='open' and type='Empty' and sale_point_id='$sale_point_id' and rate>0 ORDER BY recvd_date asc  LIMIT 1")->row_array();

				$s_qan =$qty ;
	// $pur_qty =$this->db->query("select sum(Batch_stock) as qan from  tbl_goodsreceiving_detail where itemid='$id' and batch_status='open' and sale_point_id='$sale_point_id' and type='Filled'")->row_array()['qan'];
	// //pm($pur_qty);exit();
 // if ($s_qan>$pur_qty) {
			
	// 	 	$this->session->set_flashdata('error', 'Some thing is Wrong In Stock!');
	// 	 	$itemname =$this->db->query("select itemname from  tblmaterial_coding where materialcode='$id'")->row_array()['itemname'];
	// 	 	//echo $s_qan;
	//              echo "Stock Is Not Avaiable for this item no ".$itemname." Plz Enter Purchase for this Item ";
	//              $date=$this->input->post('from_date_1');
	//              $this->unclose_again_empty($date);
	//              exit();
	// 	 }



			
				if($p_q['Batch_stock']>$s_qan){

						//$Batch_stock_left = $p_q['Batch_stock']-$s_qan;
						//$stocktaken = $s_qan;
						//$purchase_batch_no = $p_q['receipt_id'];
						//$id = $p_q['receipt_id'];
						// $this->db->query("update tbl_goodsreceiving_detail set Batch_stock='$Batch_stock_left' where receipt_id='".$p_q['receipt_id']."'");

						$totalpurchasedamt = $s_qan*$rate_empty['rate'];
						
					

					}else if($p_q['Batch_stock']==$s_qan){
						//$purchase_batch_no = $p_q['receipt_id'];
						// $this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

						//$stocktaken = $s_qan;

						$totalpurchasedamt = $s_qan*$rate_empty['rate'];

					}else{
						$halfamt=0;
						$sale_Qty_left = $s_qan-$p_q['Batch_stock'];
						// $this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

						$halfamt = $p_q['Batch_stock']*$rate_empty['rate'];
						$purchase_batch_no = $p_q['receipt_id'];

						$stocktaken = $p_q['Batch_stock'];

						$loop=2;
						$end = 0;
						while(1<$loop){
								

							$p_q = $this->db->query("select * from tbl_goodsreceiving_detail where batch_status='open' and itemid='$id' and sale_point_id='$sale_point_id' and type='Filled' order by recvd_date asc limit 1")->result_array()[0];

							$rate_empty = $this->db->query("select * from tbl_goodsreceiving_detail where batch_status='open' and itemid='$id' and sale_point_id='$sale_point_id' and type='Empty' order by recvd_date asc limit 1")->result_array()[0];

							if($sale_Qty_left>$p_q['Batch_stock']){

								$sale_Qty_left = $sale_Qty_left - $p_q['Batch_stock'];

								$stocktaken = $stocktaken.",".$p_q['Batch_stock'];

								// $this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

								$halfamt = $halfamt + ($p_q['Batch_stock']*$rate_empty['rate']);
								$purchase_batch_no = $purchase_batch_no.",".$p_q['receipt_id'];

							}else if($sale_Qty_left==$p_q['Batch_stock']){

								// $this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

								$halfamt = $halfamt + ($p_q['Batch_stock']*$rate_empty['rate']);
								$loop=0;
								$purchase_batch_no = $purchase_batch_no.",".$p_q['receipt_id'];

								$stocktaken = $stocktaken.",".$p_q['Batch_stock'];

							}else{
								
								$Batch_stock_left = $p_q['Batch_stock'] - $sale_Qty_left;
									// $this->db->query("update tbl_goodsreceiving_detail set batch_status='open',Batch_stock='$Batch_stock_left' where receipt_id='".$p_q['receipt_id']."'");

								$halfamt = $halfamt + ($sale_Qty_left*$rate_empty['rate']);
								
								$loop=0;

								$purchase_batch_no = $purchase_batch_no.",".$p_q['receipt_id'];

								$stocktaken = $stocktaken.",".$sale_Qty_left;
							}
							if ($end==60) {
								$this->session->set_flashdata('error', 'Some Thing wrong happend!');
								redirect(SURL . 'Day_closing/');
								exit();
							}
							$end=$end+1;
}
$totalpurchasedamt = $halfamt;
}

//pm($totalpurchasedamt);
$this->db->query("update tbl_issue_goods_detail set purchase_amt_empty='$totalpurchasedamt' where srno='".$value['srno']."' and type='wo_sec'");



		}
		

        $date=$this->input->post('from_date_1');
		$data =$this->db->query("select * from tbl_issue_goods where issuedate='$date' and sale_point_id='$sale_point_id'")->result_array();
		//pm($data);
		
		foreach ($data as $key => $value)
		 {
			$purchase_amt=0;
			$id = $value['issuenos'];

		$dataa =$this->db->query("select * from tbl_issue_goods_detail where ig_detail_id='$id' and sale_point_id='$sale_point_id' and type='wo_sec'")->result_array();
		foreach ($dataa as $key => $valuee) 
		{
			$purchase_amt =$purchase_amt +$valuee['purchase_amt_empty'];

		}
 $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
 $empty_stock_code=$fix_code['empty_stock_code'];
 $cylinder_wo_sec_code=$fix_code['cylinder_wo_sec_code'];
				$vno = $id."-SV-CV";
				$nar = "Cylinder Without Security = ".$id;
				 $array = array(
	    				"vno"=>$vno,
	    				"vtype"=>"CV",
	    				"damount"=>$purchase_amt,
	    				"camount"=>$purchase_amt,
	    				"svtype"=>"",
	    				"sale_point_id" =>$sale_point_id,
	    				"created_date"=>$date
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_master",$array);


	      $array = array(
	    				"vno"=>$vno,
	    				"acode"=>$cylinder_wo_sec_code,//cylinder wo security code will come here
	    				"damount"=>$purchase_amt,
	    				"camount"=>"0",
	    				"remarks"=>$nar,
	    				"vtype"=>"CV",
	    				"svtype"=>"",
	    				"vdate"=>$date,
	    				"sale_point_id" =>$sale_point_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);
	
	    
	    $array = array(
	    				"vno"=>$vno,
	    				"acode"=>$empty_stock_code,//Empty stock code will come here
	    				"damount"=>"0",
	    				"camount"=>$purchase_amt,
	    				"remarks"=>$nar,
	    				"vtype"=>"CV",
	    				"svtype"=>"",
	    				"vdate"=>$date,
	    				"sale_point_id" =>$sale_point_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);

	  





		}
		$this->security_procedure();
		// $this->session->set_flashdata('ok_message', 'Successfully close');
		 //redirect(SURL . 'Day_closing');

}
public function security_procedure(){
	$login_user=$this->session->userdata('id');
    $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

		$date=$this->input->post('from_date_1');
		
		$data =$this->db->query("select * from tblmaterial_coding inner join tbl_issue_goods_detail on tblmaterial_coding.materialcode = tbl_issue_goods_detail.itemid where tblmaterial_coding.catcode='1' and Posted_Date='$date' and sale_point_id='$sale_point_id' and tbl_issue_goods_detail.type='security'")->result_array();
		//pm($data);exit();
		foreach ($data as $key => $value) {


						$stocktaken = $value['stocktaken'];
						$con['conditions']=array(
						"srno"=> $value['srno'],
										);

                         $qty = $value['qty'];
                         $id = $value['itemid'];


	$p_q = $this->db->query("select * from tbl_goodsreceiving_detail where itemid='$id' and batch_status='open' and type='Filled' and sale_point_id='$sale_point_id' ORDER BY recvd_date asc  LIMIT 1")->row_array();

	$rate_empty = $this->db->query("select * from tbl_goodsreceiving_detail where itemid='$id' and batch_status='open' and type='Empty' and sale_point_id='$sale_point_id' and rate>0 ORDER BY recvd_date asc  LIMIT 1")->row_array();

				$s_qan =$qty ;
	// $pur_qty =$this->db->query("select sum(Batch_stock) as qan from  tbl_goodsreceiving_detail where itemid='$id' and batch_status='open' and sale_point_id='$sale_point_id' and type='Filled'")->row_array()['qan'];
	// //pm($pur_qty);exit();
 // if ($s_qan>$pur_qty) {
			
	// 	 	$this->session->set_flashdata('error', 'Some thing is Wrong In Stock!');
	// 	 	$itemname =$this->db->query("select itemname from  tblmaterial_coding where materialcode='$id'")->row_array()['itemname'];
	// 	 	//echo $s_qan;
	//              echo "Stock Is Not Avaiable for this item no ".$itemname." Plz Enter Purchase for this Item ";
	//              $date=$this->input->post('from_date_1');
	//              $this->unclose_again_empty($date);
	//              exit();
	// 	 }



			
				if($p_q['Batch_stock']>$s_qan){

						//$Batch_stock_left = $p_q['Batch_stock']-$s_qan;
						//$stocktaken = $s_qan;
						//$purchase_batch_no = $p_q['receipt_id'];
						//$id = $p_q['receipt_id'];
						// $this->db->query("update tbl_goodsreceiving_detail set Batch_stock='$Batch_stock_left' where receipt_id='".$p_q['receipt_id']."'");

						$totalpurchasedamt = $s_qan*$rate_empty['rate'];
						
					

					}else if($p_q['Batch_stock']==$s_qan){
						//$purchase_batch_no = $p_q['receipt_id'];
						//$this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

						//$stocktaken = $s_qan;

						$totalpurchasedamt = $s_qan*$rate_empty['rate'];

					}else{
						$halfamt=0;
						$sale_Qty_left = $s_qan-$p_q['Batch_stock'];
						//$this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

						$halfamt = $p_q['Batch_stock']*$rate_empty['rate'];
						$purchase_batch_no = $p_q['receipt_id'];

						$stocktaken = $p_q['Batch_stock'];

						$loop=2;
						$end = 0;
						while(1<$loop){
								

							$p_q = $this->db->query("select * from tbl_goodsreceiving_detail where batch_status='open' and itemid='$id' and sale_point_id='$sale_point_id' and type='Filled' order by recvd_date asc limit 1")->result_array()[0];

							$rate_empty = $this->db->query("select * from tbl_goodsreceiving_detail where batch_status='open' and itemid='$id' and sale_point_id='$sale_point_id' and type='Empty' order by recvd_date asc limit 1")->result_array()[0];

							if($sale_Qty_left>$p_q['Batch_stock']){

								$sale_Qty_left = $sale_Qty_left - $p_q['Batch_stock'];

								$stocktaken = $stocktaken.",".$p_q['Batch_stock'];

								//$this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

								$halfamt = $halfamt + ($p_q['Batch_stock']*$rate_empty['rate']);
								$purchase_batch_no = $purchase_batch_no.",".$p_q['receipt_id'];

							}else if($sale_Qty_left==$p_q['Batch_stock']){

								//$this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

								$halfamt = $halfamt + ($p_q['Batch_stock']*$rate_empty['rate']);
								$loop=0;
								$purchase_batch_no = $purchase_batch_no.",".$p_q['receipt_id'];

								$stocktaken = $stocktaken.",".$p_q['Batch_stock'];

							}else{
								
								$Batch_stock_left = $p_q['Batch_stock'] - $sale_Qty_left;
									//$this->db->query("update tbl_goodsreceiving_detail set batch_status='open',Batch_stock='$Batch_stock_left' where receipt_id='".$p_q['receipt_id']."'");

								$halfamt = $halfamt + ($sale_Qty_left*$rate_empty['rate']);
								
								$loop=0;

								$purchase_batch_no = $purchase_batch_no.",".$p_q['receipt_id'];

								$stocktaken = $stocktaken.",".$sale_Qty_left;
							}
							if ($end==60) {
								$this->session->set_flashdata('error', 'Some Thing wrong happend!');
								redirect(SURL . 'Day_closing/');
								exit();
							}
							$end=$end+1;
}
$totalpurchasedamt = $halfamt;
}

//pm($totalpurchasedamt);
$this->db->query("update tbl_issue_goods_detail set purchase_amt_empty='$totalpurchasedamt' where srno='".$value['srno']."' and type='security'");



		}
		

        $date=$this->input->post('from_date_1');
		$data =$this->db->query("select * from tbl_issue_goods where issuedate='$date' and sale_point_id='$sale_point_id'")->result_array();
		//pm($data);
		
		foreach ($data as $key => $value)
		 {
			$purchase_amt=0;
			$wrate=0;
			$id = $value['issuenos'];

		$dataa =$this->db->query("select * from tbl_issue_goods_detail where ig_detail_id='$id' and sale_point_id='$sale_point_id' and type='security'")->result_array();
		foreach ($dataa as $key => $valuee) 
		{
			$purchase_amt =$purchase_amt +$valuee['purchase_amt_empty'];
			$wrate =$wrate +$valuee['wrate'];
			$gain_loss=$purchase_amt-$wrate;

		}
 $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
 $empty_stock_code=$fix_code['empty_stock_code'];
 $cylinder_sec_code=$fix_code['cylinder_sec_code'];
 $gain_loss_code=$fix_code['gain_loss_code'];
				$vno = $id."-SV-CV";
				$nar = "Cylinder With Security = ".$id;
				 $array = array(
	    				"vno"=>$vno,
	    				"vtype"=>"CV",
	    				"damount"=>$purchase_amt,
	    				"camount"=>$purchase_amt,
	    				"svtype"=>"",
	    				"sale_point_id" =>$sale_point_id,
	    				"created_date"=>$date
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_master",$array);	

	      $array = array(
	    				"vno"=>$vno,
	    				"acode"=>$cylinder_sec_code,//Empty stock code will come here
	    				"damount"=>$purchase_amt,
	    				"camount"=>"0",
	    				"remarks"=>$nar,
	    				"vtype"=>"CV",
	    				"svtype"=>"",
	    				"vdate"=>$date,
	    				"sale_point_id" =>$sale_point_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);

	    $array = array(
	    				"vno"=>$vno,
	    				"acode"=>$empty_stock_code,//Empty stock code will come here
	    				"damount"=>"0",
	    				"camount"=>$wrate,
	    				"remarks"=>$nar,
	    				"vtype"=>"CV",
	    				"svtype"=>"",
	    				"vdate"=>$date,
	    				"sale_point_id" =>$sale_point_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);

	    if ($purchase_amt>$wrate) {
	    	
	      $array = array(
	    				"vno"=>$vno,
	    				"acode"=>$gain_loss_code,//gain loss code will come here
	    				"damount"=>"0",
	    				"camount"=>$gain_loss,
	    				"remarks"=>$nar,
	    				"vtype"=>"CV",
	    				"svtype"=>"",
	    				"vdate"=>$date,
	    				"sale_point_id" =>$sale_point_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);
	    }else{

	      $array = array(
	    				"vno"=>$vno,
	    				"acode"=>$gain_loss_code,//gain loss code will come here
	    				"damount"=>$gain_loss,
	    				"camount"=>"0",
	    				"remarks"=>$nar,
	    				"vtype"=>"CV",
	    				"svtype"=>"",
	    				"vdate"=>$date,
	    				"sale_point_id" =>$sale_point_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);
	    }

	  





		}
		$this->appliances_procedure();
		 // $this->session->set_flashdata('ok_message', 'Successfully close');
		 // redirect(SURL . 'Day_closing');

}
public function appliances_procedure()
	{
 	 $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
         if ($sale_point_id=='0') {
	  	$this->session->set_flashdata('err_message', '- Admin Has No Rights To Close Day!');
			redirect(SURL . 'Day_closing');
			exit();
	  }
       
		$date=$this->input->post('from_date_1');
		//echo $date;exit();
		$data =$this->db->query("select * from tblmaterial_coding inner join tbl_issue_goods_detail on tblmaterial_coding.materialcode = tbl_issue_goods_detail.itemid where tblmaterial_coding.catcode in('2','3') and Posted_Date='$date' and sale_point_id='$sale_point_id'")->result_array();



		
		//pm($data);exit();
		foreach ($data as $key => $value) {


						$stocktaken = $value['stocktaken_other'];
						$con['conditions']=array(
						"srno"=> $value['srno'],
										);

$qty = $value['qty'];
$id = $value['itemid'];


	$p_q = $this->db->query("select * from tbl_goodsreceiving_detail where itemid='$id' and batch_status='open' and type='Other' and sale_point_id='$sale_point_id' ORDER BY recvd_date asc  LIMIT 1")->row_array();


				$s_qan =$qty ;
	$pur_qty =$this->db->query("select sum(Batch_stock) as qan from  tbl_goodsreceiving_detail where itemid='$id' and batch_status='open' and sale_point_id='$sale_point_id' and type='Other'")->row_array()['qan'];

//echo $s_qan;exit();
//echo $pur_qty;exit();
 if ($s_qan>$pur_qty) {
			
		 	$this->session->set_flashdata('error', 'Some thing is Wrong In Stock!');
		 	$itemname =$this->db->query("select itemname from  tblmaterial_coding where materialcode='$id'")->row_array()['itemname'];
		 	//echo $s_qan;
	             echo "Stock Is Not Avaiable for this item no ".$itemname." Plz Enter Purchase for this Item ";
	             $date=$this->input->post('from_date_1');
	             $this->unclose_again($date);
	             exit();
		 }



			
				if($p_q['Batch_stock']>$s_qan){

						$Batch_stock_left = $p_q['Batch_stock']-$s_qan;
						$stocktaken = $s_qan;
						$purchase_batch_no = $p_q['receipt_id'];
						$id = $p_q['receipt_id'];
						$this->db->query("update tbl_goodsreceiving_detail set Batch_stock='$Batch_stock_left' where receipt_id='".$p_q['receipt_id']."'");

						$totalpurchasedamt = $s_qan*$p_q['rate'];
						
					

					}else if($p_q['Batch_stock']==$s_qan){
						$purchase_batch_no = $p_q['receipt_id'];
						$this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

						$stocktaken = $s_qan;

						$totalpurchasedamt = $s_qan*$p_q['rate'];

					}else{
						$halfamt=0;
						$sale_Qty_left = $s_qan-$p_q['Batch_stock'];
						$this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

						$halfamt = $p_q['Batch_stock']*$p_q['rate'];
						$purchase_batch_no = $p_q['receipt_id'];

						$stocktaken = $p_q['Batch_stock'];

						$loop=2;
						$end = 0;
						while(1<$loop){
								

							$p_q = $this->db->query("select * from tbl_goodsreceiving_detail where batch_status='open' and itemid='$id' and sale_point_id='$sale_point_id' and type='Other' order by recvd_date asc limit 1")->result_array()[0];

							if($sale_Qty_left>$p_q['Batch_stock']){

								$sale_Qty_left = $sale_Qty_left - $p_q['Batch_stock'];

								$stocktaken = $stocktaken.",".$p_q['Batch_stock'];

								$this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

								$halfamt = $halfamt + ($p_q['Batch_stock']*$p_q['rate']);
								$purchase_batch_no = $purchase_batch_no.",".$p_q['receipt_id'];

							}else if($sale_Qty_left==$p_q['Batch_stock']){

								$this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

								$halfamt = $halfamt + ($p_q['Batch_stock']*$p_q['rate']);
								$loop=0;
								$purchase_batch_no = $purchase_batch_no.",".$p_q['receipt_id'];

								$stocktaken = $stocktaken.",".$p_q['Batch_stock'];

							}else{
								
								$Batch_stock_left = $p_q['Batch_stock'] - $sale_Qty_left;
									$this->db->query("update tbl_goodsreceiving_detail set batch_status='open',Batch_stock='$Batch_stock_left' where receipt_id='".$p_q['receipt_id']."'");

								$halfamt = $halfamt + ($sale_Qty_left*$p_q['rate']);
								
								$loop=0;

								$purchase_batch_no = $purchase_batch_no.",".$p_q['receipt_id'];

								$stocktaken = $stocktaken.",".$sale_Qty_left;
							}
							if ($end==60) {
								$this->session->set_flashdata('error', 'Some Thing wrong happend!');
								redirect(SURL . 'Day_closing/');
								exit();
							}
							$end=$end+1;
}
$totalpurchasedamt = $halfamt;
}

//pm($totalpurchasedamt);
$this->db->query("update tbl_issue_goods_detail set purchase_amt_other='$totalpurchasedamt',purchase_batch_no_other='$purchase_batch_no',stocktaken_other='$stocktaken' where srno='".$value['srno']."'");



		}


        $date=$this->input->post('from_date_1');
		$data =$this->db->query("select * from tbl_issue_goods where issuedate='$date' and sale_point_id='$sale_point_id'")->result_array();
		//pm($data);
		
		foreach ($data as $key => $value)
		 {
			$purchase_amt=0;
			$id = $value['issuenos'];

		$dataa =$this->db->query("select * from tbl_issue_goods_detail where ig_detail_id='$id' and sale_point_id='$sale_point_id'")->result_array();
		foreach ($dataa as $key => $valuee) 
		{
			$purchase_amt =$purchase_amt +$valuee['purchase_amt_other'];

		}
 $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
 $cost_code=$fix_code['cost_of_goods_appliances_code'];
 $stock_code=$fix_code['appliances_code'];
				$vno = $id."-SV-CV";
				$nar = "Cost of good sold for Appliances sale= ".$id;
				 $array = array(
	    				"vno"=>$vno,
	    				"vtype"=>"CV",
	    				"damount"=>$purchase_amt,
	    				"camount"=>$purchase_amt,
	    				"svtype"=>"",
	    				"sale_point_id" =>$sale_point_id,
	    				"created_date"=>$date
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_master",$array);
	
	    
	    $array = array(
	    				"vno"=>$vno,
	    				"acode"=>$stock_code,//stock code will come here
	    				"damount"=>"0",
	    				"camount"=>$purchase_amt,
	    				"remarks"=>$nar,
	    				"vtype"=>"CV",
	    				"svtype"=>"",
	    				"vdate"=>$date,
	    				"sale_point_id" =>$sale_point_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);

	    $array = array(
	    				"vno"=>$vno,
	    				"acode"=>$cost_code,
	    				"damount"=>$purchase_amt,
	    				"camount"=>"0",
	    				"remarks"=>$nar,
	    				"vtype"=>"CV",
	    				"svtype"=>"",
	    				"vdate"=>$date,
	    				"sale_point_id" =>$sale_point_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);





		}

$this->empty_procedure();

		// $this->session->set_flashdata('ok_message', 'Successfully close');
		// redirect(SURL . 'Day_closing');	
	}
	public function empty_procedure()
	{
 	 $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
         if ($sale_point_id=='0') {
	  	$this->session->set_flashdata('err_message', '- Admin Has No Rights To Close Day!');
			redirect(SURL . 'Day_closing');
			exit();
	  }
       
		$date=$this->input->post('from_date_1');
		//echo $date;exit();
		$data =$this->db->query("select * from tblmaterial_coding inner join tbl_issue_goods_detail on tblmaterial_coding.materialcode = tbl_issue_goods_detail.itemid where tblmaterial_coding.catcode='1' and Posted_Date='$date' and sale_point_id='$sale_point_id' and tbl_issue_goods_detail.type ='Empty'")->result_array();



		
		//pm($data);exit();
		foreach ($data as $key => $value) {


						$stocktaken = $value['stocktaken_empty'];
						$con['conditions']=array(
											"srno"=> $value['srno'],
										);

$qty = $value['qty'];
$id = $value['itemid'];


	$p_q = $this->db->query("select * from tbl_goodsreceiving_detail where itemid='$id' and batch_status='open' and type='Empty' and sale_point_id='$sale_point_id' ORDER BY recvd_date asc  LIMIT 1")->row_array();


				$s_qan =$qty ;
	$pur_qty =$this->db->query("select sum(Batch_stock) as qan from  tbl_goodsreceiving_detail where itemid='$id' and batch_status='open' and sale_point_id='$sale_point_id' and type='Empty'")->row_array()['qan'];

//echo $s_qan;exit();
//echo $pur_qty;exit();
 if ($s_qan>$pur_qty) {
			
		 	$this->session->set_flashdata('error', 'Some thing is Wrong In Stock!');
		 	$itemname =$this->db->query("select itemname from  tblmaterial_coding where materialcode='$id'")->row_array()['itemname'];
		 	//echo $s_qan;
	             echo "Stock Is Not Avaiable for this item no ".$itemname." Plz Enter Purchase for this Item ";
	             $date=$this->input->post('from_date_1');
	             $this->unclose_again($date);
	             exit();
		 }



			
				if($p_q['Batch_stock']>$s_qan){

						$Batch_stock_left = $p_q['Batch_stock']-$s_qan;
						$stocktaken = $s_qan;
						$purchase_batch_no = $p_q['receipt_id'];
						$id = $p_q['receipt_id'];
						$this->db->query("update tbl_goodsreceiving_detail set Batch_stock='$Batch_stock_left' where receipt_id='".$p_q['receipt_id']."'");

						$totalpurchasedamt = $s_qan*$p_q['rate'];
						
					

					}else if($p_q['Batch_stock']==$s_qan){
						$purchase_batch_no = $p_q['receipt_id'];
						$this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

						$stocktaken = $s_qan;

						$totalpurchasedamt = $s_qan*$p_q['rate'];

					}else{
						$halfamt=0;
						$sale_Qty_left = $s_qan-$p_q['Batch_stock'];
						$this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

						$halfamt = $p_q['Batch_stock']*$p_q['rate'];
						$purchase_batch_no = $p_q['receipt_id'];

						$stocktaken = $p_q['Batch_stock'];

						$loop=2;
						$end = 0;
						while(1<$loop){
								

							$p_q = $this->db->query("select * from tbl_goodsreceiving_detail where batch_status='open' and itemid='$id' and sale_point_id='$sale_point_id' and type='Empty' order by recvd_date asc limit 1")->result_array()[0];

							if($sale_Qty_left>$p_q['Batch_stock']){

								$sale_Qty_left = $sale_Qty_left - $p_q['Batch_stock'];

								$stocktaken = $stocktaken.",".$p_q['Batch_stock'];

								$this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

								$halfamt = $halfamt + ($p_q['Batch_stock']*$p_q['rate']);
								$purchase_batch_no = $purchase_batch_no.",".$p_q['receipt_id'];

							}else if($sale_Qty_left==$p_q['Batch_stock']){

								$this->db->query("update tbl_goodsreceiving_detail set batch_status='complete',Batch_stock='0' where receipt_id='".$p_q['receipt_id']."'");

								$halfamt = $halfamt + ($p_q['Batch_stock']*$p_q['rate']);
								$loop=0;
								$purchase_batch_no = $purchase_batch_no.",".$p_q['receipt_id'];

								$stocktaken = $stocktaken.",".$p_q['Batch_stock'];

							}else{
								
								$Batch_stock_left = $p_q['Batch_stock'] - $sale_Qty_left;
									$this->db->query("update tbl_goodsreceiving_detail set batch_status='open',Batch_stock='$Batch_stock_left' where receipt_id='".$p_q['receipt_id']."'");

								$halfamt = $halfamt + ($sale_Qty_left*$p_q['rate']);
								
								$loop=0;

								$purchase_batch_no = $purchase_batch_no.",".$p_q['receipt_id'];

								$stocktaken = $stocktaken.",".$sale_Qty_left;
							}
							if ($end==60) {
								$this->session->set_flashdata('error', 'Some Thing wrong happend!');
								redirect(SURL . 'Day_closing/');
								exit();
							}
							$end=$end+1;
}
$totalpurchasedamt = $halfamt;
}

//pm($totalpurchasedamt);
$this->db->query("update tbl_issue_goods_detail set purchase_amt_empty='$totalpurchasedamt',purchase_batch_no_empty='$purchase_batch_no',stocktaken_empty='$stocktaken' where srno='".$value['srno']."'");



		}

$date=$this->input->post('from_date_1');
		$data =$this->db->query("select * from tbl_issue_goods where issuedate='$date' and sale_point_id='$sale_point_id'")->result_array();
		//pm($data);
		
		foreach ($data as $key => $value)
		 {
			$purchase_amt=0;
			$id = $value['issuenos'];

		$dataa =$this->db->query("select * from tbl_issue_goods_detail where ig_detail_id='$id' and sale_point_id='$sale_point_id' and type='Empty'")->result_array();
		foreach ($dataa as $key => $valuee) 
		{
			$purchase_amt =$purchase_amt +$valuee['purchase_amt_empty'];

		}
 $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
 $cost_code_cylinder=$fix_code['cost_of_goods_cylinder_code'];
 $sale_cylinder_code=$fix_code['empty_stock_code'];
				$vno = $id."-SV-CV";
				$nar = "Cost of good sold for empty cylinder sale = ".$id;
				 $array = array(
	    				"vno"=>$vno,
	    				"vtype"=>"CV",
	    				"damount"=>$purchase_amt,
	    				"camount"=>$purchase_amt,
	    				"svtype"=>"",
	    				"sale_point_id" =>$sale_point_id,
	    				"created_date"=>$date
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_master",$array);
	
	    
	    $array = array(
	    				"vno"=>$vno,
	    				"acode"=>$sale_cylinder_code,//stock code will come here
	    				"damount"=>"0",
	    				"camount"=>$purchase_amt,
	    				"remarks"=>$nar,
	    				"vtype"=>"CV",
	    				"svtype"=>"",
	    				"vdate"=>$date,
	    				"sale_point_id" =>$sale_point_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);

	    $array = array(
	    				"vno"=>$vno,
	    				"acode"=>$cost_code_cylinder,//cost of goods cylinder code will come here
	    				"damount"=>$purchase_amt,
	    				"camount"=>"0",
	    				"remarks"=>$nar,
	    				"vtype"=>"CV",
	    				"svtype"=>"",
	    				"vdate"=>$date,
	    				"sale_point_id" =>$sale_point_id,
	    			  );	

	    $this->mod_common->insert_into_table("tbltrans_detail",$array);





		}



		$this->session->set_flashdata('ok_message', 'Successfully close');
		redirect(SURL . 'Day_closing');	
	}


public function unclose_again($date)
	{
	$login_user=$this->session->userdata('id');
    $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
//echo $date;exit;
			
			$last_date=$date;
//echo $last_date;exit;


		$data =$this->db->query("select * from tbl_issue_goods_detail where Posted_Date='$last_date' and sale_point_id='$sale_point_id'")->result_array();

	
			foreach ($data as $key => $value) {
				$purchase_batch_no = $value['purchase_batch_no'];
				$stocktaken = $value['stocktaken'];
				$e= explode(",",$purchase_batch_no);

				$ss= explode(",",$stocktaken);

				foreach ($e as $key => $val) {

					
				$qty = $ss[$key];
			

			$this->db->query("update tbl_goodsreceiving_detail SET Batch_stock = Batch_stock + '$qty', batch_status='open' WHERE receipt_id = '$val' and sale_point_id='$sale_point_id'");

				}
			

}
			$this->db->query("update tbl_issue_goods_detail set purchase_batch_no='',stocktaken='',purchase_amt='' where Posted_Date='".$last_date."' and sale_point_id='$sale_point_id'");
			$this->db->query("delete from tbl_posting_stock where post_date='$last_date' and sale_point_id='$sale_point_id'");
				$this->db->query("delete from tbltrans_master where created_date='$last_date' and vtype='CV' and sale_point_id='$sale_point_id'");
			$this->db->query("delete from tbltrans_detail where vdate='$last_date' and vtype='CV' and sale_point_id='$sale_point_id'");
		
		 	


		
		
	}
	public function unclose_again_empty($date)
	{
	$login_user=$this->session->userdata('id');
    $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
//echo $date;exit;
			
			$last_date=$date;
//echo $last_date;exit;


		$data =$this->db->query("select * from tbl_issue_goods_detail where Posted_Date='$last_date' and type='sale' and sale_point_id='$sale_point_id'")->result_array();

	
// 			foreach ($data as $key => $value) {
// 				$purchase_batch_no_empty = $value['purchase_batch_no_empty'];
// 				$stocktaken_empty = $value['stocktaken_empty'];
// 				$e= explode(",",$purchase_batch_no_empty);

// 				$ss= explode(",",$stocktaken_empty);

// 				foreach ($e as $key => $value) {

					
// 				$qty = $ss[$key];
			

// 			$this->db->query("update tbl_goodsreceiving_detail SET Batch_stock = Batch_stock + '$qty', batch_status='open' WHERE receipt_id = '$value' and type='Filled' and sale_point_id='$sale_point_id'");

// 				}
			

// }
			$this->db->query("update tbl_issue_goods_detail set purchase_batch_no_empty='',stocktaken_empty='',purchase_amt_empty='' where Posted_Date='".$last_date."' and sale_point_id='$sale_point_id'");
			$this->db->query("delete from tbl_posting_stock where post_date='$last_date' and sale_point_id='$sale_point_id'");
				$this->db->query("delete from tbltrans_master where created_date='$last_date' and vtype='CV' and sale_point_id='$sale_point_id'");
			$this->db->query("delete from tbltrans_detail where vdate='$last_date' and vtype='CV' and sale_point_id='$sale_point_id'");
		
		 	


		
		
	}

}
