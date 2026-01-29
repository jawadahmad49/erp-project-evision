<?php

defined('BASEPATH') or exit('No direct script access allowed');



class Delivery_charges extends CI_Controller
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
		$sale_point_ids = $this->db->query("SELECT location FROM tbl_admin WHERE id = '$login_user'")->row_array()['location'];
		if ($sale_point_ids) {
			$sale_point_id_array = explode(',', $sale_point_ids);
			$sale_point_id_list = implode("','", $sale_point_id_array);
			$where_location = "WHERE sale_point_id IN ('$sale_point_id_list')";
		} else {
			$where_location = "";
		}
		$salepoint = $this->db->query("SELECT GROUP_CONCAT(sale_point_id) as sale_point_ids FROM tbl_sales_point $where_location")->row_array()['sale_point_ids'];
		$data["filter"] = '';
		$data["title"] = "Manage Delivery Charges";
		$data['tbl_data'] = $this->db->query("SELECT * FROM tbl_delivery_charges WHERE sale_point_id IN ($salepoint)")->result_array();
		$this->load->view("app/Delivery_charges/manage_customer", $data);
	}
	public function getExtra_DevCharges()
	{
		$range = $_POST['range'];
		$per_km_charges = $_POST['per_km_charges'];
		$stan_delivery_charges = $_POST['stan_delivery_charges'];
		$express_delivery_charges = $_POST['express_delivery_charges'];
		$night_delivery_charges = $_POST['night_delivery_charges'];
		$total_dist = $_POST['total_dist'];

		$extra_dist = $total_dist - $range;

		// Calculate the percentage increase for express and night delivery charges
		$express_percentage_increase = (($express_delivery_charges - $stan_delivery_charges) / $stan_delivery_charges) * 100;
		$night_percentage_increase = (($night_delivery_charges - $stan_delivery_charges) / $stan_delivery_charges) * 100;

		// Adjust per kilometer charges based on the percentage increase
		$express_per_km_charges = $per_km_charges + ($per_km_charges * $express_percentage_increase / 100);
		$night_per_km_charges = $per_km_charges + ($per_km_charges * $night_percentage_increase / 100);

		if ($total_dist > $range) {
			// Calculate the extra charges for each type of delivery
			$stan_Delivery_charges = $extra_dist * $per_km_charges;
			$express_Delivery_charges = $extra_dist * $express_per_km_charges;
			$night_Delivery_charges = $extra_dist * $night_per_km_charges;
		} else {
			// If the distance is within the range, no extra charges apply
			$stan_Delivery_charges = 0;
			$express_Delivery_charges = 0;
			$night_Delivery_charges = 0;
		}

		// Prepare the response data
		$delivery_data = [
			'delivery' => $stan_Delivery_charges,
			'expressdelivery' => $express_Delivery_charges,
			'nightdelivery' => $night_Delivery_charges,
		];

		// Return the calculated delivery charges as JSON
		echo json_encode($delivery_data);
	}



	public function add_charges()
	{
		$login_user = $this->session->userdata('id');
		$sale_point_ids = $this->db->query("SELECT location FROM tbl_admin WHERE id = '$login_user'")->row_array()['location'];

		if ($sale_point_ids) {
			$sale_point_id_array = explode(',', $sale_point_ids);
			$sale_point_id_list = implode("','", $sale_point_id_array);
			$where_location = "WHERE sale_point_id IN ('$sale_point_id_list')";
		} else {
			$where_location = "";
		}
		$data['salepoint'] = $this->db->query("SELECT * FROM tbl_sales_point $where_location")->result_array();
		$data['status'] = $this->db->query("SELECT * FROM tbl_company WHERE id = '1'")->row_array();
		$data["filter"] = 'add';
		$this->load->view("app/Delivery_charges/add_charges", $data);
	}


	public function add()
	{

		$login_user = $this->session->userdata('id');
		$edit = $this->input->post("edit");
		$type = $adata['type'] = $this->input->post("type");
		$comp_id = $this->session->userdata('comp_id');


		$adata['kilo_meters'] = trim($_POST["kilo_meters"]);
		$adata['charges_outside_range'] = trim($_POST["pkm_charges"]);
		$adata['standard_range'] = trim($_POST["std_charges"]);
		$adata['express_range'] = trim($_POST["exp_charges"]);
		$adata['night_range'] = trim($_POST["night_charges"]);

		// $adata['stdrd_time'] = trim($_POST["s_hours"]) . ':' . trim($_POST["s_minutes"]);
		// $adata['expres_time'] = trim($_POST["ex_hours"]) . ':' . trim($_POST["ex_minutes"]);
		// $adata['night_time'] = trim($_POST["nit_hours"]) . ':' . trim($_POST["nit_minutes"]);

		$adata['e_date'] = $e_date = $_POST["e_date"];
		$adata['created_by'] = $login_user;
		$adata['created_date'] = date('Y-m-d');
		$adata['comp_id'] = $comp_id;
		$adata['sale_point_id'] = $_POST["location"];
		$adata['zone'] = $_POST["zone"];


		if (empty($edit)) {

			$check = $this->db->query("SELECT * from tbl_delivery_charges where e_date='$e_date' and type='$type' ")->row_array();

			if (!empty($check)) {

				$this->session->set_flashdata('err_message', 'Cannot Add Delivery Charges Against Same Effective Date !');

				redirect(SURL . 'app/Delivery_charges/');
			}

			$res = $this->mod_common->insert_into_table("tbl_delivery_charges", $adata);
		} else {

			$adata['modified_by'] = $login_user;

			$adata['modified_date'] = date('Y-m-d');

			$check = $this->db->query("SELECT * from tbl_delivery_charges where e_date='$e_date' and id !='$edit'  and type='$type'")->row_array();

			if (!empty($check)) {

				$this->session->set_flashdata('err_message', 'Cant Add Delivery Charges Against Same Effective Date !');

				redirect(SURL . 'app/Delivery_charges/');
			}

			$this->mod_common->update_table("tbl_delivery_charges", array("id" => $edit), $adata);

			$res = $this->input->post("id");
		}

		if ($res) {

			$this->session->set_flashdata('ok_message', 'You have successfully added.');

			redirect(SURL . 'app/Delivery_charges/');
		} else {

			$this->session->set_flashdata('err_message', 'Adding Operation Failed.');

			redirect(SURL . 'app/Delivery_charges/');
		}
	}



	public function delete($id)
	{

		$login_user = $this->session->userdata('id');

		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '1055' limit 1")->row_array();

		if ($role['delete'] != 1) {

			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');

			redirect(SURL . 'app/Delivery_charges/index/');
		}



		#-------------delete record--------------#

		$table = "tbl_delivery_charges";

		$where = "id = " . $id . "";

		$delete_area = $this->mod_common->delete_record($table, $where);



		if ($delete_area) {

			$this->session->set_flashdata('ok_message', 'You have successfully deleted.');

			redirect(SURL . 'app/Delivery_charges/index/');
		} else {

			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');

			redirect(SURL . 'app/Delivery_charges/index/');
		}
	}


	public function edit($id)
	{
		$login_user = $this->session->userdata('id');
		$sale_point_ids = $this->db->query("SELECT location FROM tbl_admin WHERE id = '$login_user'")->row_array()['location'];
		if ($sale_point_ids) {
			$sale_point_id_array = explode(',', $sale_point_ids);
			$sale_point_id_list = implode("','", $sale_point_id_array);
			$where_location = "WHERE sale_point_id IN ('$sale_point_id_list')";
		} else {
			$where_location = "";
		}
		$data['salepoint'] = $this->db->query("SELECT * FROM tbl_sales_point $where_location")->result_array();
		$data['record'] = $this->db->query("SELECT * FROM tbl_delivery_charges WHERE id='$id'")->row_array();
		$data['status'] = $this->db->query("SELECT * FROM tbl_company WHERE id = '1'")->row_array();
		$data["filter"] = 'add';
		$this->load->view("app/Delivery_charges/add_charges", $data);
	}
	public function get_zone()
	{
		$edit = $_POST['edit'];
		$location = $_POST['location'];

		error_log("Edit: " . $edit);
		error_log("Location: " . $location);

		$salepoint = $this->db->query("SELECT * from tbl_sales_point where sale_point_id = '$location'")->row_array();

		if (!empty($salepoint)) {
			$zone_ids = explode(',', $salepoint['zone_id']);
			$zones = $this->db->query("SELECT * FROM tbl_zone WHERE id IN (" . implode(',', $zone_ids) . ")")->result_array();
			foreach ($zones as $zone) { ?>
				<option value="<?php echo $zone['id']; ?>" <?php if ($edit == $zone['id']) {
																echo 'selected';
															} ?>><?php echo $zone['zone_name']; ?></option>
<?php }
		}
	}
}
