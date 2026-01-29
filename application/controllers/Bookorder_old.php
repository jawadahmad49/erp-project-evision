<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BookOrder extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model(array(
			"mod_customer", "mod_common", "mod_bookorder"
		));
	}
	public function index()
	{

		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		if (isset($_POST['submit'])) {
			$from_date = date("Y-m-d", strtotime($_POST['from']));
			$to_date = date("Y-m-d", strtotime($_POST['to']));
		} else {
			$from_date = date('Y-m-d');
			$to_date = date('Y-m-d');
		}
		$login_user = $this->session->userdata('id');

		$data['bookorder_list'] = $this->db->query("select * from tbl_orderbooking where date between'$from_date'and'$to_date' and salepoint='$sale_point_id' order by id desc")->result_array();

		//$data['bookorder_list'] = $this->mod_bookorder->manage_bookorder($from_date,$to_date,$sale_point_id,'Booked,Delivered');

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Book Order";
		$this->load->view("en/bookorder/bookorder", $data);
	}

	public function add_bookorder()
	{
		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();

		$table = 'tblmaterial_coding';
		$data['item_list'] = $this->mod_common->get_all_records($table, "*");

		$table = 'tbl_sales_point';
		$data['salepoint_list'] = $this->mod_common->get_all_records($table, "*");

		$table = 'tbl_city';
		$data['city_list'] = $this->mod_common->get_all_records($table, "*");
		$data["filter"] = 'add';

		$table = 'tblacode';
		$where = array('general' => 3001000000, 'ac_status' => "Active");
		$data['salesman'] = $this->mod_common->select_array_records($table, "*", $where);

		//showing bellow direct customers only
		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$general = $this->db->query("select customer_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['customer_code'];
		$data['customer_list'] = $this->db->query("select * from tblacode where general='$general'")->result_array();

		// showing salepoint below
		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
		$data['sale_point_id'] = $sale_point_id = $fix_code['sale_point_id'];

		if ($sale_point_id != '') {
			$where_sale_point_id = "and sale_point_id='$sale_point_id'  ";
		} else {
			$where_sale_point_id = "";
		}
		$data['location'] = $this->db->query("select * from tbl_sales_point where sale_point_id in (select sale_point_id from tbl_code_mapping) $where_sale_point_id")->result_array();

		$data["title"] = "Add Book Order";
		$this->load->view("en/bookorder/add_bookorder", $data);
	}

	public function get_cities()
	{
		$salepoint_id = $_POST["salepoint_id"];
		$get_city_id = $this->db->get_where('tbl_sales_point', array('sale_point_id' => $salepoint_id))->row();

		//City
		$table = 'tbl_city';
		$where = array('city_id' => $get_city_id->city_id);
		$city = $this->db->get_where($table, $where)->result();

		//Area
		$table = 'tbl_area';
		$where = array('area_id' => $get_city_id->area_id);
		$area = $this->db->get_where($table, $where)->result();

		//Salesman
		$table = 'tbl_admin';
		$where = array('loccode' => $salepoint_id);
		$salesman = $this->db->get_where($table, $where)->result();

		$response = array('city' => $city, 'area' => $area, 'salesman' => $salesman);
		echo json_encode($response);
	}

	function get_customer_detail()
	{
		$id = $_POST['u_id'];
		$data = $this->db->get_where('tblacode', array('acode' => $id))->result_array();

		$get_loc = $this->db->get_where('tbl_sales_point', array('sale_point_id' => $data[0]['loccode']))->result_array();

		$response = array('data' => $data, 'loc' => $get_loc);

		echo json_encode($response);
	}


	public function add()
	{

		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$add =  $this->mod_bookorder->add_bookorder($this->input->post());


			if ($add) {
				$this->session->set_flashdata('ok_message', '- Added Successfully!');
				redirect(SURL . 'Bookorder/');
			} else {
				$this->session->set_flashdata('err_message', '- Error in adding please try again!');
				redirect(SURL . 'Bookorder/');
			}
		}
	}

	public function delete($id)
	{
		#-------------delete record--------------#
		$table = "tbl_orderbooking";
		$where = "id = '" . $id . "'";
		$delete = $this->mod_common->delete_record($table, $where);

		if ($delete) {
			$this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
			redirect(SURL . 'Bookorder/');
		} else {
			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
			redirect(SURL . 'Bookorder/');
		}
	}
	public function edit($id)
	{
		if ($id) {
			$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
			$table = 'tblmaterial_coding';
			$data['item_list'] = $this->mod_common->get_all_records($table, "*");
			$table = 'tbl_orderbooking';
			$where = "id='$id'";
			$data['single_edit'] = $this->mod_common->select_single_records($table, $where);
			$data['edit_list'] = $this->mod_bookorder->edit_bookorder($id);
			$data["filter"] = '';
			#----load view----------#
			$data["title"] = "Update Book Order";
			$this->load->view("en/bookorder/edit", $data);
		}
	}

	public function update()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {

			$add_bookorder =  $this->mod_bookorder->update_bookorder($this->input->post());
			//echo "<pre>";print_r($add_bookorder);exit;
			if ($add_bookorder || $add_bookorder == 0) {
				$this->session->set_flashdata('ok_message', '- Updated Successfully!');
				redirect(SURL . 'Bookorder/');
			} else {
				$this->session->set_flashdata('err_message', '- Error in updating please try again!');
				redirect(SURL . 'Bookorder/');
			}
		}
	}

	function record_delete()
	{
		#-------------delete record ajax--------------#
		$table = "tbl_orderbooking_detail";
		$deleteid =	$this->input->post('deleteid');
		$where = "id = '" . $deleteid . "'";
		$delete_goods = $this->mod_common->delete_record($table, $where);

		if ($delete_goods) {
			echo '1';
			exit;
		} else {
			echo '0';
			exit;
		}
	}

	public function detail($id)
	{

		if ($id) {
			$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
			$table = 'tblmaterial_coding';
			$data['item_list'] = $this->mod_common->get_all_records($table, "*");
			$table = 'tbl_issue_goods';
			$where = "issuenos='$id'";
			$data['single_edit'] = $this->mod_common->select_single_records($table, $where);

			$data['edit_list'] = $this->mod_bookorder->edit_bookorder($id);

			$data["filter"] = '';
			#----load view----------#
			$data["title"] = "Customer Invoice";
			$this->load->view("en/bookorder/single", $data);
		}
	}

	public function get_Diret_Customer()
	{
		$sagment = $this->input->post('sagment');

		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

		if ($sale_point_id == '0') {
			$where_sale_point_id = "";
		} else {
			$where_sale_point_id = "and sale_point_id='$sale_point_id'";
		}

		$diect_customer_detail = $this->db->query("select id,name,cell_no from tbl_direct_customer where type='$sagment' $where_sale_point_id")->result_array();
		$direct_customer = $_SESSION["direct_customer"];


?>
		<?php
		foreach ($diect_customer_detail as $key => $data) {
		?>
			<option value="<?php echo $data['id']; ?>" <?php if ($data['id'] == $direct_customer) { ?> selected <?php } ?>><?php echo ucwords($data['cell_no'] . " " . $data['name']); ?></option>

		<?php }
	}


	public function get_dcustomer_details()
	{
		$d_customer = $this->input->post('d_customer');

		$direct_customer_data = $this->db->query("SELECT cell_no,address FROM tbl_direct_customer WHERE id = '$d_customer'")->row_array();

		if ($direct_customer_data['cell_no'] || $direct_customer_data['address']) {
			echo $direct_customer_data['cell_no'] . '-' . $direct_customer_data['address'];
		} else {
			echo 0;
		}
	}


	public function get_branch()
	{

		$customer = $this->input->post('customer');
		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		if ($sale_point_id == '0') {
			$where_sale_point_id = "";
		} else {
			$where_sale_point_id = "and sale_point_id='$sale_point_id'";
		}

		$customer_code = $this->db->query("select * from tblsledger where acode='$customer' $where_sale_point_id")->result_array();
		$scode = $_SESSION["scode"];

		if ($customer_code[0]['scode'] > 0) {

		?>
			<?php
			foreach ($customer_code as $key => $data) {
			?>

				<option value="<?php echo $data['scode']; ?>" <?php if ($data['scode'] == $scode) { ?> selected <?php } ?>><?php echo ucwords($data['stitle']); ?></option>

<?php }
		} else {
			echo 0;
		}
	}

	public function get_type()
	{

		$customer = $this->input->post('customer');

		$record = $this->db->query("select * from tblacode where acode='$customer'")->row_array();
		echo json_encode($record);
	}
}
