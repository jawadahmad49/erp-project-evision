<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchasereturn extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_vendor","mod_common","mod_purchasereturn","mod_stockreport","mod_salelpg"
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
		
		$data['purchasereturn_list'] = $this->mod_purchasereturn->manage_purchasereturn($from_date,$to_date);
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Purchase Return";
		$this->load->view($this->session->userdata('language')."/purchase_return/purchase_return",$data);
	}

	public function add_purchase_return()
	{
		$data['vendor_list'] = $this->mod_vendor->getOnlyVendors_only();
		$table='tbl_company';       
		$data['pricing_centralized'] = $this->mod_common->get_all_records($table,"*");
		//echo "<pre>";print_r($data['vendor_list']);exit;
		$table='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		$this->load->view($this->session->userdata('language')."/purchase_return/add_purchase_return",$data);
	}
	public function add(){

		if($this->input->server('REQUEST_METHOD') == 'POST'){

			$sale_date=$this->input->post('date');
			$date_array = array('post_date' => $sale_date);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'Purchasereturn/add_purchase_return');
			}
			
			//echo "<pre>";print_r($this->input->post());exit;
			$add=  $this->mod_purchasereturn->add_purchase_return($this->input->post());
            //echo "<pre>";print_r($add);exit;
             $same_page = $this->db->query("select same_page from tbl_company")->row_array()['same_page'];
			if($add and $same_page=='true') {
		            $this->session->set_flashdata('ok_message', 'Added Successfully!');
		            redirect(SURL . 'purchasereturn/add_purchase_return');
		        } else if ($add) {
		            $this->session->set_flashdata('ok_message', '- Added Successfully!');
		            redirect(SURL . 'purchasereturn/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in adding please try again!');
		            redirect(SURL . 'purchasereturn/');
		        }
		}
		//$this->add_direct_girn();
	}

	public function delete($id) {

	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////
			//$sale_date=$this->input->post('date');
			$date_array = array('irnos' => $id);
			$get_rec_date =  $this->mod_common->select_single_records('tbl_issue_return',$date_array);

			//$sale_date=$this->input->post('date');
			$date_array = array('post_date>=' => $get_rec_date['irdate']);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'purchasereturn/');
			}
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
 
 
 
		$saleid=$id."-Return";
		$receiveid=$id."-Receive";

		#-------------delete record--------------#
        $table = "tbl_issue_return";
        $where = "irnos = '" . $id . "'";
        $delete = $this->mod_common->delete_record($table, $where);

        $tables = "tbl_issue_return_detail";
        $wheres = "irnos = '".$id."'";
        $deletes = $this->mod_common->delete_record($tables, $wheres);

        $tablems = "tbltrans_master";
        $wherems = "vno = '".$saleid."'";
        $deletems = $this->mod_common->delete_record($tablems, $wherems);

        $tableds = "tbltrans_detail";
        $whereds = "vno = '".$saleid."'";
        $deleteds = $this->mod_common->delete_record($tableds, $whereds);

        $tablemr = "tbltrans_master";
        $wheremr = "vno = '".$receiveid."'";
        $deletemr = $this->mod_common->delete_record($tablemr, $wheremr);

        $tabledr = "tbltrans_detail";
        $wheredr = "vno = '".$receiveid."'";
        $deletedr = $this->mod_common->delete_record($tabledr, $wheredr);

        if ($delete) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'purchasereturn/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'purchasereturn/');
        }
    }
	public function edit($id){
		if($id){
			
				/////////////////////////////////////////////////////////////////////////////////////////////////////////
			//$sale_date=$this->input->post('date');
			$date_array = array('irnos' => $id);
			$get_rec_date =  $this->mod_common->select_single_records('tbl_issue_return',$date_array);

			//$sale_date=$this->input->post('date');
			$date_array = array('post_date>=' => $get_rec_date['irdate']);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'purchasereturn/');
			}
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
 
 
		$data['vendor_list'] = $this->mod_vendor->getOnlyVendors_only();
		$tablem='tblmaterial_coding';       
        
        $where_cat_id = array('catcode' => 1);
        $data['item_list']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_cat_id);

		$table='tbl_issue_return';
		$where = "irnos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);
//echo '<pre>';print_r($data['single_edit']);exit;
		$data['edit_list'] = $this->mod_purchasereturn->edit_purchasereturn($id);
		//echo '<pre>';print_r($data['edit_list']);exit;
	
		foreach ($data['edit_list'] as $key => $value) {
			$data['emptystock'][]=  $this->mod_salelpg->get_details($value['itemid'],$data['single_edit']['irdate']);
		}
		//echo '<pre>';print_r($data['item_list']);exit;
		//pm($data['filledstock']);

		$data["filter"] = '';
		$table='tbl_company';       
		$data['pricing_centralized'] = $this->mod_common->get_all_records($table,"*");
		#----load view----------#
		$data["title"] = "Update Purchase Return";
		$this->load->view($this->session->userdata('language')."/purchase_return/edit",$data);
		}
	}

	public function makenew($id){
		if($id){
		$data['vendor_list'] = $this->mod_vendor->getOnlyVendors();
		$table='tblmaterial_coding';
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		$table='tbl_orderbooking';
		$where = "id='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);

		$data['edit_list'] = $this->mod_purchasereturn->edit_makeneworder($id);
		//echo '<pre>';print_r($data['edit_list']);exit;
		$data["filter"] = '';
		$data["id"] = $id;
		#----load view----------#
		$data["title"] = "Update Purchase Return";
		$this->load->view($this->session->userdata('language')."/purchase_return/add_purchase_return",$data);
		}
	}

	public function update(){
		if($this->input->server('REQUEST_METHOD') == 'POST'){

			$sale_date=$this->input->post('date');
			
			$date_array = array('post_date' => $sale_date);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'Purchasereturn');
			}




			$add_purchasereturn=  $this->mod_purchasereturn->update_purchase_return($this->input->post());
            //echo "<pre>";print_r($add_purchasereturn);exit;
		        if ($add_purchasereturn || $add_purchasereturn==0) {
		            $this->session->set_flashdata('ok_message', '- Updated Successfully!');
		            redirect(SURL . 'purchasereturn/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in updating please try again!');
		            redirect(SURL . 'purchasereturn/');
		        }
		}
		//$this->add_direct_girn();
	}

	function record_delete()
	{
		$parentid=	$this->input->post('parentid');

		$this->db->where('irnos',$parentid);
		$count = $this->db->count_all_results('tbl_issue_return_detail');
		
		
        $saleid=$parentid."-Return";
		$receiveid=$parentid."-Receive";

 		$tablems = "tbltrans_master";
        $wherems = "vno = '".$saleid."'";
        $deletems = $this->mod_common->delete_record($tablems, $wherems);

        $tableds = "tbltrans_detail";
        $whereds = "vno = '".$saleid."'";
        $deleteds = $this->mod_common->delete_record($tableds, $whereds);

        $tablemr = "tbltrans_master";
        $wheremr = "vno = '".$receiveid."'";
        $deletemr = $this->mod_common->delete_record($tablemr, $wheremr);

        $tabledr = "tbltrans_detail";
        $wheredr = "vno = '".$receiveid."'";
        $deletedr = $this->mod_common->delete_record($tabledr, $wheredr);

        if($count <= 1){
	       	$this->db->where(array("irnos"=>$parentid));
	       	$delete_goods = $this->db->delete("tbl_issue_return");
	    }

        $table = "tbl_issue_return_detail";
        $deleteid=	$this->input->post('deleteid');
        $where = "sr_no = '" . $deleteid . "'";
        $delete_goods = $this->mod_common->delete_record($table, $where);

        $repost = $this->mod_purchasereturn->repost_return($parentid);


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
		$data['vendor_list'] = $this->mod_vendor->getOnlyVendors();
		$table='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		$table='tbl_issue_return';
		$where = "irnos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);

		$data['edit_list'] = $this->mod_purchasereturn->edit_purchasereturn($id);
		//echo '<pre>';print_r($data);
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
		//exit;
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Invoice";
		$this->load->view($this->session->userdata('language')."/purchase_return/single",$data);
		}
	}

	function get_filledstock()
	{
		$data['report']=  $this->mod_purchasereturn->get_details($this->input->post());
		//pm($data['report']);
		foreach ($data['report'] as $key => $value) {
			//pm($value);
		 	//echo $value['filled'];
		 	//print $value;
		 	echo json_encode($value);
		}
		
	}
	function get_filledstockdate()
	{
		$data['report']=  $this->mod_purchasereturn->get_details($this->input->post());
		//pm($data['report']);
		foreach ($data['report'] as $key => $value) {
			//pm($value);
		 	echo $value['empty'];
		}
		
	}
}
