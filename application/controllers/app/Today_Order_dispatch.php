<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Today_Order_dispatch extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model(array(
			"mod_user", "mod_common"
		));
	}

	public function index()
	{


		$login_user = $this->session->userdata('id');
		$today = date('Y-m-d');
		$data['item'] = $this->db->query("SELECT * FROM `tbl_orderstatushistory` where  status ='Complete' and date ='$today' order by id desc")->result_array();

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Order Dispatch";
		$this->load->view("app/Today_Order_dispatch/manage_menu_item", $data);
	}
	public function complete()
	{
		$login_user = $this->session->userdata('id');

		$data['item'] = $this->db->query("SELECT * FROM `tbl_orderstatushistory` where status ='Delivered' order by id desc")->result_array();
		$data["title"] = "Completed Orders";

		$this->load->view("app/order_details", $data);
	}
	public function cancel()
	{
		$login_user = $this->session->userdata('id');

		$data['item'] = $this->db->query("SELECT * FROM `tbl_orderstatushistory` where  status ='Reject'  order by id desc")->result_array();
		$data["title"] = "Cancelled Orders";

		$this->load->view("app/order_details", $data);
	}
	public function filter()
	{
		$login_user = $this->session->userdata('id');

		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$from_date = $this->input->post("from");
			$to_date = $this->input->post("to");
			$data['item'] = $this->db->query("SELECT * FROM `tbl_orderstatushistory` where status ='Delivered' and date between '$from_date' and '$to_date' order by id desc ")->result_array();
		}
		$data["title"] = "Order Dispatch";

		$this->load->view("app/Today_Order_dispatch/manage_menu_item", $data);
	}
	public function detail_invoice($id = '')
	{
		$data['id'] = $id;
		$data["title"] = "Customer Invoice";
		$this->load->view("app/Today_Order_dispatch/detail_invoice", $data);
	}
	public function small_invoice($id = '')
	{
		$data['id'] = $id;
		$data["title"] = "Customer Invoice";
		$this->load->view("app/Today_Order_dispatch/invoice", $data);
	}
}
