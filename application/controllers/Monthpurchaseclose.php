<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Monthpurchaseclose extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_monthpurchaseclose","mod_common"
        ));
        
    }

	public function index()
	{
		$data['purchase_list'] = $this->mod_monthpurchaseclose->manage_monthpurchaseclose();
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Monthpurchase Close";		

		$this->load->view($this->session->userdata('language')."/Monthpurchaseclose/manage_monthpurchaseclose",$data);
	}

	public function add_monthpurchaseclose()
	{
		$table='tblclass';       
        $data['class_list'] = $this->mod_common->get_all_records($table,"*");
		$this->load->view($this->session->userdata('language')."/Monthpurchaseclose/add_monthpurchaseclose",$data);
	}

	public function add(){

		//$udata['catcode'] = "test";
		$udata['month_no'] = trim($this->input->post('month'));
		$udata['year_no'] = $this->input->post('year');
		$udata['stock_tons'] = $this->input->post('stock');
		$udata['per_kg'] = $this->input->post('price');
		$udata['stock_value'] = $this->input->post('price_ton');
		$udata['created_by'] = 1;
		$udata['created_dt'] = date('Y-m-d');
		
		if ($this->mod_monthpurchaseclose->get_by_title($udata['month_no'],$udata['year_no'])) {
				$this->session->set_flashdata('err_message', 'Month Already Exist.');
				redirect(SURL . 'Monthpurchaseclose/add_monthpurchaseclose');
				exit();
			}
		$table='tbl_monthly_closing';
		$res = $this->mod_common->insert_into_table($table,$udata);
		if ($res) {
		 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
            redirect(SURL . 'Monthpurchaseclose/');
        } else {
            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
            redirect(SURL . 'Monthpurchaseclose/');
        }
	}

	public function delete($id) {

		/*if ($this->mod_monthpurchaseclose->under_items($id)) {
			$this->session->set_flashdata('err_message', 'There are items under category you can not delete it.');
			redirect(SURL . 'Monthpurchaseclose/');
			exit();
		}*/
		#-------------delete record--------------#
        $table = "tbl_monthly_closing";
        $where = "trans_id = '" . $id . "'";
        $delete_country = $this->mod_common->delete_record($table, $where);

        if ($delete_country) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Monthpurchaseclose/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Monthpurchaseclose/');
        }
    }

    public function edit($id){
		$table='tbl_monthly_closing';
		$where = "trans_id='$id'";
		$data['purchase'] = $this->mod_common->select_single_records($table,$where);
		$table='tblclass';
		$data['class_list'] = $this->mod_common->get_all_records($table,"*");
		//pme($data['country']);
		$this->load->view($this->session->userdata('language')."/Monthpurchaseclose/edit", $data);
	}

	public function update(){

		$udata['month_no'] = trim($this->input->post('month'));
		$udata['year_no'] = $this->input->post('year');
		$udata['stock_tons'] = $this->input->post('stock');
		$udata['per_kg'] = $this->input->post('price');
		$udata['stock_value'] = $this->input->post('price_ton');
		$udata['modify_by'] = 1;
		$udata['modify_dt'] = date('Y-m-d');
		$id = $_POST['id'];
		if ($this->mod_monthpurchaseclose->get_by_title_update($udata['month_no'],$udata['year_no'],$id)) {
				$this->session->set_flashdata('err_message', 'Month Already Exist.');
				redirect(SURL . 'Monthpurchaseclose/edit/'.$id);
				exit();
			}
		$where = "trans_id='$id'";
		
		$table='tbl_monthly_closing';
		$res=$this->mod_common->update_table($table,$where,$udata);

		if ($res) {
		 	$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
            redirect(SURL . 'Monthpurchaseclose/');
        } else {
            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
            redirect(SURL . 'Monthpurchaseclose/');
        }

	}



}
