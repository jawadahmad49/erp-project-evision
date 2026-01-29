<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model(array(
			"mod_user",
			"mod_common"
		));
	}

	public function index()
	{
		$data['user_list'] = $this->mod_user->manage_users();
		// echo $this->db->last_query();
		// echo "<pre>";print_r($data['user_list']) ;exit();
		// $data['name'] =$this->db->query("select sp_name from tbl_sales_point where $")->row_array();
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage User";
		$this->load->view($this->session->userdata('language') . "/user/manage_users", $data);
	}

	public function role($id)
	{
		$login_user = $this->session->userdata('id');

		if ($login_user != 1) {
			redirect(SURL . 'User');
		}

		$where = "id=$id";
		$admin_user = $this->mod_common->select_single_records('tbl_admin', $where, '*');
		if (empty($admin_user)) {

			redirect(SURL . 'User');
		}
		$where_user = "uid=$id";
		$data['rights'] = $this->db->query("select * from tbl_user_rights where  $where_user")->result_array();

		$table = 'tbl_menu';
		$where = array('sts' => 'Active', 'pageid >' => 0, 'pageid <' => 50);
		$data['config_list'] = $this->mod_common->select_array_records($table, '*', $where);

		$where = array('sts' => 'Active', 'pageid >' => 100, 'pageid <' => 201);
		$data['sale_list'] = $this->mod_common->select_array_records($table, '*', $where);

		// $where = array('sts' => 'Active','pageid >'=>701,'pageid <'=>799);
		// $data['return_list'] = $this->mod_common->select_array_records($table,'*',$where);

		$where = array('sts' => 'Active', 'pageid >' => 300, 'pageid <' => 401);
		$data['payment_list'] = $this->mod_common->select_array_records($table, '*', $where);

		$where = array('sts' => 'Active', 'pageid >' => 400, 'pageid <' => 501);
		$data['report_list'] = $this->mod_common->select_array_records($table, '*', $where);
		
		$where = array('sts' => 'Active', 'pageid >' => 500, 'pageid <' => 601);
		$data['misc_list'] = $this->mod_common->select_array_records($table, '*', $where);

		$where = array('sts' => 'Active', 'pageid >' => 600, 'pageid <' => 701);
		$data['purchase_list'] = $this->mod_common->select_array_records($table, '*', $where);

		$where = array('sts' => 'Active', 'pageid >' => 700, 'pageid <' => 750);
		$data['return_list'] = $this->mod_common->select_array_records($table, '*', $where);

		$where = array('sts' => 'Active', 'pageid >' => 200, 'pageid <' => 301);
		$data['partial_list'] = $this->mod_common->select_array_records($table, '*', $where);

		$where = array('sts' => 'Active', 'pageid >' => 800, 'pageid <' => 900);
		$data['Import_list'] = $this->mod_common->select_array_records($table, '*', $where);

		$where = array('sts' => 'Active', 'pageid >' => 900, 'pageid <' => 999);
		$data['dashboard'] = $this->mod_common->select_array_records($table, '*', $where);

		$where = array('sts' => 'Active', 'pageid >' => 1000, 'pageid <' => 1015);
		$data['booking_list'] = $this->mod_common->select_array_records($table, '*', $where);

		$where = array('sts' => 'Active', 'pageid >' => 1100, 'pageid <' => 1140);
		$data['crm_config'] = $this->mod_common->select_array_records($table, '*', $where);
		$where_user = "uid=$id";

		$where = array('sts' => 'Active', 'pageid >' => 1140, 'pageid <' => 1159);

		$data['order'] = $this->mod_common->select_array_records($table, '*', $where);

		////CMR///
		$where = array('sts' => 'Active', 'pageid >' => 50, 'pageid <' => 101);
		$data['crm_config'] = $this->mod_common->select_array_records($table, '*', $where);

		$where = array('sts' => 'Active', 'pageid >' => 1015, 'pageid <' => 1020);
		$data['crm_complaints'] = $this->mod_common->select_array_records($table, '*', $where);

		$where = array('sts' => 'Active', 'pageid >' => 1020, 'pageid <' => 1050);
		$data['crm_lead_gen'] = $this->mod_common->select_array_records($table, '*', $where);

		$where = array('sts' => 'Active', 'pageid >' => 750, 'pageid <' => 760);
		$data['crm_reports'] = $this->mod_common->select_array_records($table, '*', $where);
		////CMR///

		// APP
		$where = array('sts' => 'Active', 'pageid >' => 1050, 'pageid <' => 1065);
		$data['app_config'] = $this->mod_common->select_array_records($table, '*', $where);

		$where = array('sts' => 'Active', 'pageid >' => 1071, 'pageid <' => 1085);
		$data['app_order_management'] = $this->mod_common->select_array_records($table, '*', $where);

		$where = array('sts' => 'Active', 'pageid >' => 1086, 'pageid <' => 1095);
		$data['app_notific_management'] = $this->mod_common->select_array_records($table, '*', $where);

		$where = array('sts' => 'Active', 'pageid >' => 1095, 'pageid <' => 1105);
		$data['app_customer_management'] = $this->mod_common->select_array_records($table, '*', $where);
		// APP


		$data['user_rights'] = $this->mod_common->select_array_records('tbl_user_rights', 'pageid', $where_user);


		$i = 0;
		foreach ($data['user_rights'] as $key => $value) {
			$array_new[$i++] = $value['pageid'];
		}


		$data['user_rights'] = $array_new;
		$data['userid'] = $id;

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Role";
		$data["id"] = $id;
		$this->load->view($this->session->userdata('language') . "/user/user_rights", $data);
	}

	function show_dashboard()
	{
		$user = $_POST['uid'];
		$data['dashboard'] = $_POST['status'];

		$this->db->where('id', $user);
		$upd = $this->db->update('tbl_admin', $data);

		if ($upd) {
			$res = array('success' => "Dashboard is visible to user.");
		} else {
			$res = array('error' => "Dashboard is not visible to user.");
		}

		echo json_encode($res);
	}

	public function add_user()
	{
		$data['name'] = $this->db->query("select * from tbl_sales_point")->result_array();
		//echo "<pre>";print_r($data['name']);exit;
		$company_id = $this->session->userdata('comp_id');

		$where = "id=$company_id";

		$company_user = $this->mod_common->select_single_records('tbl_company', $where, 'no_of_user');

		$company_user['no_of_user'];

		$where = "comp_id=$company_id";

		$total_user = $this->mod_common->get_all_records_nums('tbl_admin', "*", $where);

		$data['remaining_user'] = $company_user['no_of_user'] - $total_user;


		$data["title"] = "Add User";
		$this->load->view($this->session->userdata('language') . "/user/add_user", $data);
	}

	public function update_role()
	{

		$data["title"] = "Update Role";
		$pageid = $this->input->post('pageid');
		$uid = $this->input->post('uid');

		$status = $this->input->post('status');
		$edit = $this->input->post('edit');
		$del = $this->input->post('del');
		$add = $this->input->post('add');
		//echo $add;exit();
		$print = $this->input->post('print');
		$view = $this->input->post('view');

		if ($status == 0) {

			$table = "tbl_user_rights";
			$where = array('uid' => $uid, 'pageid' => $pageid);

			$delete_country = $this->mod_common->delete_record($table, $where);
		} else if ($status == 1) {
			$v = $this->db->query("select * from tbl_user_rights where uid='$uid' and pageid='$pageid'")->row_array();

			if ($v == '') {

				$udata['pageid'] = $pageid;
				$udata['uid'] = $uid;

				$table = 'tbl_user_rights';
				$res = $this->mod_common->insert_into_table($table, $udata);
			}
		}
		if ($edit == 0) {

			$table = "tbl_user_rights";
			$where = array('uid' => $uid, 'pageid' => $pageid);
			$udata['pageid'] = $pageid;
			$udata['uid'] = $uid;

			$udata['edit'] = 0;
			$this->db->where($where);
			$update = $this->db->update($table, $udata);
		} else if ($edit == 1) {
			$table = "tbl_user_rights";
			$where = array('uid' => $uid, 'pageid' => $pageid);
			$udata['pageid'] = $pageid;
			$udata['uid'] = $uid;
			$udata['edit'] = 1;
			$this->db->where($where);
			$update = $this->db->update($table, $udata);
		}
		if ($add == 0) {

			$table = "tbl_user_rights";
			$where = array('uid' => $uid, 'pageid' => $pageid);
			$udata['pageid'] = $pageid;
			$udata['uid'] = $uid;

			$udata['add'] = 0;
			$this->db->where($where);
			$update = $this->db->update($table, $udata);
		} else if ($add == 1) {
			//echo "string";
			$table = "tbl_user_rights";
			$where = array('uid' => $uid, 'pageid' => $pageid);
			$udata['pageid'] = $pageid;
			$udata['uid'] = $uid;
			$udata['add'] = 1;

			$this->db->where($where);
			$s = $this->db->update($table, $udata);
			// pm($s);
		}
		if ($print == 0) {

			$table = "tbl_user_rights";
			$where = array('uid' => $uid, 'pageid' => $pageid);
			$udata['pageid'] = $pageid;
			$udata['uid'] = $uid;

			$udata['print'] = 0;
			$this->db->where($where);
			$update = $this->db->update($table, $udata);
		} else if ($print == 1) {
			$table = "tbl_user_rights";
			$where = array('uid' => $uid, 'pageid' => $pageid);
			$udata['pageid'] = $pageid;
			$udata['uid'] = $uid;
			$udata['print'] = 1;
			$this->db->where($where);
			$update = $this->db->update($table, $udata);
		}
		if ($view == 0) {

			$table = "tbl_user_rights";
			$where = array('uid' => $uid, 'pageid' => $pageid);
			$udata['pageid'] = $pageid;
			$udata['uid'] = $uid;

			$udata['view'] = 0;
			$this->db->where($where);
			$update = $this->db->update($table, $udata);
		} else if ($view == 1) {
			$table = "tbl_user_rights";
			$where = array('uid' => $uid, 'pageid' => $pageid);
			$udata['pageid'] = $pageid;
			$udata['uid'] = $uid;
			$udata['view'] = 1;
			$this->db->where($where);
			$update = $this->db->update($table, $udata);
		}
		if ($del == 0) {

			$table = "tbl_user_rights";
			$where = array('uid' => $uid, 'pageid' => $pageid);
			$udata['pageid'] = $pageid;
			$udata['uid'] = $uid;

			$udata['delete'] = 0;
			$this->db->where($where);
			$update = $this->db->update($table, $udata);
		} else if ($del == 1) {
			$table = "tbl_user_rights";
			$where = array('uid' => $uid, 'pageid' => $pageid);
			$udata['pageid'] = $pageid;
			$udata['uid'] = $uid;
			$udata['delete'] = 1;
			$this->db->where($where);
			$update = $this->db->update($table, $udata);
		}
	}



	public function add()
	{


		if ($this->input->server('REQUEST_METHOD') == 'POST') {

			$company_id = $this->session->userdata('comp_id');

			$where = "id=$company_id";

			$company_user = $this->mod_common->select_single_records('tbl_company', $where, 'no_of_user');

			$company_user['no_of_user'];

			$where = "comp_id=$company_id";

			$total_user = $this->mod_common->get_all_records_nums('tbl_admin', "*", $where);


			if ($total_user >= $company_user['no_of_user']) {
				$this->session->set_flashdata('err_message', 'User Limit completed.');
				redirect(SURL . 'User');
			}


			$udata['loginid'] = trim($this->input->post('loginid'));

			#----check name already exist---------#
			if ($this->mod_user->get_by_title($udata['loginid'])) {
				$this->session->set_flashdata('err_message', 'Login Id Already Exist.');
				redirect(SURL . 'User/add_user');
				exit();
			}


			// "select tbl_company.*,tblmaterial_coding.* , priceconfig.* from tbl_company inner join priceconfig on tbl_company.id = priceconfig.location inner join tblmaterial_coding on tblmaterial_coding.materialcode =priceconfig.item_name "
			$locationArray = $this->input->post('location');

			// If you need to store it as a comma-separated string:
			$udata['location'] = is_array($locationArray) ? implode(',', $locationArray) : '';
			// $udata['location'] = $this->input->post('location');
			$udata['admin_name'] = $this->input->post('admin_name');
			$udata['email'] = $this->input->post('email');
			$udata['admin_pwd'] = base64_encode(trim($this->input->post('admin_pwd')));
			$udata['status'] = $this->input->post('status');
			$udata['address'] = $this->input->post('address');
			$udata['comp_id'] = $company_id;

			$table = 'tbl_admin';
			$res = $this->mod_common->insert_into_table($table, $udata);

			if ($res) {
				$this->session->set_flashdata('ok_message', 'You have succesfully added.');
				redirect(SURL . 'User/');
			} else {
				$this->session->set_flashdata('err_message', 'Adding Operation Failed.');
				redirect(SURL . 'User/');
			}
		}
	}

	public function edit($id)
	{
		if ($id) {
			$table = 'tbl_admin';
			$where = "id='$id'";
			$data['user'] = $this->mod_common->select_single_records($table, $where);
			$data['name'] = $this->db->query("select * from tbl_sales_point")->result_array();
			//pm($data['user']);
			$this->load->view($this->session->userdata('language') . "/user/edit", $data);
		}
	}

	public function update()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			// Handle multiple locations as an array
			$locationArray = $this->input->post('location');

			// If you need to store it as a comma-separated string:
			$cdata['location'] = is_array($locationArray) ? implode(',', $locationArray) : '';

			$cdata['admin_name'] = trim($this->input->post('admin_name'));
			$cdata['email'] = trim($this->input->post('email'));
			$cdata['admin_pwd'] = base64_encode(trim($this->input->post('admin_pwd')));
			$cdata['loginid'] = trim($this->input->post('loginid'));
			$cdata['status'] = $this->input->post('status');
			$cdata['address'] = $this->input->post('address');
			$id = $_POST['id'];

			// Check if the login ID already exists
			if ($this->mod_user->edit_by_title($cdata['loginid'], $id)) {
				$this->session->set_flashdata('err_message', 'Login Id Already Exist.');
				redirect(SURL . 'User/edit/' . $id);
				exit();
			}

			// Update the database
			$where = "id='$id'";
			$table = 'tbl_admin';
			$res = $this->mod_common->update_table($table, $where, $cdata);
			if ($res) {
				$this->session->set_flashdata('ok_message', 'You have successfully updated.');
				redirect(SURL . 'User/');
			} else {
				$this->session->set_flashdata('err_message', 'Update Operation Failed.');
				redirect(SURL . 'User/');
			}
		}
	}
	public function delete($id)
	{

		// if ($this->mod_user->under_area($id)) {
		// 	$this->session->set_flashdata('err_message', 'There are areas under user you can not delete it.');
		// 	redirect(SURL . 'User/');
		// 	exit();
		// } 
		#-------------delete record--------------#
		$table = "tbl_admin";
		$where = "id = '" . $id . "'";
		$delete_country = $this->mod_common->delete_record($table, $where);

		if ($delete_country) {
			$this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
			redirect(SURL . 'User/');
		} else {
			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
			redirect(SURL . 'User/');
		}
	}

	public function getCountryName()
	{
	}
}
