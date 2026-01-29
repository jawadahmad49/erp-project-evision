<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DirectGIRN extends CI_Controller {

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
            "mod_vendor","mod_common","mod_girndirect"
        ));
        
    }
    
	public function index()
	{
		$data['directgirn_list'] = $this->mod_girndirect->manage_directgirn();
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Direct GIRN";
		$this->load->view('direct_girn/direct_girn',$data);
	}

		public function add_direct_girn()
	{
		$data['vendor_list'] = $this->mod_vendor->getOnlyVendors();
		//echo "<pre>";print_r($data['vendor_list']);exit;
		$table='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		$this->load->view('direct_girn/add_direct_girn',$data);
	}

	public function add(){

		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$add_directgirn=  $this->mod_girndirect->add_direct_girn($this->input->post());
            //echo "<pre>";print_r($add_directgirn);exit;
		        if ($add_directgirn) {
		            $this->session->set_flashdata('ok_message', '- Added Successfully!');
		            redirect(SURL . 'directgirn/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in adding please try again!');
		            redirect(SURL . 'directgirn/');
		        }
		}
		//$this->add_direct_girn();
	}

		public function delete($id) {
		#-------------delete record--------------#
        $table = "tbl_goodsreceiving";
        $where = "receiptnos = '" . $id . "'";
        $delete_goods = $this->mod_common->delete_record($table, $where);

        if ($delete_goods) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'directgirn/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'directgirn/');
        }
    }

	public function edit($id){
		if($id){
		$data['vendor_list'] = $this->mod_vendor->getOnlyVendors();
		$table='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		$table='tbl_goodsreceiving';
		$where = "receiptnos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);

		$data['edit_list'] = $this->mod_girndirect->edit_directgirn($id);
		//echo '<pre>';print_r($data['edit_list']);exit;
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Update Direct GIRN";
		$this->load->view('direct_girn/edit',$data);
		}
	}
	public function update(){
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$add_directgirn=  $this->mod_girndirect->update_direct_girn($this->input->post());
            //echo "<pre>";print_r($add_directgirn);exit;
		        if ($add_directgirn || $add_directgirn==0) {
		            $this->session->set_flashdata('ok_message', '- Updated Successfully!');
		            redirect(SURL . 'directgirn/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in updating please try again!');
		            redirect(SURL . 'directgirn/');
		        }
		}
		//$this->add_direct_girn();
	}
}
