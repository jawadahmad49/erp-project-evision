<?php
defined('BASEPATH') or exit('No direct script access allowed');

require __DIR__ . '/../../../vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class Push_notification extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model(array(
			"mod_common", "mod_user_log",
		));
	}

	public function index()
	{
		$today = date('Y-m-d');

		$expiry_notification = $this->db->query("SELECT * FROM `tbl_notifications` where expiry_date='$today'")->result_array();
		foreach ($expiry_notification as $key => $value) {
			$transid = $value['transid'];
			$this->db->query("UPDATE tbl_notifications set sts='Ended' where transid='$transid'");
			$this->db->query("UPDATE tbl_push_notification set status='Ended' where notification_id='$transid'");
		}

		$expiry_prome = $this->db->query("SELECT * FROM `tbl_promo_code` where expiry_date='$today'")->result_array();
		foreach ($expiry_prome as $key => $value) {
			$transid = $value['transid'];
			$this->db->query("UPDATE tbl_promo_code set sts='Ended' where transid='$transid'");
			$this->db->query("UPDATE tbl_notifications set promo_code='0' where promo_code='$transid' and sts='Active'");
		}

		$data['push_notification'] = $this->db->query("SELECT * FROM tbl_push_notification group by notification_id")->result_array();
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Push Notification";
		$this->load->view("app/Push_notification/manage", $data);
	}

	public function add()
	{
		$data['notification_list'] = $this->db->query("SELECT * FROM `tbl_notifications` where sts='Active'")->result_array();

		$data["title"] = "Send Notification";
		$this->user_activity('Send Notification', 'View');
		$this->load->view("app/Push_notification/add", $data);
	}
	public function add_location()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {

			$notification_id = $_POST['notification'];
			$user = $_POST['user'];
			$notification_detail = $this->db->query("SELECT * FROM `tbl_notifications` where transid='$notification_id'")->row_array();


			if (empty($this->input->post('edit'))) {
				foreach ($user as $key => $value) {
					$user = $this->db->query("SELECT * from tbl_user where phone='$value'")->row_array();

					$adata['notification_id'] = $notification_id;
					$adata['user_id'] = $value;
					$adata['start_date'] = $notification_detail['start_date'];
					$adata['expiry_date'] = $notification_detail['expiry_date'];
					$adata['status'] = $_POST['status'];
					$adata['created_by'] = $this->session->userdata('id');
					$adata['created_date'] = date('Y-m-d');

					$table = 'tbl_push_notification';
					$res = $this->mod_common->insert_into_table($table, $adata);

					$body = $notification_detail['details'];
					$title = $notification_detail['title'];
					$image = SURL . $notification_detail['logo'];

					// Send notification
					$token = 'dAhHm0KUQtO-ken9dB-syT:APA91bHJ459zJUk1iaAkXetaMUIzy8hKwmpNdgfAxzJAIq1beD18i42AkCuXTJfrzQwbvm8wTzTjW0oSFWVXU486SDQqPr642Z0_oB1MKlWpylctqu1RctT4CxNiDPhU0TQQZpk0iuJH';
					if ($token) {
						try {
							$factory = (new Factory)
								->withServiceAccount(__DIR__ . '/gasablepk-848b5-firebase-adminsdk-l4hx9-17f9a9366d.json')
								->withDatabaseUri('https://gasablepk-848b5.firebaseio.com');

							$messaging = $factory->createMessaging();

							$message = CloudMessage::withTarget('token', $token)
								->withNotification(Notification::create($title, $body))
								->withAndroidConfig([
									'notification' => [
										'icon' => 'https://lpginsight.com/GasablePK/assets/images/logo',
										// 'image' => "https://lpginsight.com/GasablePK/assets/images/logo.png",
										'image' => $image,
										'color' => '#FF0000', // Optional: Notification color
									]
								]);

							$response = $messaging->send($message);

							$notificationStatus = true;
						} catch (\Exception $e) {
							$notificationStatus = false;
						}
					} else {
						$notificationStatus = false;
					}

					// $url = "https://fcm.googleapis.com/fcm/send";
					// $serverKey = 'AAAAZF-r-KM:APA91bH19zESpY36xOCTrv8PzA4JOCDXYU3MJyH2u77O57jf6iECZq4O_-a0BusKgq64pUB7oI5BNo2fsZmMCwd385-a_xz_QvmvVD4t3sydfaKNJSE7qop5Pp3e1W_lHSNc3lJQFefb';

					// $title = $notification_detail['title'];
					// $body = $notification_detail['details'];
					// $image = IMG . "notification/" . $notification_detail['logo'];
					// $notification = array('title' => $title, 'body' => $body, 'image' => $image,  'sound' => 'default', 'badge' => '1');
					// $arrayToSend = array('to' => $token, 'notification' => $notification, 'priority' => 'high');
					// $json = json_encode($arrayToSend);
					// $headers = array();
					// $headers[] = 'Content-Type: application/json';
					// $headers[] = 'Authorization: key=' . $serverKey;
					// $ch = curl_init();
					// curl_setopt($ch, CURLOPT_URL, $url);
					// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
					// curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
					// curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
					// //Send the request  
					// $response = curl_exec($ch); // Store the response in a variable
					// curl_close($ch);
				}
				if ($response) {
					$this->session->set_flashdata('ok_message', 'You have succesfully Added.');
					redirect(SURL . 'app/Push_notification/');
				} else {
					$this->session->set_flashdata('err_message', 'Operation Failed!');
					redirect(SURL . 'app/Push_notification/');
				}
			} else {
				$id = $this->input->post('edit');
				$notification_id = $this->db->query("SELECT * FROM `tbl_push_notification` where id='$id'")->row_array()['notification_id'];
				$this->db->query("DELETE from tbl_push_notification where notification_id='$notification_id'");
				foreach ($user as $key => $value) {
					$user = $this->db->query("select *  from tbl_user where phone='$value'")->row_array();

					$adata['notification_id'] = $notification_id;
					$adata['user_id'] = $value;
					$adata['start_date'] = $notification_detail['start_date'];
					$adata['expiry_date'] = $notification_detail['expiry_date'];
					$adata['status'] = $_POST['status'];
					$adata['created_by'] = $this->session->userdata('id');
					$adata['created_date'] = date('Y-m-d');

					$table = 'tbl_push_notification';
					$res = $this->mod_common->insert_into_table($table, $adata);

					$token = $user['token'];
					$url = "https://fcm.googleapis.com/fcm/send";
					$serverKey = 'AAAAZF-r-KM:APA91bH19zESpY36xOCTrv8PzA4JOCDXYU3MJyH2u77O57jf6iECZq4O_-a0BusKgq64pUB7oI5BNo2fsZmMCwd385-a_xz_QvmvVD4t3sydfaKNJSE7qop5Pp3e1W_lHSNc3lJQFefb';

					$title = $notification_detail['title'];
					$body = $notification_detail['details'];
					$image = IMG . "notification/" . $notification_detail['logo'];
					$notification = array('title' => $title, 'body' => $body, 'image' => $image,  'sound' => 'default', 'badge' => '1');
					$arrayToSend = array('to' => $token, 'notification' => $notification, 'priority' => 'high');
					$json = json_encode($arrayToSend);
					$headers = array();
					$headers[] = 'Content-Type: application/json';
					$headers[] = 'Authorization: key=' . $serverKey;
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
					curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
					//Send the request  
					$response = curl_exec($ch); // Store the response in a variable
					curl_close($ch);
				}
				if ($response) {
					$this->session->set_flashdata('ok_message', 'You have succesfully Added.');
					redirect(SURL . 'app/Push_notification/');
				} else {
					$this->session->set_flashdata('err_message', 'Operation Failed!');
					redirect(SURL . 'app/Push_notification/');
				}
			}
			if ($res) {
				$this->session->set_flashdata('ok_message', 'You have succesfully Added.');
				redirect(SURL . 'app/Push_notification/');
			} else {
				$this->session->set_flashdata('err_message', 'Operation Failed!');
				redirect(SURL . 'app/Push_notification/');
			}
		}
	}

	public function edit($id)
	{
		$data['notification_list'] = $this->db->query("SELECT * FROM `tbl_notifications` where sts='Active'")->result_array();

		$table = 'tbl_push_notification';
		$where = "id='$id'";
		$data['record'] = $this->mod_common->select_single_records($table, $where);
		$data['id'] = $id;
		$data["title"] = "Edit Push Notification";
		$this->load->view("app/Push_notification/add", $data);
	}

	function user_activity($title, $action)
	{
		$url = '';
		$url .= $_SERVER['HTTP_HOST'];
		$url .= $_SERVER['REQUEST_URI'];
		$ip = $_SERVER['REMOTE_ADDR'];
		$arrayy = array(
			'user_id' => $this->session->userdata('id'),
			'ip_address' => $ip,
			'page_url' => "http://" . $url,
			'section_name' => $title,
			'action' => $action,
			'date' => date('Y-m-d'),
			'time' => date('h:i:sa'),

		);
		$this->mod_common->insert_into_table("tbl_user_activity", $arrayy);
	}
}
