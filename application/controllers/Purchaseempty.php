<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchaseempty extends CI_Controller {

	
	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_vendor","mod_common","mod_purchaseempty","mod_salelpg","mod_bank","mod_admin","mod_vendorledger"
        ));
        
    }
    
	public function index()
	{
		if(isset($_POST['submit'])){			
			$from_date = date("Y-m-d", strtotime($_POST['from']));
			
			$to_date = date("Y-m-d", strtotime($_POST['to']));
			
		}else{
			$from_date = date('Y-m-d', strtotime('-15 day'));
			$to_date = date('Y-m-d');
		}
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$data['purchaseempty_list'] = $this->mod_purchaseempty->manage_purchaseempty($from_date,$to_date,$sale_point_id);
		

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Purchase Empty";
		$this->load->view($this->session->userdata('language')."/purchase_empty/purchase_empty",$data);
	}

	public function add_purchase_empty()
	{        
		 $login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '101' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Purchaseempty/index/');
			}
		    $login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
                 if ($sale_point_id=='0') {
	  	          $this->session->set_flashdata('err_message', '- Admin Has No Rights To Add Purchase!');
			       redirect(SURL . 'Purchaseempty');
			       exit(); }
            $general = $this->db->query("select vendor_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['vendor_code'];
            $bank = $this->db->query("select bank_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['bank_code'];
            //$data['vendor_list'] = $this->mod_vendor->getOnlyVendors_only();
            $data['vendor_list'] =$this->db->query("select * from tblacode where general='$general'")->result_array();
            $data['bank_list'] =$this->db->query("select * from tblacode where general='$bank'")->result_array();

		$data['cash_position'] = $this->mod_admin->cash_position();
		foreach ($data['cash_position'] as $key=>$datas) {
		$opening= $datas[opngbl]; 
		if($datas[optype]=='Credit'){ $opening=-1*$opening; } 
		$bal=$datas[damount]-$datas[camount]+$opening;
		}
		$data['cash_balance']=$bal;


        $where_cat_id = array('catcode' => 1);

        $data['item_list']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_cat_id);
        //$data['bank_list'] = $this->mod_bank->getOnlyBanks();

                
		$this->load->view($this->session->userdata('language')."/purchase_empty/add_purchase_empty",$data);
	}

	public function add(){

		if($this->input->server('REQUEST_METHOD') == 'POST'){
         
			
			$login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
            $sale_date=$this->input->post('date');
			//$date_array = array('post_date>=' => $sale_date);
			$date_array = array('sale_point_id =' => $sale_point_id,'post_date>=' => $sale_date);
			//pm($date_array);exit();
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);
			if(!empty($last_date))
			{
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'Purchaseempty/add_purchase_empty');
			}
			
			$add_purchaseempty=  $this->mod_purchaseempty->add_purchase_empty($this->input->post());
			 $same_page = $this->db->query("select same_page from tbl_company")->row_array()['same_page'];
			if($add_purchaseempty and $same_page=='true') {
		            $this->session->set_flashdata('ok_message', 'Added Successfully!');
		            redirect(SURL . 'Purchaseempty/');
		        } else if ($add_purchaseempty || $add_purchaseempty==0) {
		            $this->session->set_flashdata('ok_message', 'Added Successfully!');
		            redirect(SURL . 'Purchaseempty/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in adding please try again!');
		            redirect(SURL . 'Purchaseempty/');
		        }
		}
	}

		public function delete($id) {
			
			 $login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '101' limit 1")->row_array();
		if ($role['delete']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Purchaseempty/index/');
			}
			$date_array = array('trans_id' => $id);
			$get_rec_date =  $this->mod_common->select_single_records('tbl_goodsreceiving',$date_array);
			$login_user=$this->session->userdata('id');
			$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

			$date_array = array('post_date>=' => $get_rec_date['receiptdate'],'sale_point_id=' => $sale_point_id);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'Purchaseempty/');
			}
 
		$purchaseid=$sale_point_id."-Purchase-".$id;
		#-------------delete record--------------#
        $table = "tbl_goodsreceiving";
        $where = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";
        $delete_goods = $this->mod_common->delete_record($table, $where);

        $tables = "tbl_goodsreceiving_detail";
        $wheres = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";
        $deletes = $this->mod_common->delete_record($tables, $wheres);

        $tablems = "tbltrans_master";
        $wherems = "vno = '".$purchaseid."'";
        $deletems = $this->mod_common->delete_record($tablems, $wherems);

        $tableds = "tbltrans_detail";
        $whereds = "vno = '".$purchaseid."'";
        $deleteds = $this->mod_common->delete_record($tableds, $whereds);

		

        if ($delete_goods) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Purchaseempty/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Purchaseempty/');
        }
    }

	public function edit($id){
		 $login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '101' limit 1")->row_array();
		if ($role['edit']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Purchaseempty/index/');
			}
		if($id){
			$login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			$date_array = array('receiptnos' => $id);
			$get_rec_date =  $this->mod_common->select_single_records('tbl_goodsreceiving',$date_array);
			$date_array = array('post_date>=' => $get_rec_date['receiptdate'],'sale_point_id =' => $sale_point_id);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'Purchaseempty/');
			}
			
		//$data['vendor_list'] = $this->db->query("select * from tblacode where  atype='child'")->result_array();
			$login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
            $general = $this->db->query("select vendor_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['vendor_code'];
            $bank = $this->db->query("select bank_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['bank_code'];
            //$data['vendor_list'] = $this->mod_vendor->getOnlyVendors_only();
            $data['vendor_list'] =$this->db->query("select * from tblacode where general='$general'")->result_array();
            $data['bank_list'] =$this->db->query("select * from tblacode where general='$bank'")->result_array();

        $where_cat_id = array('catcode' => 1);

        $data['item_list']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_cat_id);

		$table='tbl_goodsreceiving';
		$where = "receiptnos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);
		
			if($data['single_edit']['pay_mode']=='cash'){
				$data['cash_position'] = $this->mod_admin->cash_position();
				foreach ($data['cash_position'] as $key=>$datas) {
				$opening= $datas[opngbl]; 
				if($datas[optype]=='Credit'){ $opening=-1*$opening; } 
				$bal=$datas[damount]-$datas[camount]+$opening;
			  $data['single_edit']['Cash_Balance']=$bal+$data['single_edit']['total_paid'];
			}
		}
			
			 
			if($data['single_edit']['pay_mode']=='bank'){
				//bank_code
					$date=  $data['single_edit']['receiptdate'];				 
				  	$data['bank_position']=  $this->mod_vendorledger->get_report($date,$data['single_edit']['bank_code']);					
					  $bal=str_replace(",", "", $data['bank_position'][0]['tbalance']); 
				  $data['single_edit']['Bank_Balance']=$bal+$data['single_edit']['total_paid'];
			}
		
		
		$data['edit_list'] = $this->mod_purchaseempty->edit_purchaseempty($id);
		foreach ($data['edit_list'] as $key => $value) {
			$data['emptystock'][]=  $this->mod_salelpg->get_details($value['itemid'],$data['single_edit']['receiptdate']);
		}
		$trans_id=$this->db->query("select trans_id from tbl_goodsreceiving where receiptnos='$id'")->row_array()['trans_id'];
		$id_tars=$sale_point_id."-Purchase-".$trans_id;
		//$id_tars=$id."-Purchase";
		$where_cat_id = array('vno' => $id_tars);
        $data['trans_data']= $this->mod_purchaseempty->select_single_trans($id_tars);
		//$id_tars=$id."-Purchase";
		$id_tars=$sale_point_id."-Purchase-".$trans_id;

        $data['trans_detail']= $this->mod_purchaseempty->select_single_trans_detail($id_tars);

        $data['trans_bank']= $this->mod_purchaseempty->select_single_trans_bank($where_cat_id);


        $data['bank_flag']='no';
        if(!empty($data['trans_bank'])) 
        {
        	$data['bank_flag']='yes';
        }
        //$data['bank_list'] = $this->mod_bank->getOnlyBanks();

		$data["filter"] = '';
		$data["edit_id"] = $id;
		#----load view----------#
		$data["title"] = "Update Purchase Empty";
		$this->load->view($this->session->userdata('language')."/purchase_empty/edit",$data);
		}
	}
	function trans_delete()
	{
		
		#-------------delete record--------------#
		$parentid=	$this->input->post('deleteid');
        $purchaseid=$parentid."-Purchase";

        $tablems = "tbltrans_master";
        $wherems = "vno = '".$purchaseid."'";
        $deletems = $this->mod_common->delete_record($tablems, $wherems);

        $tableds = "tbltrans_detail";
        $whereds = "vno = '".$purchaseid."'";
        $deleteds = $this->mod_common->delete_record($tableds, $whereds);

        if ($delete_goods) {
            echo '1';
		 	exit;
		 }
		 else {
		 	echo '0';
		 	exit;
		 }
	}
	
	public function update(){
		if($this->input->server('REQUEST_METHOD') == 'POST'){


			
			$login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
            $sale_date=$this->input->post('date');

			$date_array = array('post_date>=' => $sale_date,'sale_point_id =' => $sale_point_id);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'Purchaseempty');
			}
			
			$add_purchaseempty=  $this->mod_purchaseempty->update_purchase_empty($this->input->post());
            //echo "<pre>";print_r($add_purchaseempty);exit;
		        if ($add_purchaseempty || $add_purchaseempty==0) {
		            $this->session->set_flashdata('ok_message', '- Updated Successfully!');
		            redirect(SURL . 'Purchaseempty/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in updating please try again!');
		            redirect(SURL . 'Purchaseempty/');
		        }
		}
		//$this->add_purchase_empty();
	}

	// function record_delete()
	// {
	// 	$login_user=$this->session->userdata('id');
 //        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
	// 	$parentid=	$this->input->post('parentid');
	// 	$purchaseid=$sale_point_id."-Purchase-".$parentid;
		

	// 	 $this->db->where('receipt_detail_id',$parentid,'sale_point_id',$sale_point_id);
	//      $count = $this->db->count_all_results('tbl_goodsreceiving_detail');



 //        $tablems = "tbltrans_master";
 //        $wherems = "vno = '".$purchaseid."'";
 //        $deletems = $this->mod_common->delete_record($tablems, $wherems);

 //        $tableds = "tbltrans_detail";
 //        $whereds = "vno = '".$purchaseid."'";
 //        $deleteds = $this->mod_common->delete_record($tableds, $whereds);

 //        if($count <= 1){
	//        	$this->db->where(array("trans_id"=>$parentid,"sale_point_id"=>$sale_point_id));
	//        	$delete_goods = $this->db->delete("tbl_goodsreceiving");
	//     }

 //        $table = "tbl_goodsreceiving_detail";
 //        $deleteid=	$this->input->post('deleteid');
 //        $where = "receipt_id = '" . $deleteid . "'";
 //        $delete_goods = $this->mod_common->delete_record($table, $where);

 //        //$repost = $this->mod_purchaseempty->repost_purchase($parentid);

 //        if ($delete_goods) {
 //            echo '1';
	// 	 	exit;
	// 	 }
	// 	 else {
	// 	 	echo '0';
	// 	 	exit;
	// 	 }
	// }
	function record_delete()
	{   
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$id = $_POST['parentid'];
		$purchaseid=$sale_point_id."-Purchase-".$id;
		$this->db->where('trans_id',$id,'sale_point_id',$sale_point_id);
		$count = $this->db->query("SELECT COUNT(receipt_detail_id) as count FROM tbl_goodsreceiving_detail where trans_id='$id' and sale_point_id='$sale_point_id'")->row_array()['count'];
        $table = "tbl_goodsreceiving_detail";
        $deleteid=	$this->input->post('deleteid');
        $where = "receipt_id = '" . $deleteid . "'";
        $delete_goods = $this->mod_common->delete_record($table, $where);
        if ($count==1) {
        $table = "tbl_goodsreceiving";
        $where = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";
        $delete_goods = $this->mod_common->delete_record($table, $where);
			
        $tablems = "tbltrans_master";
        $wherems = "vno = '".$purchaseid."'";
        $deletems = $this->mod_common->delete_record($tablems, $wherems);

        $tableds = "tbltrans_detail";
        $whereds = "vno = '".$purchaseid."'";
        $deleteds = $this->mod_common->delete_record($tableds, $whereds);
        
        }
 

		
        if ($delete_goods) {
            echo '1';
		 	exit;
		 }
		 else {
		 	echo '0';
		 	exit;
		 }
	}

	public function detail($id){
		if($id){
		$data['vendor_list'] = $this->db->query("select * from tblacode where  atype='child'")->result_array();
		$table='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		$table='tbl_goodsreceiving';
		$where = "receiptnos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);

		$data['edit_list'] = $this->mod_purchaseempty->edit_purchaseempty($id);
		//echo '<pre>';print_r($data);
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Vendor Invoice";
		$this->load->view($this->session->userdata('language')."/purchase_empty/single",$data);
		}
	}
}
