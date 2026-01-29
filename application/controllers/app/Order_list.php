<?php
defined('BASEPATH') or exit('No direct script access allowed');
//require_once APPPATH . 'vendor\PHPExcel\Classes\PHPExcel.php';

class Order_list extends CI_Controller
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
		if ($sale_point_ids) {
			$where_master = "WHERE master_id IN ('$sale_point_id_list')";
		} else {
			$where_master = "";
		}
		// $data['zone_lists'] = $this->db->query("SELECT * FROM tbl_sales_point_detail $where_master")->result_array();

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Order Report";
		$this->load->view("app/Order_list/search", $data);
	}
	public function get_zone()
	{
		$edit = $_POST['edit'];
		$salepoint = $_POST['salepoint'];
		$salepoint = $this->db->query("SELECT * from tbl_sales_point where sale_point_id = '$salepoint'")->row_array();

		if (!empty($salepoint)) {
			$zone_ids = $salepoint['zone_id'];
			$zones = $this->db->query("SELECT * FROM tbl_zone WHERE id IN ($zone_ids)")->result_array();
?>
			<option value="All">All Zones</option>
			<?php
			foreach ($zones as $zone) { ?>
				<option value="<?php echo $zone['id']; ?>" <?php if ($edit == $zone['zone_name']) {
																echo 'selected';
															} ?>><?php echo $zone['zone_name']; ?></option>
			<?php }
		}
	}
	public function details()
	{

		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			// echo pm($_POST);exit;
			$data['fromdate'] = $fromdate = $this->input->post('from_date');
			$data['todate'] = $todate = $this->input->post('to_date');
			$data['user'] = $user = $this->input->post('user');
			$data['zone_id'] = $zone_id = $this->input->post('zone_id');
			$data['sale_point_id'] = $sale_point_id = $this->input->post('salepoint');
			if ($zone_id != 'All') {
				$where_zone_id = "and area_id='$zone_id'";
			} else {
				$where_zone_id = "";
			}
			if ($user != 'All') {
				$where_user = "and userid='$user'";
			} else {
				$where_user = "";
			}
			if ($sale_point_id != 'All') {
				$where_sale_point_id = "and sale_point_id='$sale_point_id'";
			} else {
				$where_sale_point_id = "";
			}
			// echo "SELECT * from tbl_place_order where date BETWEEN '$fromdate' and '$todate' $where_user $where_zone_id  $where_sale_point_id order by id asc";exit;
			$data['report'] = $this->db->query("SELECT * from tbl_place_order where date BETWEEN '$fromdate' and '$todate' $where_user $where_zone_id  $where_sale_point_id order by id asc")->result_array();


			$this->load->view("app/Order_list/new", $data);
			if (empty($data['report'])) {
				$this->session->set_flashdata('err_message', 'No Record Found.');
				redirect(SURL . 'app/Order_list/');
			}
		}
	}

	function get_sale_point()
	{
		$login_user = $this->session->userdata('id');
		// $user = $this->input->post('user');
		$sale_Point = $this->input->post('sale_Point');
		$table = 'tbl_sales_point';

		if ($login_user == 1) {
			echo "asdad";
			exit;
			$where = '';
			// $all = '<option value="All">All</option>';
			$all = '';
		} else {
			$sale_point_id = $this->db->query("SELECT location from tbl_admin where id='$login_user'")->row_array()['location'];
			$where = "sale_point_id='$sale_point_id'";
			$all = '';
		}

		$sale_point_detail = $this->mod_common->select_array_records($table, "*", $where);
		echo $all;
		foreach ($sale_point_detail as $key => $value) { ?>

			<option value="<?php echo  $value['sale_point_id']; ?>" <?php if ($sale_Point == $value['sale_point_id']) {
																		echo "selected";
																	} ?>><?php echo  $value['sp_name']; ?></option>

<?php }
	}
}
