<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Country extends CI_Controller {

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
            "mod_country","mod_common"
        ));
        
    }

	public function index()
	{
		$table='tbl_country';
		$data['country_list'] = $this->mod_common->get_all_records($table,"*");
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Countries";		

		$this->load->view($this->session->userdata('language')."/country/manage_countries",$data);
	}

	public function add_country()
	{
		$data["title"] = "Add Country";
		$this->load->view($this->session->userdata('language')."/country/add_country",$data);
	}

	public function add(){

		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$udata['country_name'] = trim($this->input->post('cname'));

			#----check name already exist---------#
			if ($this->mod_country->get_by_title($udata['country_name'])) {
				$this->session->set_flashdata('err_message', 'Name Already Exist.');
				redirect(SURL . 'Country/add_country');
				exit();
			}

			$udata['status'] = $this->input->post('status');
			
			$table='tbl_country';
			$res = $this->mod_common->insert_into_table($table,$udata);

			if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            redirect(SURL . 'Country/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Country/');
	        }
	    }
	}

	public function edit($id){
		if($id){
			$table='tbl_country';
			$where = "country_id='$id'";
			$data['country'] = $this->mod_common->select_single_records($table,$where);
			//pme($data['country']);
			$this->load->view($this->session->userdata('language')."/country/edit", $data);
		}
	}

	public function update(){
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$mdata['country_name']=trim($_POST['cname']);
			#----check name already exist---------#
				if ($this->mod_country->edit_by_title($mdata['country_name'],$_POST['id'])) {
					$this->session->set_flashdata('err_message', 'Name Already Exist.');
					redirect(SURL . 'Country/edit/'.$_POST['id']);
					exit();
				}
			$mdata['status']=$_POST['status'];
			$id = $_POST['id'];
			$where = "country_id='$id'";
			
			$table='tbl_country';
			$res=$this->mod_common->update_table($table,$where,$mdata);

			if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
	            redirect(SURL . 'Country/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Country/');
	        }
    	}

	}

	public function delete($id) {

		if ($this->mod_country->under_country($id)) {
			$this->session->set_flashdata('err_message', 'There are cities under country you cannot delete it.');
			redirect(SURL . 'Country/');
			exit();
		} 
		#-------------delete record--------------#
        $table = "tbl_country";
        $where = "country_id = '" . $id . "'";
        $delete_country = $this->mod_common->delete_record($table, $where);

        if ($delete_country) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Country/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Country/');
        }
    }

}
