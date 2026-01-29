<?php
defined('BASEPATH') or exit('No direct script access allowed');
//require_once APPPATH . 'vendor\PHPExcel\Classes\PHPExcel.php';

class App_feedback extends CI_Controller
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
			$where_master = "WHERE master_id IN ('$sale_point_id_list')";
		} else {
			$where_master = "";
		}
		// $data['zone_lists'] = $this->db->query("SELECT * FROM tbl_sales_point_detail $where_master")->result_array();
		// echo "SELECT * FROM `tbl_place_order` WHERE sale_point_id IN ($sale_point_ids)";exit;
		$data['zone_lists'] = $this->db->query("SELECT * FROM `tbl_place_order` WHERE sale_point_id IN ($sale_point_ids)")->result_array();
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Feedback Report";
		$this->load->view("app/App_feedback/search", $data);
	}

	public function details()
	{

		if ($this->input->server('REQUEST_METHOD') == 'POST') {

			$data['fromdate'] = $fromdate = $this->input->post('from_date');
			$data['todate'] = $todate = $this->input->post('to_date');
			$data['user'] = $user = $this->input->post('user');
			$data['zone_id'] = $zone_id = $this->input->post('zone_id');
			$data['sale_point_id'] = $sale_point_id = $this->input->post('salepoint');
			// if ($zone_id != 'All') {
			// 	$where_zone_id = "and area_id='$zone_id'";
			// } else {
			// 	$where_zone_id = "";
			// }
			if ($user != 'All') {
				$where_user = "and userid='$user'";
			} else {
				$where_user = "";
			}
			$data['report'] = $this->db->query("SELECT * from tbl_feedback_app INNER JOIN tbl_user On tbl_feedback_app.userid = tbl_user.id where created_date BETWEEN '$fromdate' and '$todate' $where_user $where_zone_id")->result_array();


			$this->load->view("app/App_feedback/new", $data);
			if (empty($data['report'])) {
				$this->session->set_flashdata('err_message', 'No Record Found.');
				redirect(SURL . 'app/App_feedback/');
			}
		}
	}
	function user_zones()
	{
		$user = $this->input->post('user');
		$login_user = 1;

		if ($login_user == 1) {
			$where = '';
			$all = '<option value="All">All</option>';
		} else {
			$sale_point_ids = $this->db->query("SELECT location FROM tbl_admin WHERE id='$user'")->row_array()['location'];
			$sale_point_ids = implode(',', array_map('intval', explode(',', $sale_point_ids)));
			$where = "where sale_point_id IN ($sale_point_ids)";
			$all = '';
		}

		$zone_points = $this->db->query("SELECT * FROM tbl_place_order $where")->result_array();
		$zone_points = $this->db->query("SELECT * FROM tbl_zone where id='id'")->result_array();
		echo $all;
		foreach ($zone_points as $key => $value) { ?>
			<option value="<?php echo $value['sale_point_id']; ?>" <?php if ($sale_Point == $value['sale_point_id']) {
				echo "selected";
			} ?>><?php echo $value['sp_name']; ?></option>
		<?php }
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
