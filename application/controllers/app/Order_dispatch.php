<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Order_dispatch extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model(array(
			"mod_user", "mod_common",
		));
	}

	public function index()
	{
		$login_user = $this->session->userdata('id');
		$today = date('Y-m-d');
		$data['item'] = $this->db->query("SELECT * FROM `tbl_orderstatushistory` where status ='Delivered' and date = '$today' order by id desc ")->result_array();
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Today Order";
		$this->load->view("app/Order_dispatch/manage_menu_item", $data);
	}
	public function filter()
	{
		$login_user = $this->session->userdata('id');

		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$from_date = $this->input->post("from");
			$to_date = $this->input->post("to");
			$data['item'] = $this->db->query("SELECT * FROM `tbl_orderstatushistory` where status ='Booked' and date between '$from_date' and '$to_date' order by id desc ")->result_array();
		}
		$data["title"] = "Today Order";

		$this->load->view("app/Order_dispatch/manage_menu_item", $data);
	}
	public function detail_invoice($id)
	{
		$data['id'] = $id;
		$data["title"] = "Order Invoice";
		$this->load->view("app/Order_dispatch/detail_invoice", $data);
	}
	public function confirm($id)
	{
		$today = date('Y-m-d');
		$id = $this->input->post("issuenos");
		$data['issuenos'] = $id;

		$this->db->query("UPDATE`tbl_issue_goods` SET status = 'Complete' , effective_date ='$today'  where issuenos = '$id' ");
		$get_user_id = $this->db->query("select user_id   from  tbl_issue_goods where issuenos='$id'")->row_array()['user_id'];
		$get_token = $this->db->query("select token  from  tbl_admin where id='$get_user_id'")->row_array()['token'];
		$url = "https://fcm.googleapis.com/fcm/send";
		$token = $get_token;
		$serverKey = 'AAAA2E7H8H0:APA91bEVxMenVXyLf9wKj45gCF6jNEbJ8_SA3bqTFIOOVUSn6NolinC-EeUc40Pw-qhob8k51pXdkuYg4MZu8NcSEnE-aQLEtRkCE3OAupt3XkusR-cngg0EzKi75lWhLTo0PhdNGEAW';
		$user = $this->db->query("select *  from  tbl_admin where id='$get_user_id'")->row_array();
		$name = $user['name'];
		$title = "NCP Order Dispatched";
		$body = "Hello " . $name . "\nYour Order Has Been Dispatched\nThanks.";
		$notification = array('title' => $title, 'body' => $body, 'sound' => 'default', 'badge' => '1');
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
		curl_exec($ch);
		curl_close($ch);
		echo "0";

		// curl_close($ch);
		/////////////////////////////////THE END///////////////////////////////////////////////////
	}
	public function pdffile($id)
	{
		$data['issuenos'] = $id;
		$this->load->view('app/admin/pdffile', $data);
		if (isset($_POST['pdf']) && isset($_POST['name'])) {
			$pdf = $_POST['pdf'];
			$name = $_POST['name'];
			$id = $_POST['id'];

			// Remove the data URI header
			$pdf = substr($pdf, strpos($pdf, ",") + 1);

			// Decode the base64-encoded string
			$pdf = base64_decode($pdf);

			// Save the PDF to the desired location on the server
			file_put_contents('uploads/' . $name, $pdf);
			$name = $id . '.pdf';
			$url = SURL . "uploads/" . $name;
			$this->db->query("UPDATE`tbl_issue_goods` SET issue_invoice='$url' where issuenos = '$id' ");

			// $url = SURL . "uploads/" . $name;
			// echo $url;
		}
	}
	public function cancel($id)
	{
		$today = date('Y-m-d');
		$this->db->query("UPDATE`tbl_issue_goods` SET status = 'Cancelled' , effective_date ='$today' where issuenos = '$id' ");

		redirect(SURL . 'app/Order_dispatch/');
	}
	public function second_function()
	{


		redirect(SURL . 'app/Order_dispatch/');
	}
}
