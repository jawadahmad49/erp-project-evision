<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gazzetted_holidays extends CI_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->load->model(array(
			"mod_vendor", "mod_common", "mod_customer"
		));
	}
	public function index()
	{
		$login_user = $this->session->userdata('id');

		$data['holiday_list'] = $this->db->query("select * from tbl_hrm_holiday")->result_array();




		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Holidays";
		$this->load->view("app/gazzetted_holiday/manage_holiday", $data);
	}

	public function add_gazzetted_holiday()
	{


		$data["title"] = "Add Holidays";


		$this->load->view("app/gazzetted_holiday/add", $data);
	}

	public function insert()
	{
		$holiday_name = $this->input->post('holiday_name');
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$full_half = $this->input->post('full_half');
		$duration = $this->input->post('duration');
		$repeats_annualy = $this->input->post('repeats_annualy');
		$user_id = $this->session->userdata('id');
		if (isset($user_id)) {


			$qresult = $this->db->query("SELECT * FROM tbl_hrm_holiday where  from_date='$from_date' and to_date='$to_date'")->result_array();
			if (!empty($qresult)) {
				echo "already_on_same";
				exit;
			}
			$qresult = $this->db->query("SELECT * FROM tbl_hrm_holiday where  (from_date between '$from_date'  and '$to_date' )
				or( to_date  between '$from_date' and  '$to_date')")->result_array();
			if (!empty($qresult)) {
				echo "already_on_period";
				exit;
			}





			$query1 = $this->db->query("select COALESCE(max(holiday_code),0) as 'code' from tbl_hrm_holiday")->row_array();
			$newid = $query1["code"] + 1;

			$today = date('Y-m-d');
			$query = "insert into tbl_hrm_holiday(holiday_code,holiday_name,from_date,to_date,duration,full_half,repeats_annualy,created_by,created_date)
				VALUES('$newid','$holiday_name','$from_date','$to_date',$full_half,'$full_half','$repeats_annualy',$user_id,'$today')";
			$this->db->query($query);

			echo "success";
			exit;
		}
		// redirect(SURL . 'app/Gazzetted_holidays/');
	}
	public function update()
	{
		$holiday_name = $this->input->post('holiday_name');
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$full_half = $this->input->post('full_half');
		$repeats_anualy = $this->input->post('repeats_annualy');
		$duration = $this->input->post('duration');
		$code = $this->input->post('code');
		$user_id = $this->session->userdata('id');
		if (isset($user_id)) {
			$qresult = $this->db->query("SELECT * FROM tbl_hrm_holiday where holiday_code!='$code' AND holiday_name = '$holiday_name'")->result_array();
			if (!empty($qresult)) {
				echo 'already';
				exit;
			}
			$now = strtotime("$to_date");
			$your_date = strtotime("$from_date");
			$duration = (($now - $your_date) / 86400) + 1;
			$userid = $user_id;
			$today = date('Y-m-d');

			/// update perviosuly addedd holiday to absent
			$query1 = $this->db->query(" SELECT from_date,to_date,duration FROM tbl_hrm_holiday  where holiday_code='$code'")->result_array();
			$duration_last = $query1["duration"];
			$from_date_last = $query1["from_date"];


			for ($i = 0; $i < $duration_last; $i++) {
				$from_date_n =	date('Y-m-d', strtotime($from_date_last . ' + ' . $i . ' days'));

				$pieces = explode("-", $from_date_n);

				$curr_year = $pieces[0];
				$curr_mon = $pieces[1];
				$day  = $pieces[2];


				$this->db->query("update  from `tbl_hrm_daily_attendence` set sts='Absent' where  curr_date='$from_date_n' and  curr_month='$curr_mon'");
			}

			$query = "update tbl_hrm_holiday set
		holiday_name='$holiday_name',from_date='$from_date',to_date='$to_date',duration='$duration',full_half='$full_half',repeats_annualy='$repeats_anualy',created_by='$userid',created_date=CURDATE()
		where holiday_code='$code'";

			$this->db->query($query);
		}
	}

	public function delete($id)
	{

		$table = "tbl_hrm_holiday";
		$where = "holiday_code = '" . $id . "'";
		$delete_area = $this->mod_common->delete_record($table, $where);
		if ($delete_area) {
			$this->session->set_flashdata('ok_message', 'You have successfully deleted.');
			redirect(SURL . 'app/Gazzetted_holidays/');
		} else {
			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
			redirect(SURL . 'app/Gazzetted_holidays/');
		}
	}

	public function edit($id)
	{


		if ($id) {
			$data['holiday_list'] = $this->db->query("select * from tbl_hrm_holiday")->result_array();
			$data['record'] = $this->db->query("select * from tbl_hrm_holiday where holiday_code='$id'")->row_array();
			$data["title"] = "Edit Holidays";

			$this->load->view("app/gazzetted_holiday/add", $data);
		}
	}
}
