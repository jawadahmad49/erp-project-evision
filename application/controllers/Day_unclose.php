<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Day_unclose extends CI_Controller {

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

		$this->load->view($this->session->userdata('language')."/day_unclose",$data);
		}
	
public function unclose()
	{
		
 $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
         if ($sale_point_id=='0') {
	  	$this->session->set_flashdata('err_message', '- Admin Has No Rights To Unclose Day!');
			redirect(SURL . 'Day_unclose');
			exit();
	  }
		if($this->input->post('from_date_1'))
		{
		$date = $this->input->post('from_date_1');	
			
			$last_date= date('Y-m-d', strtotime('-1 day', strtotime($date)));
//echo $last_date;exit;


		$data =$this->db->query("select * from tbl_issue_goods_detail where Posted_Date='$last_date' and sale_point_id='$sale_point_id'")->result_array();

	
			foreach ($data as $key => $value) {

				$purchase_batch_no = $value['purchase_batch_no'];
				$stocktaken = $value['stocktaken'];
				$e= explode(",",$purchase_batch_no);

				$ss= explode(",",$stocktaken);

				foreach ($e as $key => $val) {

					
				$qty = $ss[$key];
			

			$this->db->query("update tbl_goodsreceiving_detail SET Batch_stock =Batch_stock + '$qty', batch_status='open' WHERE receipt_id = '$val' and sale_point_id='$sale_point_id'");
 
				}
			

}
			$this->db->query("update tbl_issue_goods_detail set purchase_batch_no='',stocktaken='',purchase_amt='' where Posted_Date='".$last_date."' and sale_point_id='$sale_point_id'");

// 			$query=$this->db->query("SELECT * FROM `tbl_issue_goods_detail` where Posted_Date>'$last_date' and purchase_batch_no>0 and sale_point_id='$sale_point_id'")->result_array();
// if (!empty($query)) {

// 			 foreach ($query as $key => $val) {
// 				$purchase_batch_no = $val['purchase_batch_no'];
// 				$stocktaken = $val['stocktaken'];
// 				$e= explode(",",$purchase_batch_no);

// 				$ss= explode(",",$stocktaken);

// 				foreach ($e as $key => $dataa) {

					
// 				$qty = $ss[$key];
			

// 			$this->db->query("update tbl_goodsreceiving_detail SET Batch_stock =Batch_stock + '$qty', batch_status='open' WHERE receipt_id = '$dataa' and sale_point_id='$sale_point_id'");
			

// 				}
// 				}
// 			$this->db->query("update tbl_issue_goods_detail set purchase_batch_no='',stocktaken='',purchase_amt='' where Posted_Date>'".$last_date."' and sale_point_id='$sale_point_id'");
// 		}
		 
		
		 	

   $this->unclose_empty();

		}
		
	}
	public function unclose_empty()
	{
		
        $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
      
		if($this->input->post('from_date_1'))
		{
		$date = $this->input->post('from_date_1');	
			
		$last_date= date('Y-m-d', strtotime('-1 day', strtotime($date)));
//echo $last_date;exit;


		$data =$this->db->query("select * from tbl_issue_goods_detail where Posted_Date='$last_date' and sale_point_id='$sale_point_id' and type='sale' ")->result_array();

	
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
			$this->db->query("update tbl_issue_goods_detail set purchase_batch_no_empty='',stocktaken_empty='',purchase_amt_empty='' where Posted_Date='".$last_date."' and sale_point_id='$sale_point_id' and type in('sale','security','wo_sec')");
			// $this->db->query("delete from tbl_posting_stock where post_date='$last_date' and sale_point_id='$sale_point_id'");
			// 	$this->db->query("delete from tbltrans_master where created_date='$last_date' and vtype='CV' and sale_point_id='$sale_point_id'");
			// $this->db->query("delete from tbltrans_detail where vdate='$last_date' and vtype='CV' and sale_point_id='$sale_point_id'");
		
$this->unclose_appliances();


		}
		
	}
		public function unclose_appliances()
	{
		
        $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
      
		if($this->input->post('from_date_1'))
		{
		$date = $this->input->post('from_date_1');	
			
			$last_date= date('Y-m-d', strtotime('-1 day', strtotime($date)));
//echo $last_date;exit;


		$data =$this->db->query("select * from tbl_issue_goods_detail where Posted_Date='$last_date' and sale_point_id='$sale_point_id'")->result_array();

	
			foreach ($data as $key => $value) {
				$purchase_batch_no = $value['purchase_batch_no_other'];
				$stocktaken = $value['stocktaken_other'];
				$e= explode(",",$purchase_batch_no);

				$ss= explode(",",$stocktaken);

				foreach ($e as $key => $data) {

					
				$qty = $ss[$key];
			

			$this->db->query("update tbl_goodsreceiving_detail SET Batch_stock =Batch_stock + '$qty', batch_status='open' WHERE receipt_id = '$data' and sale_point_id='$sale_point_id'");

				}
			

}
			$this->db->query("update tbl_issue_goods_detail set purchase_batch_no_other='',stocktaken_other='',purchase_amt_other='' where Posted_Date='".$last_date."' and sale_point_id='$sale_point_id'");

			// $this->db->query("delete from tbl_posting_stock where post_date='$last_date' and sale_point_id='$sale_point_id'");

			// 	$this->db->query("delete from tbltrans_master where created_date='$last_date' and vtype='CV' and sale_point_id='$sale_point_id'");
				
			// $this->db->query("delete from tbltrans_detail where vdate='$last_date' and vtype='CV' and sale_point_id='$sale_point_id'");
		
	$this->unclose_just_Empty();	 	

   //     $this->session->set_flashdata('ok_message', 'Successfully unclose');
		 // redirect(SURL . 'Day_closing');

		}
		 // $this->session->set_flashdata('ok_message', 'Some thing is wrong');
		 // redirect(SURL . 'Day_closing');
	}
	public function unclose_just_Empty()
	{
		
 $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
         if ($sale_point_id=='0') {
	  	$this->session->set_flashdata('err_message', '- Admin Has No Rights To Unclose Day!');
			redirect(SURL . 'Day_unclose');
			exit();
	  }
		if($this->input->post('from_date_1'))
		{
		$date = $this->input->post('from_date_1');	
			
			$last_date= date('Y-m-d', strtotime('-1 day', strtotime($date)));
//echo $last_date;exit;


		$data =$this->db->query("select * from tbl_issue_goods_detail where Posted_Date='$last_date' and sale_point_id='$sale_point_id' and type='Empty'")->result_array();

	
			foreach ($data as $key => $value) {
				$purchase_batch_no = $value['purchase_batch_no_empty'];
				$stocktaken = $value['stocktaken_empty'];
				$e= explode(",",$purchase_batch_no);

				$ss= explode(",",$stocktaken);

				foreach ($e as $key => $data) {

					
				$qty = $ss[$key];
			

			$this->db->query("update tbl_goodsreceiving_detail SET Batch_stock =Batch_stock + '$qty', batch_status='open' WHERE receipt_id = '$data' and sale_point_id='$sale_point_id'");

				}
			

}
			$this->db->query("update tbl_issue_goods_detail set purchase_batch_no_empty='',stocktaken_empty='',purchase_amt_empty='' where Posted_Date='".$last_date."' and sale_point_id='$sale_point_id'");
			$this->db->query("delete from tbl_posting_stock where post_date='$last_date' and sale_point_id='$sale_point_id'");
			$this->db->query("delete from tbltrans_master where created_date='$last_date' and vtype='CV' and sale_point_id='$sale_point_id'");
			$this->db->query("delete from tbltrans_detail where vdate='$last_date' and vtype='CV' and sale_point_id='$sale_point_id'");
		
		 	

   // $this->unclose_empty();
			$this->session->set_flashdata('ok_message', 'Successfully unclose');
		 redirect(SURL . 'Day_unclose');

		}
		$this->session->set_flashdata('ok_message', 'Some thing is wrong');
		 redirect(SURL . 'Day_unclose');
		
	}
 
 

public function unclose_again($date)
	{
	$login_user=$this->session->userdata('id');
    $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
//echo $date;exit;
			
			$last_date=$date;
//echo $last_date;exit;


		$data =$this->db->query("select * from tbl_issue_goods_detail where Posted_Date='$last_date' and sale_point_id='$sale_point_id'")->result_array();

	
// 			foreach ($data as $key => $value) {
// 				$purchase_batch_no = $value['purchase_batch_no'];
// 				$stocktaken = $value['stocktaken'];
// 				$e= explode(",",$purchase_batch_no);

// 				$ss= explode(",",$stocktaken);

// 				foreach ($e as $key => $value) {

					
// 				$qty = $ss[$key];
			

// 			$this->db->query("update tbl_goodsreceiving_detail SET Batch_stock = Batch_stock + '$qty', batch_status='open' WHERE receipt_id = '$value' and sale_point_id='$sale_point_id'");

// 				}
			

// }
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
