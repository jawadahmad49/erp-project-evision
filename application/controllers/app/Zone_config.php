<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Zone_config extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model(array(
			"mod_user",
			"mod_common",
			"mod_city"
		));
	}

	public function index()
	{

		$data['result'] = $this->db->query("select * from tbl_zone  order by id desc ")->result_array();

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Zone";
		$this->load->view("app/Zone_config/manage", $data);
	}


	public function add_sale_point()
	{

		$data['city_list'] = $this->db->query("SELECT * from tbl_city where status = 'Active'")->result_array();

		$data['city_config'] = $this->db->query("SELECT city from tbl_city_config")->row_array()['city'];

		$data["title"] = "Add Zone";
		$this->load->view("app/Zone_config/add", $data);
	}

	public function add()
	{


		if ($this->input->server('REQUEST_METHOD') == 'POST') {




			$udata['zone_name'] = $this->input->post('zone_name');

			$udata['city_id'] = $this->input->post('city_id');
			$udata['status'] = $this->input->post('status');


			if (empty($this->input->post("id"))) {
				$udata['created_by'] = $this->session->userdata('id');
				$udata['created_date'] = date('Y-m-d');
				$res = $this->mod_common->insert_into_table("tbl_zone", $udata);
			} else {
				$udata['modify_by'] = $this->session->userdata('id');
				$udata['modify_dt'] = date('Y-m-d');
				$last_id = $this->input->post("id");
				$this->mod_common->update_table("tbl_zone", array("id" => $last_id), $udata);
				$res = $last_id;
				$this->db->query("DELETE from tbl_zone_detail where zone_id='$res'");
			}

			foreach ($this->input->post("area") as $key => $value) {
				$ddata['zone_id'] = $res;
				if ($this->input->post('detail_id')[$key] != 0) {
					$id = $this->input->post('detail_id')[$key];
					$check_id = $this->db->query("SELECT id from tbl_zone_detail where id='$id'")->row_array()['id'];
					if (empty($check_id)) {
						$ddata['id'] = $this->input->post('detail_id')[$key];
					} else {
						$ddata['id'] = $this->db->query("SELECT count(*)+1 as id from tbl_zone_detail")->row_array()['id'];
					}
				} else {
					$ddata['id'] = $this->db->query("SELECT count(*)+1 as id from tbl_zone_detail")->row_array()['id'];
				}
				$ddata['area_name'] =  $this->input->post('area_name')[$key];
				$ddata['area'] =  $this->input->post('area')[$key];
				$this->mod_common->insert_into_table("tbl_zone_detail", $ddata);
			}
			if ($res) {
				$this->session->set_flashdata('ok_message', 'You have succesfully added.');
				redirect(SURL . 'app/Zone_config/');
			} else {
				$this->session->set_flashdata('err_message', 'Adding Operation Failed.');
				redirect(SURL . 'app/Zone_config/');
			}
		}
	}

	public function edit($id = '')
	{
		if ($id) {
			$data['city_list'] = $this->db->query("SELECT * from tbl_city where status = 'Active'")->result_array();
			$data['record'] = $this->db->query("select * from tbl_zone where id='$id'")->row_array();
			$data['detail'] = $this->db->query("SELECT * FROM tbl_zone_detail WHERE zone_id='$id'")->result_array();

			$area_names = array_column($data['detail'], 'area_name'); // Extract all area_name values from the result
			$data['area_name'] = implode(',', $area_names);

			$areas = array_column($data['detail'], 'area');
			$data['area'] = implode('|', $areas);

			$data['check'] = $check = 0;

			$check_user = $this->db->query("SELECT * from tbl_user INNER JOIN tbl_zone_detail ON tbl_zone_detail.id=tbl_user.area_id where zone_id='" . $id . "'")->row_array();
			$check_sales_point = $this->db->query("SELECT * from tbl_sales_point where FIND_IN_SET('" . $id . "', zone_id) > 0")->row_array();
			if (!empty($check_user) || !empty($check_sales_point)) {
				$data['check'] = $check = 1;
			}

			// pm($data);exit;
			$this->load->view("app/Zone_config/add", $data);
		}
	}
	public function preview($id = '')
	{
		if ($id) {
			$data['city_list'] = $this->db->query("SELECT * from tbl_city where status = 'Active'")->result_array();
			$data['record'] = $this->db->query("select * from tbl_zone where id='$id'")->row_array();
			$data['detail'] = $this->db->query("SELECT * FROM tbl_zone_detail WHERE zone_id='$id'")->result_array();

			$area_names = array_column($data['detail'], 'area_name'); // Extract all area_name values from the result
			$data['area_name'] = implode(',', $area_names);

			$areas = array_column($data['detail'], 'area');
			$data['area'] = implode('|', $areas);


			$this->load->view("app/Zone_config/preview", $data);
		}
	}

	public function delete($id)
	{


		$table = "tbl_zone";
		$where = "id = '" . $id . "'";
		$delete_country = $this->mod_common->delete_record($table, $where);

		$table = "tbl_zone_detail";
		$where = "zone_id = '" . $id . "'";
		$this->mod_common->delete_record($table, $where);

		if ($delete_country) {
			$this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
			redirect(SURL . 'app/Zone_config/');
		} else {
			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
			redirect(SURL . 'app/Zone_config/');
		}
	}
}
