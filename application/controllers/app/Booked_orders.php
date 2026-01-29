<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Booked_orders extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array(
			"mod_item",
			"mod_common"
		));
	}

	public function index()
	{
		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("SELECT location from tbl_admin where id = '$login_user'")->row_array()['location'];

		if ($sale_point_id) {
			$where_location = "where sale_point_id = '$sale_point_id'";
		} else {
			$where_location = "";
		}
		$salepoint = $this->db->query("SELECT * from tbl_sales_point $where_location")->row_array()['sale_point_id'];

		if (isset($_POST['submit'])) {
			$data['from_date'] = date("Y-m-d", strtotime($_POST['from']));
			$data['to_date'] = date("Y-m-d", strtotime($_POST['to']));
		} else {
			$data['from_date'] = date('Y-m-d', strtotime('-60 day'));
			$data['to_date'] = date('Y-m-d');
		}

		$data["filter"] = '';

		#----load view----------#

		$data["title"] = "Manage Booked Orders";

		$this->load->view("app/Booked_orders/manage_orders", $data);
	}

	public function your_ajax_endpoint()
	{
		$login_user = $this->session->userdata('id');
		$this->db->select('location');
		$this->db->from('tbl_admin');
		$this->db->where('id', $login_user);
		$sale_point_ids = $this->db->get()->row_array()['location'];

		$draw = $this->input->post('draw');
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$searchValue = $this->input->post('search')['value'];

		$orderColumnIndex = $this->input->post('order')[0]['column'];
		$orderDirection = $this->input->post('order')[0]['dir'];
		$columns = $this->input->post('columns');

		$orderColumn = $columns[$orderColumnIndex]['data'];

		if (isset($_POST['datepicker']) && isset($_POST['datepicker1'])) {
			$from_date = date("Y-m-d", strtotime($_POST['datepicker']));
			$to_date = date("Y-m-d", strtotime($_POST['datepicker1']));
		} else {
			$from_date = date('Y-m-d', strtotime('-60 day'));
			$to_date = date('Y-m-d');
		}
		$baseQuery = "SELECT COUNT(*) as count 
              FROM `tbl_place_order` 
              WHERE deliveryStatus = 'Booked' 
              AND status_dt BETWEEN '$from_date' AND '$to_date'";
		if (!empty($searchValue)) {
			$baseQuery .= " AND (";
			$baseQuery .= "id LIKE '%" . $this->db->escape_like_str($searchValue) . "%' OR ";
			$baseQuery .= "date LIKE '%" . $this->db->escape_like_str($searchValue) . "%' OR ";
			$baseQuery .= "deliveryType LIKE '%" . $this->db->escape_like_str($searchValue) . "%' OR ";
			$baseQuery .= "deliveryStatus LIKE '%" . $this->db->escape_like_str($searchValue) . "%'";
			$baseQuery .= ")";
		}
		if (!empty($sale_point_ids)) {
			$sale_point_id_array = explode(',', $sale_point_ids);
			$escaped_ids = array_map([$this->db, 'escape'], $sale_point_id_array);
			$baseQuery .= " AND sale_point_id IN (" . implode(',', $escaped_ids) . ")";
		}
		$recordsTotal = $this->db->query($baseQuery)->row()->count;
		$query = "SELECT * 
			FROM `tbl_place_order` 
			WHERE deliveryStatus = 'Booked' 
			AND status_dt BETWEEN '$from_date' AND '$to_date'";
		if (!empty($searchValue)) {
			$query .= " AND (";
			$query .= "id LIKE '%" . $this->db->escape_like_str($searchValue) . "%' OR ";
			$query .= "date LIKE '%" . $this->db->escape_like_str($searchValue) . "%' OR ";
			$query .= "deliveryType LIKE '%" . $this->db->escape_like_str($searchValue) . "%' OR ";
			$query .= "deliveryStatus LIKE '%" . $this->db->escape_like_str($searchValue) . "%'";
			$query .= ")";
		}
		if (!empty($sale_point_ids)) {
			$sale_point_id_array = explode(',', $sale_point_ids);
			$escaped_ids = array_map([$this->db, 'escape'], $sale_point_id_array);
			$query .= " AND sale_point_id IN (" . implode(',', $escaped_ids) . ")";
		}
		if (!empty($orderColumn) && !empty($orderDirection)) {
			$query .= " ORDER BY " . $this->db->escape_str($orderColumn) . " " . $this->db->escape_str($orderDirection);
		} else {
			$query .= " ORDER BY id DESC";
		}
		if (!empty($start) || !empty($length)) {
			$query .= " LIMIT " . intval($start) . ", " . intval($length);
		}
		$data = array();
		$results = $this->db->query($query)->result_array();
		$sno = 0;
		foreach ($results as $value) {
			$sno++;
			$id = $value['id'];
			$delivery_status = '<strong class="green"> ' . $value['deliveryStatus'] . ' </strong>';

			$action_buttons = '<div class="action-buttons" style="display: flex; align-items: center;">';
			$action_buttons .= '<a class="btn btn-info btn-sm" target="_blank" title="Print Invoice" href="' . SURL . 'app/Today_Order_dispatch/detail_invoice/' . $id . '"> View Detail </a>';
			$action_buttons .= '<a id="firstprint" target="_blank" class="ml-2" title="Print Invoice" href="' . SURL . 'app/Today_Order_dispatch/small_invoice/' . $id . '">
									<i class="ace-icon fa fa-print bigger-130 green"></i>
								</a></div>';
			$data[] = array(
				'count' => $sno,
				'id' => $value['id'],
				'date' => $value['date'],
				'deliveryType' => $value['deliveryType'],
				'deliveryStatus' => $delivery_status,
				'actions' => $action_buttons
			);
		}

		echo json_encode([
			'draw' => intval($draw),
			'recordsTotal' => intval($recordsTotal),
			'recordsFiltered' => intval($recordsTotal),
			'data' => $data
		]);
	}
}
