<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notification extends CI_Controller
{
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array(
			"Mod_notification1",
			"mod_common"
		));
	}
	public function index()
	{
		$today = date('Y-m-d');
		$expiry_notification = $this->db->query("SELECT * FROM `tbl_notification` where expiry_date='$today'")->result_array();
		foreach ($expiry_notification as $key => $value) {
			$transid = $value['transid'];
			$this->db->query("UPDATE tbl_notification set sts='Ended' where transid='$transid'");
		}
		$data['notification_list'] = $this->Mod_notification1->manage_notification();
		$data['notification_show'] = $this->db->query("SELECT transid FROM tbl_notification WHERE app = 'show'")->row_array()['transid'];
		$data['notification_list'] = $this->db->query("SELECT * FROM tbl_notification")->result_array();
		$data["filter"] = '';
		// #----load view----------#
		$data["title"] = "Manage Notifications";
		$this->load->view("app/Notification/manage_notification", $data);
	}
	function show_app()
	{
		$user = $_POST['uid'];
		$data['app'] = $_POST['status'];

		$this->db->where('transid', $user);
		$upd = $this->db->update('tbl_notification', $data);

		if ($upd) {
			$res = array('success' => "App is visible to user.");
		} else {
			$res = array('error' => "App is not visible to user.");
		}

		echo json_encode($res);
	}
	public function add_notification()
	{
		$data['zone_list'] = $this->db->query("SELECT * from tbl_zone where status = 'Active'")->result_array();
		$data['location'] = $this->db->query("SELECT * from tbl_sales_point where sts = 'Active'")->result_array();
		$this->load->view("app/Notification/add_notification", $data);
	}
	public function add()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$add = $this->Mod_notification1->add_notification($this->input->post());

			if ($add) {
				$firebase_error = $this->session->flashdata('firebase_error');
				$firebase_success = $this->session->flashdata('firebase_success');
				$no_tokens = $this->session->flashdata('no_tokens');

				if ($firebase_error) {
					$this->session->set_flashdata('ok_message', 'Notification added but push notifications failed: ' . $firebase_error);
				} elseif ($no_tokens) {
					$this->session->set_flashdata('ok_message', 'Notification added (no devices registered for push notifications)');
				} elseif ($firebase_success) {
					$this->session->set_flashdata('ok_message', 'Notification added successfully. ' . $firebase_success);
				} else {
					$this->session->set_flashdata('ok_message', 'Notification added successfully with push notifications sent');
				}
				redirect(SURL . 'app/Notification/');
			} else {
				$this->session->set_flashdata('err_message', 'Adding operation failed.');
				redirect(SURL . 'app/Notification/');
			}
		}

		$data["filter"] = 'notification';
		$data["title"] = "notification";
		$this->load->view("app/notification/add_notification", $data);
	}
	public function edit($id)
	{
		if ($id) {
			// Get the notification row
			$data['notification'] = $this->mod_common
				->select_single_records('tbl_notification', "transid='$id'");

			// Parse CSV of zone IDs saved in tbl_notification.zone_id
			$zoneIdsCsv = isset($data['notification']['zone_id']) ? $data['notification']['zone_id'] : '';
			$locationIdsCsv = isset($data['notification']['location_id']) ? $data['notification']['location_id'] : '';
			$assigned_location_ids = [];
			if (!empty($locationIdsCsv)) {
				$assigned_location_ids = array_map(
					'intval',
					array_filter(array_map('trim', explode(',', $locationIdsCsv)))
				);
			}
			$data['assigned_location_ids'] = $assigned_location_ids;
			$assigned_zone_ids = [];
			if (!empty($zoneIdsCsv)) {
				$assigned_zone_ids = array_map(
					'intval',
					array_filter(array_map('trim', explode(',', $zoneIdsCsv)))
				);
			}
			$data['assigned_zone_ids'] = $assigned_zone_ids;
			// Load all zones for dropdown
			$data['zone_list'] = $this->db
				->select('id, zone_name')
				->from('tbl_zone')
				->order_by('zone_name', 'ASC')
				->get()
				->result_array();
			$data['location'] = $this->db->query("SELECT * from tbl_sales_point where sts = 'Active'")->result_array();
			$data['target_mode'] = $data['notification']['target_mode'];
			$scheduledDate = date("Y-m-d", $data['notification']['scheduled_at']);
			$scheduledTime = date('H:i', $data['notification']['scheduled_at']);
			$data['scheduled_date'] = $scheduledDate;
			$data['scheduled_time'] = $scheduledTime;
			$this->load->view("app/Notification/edit", $data);
		}
	}
	public function update()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$update = $this->Mod_notification1->update_notification($this->input->post());
			if ($update) {
				$firebase_error = $this->session->flashdata('firebase_error');
				$firebase_success = $this->session->flashdata('firebase_success');
				$no_tokens = $this->session->flashdata('no_tokens');

				if ($firebase_error) {
					$this->session->set_flashdata('ok_message', 'Notification updated but push notifications failed: ' . $firebase_error);
				} elseif ($no_tokens) {
					$this->session->set_flashdata('ok_message', 'Notification updated (no devices registered for push notifications)');
				} elseif ($firebase_success) {
					$this->session->set_flashdata('ok_message', 'Notification updated successfully. ' . $firebase_success);
				} else {
					$this->session->set_flashdata('ok_message', 'Notification updated successfully with push notifications sent');
				}
				redirect(SURL . 'app/Notification/');
			} else {
				$this->session->set_flashdata('err_message', 'Update operation failed.');
				redirect(SURL . 'app/Notification/add_notification');
			}
		}
	}
	public function delete($promo)
	{
		#-------------delete record--------------#
		$table = "tbl_notification";
		$where = "transid = '" . $promo . "'";
		$delete_country = $this->mod_common->delete_record($table, $where);
		if ($delete_country) {
			$this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
			redirect(SURL . 'app/Notification/');
		} else {
			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
			redirect(SURL . 'app/Notification/');
		}
	}
	public function delete_token()
	{
		#-------------delete record--------------#
		$table = "tbl_token";
		$where = "1 = 1";
		$delete_country = $this->mod_common->delete_record($table, $where);
		if ($delete_country) {
			$this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
			redirect(SURL . 'notification/');
		} else {
			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
			redirect(SURL . 'notification/');
		}
	}
	public function get_token()
	{
		#-------------delete record--------------#
		$data_tokens = $this->db->query("SELECT * FROM tbl_token")->result_array();

		if ($data_tokens) {
			$this->session->set_flashdata('ok_message', pm($data_tokens));
			redirect(SURL . 'notification/');
		} else {
			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
			redirect(SURL . 'notification/');
		}
	}
	function send_message()
	{
		$title = $this->input->post("title");
		$short = $this->input->post("short");
		$message = "just a test";
		$content = array(
			"en" => "$short"
		);
		// $fields = array(
		// 	'app_id' => "be4a92de-bfe3-4cc4-9a9c-be96773bbd2f",
		// 	//'filters' => array(array("field" => "tag", "key" => "user_id", "relation" => "=", "value" => "$user_id")),
		// 	'included_segments' => array('All'),
		// 	'headings' => array("en" => "$title"),
		// 	'large_icon' => 'https://evtsolutionz.com/lpgapp/icon/company.png',
		// 	'chrome_web_image' => 'https://cdn4.iconfinder.com/data/icons/iconsimple-logotypes/512/github-512.png',
		// 	'contents' => $content
		// );
		// $fields = json_encode($fields);
		// // print("\nJSON sent:\n");
		// // print($fields);
		// $ch = curl_init();
		// curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		// curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		// 	'Content-Type: application/json; charset=utf-8',
		// 	'Authorization: Basic ZmM3NTU4OTItYmE3MS00ZWFkLTgzOWUtMzdiZGIzZWNlOWNi'
		// ));
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		// curl_setopt($ch, CURLOPT_HEADER, FALSE);
		// curl_setopt($ch, CURLOPT_POST, TRUE);
		// curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		// $response = curl_exec($ch);
		// curl_close($ch);
		// if ($response) {
		if ($this->input->server('REQUEST_METHOD') == 'POST') {

			$add = $this->Mod_notification1->add_notification($this->input->post());

			if ($add) {
				$this->session->set_flashdata('ok_message', 'You have succesfully added.');
				$data['notification_list'] = $this->Mod_notification1->manage_notification();
				$data["filter"] = '';
				#----load view----------#
				$data["title"] = "Manage Notifications";
				$this->load->view("app/Notification/manage_notification", $data);
			} else {
				$this->session->set_flashdata('err_message', 'Adding Operation Failed.');
				$this->load->view('app/Notification/add_notification');
			}
			// }
		}
	}

	public function save_token()
	{
		$input = json_decode(file_get_contents('php://input'), true);
		$token = isset($input['token']) ? $input['token'] : '';

		if ($token) {
			$user_id = $this->session->userdata('id');

			$this->db->where('id', $user_id);
			$this->db->update('tbl_user', ['token' => $token]);

			echo json_encode(['status' => 'success', 'message' => 'Token saved']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'No token received']);
		}
	}
}
