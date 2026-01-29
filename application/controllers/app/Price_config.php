<?php

defined('BASEPATH') or exit('No direct script access allowed');



class Price_config extends CI_Controller

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

			$where_sale_point_id = "";

		} else {

			$where_sale_point_id = "where sale_point_id='$sale_point_id'";

		}

		$data['price'] = $this->db->query("select * from priceconfig $where_sale_point_id order by id desc")->result_array();



		$data["filter"] = 'add';



		$data["filter"] = '';

		#----load view----------#

		$data["title"] = "Manage Price Configuration";

		$this->load->view("app/Price_config/manage_price", $data);

	}



	public function add_price()

	{

		$login_user = $this->session->userdata('id');

		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '1054' limit 1")->row_array();

		if ($role['add'] != 1) {

			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');

			redirect(SURL . 'app/Price_config/index/');

		}

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

		$table = 'tblmaterial_coding';

		$data['item_list'] = $this->mod_common->get_all_records($table, "*");

		$data["filter"] = 'add';

		$data["title"] = "Price Configuration";



		$this->load->view("app/Price_config/add", $data);

	}



	public function add()

	{

		//echo "<pre>";print_r($_POST);exit;

		// $data['datas'] = $this->mod_customer->accountcode_forcustomer();

		$Date = date("Y-m-d");

		$adata['sale_point_id'] = trim($_POST["location"]);

		$adata['price'] = trim($_POST["price"]);

		$adata['status'] = trim($_POST["status"]);

		$adata['date'] = trim($_POST["date"]);

		// echo "<pre>";print_r($_POST);exit;

		$chkrecord = $this->db->query("select * from priceconfig where date='$Date' and id='$id'");

		//echo "select * from priceconfig where date='$Date' and id='$id'";

		if ($chkrecord->num_rows() > 0) {









			redirect("/Price_config/");

		}



		$table = 'priceconfig';

		$res = $this->mod_common->insert_into_table($table, $adata);



		if ($res) {

			$this->session->set_flashdata('ok_message', 'You have successfully added.');

			redirect(SURL . 'app/Price_config/');

		} else {

			$this->session->set_flashdata('err_message', 'Adding Operation Failed.');

			redirect(SURL . 'app/Price_config/');

		}

	}



	public function delete($id)

	{



		$login_user = $this->session->userdata('id');

		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '1054' limit 1")->row_array();

		if ($role['delete'] != 1) {

			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');

			redirect(SURL . 'app/Price_config/index/');

		}



		#-------------delete record--------------#

		$table = "priceconfig";

		$where = "id = '" . $id . "'";

		$delete_area = $this->mod_common->delete_record($table, $where);



		if ($delete_area) {

			$this->session->set_flashdata('ok_message', 'You have successfully deleted.');

			redirect(SURL . 'app/Price_config/');

		} else {

			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');

			redirect(SURL . 'app/Price_config/');

		}

	}



	public function edit($id)

	{



		$login_user = $this->session->userdata('id');

		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '1054' limit 1")->row_array();

		if ($role['edit'] != 1) {

			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');

			redirect(SURL . 'app/Price_config/index/');

		}



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

		$table = 'tblmaterial_coding';

		$data['item_list'] = $this->mod_common->get_all_records($table, "*");

		$data['city_list'] = $this->db->query("select * from  priceconfig where id='$id'")->row_array();

		//echo "<pre>";print_r(  $data['city_list'] );die; 







		$data["filter"] = 'update';

		//echo "<pre>";print_r(  $data['city_list'] );die;

		$this->load->view("app/Price_config/edit", $data);

	}

	public function update()

	{



		$adata['sale_point_id'] = trim($_POST["location"]);

		$adata['price'] = trim($_POST["price"]);

		$adata['status'] = trim($_POST["status"]);

		$adata['date'] = trim($_POST["date"]);

		// echo "<pre>";print_r($_POST);exit;

		$id = trim($_POST["id"]);

		$where = "id='$id'";



		$table = 'priceconfig';

		$res = $this->mod_common->update_table($table, $where, $adata);



		if ($res) {

			$this->session->set_flashdata('ok_message', 'You have successfully updated.');

			redirect(SURL . 'app/Price_config/');

		} else {

			$this->session->set_flashdata('err_message', 'Adding Operation Failed.');

			redirect(SURL . 'app/Price_config/');

		}

	}

}

