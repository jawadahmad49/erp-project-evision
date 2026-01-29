<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Customer_profile extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array(
			"mod_item", "mod_common"
		));
	}

	public function index()
	{
		$data["customer_list"] =  $this->db->query("SELECT * from tbl_user")->result_array();

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

		$data["title"] = "Manage Customer Profile";

		$this->load->view("app/Customer_profile/manage_customer", $data);
	}
	
}
