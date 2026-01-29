<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Company extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model(array(
			"mod_company",
			"mod_common"
		));
	}

	public function index($id = '')
	{
		$table = 'tbl_company';
		$data['company'] = $this->mod_common->get_all_records_row($table, "*");
		if ($data['company']) {
			$data["filter"] = 'edit';
		} else {
			$data["filter"] = 'add';
		}
		#----load view----------#
		$data["title"] = "Company";
		//echo "<pre>"; var_dump( $data['company']);

		$this->load->view("app/Company/add", $data);
	}

	public function add()
	{
		$login_user = $this->session->userdata('id');
		$role = $this->db->query("SELECT * FROM tbl_user_rights WHERE uid = '$login_user' AND pageid = '1042' LIMIT 1")->row_array();

		// Check if the user has permission to add
		if ($role['add'] != 1) {
			$this->session->set_flashdata('err_message', 'You have no authority to complete this task.');
			redirect(SURL . 'app/Company/index/');
		}

		// Check if the request method is POST
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			// Retrieve and sanitize input data
			$standard_range = trim($this->input->post("standard"));
			$express_range = trim($this->input->post("express"));
			$night_range = trim($this->input->post("night"));

			$delivery_by = trim($this->input->post("delivery_by"));

			$delivery_charges = $this->db->query('SELECT * FROM tbl_delivery_charges')->result_array();

			$trips = $this->db->query('SELECT * FROM tbl_trip_coding')->result_array();

			$standard_conflict = false;
			$express_conflict = false;
			$night_conflict = false;

			$delivery_by_rider = false;

			foreach ($delivery_charges as $charge) {
				if (empty($standard_range) && !empty($charge['standard_range']) && $charge['standard_range'] !== '0') {
					$standard_conflict = true;
				}
				if (empty($express_range) && !empty($charge['express_range']) && $charge['express_range'] !== '0') {
					$express_conflict = true;
				}
				if (empty($night_range) && !empty($charge['night_range']) && $charge['night_range'] !== '0') {
					$night_conflict = true;
				}
			}
			foreach ($trips as $key) {
				if (!empty($delivery_by) && $delivery_by == 'delivery_by_rider' && $key['vehicle_id'] !== '0' && $key['rider_id'] !== '0') {
					$delivery_by_rider = true;
				}
			}

			if ($standard_conflict) {
				$this->session->set_flashdata('err_message', 'Standard delivery charges are already defined and cannot be updated.');
				redirect(SURL . 'app/Company/');
				exit;
			}

			if ($express_conflict) {
				$this->session->set_flashdata('err_message', 'Express delivery charges are already defined and cannot be updated.');
				redirect(SURL . 'app/Company/');
				exit;
			}

			if ($night_conflict) {
				$this->session->set_flashdata('err_message', 'Night delivery charges are already defined and cannot be updated.');
				redirect(SURL . 'app/Company/');
				exit;
			}
			if ($delivery_by_rider) {
				$this->session->set_flashdata('err_message', 'Trips are already Created against rider app, delete them first.');
				redirect(SURL . 'app/Company/');
				exit;
			}
			// Proceed to add company
			$add = $this->mod_company->add_company($this->input->post());

			if ($add) {
				$this->session->set_flashdata('ok_message', 'You have successfully added.');
				redirect(SURL . 'app/Company/edit/' . $add);
			} else {
				$this->session->set_flashdata('err_message', 'Adding operation failed.');
				redirect(SURL . 'app/Company/');
			}
		}

		// Load the view for adding company
		$data["filter"] = 'Company';
		$data["title"] = "Company";
		$this->load->view("app/Company/add", $data);
	}

	public function edit($id = '')
	{

		$login_user = $this->session->userdata('id');
		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '1042' limit 1")->row_array();
		if ($role['edit'] != 1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'app/Company/index/');
		}
		$standard_range = trim($this->input->post("standard"));
		$express_range = trim($this->input->post("express"));
		$night_range = trim($this->input->post("night"));

		$delivery_charges = $this->db->query('SELECT * FROM tbl_delivery_charges')->result_array();

		$standard_conflict = false;
		$express_conflict = false;
		$night_conflict = false;

		foreach ($delivery_charges as $charge) {
			if (empty($standard_range) && !empty($charge['standard_range']) && $charge['standard_range'] !== '0') {
				$standard_conflict = true;
			}
			if (empty($express_range) && !empty($charge['express_range']) && $charge['express_range'] !== '0') {
				$express_conflict = true;
			}
			if (empty($night_range) && !empty($charge['night_range']) && $charge['night_range'] !== '0') {
				$night_conflict = true;
			}
		}

		if ($standard_conflict) {
			$this->session->set_flashdata('err_message', 'Standard delivery charges are already defined and cannot be updated.');
			redirect(SURL . 'app/Company/');
			exit;
		}

		if ($express_conflict) {
			$this->session->set_flashdata('err_message', 'Express delivery charges are already defined and cannot be updated.');
			redirect(SURL . 'app/Company/');
			exit;
		}

		if ($night_conflict) {
			$this->session->set_flashdata('err_message', 'Night delivery charges are already defined and cannot be updated.');
			redirect(SURL . 'app/Company/');
			exit;
		}

		if ($id) {
			$table = 'tbl_company';
			$where = "id='$id'";
			$data['company'] = $this->mod_common->select_single_records($table, $where);
			$data["filter"] = 'edit';
			$data["title"] = "Company";
			$this->load->view("app/Company/add", $data);
		}
		/* Update Data */
		if ($this->input->server('REQUEST_METHOD') == 'POST') {


			$update = $this->mod_company->update_company($this->input->post());

			if ($update) {
				$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
				redirect(SURL . 'app/Company/edit/' . $update);
			} else {
				$this->session->set_flashdata('err_message', 'Operation Failed.');
				redirect(SURL . 'app/Company/edit/' . $update);
			}
		}
	}
}
