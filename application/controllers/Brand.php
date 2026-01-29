<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Brand extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_brand","mod_common"
        ));
        
    }

	public function index()
	{
		$table='tbl_brand';
		$data['brand_list'] = $this->mod_common->get_all_records($table,"*");
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Brandes";		

		$this->load->view($this->session->userdata('language')."/brand/manage_brandes",$data);
	}

	public function add_brand()
	{
		$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '6' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Brand/index/');
			}
 
		$company_id=$this->session->userdata('comp_id');
		$where ="id=$company_id";
        $company_user = $this->mod_common->select_single_records('tbl_company',$where,'no_of_brands');
        $company_user['no_of_brands'];
        $where ="comp_id=$company_id";
        $total_user=$this->mod_common->get_all_records_nums('tbl_brand',"*",$where);
        $data['remaining_brand']=$company_user['no_of_brands']-$total_user;
		$data["title"] = "Add Brand";
		$this->load->view($this->session->userdata('language')."/brand/add_brand",$data);
	}
	public function add(){

		if($this->input->server('REQUEST_METHOD') == 'POST'){

			$company_id=$this->session->userdata('comp_id');

			$where ="id=$company_id";
	        	
	        $company_user = $this->mod_common->select_single_records('tbl_company',$where,'no_of_brands');
	        	
	        $company_user['no_of_brands'];

	        $where ="comp_id=$company_id";

	        $total_user=$this->mod_common->get_all_records_nums('tbl_brand',"*",$where);

	        if($total_user >= $company_user['no_of_brands'])
			{
				$this->session->set_flashdata('err_message', 'Brand Limit completed.');
				redirect(SURL . 'brand');
			}




			$udata['brand_name'] = trim($this->input->post('bname'));

			#----check name already exist---------#
			if ($this->mod_brand->get_by_title($udata['brand_name'])) {
				$this->session->set_flashdata('err_message', 'Name Already Exist.');
				redirect(SURL . 'Brand/add_brand');
				exit();
			}

			$udata['status'] = $this->input->post('status');
			$udata['created_by'] = 1;
			$udata['comp_id'] = $this->session->userdata('comp_id');
			$udata['created_date'] = date('Y-m-d');
			
			$table='tbl_brand';
			$res = $this->mod_common->insert_into_table($table,$udata);

			if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have successfully added.');
	            redirect(SURL . 'Brand/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Brand/');
	        }
	    }
	}

	public function edit($id){
		$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '6' limit 1")->row_array();
		if ($role['edit']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Brand/index/');
			}
		if($id){
			$table='tbl_brand';
			$data["title"] = "Edit Brand";
			$where = "brand_id='$id'";
			$data['brand'] = $this->mod_common->select_single_records($table,$where);
			//pme($data['country']);
			$this->load->view($this->session->userdata('language')."/brand/edit", $data);
		}
	}

	public function update(){
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$mdata['brand_name']=trim($_POST['bname']);
			#----check name already exist---------#
				if ($this->mod_brand->edit_by_title($mdata['brand_name'],$_POST['id'])) {
					$this->session->set_flashdata('err_message', 'Name Already Exist.');
					redirect(SURL . 'Brand/edit/'.$_POST['id']);
					exit();
				}
			$mdata['status']=$_POST['status'];
			$id = $_POST['id'];
			$where = "brand_id='$id'";

			$mdata['modify_by'] = 1;
			$mdata['modify_date'] = date('Y-m-d');
			
			$table='tbl_brand';
			$res=$this->mod_common->update_table($table,$where,$mdata);

			if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have successfully updated.');
	            redirect(SURL . 'Brand/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Brand/');
	        }
    	}

	}

	public function delete($id) {
		$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '6' limit 1")->row_array();
		if ($role['delete']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Brand/index/');
			}

		if ($this->mod_brand->under_item($id)) {
			$this->session->set_flashdata('err_message', 'There are item under brand you cannot delete it.');
			redirect(SURL . 'Brand');
			exit();
		} 
		#-------------delete record--------------#
        $table = "tbl_brand";
        $where = "brand_id = '" . $id . "'";
        $delete_country = $this->mod_common->delete_record($table, $where);

        if ($delete_country) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Brand');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Brand/');
        }
    }

}
