<?php

defined('BASEPATH') or exit('No direct script access allowed');



class Vehicle_coding extends CI_Controller
{
	public function __construct()
	{

		parent::__construct();



		$this->load->model(array(

			"mod_common"

		));
	}

	public function index()
	{
		$login_user = $this->session->userdata('id');
		$this->db->select('location');
		$this->db->from('tbl_admin');
		$this->db->where('id', $login_user);
		$sale_point_ids = $this->db->get()->row_array()['location'];

		if ($sale_point_ids) {
			$sale_point_id_array = explode(',', $sale_point_ids);

			$this->db->select('*');
			$this->db->from('tbl_vehicle_coding');
			$this->db->where_in('sale_point_id', $sale_point_id_array);
			$data['tbl_data'] = $this->db->get()->result_array();
		} else {
			$data['tbl_data'] = [];
		}

		$data["filter"] = '';

		$data["title"] = "Manage Vehicle Coding";
		$this->load->view("app/Vehicle_coding/manage_customer", $data);
	}

	public function add_vehicle()
	{
		$login_user = $this->session->userdata('id');
		$this->db->select('location');
		$this->db->from('tbl_admin');
		$this->db->where('id', $login_user);
		$sale_point_ids = $this->db->get()->row_array()['location'];

		if ($sale_point_ids) {
			$sale_point_id_array = explode(',', $sale_point_ids);

			$this->db->select('*');
			$this->db->from('tbl_sales_point');
			$this->db->where_in('sale_point_id', $sale_point_id_array);
			$data['salepoint'] = $this->db->get()->result_array();
		} else {
			$data['salepoint'] = [];
		}
		$data["title"] = "Add Vehicle";
		$data["filter"] = 'add';
		$this->load->view("app/Vehicle_coding/add_charges", $data);
	}


	public function add()
	{
		// Get session data
		$login_user = $this->session->userdata('id');
		$comp_id = $this->session->userdata('comp_id');
		$edit = $this->input->post("edit");

		// Collect and sanitize input data
		$adata = array(
			'sale_point_id' => trim($this->input->post("location")),
			'vehicle_type' => trim($this->input->post("vehicle_type")),
			'vehicle_number' => trim($this->input->post("vehicle_number")),
			'vehicle_capacity' => trim($this->input->post("vehicle_capacity")),
			'registration_date' => trim($this->input->post("registration_date")),
			'created_by' => $login_user,
			'created_date' => date('Y-m-d'),
			'comp_id' => $comp_id
		);
		// Check for existing records
		$this->db->where('vehicle_number', $adata['vehicle_number']);
		if (!empty($edit)) {
			$this->db->where('id !=', $edit);
		}
		$existing_record = $this->db->get('tbl_vehicle_coding')->row_array();

		if (!empty($existing_record)) {
			$this->session->set_flashdata('err_message', 'Vehicle with this Vehicle Number already exists.');
			redirect(SURL . 'app/Vehicle_coding/');
			return;
		}
		$adata['description'] = $this->input->post("description");
		// Insert or update record
		if (empty($edit)) {
			$adata['status'] = "Unallocated";
			$res = $this->mod_common->insert_into_table("tbl_vehicle_coding", $adata);
		} else {
			$adata['modified_by'] = $login_user;
			$adata['modified_date'] = date('Y-m-d');
			$this->mod_common->update_table("tbl_vehicle_coding", array("id" => $edit), $adata);
			$res = $edit;
		}

		// Handle result
		if ($res) {
			$this->session->set_flashdata('ok_message', 'Operation successful.');
			redirect(SURL . 'app/Vehicle_coding/');
		} else {
			$this->session->set_flashdata('err_message', 'Operation failed.');
			redirect(SURL . 'app/Vehicle_coding/');
		}
	}
	public function delete($id)
	{
		$login_user = $this->session->userdata('id');
		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '1061' limit 1")->row_array();
		if ($role['delete'] != 1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'app/Vehicle_coding/index/');
		}
		#-------------delete record--------------#
		$table = "tbl_vehicle_coding";
		$where = "id = " . $id . "";
		$delete_area = $this->mod_common->delete_record($table, $where);
		if ($delete_area) {
			$this->session->set_flashdata('ok_message', 'You have successfully deleted.');
			redirect(SURL . 'app/Vehicle_coding/index/');
		} else {
			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
			redirect(SURL . 'app/Vehicle_coding/index/');
		}
	}


	public function edit($id)
	{
		$login_user = $this->session->userdata('id');
		$this->db->select('location');
		$this->db->from('tbl_admin');
		$this->db->where('id', $login_user);
		$sale_point_ids = $this->db->get()->row_array()['location'];

		if ($sale_point_ids) {
			$sale_point_id_array = explode(',', $sale_point_ids);

			$this->db->select('*');
			$this->db->from('tbl_sales_point');
			$this->db->where_in('sale_point_id', $sale_point_id_array);
			$data['salepoint'] = $this->db->get()->result_array();
		} else {
			$data['salepoint'] = [];
		}

		$data['record'] = $this->db->query("SELECT * from tbl_vehicle_coding where id='$id'")->row_array();

		$data["filter"] = 'add';

		$this->load->view("app/Vehicle_coding/add_charges", $data);
	}
}
