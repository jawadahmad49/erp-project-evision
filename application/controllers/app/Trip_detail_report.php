<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Trip_detail_report extends CI_Controller
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
		$data['users'] = $this->db->query("SELECT * from tbl_user")->result_array();
		$login_user = $this->session->userdata('id');
		$sale_point_ids = $this->db->query("SELECT location FROM tbl_admin WHERE id = '$login_user'")->row_array()['location'];
		if ($sale_point_ids) {
			$sale_point_id_array = explode(',', $sale_point_ids);
			$sale_point_id_list = implode("','", $sale_point_id_array);
			$where_location = "WHERE sale_point_id IN ('$sale_point_id_list')";
		} else {
			$where_location = "";
		}
		$data['salepoint'] = $this->db->query("SELECT * from tbl_sales_point $where_location")->result_array();

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Trip Detail Report";
		$this->load->view("app/Trip_detail_report/search", $data);
	}
	public function details()
	{

		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			// echo pm($_POST);exit;
			$data['fromdate'] = $fromdate = $this->input->post('from_date');
			$data['todate'] = $todate = $this->input->post('to_date');
			$data['sale_point_id'] = $sale_point_id = $this->input->post('salepoint');

			$data['rider_id'] = $rider_id = $this->input->post('rider_id');
			$data['vehicle_id'] = $vehicle_id = $this->input->post('vehicle_id');
			$data['trip_id'] = $trip_id = $this->input->post('trip_id');
			if ($rider_id != 'All') {
				$where_rider_id = "and rider_id='$rider_id'";
			} else {
				$where_rider_id = "";
			}
			if ($vehicle_id != 'All') {
				$where_vehicle_id = "and vehicle_id='$vehicle_id'";
			} else {
				$where_vehicle_id = "";
			}
			if ($trip_id != 'All') {
				$where_trip_id = "and id='$trip_id'";
			} else {
				$where_trip_id = "";
			}
			if ($sale_point_id != 'All') {
				$where_sale_point_id = "and sale_point_id='$sale_point_id'";
			} else {
				$where_sale_point_id = "";
			}
			// echo "SELECT * from tbl_trip_coding where created_date BETWEEN '$fromdate' and '$todate' $where_vehicle_id $where_rider_id $where_sale_point_id order by id asc";exit;
			$data['report'] = $this->db->query("SELECT * from tbl_trip_coding where created_date BETWEEN '$fromdate' and '$todate' $where_vehicle_id $where_trip_id $where_rider_id $where_sale_point_id order by id asc")->result_array();

			$this->load->view("app/Trip_detail_report/report", $data);
			if (empty($data['report'])) {
				$this->session->set_flashdata('err_message', 'No Record Found.');
				redirect(SURL . 'app/Trip_detail_report/');
			}
		}
	}
	public function get_rider()
	{
		$salepoint = $_POST['salepoint'];
		if (!empty($salepoint)) {
			$riders = $this->db->query("SELECT * FROM tbl_rider_coding WHERE sale_point_id = '$salepoint'")->result_array(); ?>
			<option value="All">All Riders</option>
			<?php
			foreach ($riders as $value) { ?>
				<option value="<?php echo $value['id']; ?>"><?php echo $value['rider_name']; ?></option>
			<?php }
		}
	}
	public function get_vehicle()
	{
		$salepoint = $_POST['salepoint'];
		if (!empty($salepoint)) {
			$vehicles = $this->db->query("SELECT * FROM tbl_vehicle_coding WHERE sale_point_id = '$salepoint'")->result_array(); ?>
			<option value="All">All Vehicles</option>
			<?php
			foreach ($vehicles as $value) { ?>
				<option value="<?php echo $value['id']; ?>"><?= $value['vehicle_number']; ?> - <?= ucwords($value['vehicle_type']); ?></option>
			<?php }
		}
	}
	public function get_trip()
	{
		$salepoint = $_POST['salepoint'];
		$rider_id = $_POST['rider_id'];
		$vehicle_id = $_POST['vehicle_id'];
		if (!empty($salepoint) && !empty($rider_id) && !empty($vehicle_id)) {
			$trips = $this->db->query("SELECT * FROM tbl_trip_coding WHERE sale_point_id = '$salepoint' and rider_id = '$rider_id' and vehicle_id = '$vehicle_id'")->result_array(); ?>
			<option value="All">All Trips</option>
			<?php
			foreach ($trips as $value) { ?>
				<option value="<?php echo $value['id']; ?>"><?php echo 'Trip #' . $value['id']; ?></option>
			<?php }
		}
	}
}
