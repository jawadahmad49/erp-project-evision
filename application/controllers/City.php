<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class City extends CI_Controller {

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
            "mod_city","mod_common"
        ));
        
    }

	public function index()
	{
		$data['city_list'] = $this->mod_city->manage_cities();
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Cities";		

		$this->load->view($this->session->userdata('language')."/city/manage_cities",$data);
		
			 
			
	}

	public function add_city()
	{
		$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '4' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'City/index/');
			}
    	$table='tbl_country';       
        $data['country_list'] = $this->mod_common->get_all_records($table,"*");    			
		$this->load->view($this->session->userdata('language')."/city/add_city",$data);
	}

	public function add(){

		

		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$udata['country_id'] = $this->input->post('cid');
			$udata['city_name'] = trim($this->input->post('cname'));

			#----check name already exist---------#
			if ($this->mod_city->get_by_title($udata['city_name'])) {
				$this->session->set_flashdata('err_message', 'Name Already Exist.');
				redirect(SURL . 'City/add_city');
				exit();
			}

			$udata['status'] = $this->input->post('status');
			
			$table='tbl_city';
			$res = $this->mod_common->insert_into_table($table,$udata);

			if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            redirect(SURL . 'City/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'City/');
	        }
	    }
	}

	public function edit($id){
		$login_user=$this->session->userdata('id');
	    $role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '4' limit 1")->row_array();
		if ($role['edit']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'City/index/');
			}
		if($id){
			$table='tbl_city';
			$where = "city_id='$id'";
			$data['city'] = $this->mod_common->select_single_records($table,$where);
			$tablecountry='tbl_country';
			$data['country_list'] = $this->mod_common->get_all_records($tablecountry,"*"); 
			//pme($data['country']);
			$this->load->view($this->session->userdata('language')."/city/edit", $data);
		}
	}

	public function update(){
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$cdata['country_id'] = $this->input->post('cid');
			$cdata['city_name'] = trim($this->input->post('cname'));
			$cdata['status'] = $this->input->post('status');
			$id = $_POST['id'];
			#----check name already exist---------#
				if ($this->mod_city->edit_by_title($cdata['city_name'],$id)) {
					$this->session->set_flashdata('err_message', 'Name Already Exist.');
					redirect(SURL . 'City/edit/'.$id);
					exit();
				}


			$where = "city_id='$id'";
			$table='tbl_city';
			$res=$this->mod_common->update_table($table,$where,$cdata);

			if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
	            redirect(SURL . 'City/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'City/');
	        }
	    }

	}

	public function delete($id) {

		$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '4' limit 1")->row_array();
		if ($role['delete']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'City/index/');
			}

		if ($this->mod_city->under_area($id)) {
			$this->session->set_flashdata('err_message', 'There are areas under city you can not delete it.');
			redirect(SURL . 'City/');
			exit();
		} 
		#-------------delete record--------------#
        $table = "tbl_city";
        $where = "city_id = '" . $id . "'";
        $delete_country = $this->mod_common->delete_record($table, $where);

        if ($delete_country) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'City/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'City/');
        }
    }

    public function getCountryName(){

    }

}
