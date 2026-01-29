<?php
defined('BASEPATH') or exit('No direct script access allowed');
class All_orders extends CI_Controller
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
		$this->db->select('location');
		$this->db->from('tbl_admin');
		$this->db->where('id', $login_user);
		$sale_point_ids = $this->db->get()->row_array()['location'];

		if ($sale_point_ids) {
			$sale_point_id_array = explode(',', $sale_point_ids);

			$this->db->select('*');
			$this->db->from('tbl_sales_point');
			$this->db->where_in('sale_point_id', $sale_point_id_array);
			$salepoint = $this->db->get()->result_array();
		} else {
			$salepoint = [];
		}
		$data["filter"] = '';

		#----load view----------#

		$data["title"] = "Manage All Orders";

		$this->load->view("app/All_orders/manage_orders", $data);
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

		if (isset($_POST['datepicker'])) {
			$from_date = date("Y-m-d", strtotime($_POST['datepicker']));
			$to_date = date("Y-m-d", strtotime($_POST['datepicker1']));
		} else {
			$from_date = date('Y-m-d', strtotime('-60 day'));
			$to_date = date('Y-m-d');
		}

		$this->db->select('COUNT(*) as count');
		$this->db->from('tbl_place_order');
		$this->db->where('date >=', $from_date);
		$this->db->where('date <=', $to_date);

		if (!empty($searchValue)) {
			$this->db->group_start();
			$this->db->like('id', $searchValue);
			$this->db->or_like('date', $searchValue);
			$this->db->or_like('type', $searchValue);
			$this->db->or_like('deliveryType', $searchValue);
			$this->db->or_like('deliveryStatus', $searchValue);
			$this->db->or_like('trip_id', $searchValue);
			$this->db->group_end();
		}

		if ($sale_point_ids) {
			$sale_point_id_array = explode(',', $sale_point_ids);
			$this->db->where_in('sale_point_id', $sale_point_id_array);
		}

		$recordsTotal = $this->db->get()->row()->count;

		$this->db->select('*');
		$this->db->from('tbl_place_order');
		$this->db->where('date >=', $from_date);
		$this->db->where('date <=', $to_date);

		if (!empty($searchValue)) {
			$this->db->group_start();
			$this->db->like('id', $searchValue);
			$this->db->or_like('date', $searchValue);
			$this->db->or_like('type', $searchValue);
			$this->db->or_like('deliveryType', $searchValue);
			$this->db->or_like('deliveryStatus', $searchValue);
			$this->db->or_like('trip_id', $searchValue);
			$this->db->group_end();
		}

		if ($sale_point_ids) {
			$sale_point_id_array = explode(',', $sale_point_ids);
			$this->db->where_in('sale_point_id', $sale_point_id_array);
		}

		$this->db->order_by($orderColumn, $orderDirection);
		$this->db->limit($length, $start);

		$results = $this->db->get()->result_array();

		$data = [];
		$sno = 0;
		foreach ($results as $value) {
			$sno++;
			$id = $value['id'];

			$delivery_status = '';
			switch ($value["deliveryStatus"]) {
				case "Delivered":
					$delivery_status = '<strong style="color: blue;">' . $value["deliveryStatus"] . '</strong>';
					break;
				case "Booked":
					$delivery_status = '<strong style="color: green;">' . $value["deliveryStatus"] . '</strong>';
					break;
				case "Reject":
					$delivery_status = '<strong style="color: red;">' . $value["deliveryStatus"] . '</strong>';
					break;
				case "Confirm":
					$delivery_status = '<strong style="color: green;">' . $value["deliveryStatus"] . '</strong>';
					break;
				case "Dispatch":
					$delivery_status = '<strong style="color: blue;">' . $value["deliveryStatus"] . '</strong>';
					break;
			}

			$pdf_status = '';
			if (!empty($value["invoice_url"])) {
				$pdf_status = '<strong style="color: blue;">Created</strong>';
			} else {
				$pdf_status = '<strong style="color: red;">Not Created</strong>';
			}

			$exec_time = $value['date'] . ' &nbsp; ' . $value['time'] . ' <br>
                      <i class="blue fa fa-long-arrow-right bigger-140" aria-hidden="true"></i> <br>
                      ' . $value['delivery_date'] . ' &nbsp; ' . $value['delivery_time'];

			$action_buttons = '<div class="action-buttons" style="display: flex; align-items: center;">';
			$action_buttons .= '<a class="btn btn-info btn-sm" target="_blank" title="Print Invoice" href="' . SURL . 'app/Today_Order_dispatch/detail_invoice/' . $id . '"> View Detail </a>';
			if ($delivery_status !== 'Delivered' && ($value['trip_id'] == '' || $value['trip_id'] == '0') && $value['type'] !== 'walkin') {
				$action_buttons .= '<a id="firstprint" target="_blank" class="ml-2" title="Print Invoice" href="' . SURL . 'app/Order_confirmation/index/' . $id . '">
									<i class="ace-icon fa fa-pencil bigger-130"></i>
								</a>';
			} else if ($value['type'] == 'walkin') {
				$action_buttons .= '<a id="firstprint" target="_blank" class="ml-2" title="Print Invoice" href="' . SURL . 'app/Walk_in_orders/edit/' . $id . '">
				<i class="ace-icon fa fa-pencil bigger-130"></i>
			</a>';
			}
			$action_buttons .= '<a id="firstprint" target="_blank" class="ml-2" title="Print Invoice" href="' . SURL . 'app/Order_confirmation/small_invoice/' . $id . '">
									<i class="ace-icon fa fa-print bigger-130 green"></i>
								</a></div>';
			$data[] = [
				'count' => $sno,
				'id' => $value['id'],
				'date' => $value['date'],
				'order_type' => ucfirst($value['type']),
				'deliveryType' => $value['deliveryType'],
				'exec_time' => $exec_time,
				'deliveryStatus' => $delivery_status,
				'trip_id' => $value['trip_id'],
				'pdfStatus' => $pdf_status,
				'actions' => $action_buttons
			];
		}
		echo json_encode([
			'draw' => intval($draw),
			'recordsTotal' => intval($recordsTotal),
			'recordsFiltered' => intval($recordsTotal),
			'data' => $data
		]);
	}

}
