<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Feedback extends CI_Controller
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
		$today = date('Y-m-d');

		// $data['item'] = $this->db->query("SELECT * FROM `tbl_feedback` where f_date ='$today'")->result_array();
		$data['item'] = $this->db->query("SELECT tbl_place_order.*, tbl_user.phone AS uphone, tbl_user.name AS uname FROM tbl_place_order INNER JOIN tbl_user ON tbl_user.id = tbl_place_order.userid WHERE tbl_place_order.feedback_date = '$today' AND tbl_place_order.feedback != ''")->result_array();
		// $data['item'] = userid;// name
		// $data['item'] = userid;// mobile no
		// $data['item'] = id;// order no
		// $data['item'] = feedback;// feedback

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Feedback";
		$this->load->view("app/Feedback/manage", $data);
	}
	public function filter()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$data['from_date'] = $from_date = $this->input->post("from");
			$data['to_date'] = $to_date = $this->input->post("to");

			$data['item'] = $this->db->query("SELECT tbl_place_order.*, tbl_user.phone AS uphone, tbl_user.name AS uname FROM tbl_place_order INNER JOIN tbl_user ON tbl_user.id = tbl_place_order.userid WHERE tbl_place_order.feedback_date between '$from_date' and '$to_date' AND tbl_place_order.feedback != ''")->result_array();
		}
		$data["title"] = "Feedback";
		$this->load->view("app/Feedback/manage", $data);
	}
}
