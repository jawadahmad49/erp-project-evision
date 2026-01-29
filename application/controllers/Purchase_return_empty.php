<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_return_empty extends CI_Controller {

	
	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_vendor","mod_common","Mod_purchase_return_empty","mod_salelpg","mod_bank","mod_admin","mod_vendorledger"
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
		$data['return_empty'] = $this->Mod_purchase_return_empty->manage_return_empty($from_date,$to_date,$sale_point_id);
		

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Purchase Empty";
		$this->load->view($this->session->userdata('language')."/Purchase_return_empty/purchase_return_empty",$data);
	}

	public function add_purchase_return_empty()
	{        
		 $login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '101' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Purchase_return_empty/index/');
			}
		    $login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
                 if ($sale_point_id=='0') {
	  	          $this->session->set_flashdata('err_message', '- Admin Has No Rights To Add Purchase!');
			       redirect(SURL . 'Purchase_return_empty');
			       exit(); }
            $general = $this->db->query("select vendor_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['vendor_code'];
            $bank = $this->db->query("select bank_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['bank_code'];
            //$data['vendor_list'] = $this->mod_vendor->getOnlyVendors_only();
            $data['vendor_list'] =$this->db->query("select * from tblacode where general='$general'")->result_array();
            $data['bank_list'] =$this->db->query("select * from tblacode where general='$bank'")->result_array();


        $where_cat_id = array('catcode' => 1);

        $data['item_list']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_cat_id);
        //$data['bank_list'] = $this->mod_bank->getOnlyBanks();

                
		$this->load->view($this->session->userdata('language')."/Purchase_return_empty/add_purchase_return_empty",$data);
	}

	public function add(){

		if($this->input->server('REQUEST_METHOD') == 'POST'){
         
			
			$login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
            $sale_date=$this->input->post('date');
			$date_array = array('sale_point_id =' => $sale_point_id,'post_date>=' => $sale_date);
	
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);
			if(!empty($last_date))
			{
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'Purchase_return_empty/add_purchase_empty');
			}
			
			$add_purchaseempty=  $this->Mod_purchase_return_empty->add_purchase_return_empty($this->input->post());
			 $same_page = $this->db->query("select same_page from tbl_company")->row_array()['same_page'];
			if($add_purchaseempty and $same_page=='true') {
		            $this->session->set_flashdata('ok_message', 'Added Successfully!');
		            redirect(SURL . 'Purchase_return_empty/');
		        } else if ($add_purchaseempty || $add_purchaseempty==0) {
		            $this->session->set_flashdata('ok_message', 'Added Successfully!');
		            redirect(SURL . 'Purchase_return_empty/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in adding please try again!');
		            redirect(SURL . 'Purchase_return_empty/');
		        }
		}
	}

		public function delete($id) {
			
			 $login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '101' limit 1")->row_array();
		if ($role['delete']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Purchase_return_empty/index/');
			}
			$date_array = array('trans_id' => $id);
			$get_rec_date =  $this->mod_common->select_single_records('tbl_issue_goods',$date_array);
			$login_user=$this->session->userdata('id');
			$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

			$date_array = array('post_date>=' => $get_rec_date['issuedate'],'sale_point_id=' => $sale_point_id);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'Purchase_return_empty/');
			}
 
		$purchaseid=$sale_point_id."-PRE-".$id;
		#-------------delete record--------------#
        $table = "tbl_issue_goods";
        $where = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";
        $delete_goods = $this->mod_common->delete_record($table, $where);

        $tables = "tbl_issue_goods_detail";
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
            redirect(SURL . 'Purchase_return_empty/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Purchase_return_empty/');
        }
    }

	public function edit($id){
		 $login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '101' limit 1")->row_array();
		if ($role['edit']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Purchase_return_empty/index/');
			}
		if($id){
			$login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			$date_array = array('issuenos' => $id);
			$get_rec_date =  $this->mod_common->select_single_records('tbl_issue_goods',$date_array);
			$date_array = array('post_date>=' => $get_rec_date['issuedate'],'sale_point_id =' => $sale_point_id);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'Purchase_return_empty/');
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

		$table='tbl_issue_goods';
		$where = "issuenos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);
		$data['edit_list'] = $this->Mod_purchase_return_empty->edit_purchase_return($id);
	
		
        //$data['bank_list'] = $this->mod_bank->getOnlyBanks();

		$data["filter"] = '';
		$data["edit_id"] = $id;
		#----load view----------#
		$data["title"] = "Update Purchase Return Empty";
		$this->load->view($this->session->userdata('language')."/Purchase_return_empty/edit",$data);
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
				redirect(SURL . 'Purchase_return_empty');
			}
			
			$add_purchaseempty=  $this->Mod_purchase_return_empty->update_purchase_empty($this->input->post());
            //echo "<pre>";print_r($add_purchaseempty);exit;
		        if ($add_purchaseempty || $add_purchaseempty==0) {
		            $this->session->set_flashdata('ok_message', '- Updated Successfully!');
		            redirect(SURL . 'Purchase_return_empty/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in updating please try again!');
		            redirect(SURL . 'Purchase_return_empty/');
		        }
		}
		//$this->add_purchase_empty();
	}

	// function record_delete()
	// {
	// 	$login_user=$this->session->userdata('id');
 //        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
	// 	$parentid=	$this->input->post('parentid');
	// 	$purchaseid=$sale_point_id."-PRE-".$parentid;
		

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

 //        //$repost = $this->Mod_purchase_return_empty->repost_purchase($parentid);

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
		$saleid=$sale_point_id."-PRE-".$id;
 
		$this->db->where('trans_id',$id,'sale_point_id',$sale_point_id);
		 
		$count = $this->db->query("SELECT COUNT(ig_detail_id) as count FROM tbl_issue_goods_detail where trans_id='$id' and sale_point_id='$sale_point_id'")->row_array()['count'];
 
        $table = "tbl_issue_goods_detail";
        $deleteid=	$this->input->post('deleteid');
        
        $where = "srno = '" . $deleteid . "'";
        $delete_goods = $this->mod_common->delete_record($table, $where);
       
        if ($count==1) {
        $table = "tbl_issue_goods";
        $where = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";
        $delete_goods = $this->mod_common->delete_record($table, $where);
			
        $tablems = "tbltrans_master";
        $wherems = "vno = '".$saleid."'";
        $deletems = $this->mod_common->delete_record($tablems, $wherems);

        $tableds = "tbltrans_detail";
        $whereds = "vno = '".$saleid."'";
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

		$data['edit_list'] = $this->Mod_purchase_return_empty->edit_purchaseempty($id);
		//echo '<pre>';print_r($data);
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Vendor Invoice";
		$this->load->view($this->session->userdata('language')."/Purchase_return_empty/single",$data);
		}
	}
}
