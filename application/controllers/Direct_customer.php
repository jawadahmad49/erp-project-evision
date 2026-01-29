<?php

defined('BASEPATH') or exit('No direct script access allowed');



class Direct_customer extends CI_Controller

{



	public function __construct()

	{

		parent::__construct();



		$this->load->model(array(

			"mod_customer", "mod_common"

		));

	}

	public function index()

	{

		$login_user = $this->session->userdata('id');

		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];



		if ($sale_point_id == '0') {

			$where_location = "";

		} else {

			$where_location = "where sale_point_id='$sale_point_id'";

		}

		$data['customer_list'] = $this->db->query("select * from tbl_direct_customer $where_location order by id desc")->result_array();



		$data["filter"] = '';

		#----load view----------#

		$data["title"] = "Manage Direct Customers";

		$this->load->view($this->session->userdata('language') . "/DirectCustomer/manage_customer", $data);

	}



	public function add_customer()

	{

		$login_user = $this->session->userdata('id');

		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '14' limit 1")->row_array();

		if ($role['add'] != 1) {

			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');

			redirect(SURL . 'Direct_customer/index/');

		}

		$table = 'tbl_country';

		$data['country_list'] = $this->mod_common->get_all_records($table, "*");



		$table = 'tbl_city';

		$data['city_list'] = $this->mod_common->get_all_records($table, "*");



		$login_user = $this->session->userdata('id');

		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

		$fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();

		$data['sale_point_id'] = $sale_point_id = $fix_code['sale_point_id'];



		if ($sale_point_id != '') {

			$where_sale_point_id = "and sale_point_id='$sale_point_id' ";

		} else {

			$where_sale_point_id = "";

		}

		$data['location'] = $this->db->query("select * from tbl_sales_point where sale_point_id in (select sale_point_id from tbl_code_mapping) $where_sale_point_id")->result_array();



		$data["filter"] = 'add';

		$this->load->view($this->session->userdata('language') . "/DirectCustomer/add_customer", $data);

	}



	public function add()

	{

		//echo "<pre>";print_r($_POST);exit;

		// $login_user=$this->session->userdata('id');

		//     $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

		$adata['id'] = trim($_POST["id"]);



		$adata['sale_point_id'] = trim($_POST["location"]);

		$adata['name'] = trim($_POST["customername"]);

		$adata['city'] = trim($_POST["city"]);

		$adata['area'] = trim($_POST["area"]);

		$adata['cell_no'] = trim($_POST["cellno"]);

		$adata['address'] = trim($_POST["address"]);

		$adata['cell_no2'] = trim($_POST["cell_no2"]);

		$adata['ptcl_no'] = $_POST["ptcl"];

		$adata['purpose'] = trim($_POST["purpose"]);

		$adata['family_members'] = trim($_POST["fmember"]);

		$adata['consumption_days'] = trim($_POST["estDays"]);

		$adata['reminder'] = trim($_POST["SRBD"]);

		$adata['type'] = trim($_POST["ctype"]);



		$table = 'tbl_direct_customer';

		if (empty($adata['id'])) {



			$res = $this->mod_common->insert_into_table($table, $adata);

		} else {



			$id = $this->input->post("id");

			$this->mod_common->update_table("tbl_direct_customer", array("id" => $this->input->post("id")), $adata);

			//$this->db->query("delete from tbl_direct_customer where form_id='$last_id'");

			$res = $this->input->post("id");

		}



		if ($res) {

			$this->session->set_flashdata('ok_message', 'You have successfully added.');

			redirect(SURL . 'Direct_customer/');

		} else {

			$this->session->set_flashdata('err_message', 'Adding Operation Failed.');

			redirect(SURL . 'Direct_customer/');

		}

	}



	public function delete($id)

	{



		$login_user = $this->session->userdata('id');

		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '9' limit 1")->row_array();

		if ($role['delete'] != 1) {

			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');

			redirect(SURL . 'Direct_customer/index/');

		}



		if ($this->mod_customer->under_items($id)) {

			$this->session->set_flashdata('err_message', 'Sale is recorded for this customer , you can not delete it.');

			redirect(SURL . 'Direct_customer/');

		}



		#-------------delete record--------------#

		$table = "tbl_direct_customer";

		$where = "id = " . $id . "";

		$delete_area = $this->mod_common->delete_record($table, $where);





		if ($delete_area) {

			$this->session->set_flashdata('ok_message', 'You have successfully deleted.');

			redirect(SURL . 'Direct_customer/index/');

		} else {

			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');

			redirect(SURL . 'Direct_customer/index/');

		}

	}



	public function edit($id)

	{



		$login_user = $this->session->userdata('id');

		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '14' limit 1 ")->row_array();

		if ($role['edit'] != 1) {

			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');

			redirect(SURL . 'Direct_customer/index/');

		}

		$table = 'tbl_country';

		$data['country_list'] = $this->mod_common->get_all_records($table, "*");



		$data['city'] = $this->mod_customer->edit_record($id);

		$data['record'] = $this->db->query("select * from tbl_direct_customer where id='$id' ")->row_array();

		$where_id = array('country_id' => $data['city']['country_id']);

		$table = 'tbl_city';

		$data['city_list'] = $this->mod_common->select_array_records($table, "*");





		$data['area'] = $this->mod_customer->edit_record($id);

		$data['record'] = $this->db->query("select * from tbl_direct_customer where id='$id' ")->row_array();

		$where_id = array('city_id' => $data['area']['city_id']);

		$table = 'tbl_area';

		$data['area_list'] = $this->mod_common->select_array_records($table, "*", $where_id);



		$table = 'tblacode';

		$where = "acode='$id'";

		$data['customer'] = $this->mod_common->select_single_records($table, $where);





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







		$data["filter"] = 'add';

		//echo "<pre>";print_r($data);

		$this->load->view($this->session->userdata('language') . "/DirectCustomer/add_customer", $data);

	}





	function get_city()

	{

		$table = 'tbl_city';

		$country_id = $this->input->post('country_id');

		$where = array('country_id' => $country_id);

?> <option value="All">All</option><?php



									$data['city_list'] = $this->mod_common->select_array_records($table, "*", $where);



									if ($data['city_list']) { ?>

			<?php

										foreach ($data['city_list'] as $key => $value) {

			?>



				<option value="<?php echo  $value['city']; ?>"><?php echo  $value['city']; ?></option>



			<?php }

									}

								}



								function get_area()

								{





									$edit_area = $this->input->post('edit_area');



									$table = 'tbl_area';

									$city_id =	$this->input->post('city_id');

									$where = array('city_id' => $city_id);



									$data['area_list'] = $this->mod_common->select_array_records($table, "*", $where);



									foreach ($data['area_list'] as $key => $value) {

			?>

			<option value="<?php echo  $value['area_id']; ?>" <?php if ($edit_area == $value['area_id']) {

																	echo "selected";

																} ?>><?php echo  $value['aname']; ?></option>



<?php }

								}

							}

