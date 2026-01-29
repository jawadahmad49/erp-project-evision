<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends CI_Controller {

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
            "mod_category","mod_common"
        ));
        
    }

	public function index()
	{
		$data['category_list'] = $this->mod_category->manage_categories();
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Categories";		

		$this->load->view("app/category_coding/manage_category",$data);
	}

	public function add_category()
	{    
		$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '1045' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'app/Category/index/');
			}
		$table='tblclass';       
        $data['class_list'] = $this->mod_common->get_all_records($table,"*");
		$this->load->view("app/category_coding/add_category",$data);
	}

	public function add(){

		//$udata['catcode'] = "test";
		$udata['catname'] = trim($this->input->post('catname'));
		$udata['classcode'] = $this->input->post('classcode');
		$udata['status'] = $this->input->post('status');
		//$udata['pic_address'] = "testing";
		
		$table='tblcategory';
		$res = $this->mod_common->insert_into_table($table,$udata);

		if ($res) {
		 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
            redirect(SURL . 'app/Category/');
        } else {
            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
            redirect(SURL . 'app/Category/');
        }
	}

	public function delete($id) {
		$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '1045' limit 1")->row_array();
		if ($role['delete']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'app/Category/index/');
			}

		if ($this->mod_category->under_items($id)) {
			$this->session->set_flashdata('err_message', 'There are items under category you can not delete it.');
			redirect(SURL . 'app/Category/');
			exit();
		}
		#-------------delete record--------------#
        $table = "tblcategory";
        $where = "id = '" . $id . "'";
        $delete_country = $this->mod_common->delete_record($table, $where);

        if ($delete_country) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'app/Category/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'app/Category/');
        }
    }

    public function edit($id){
    	$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '1045' limit 1")->row_array();
		if ($role['edit']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'app/Category/index/');
			}
		$table='tblcategory';
		$where = "id='$id'";
		$data['category'] = $this->mod_common->select_single_records($table,$where);
		$table='tblclass';
		$data['class_list'] = $this->mod_common->get_all_records($table,"*");
		//pme($data['country']);
		$this->load->view("app/category_coding/edit", $data);
	}

	public function update(){

		$udata['catname'] = trim($this->input->post('catname'));
		$udata['classcode'] = $this->input->post('classcode');
		$udata['status'] = $this->input->post('status');
		$id = $_POST['id'];
		$where = "id='$id'";
		
		$table='tblcategory';
		$res=$this->mod_common->update_table($table,$where,$udata);

		if ($res) {
		 	$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
            redirect(SURL . 'app/Category/');
        } else {
            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
            redirect(SURL . 'app/Category/');
        }

	}



}
