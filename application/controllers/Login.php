<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->load->model(array(
			"Mod_login", "Mod_user", "Mod_common"
		));
	}


	public function index()
	{
		//pm($this->session->userdata());

		//$this->session->unset_userdata("email");
		// $this->session->sess_destroy();

		// $this->load->library('session');

		if ($this->input->post('login_submit')) {
			$this->session->set_userdata('database', $this->input->post('db'));
			$query = $this->Mod_common->newconnection($this->input->post('db'));
			$login_success =  $this->Mod_login->check_login("tbl_admin", $this->input->post());
			if (isset($login_success)) {
				$email = $this->input->post(index: 'email');
				$comp = $this->db->query("select * from tbl_admin where loginid='$email'")->row_array();

				$pwd_change_date = date('Y-m-j', strtotime($comp['pwd_change_date']));
				$date = date('Y-m-j');

				$diff = abs(strtotime($pwd_change_date) - strtotime($date));
				$days = 30 - floor($diff / (60 * 60 * 24));
				//echo $days;exit;



				if ($days == 0) {
					$this->session->set_flashdata('expire_msg', 'Your password will be expired today. Please change password otherwise your account will be blocked.');
					$_SESSION["expire_msg"] = 'Your password will be expired today. Please change password otherwise your account will be blocked.';
				} else if ($days > 0 && $days < 6) {
					$_SESSION["expire_msg"] = 'Your password will be expired after ' . $days . ' days. Please change password with in ' . $days . ' days otherwise your account will be blocked ';
				} else if ($days < 0) {

					////////////////maintain user log
					$randompassword = $this->randomPassword(6);


					$company = $this->db->query("select * from tbl_company")->row_array();

					$to = $company['phone'];
					$business_name = $company['business_name'];
					$message .= "Company name: " . $business_name . "<br/>User name: " . $this->input->post('email') . "<br/>Your account has been unblocked and password has been reset.<br/>Your new password is : " . $randompassword;
					//echo $message;exit;

					$ch = curl_init('http://my.ezeee.pk/sendsms_url.html');
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, "Username=03558567797&Password=@03558567797&From=EVTS&To=$to&Message='$massage'");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$data = curl_exec($ch);




					$to = $company['email'];

					$subject = 'Password Expiry';
					$headers = "From: evision@gmail.com \r \n";

					$headers = "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
					$headers .= 'From: Evision <hr@evisionsystem.com>';
					mail($to, $subject, $message, $headers);
					///evision
					$to = "mianirfan786@gmail.com";
					$subject = 'Password Expiry';
					$headers = "From: evision@gmail.com \r \n";

					$headers = "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
					$headers .= 'From: Evision <hr@evisionsystem.com>';
					mail($to, $subject, $message, $headers);
					$this->db->query("update tbl_admin set admin_pwd='" . base64_encode($randompassword) . "', pwd_change_date=now() where  loginid='$email'");
					$this->session->set_flashdata('err_message', 'Sorry! Your Account has been Locked and New Password Sent To Your Registered Email .');


					//$this->session->set_flashdata('err_message', '- Error in login please try again!');
					redirect(SURL . 'login');
					exit;
				}
			} else {
				$this->session->set_flashdata('err_message', '- Error in login please try again!');
				redirect(SURL . 'login');
				exit;
			}
		}


		$table = 'tbl_admin';

		$session_array = array(
			'loginid' => $this->session->userdata('loginid'),
			'admin_pwd' => $this->session->userdata('admin_pwd'),
		);

		$login_success =  $this->Mod_common->select_single_records($table, $session_array);

		if ($this->session->userdata('logincode') != $login_success['logincode']) {
			//echo 'sssssssssss'; exit;
			redirect(SURL . 'login/se_session');
		}
		if ($this->session->userdata('loginid') == '') {

			if ($this->input->post('login_submit')) {


				$company_list = $this->Mod_common->get_all_records("tbl_company", "*");

				$startDate = $company_list[0]['lic_expiry_dt'];
				$today = date('Y-m-d');
				$days_int = 0;
				// echo ($startDate);exit;

				$start = strtotime($startDate);
				$end = strtotime($today);

				$days_int = ceil(($start - $end) / 86400);

				if ($days_int < 0) {


					$this->session->set_flashdata('err_message', 'Login Failed ! Please note your License has expired, for renewal contact us :  +92 300 856 7797');

					//$this->session->set_flashdata('err_message', '- Error in login please try again!');
					redirect(SURL . 'login');
				}


				// pm($this->input->post());
				$login_success =  $this->Mod_login->check_login($table, $this->input->post());

				$randon_code = rand(1, 100000);

				//$this->session->set_userdata('logrand',$randon_code);

				if ($login_success) {



					if ($login_success['logincode'] != 0) {
						$temp_login = $login_success;

						$this->session->set_userdata('temp_email', $login_success['loginid']);
						$this->session->set_userdata('randon_code', $randon_code);
						$this->session->set_userdata('id', $login_success['id']);

						$result = $this->Mod_user->get_language($login_success['comp_id']);

						if ($result['lang_opt'] == 'both') {

							$this->session->set_userdata('temp_language', $login_success['language']);
							$this->session->set_userdata('language', $login_success['language']);
						} else {
							$this->session->set_userdata('temp_language', $result['lang_opt']);
							$this->session->set_userdata('language', $result['lang_opt']);
						}

						$this->session->set_flashdata('logout', 'Logout from other browser');
						// $this->session->set_flashdata('err_message', '-Already login from other browser');
						redirect(SURL . 'login');
					}



					$login_success['logincode'] = $randon_code;

					$this->session->set_userdata($login_success);


					if ($this->session->userdata('id')) {
						$where = array('id' => $this->session->userdata('id'));

						$data  = array('logincode' => $randon_code);


						$update_success =  $this->Mod_common->update_table($table, $where, $data);
					}
					$result = $this->Mod_user->get_language();

					if ($result['lang_opt'] == 'both') {

						$this->session->set_userdata('language', $login_success['language']);
					} else {
						$this->session->set_userdata('language', $result['lang_opt']);
					}


					$this->session->set_flashdata('ok_message', '- Login successfully!');
					redirect(SURL . 'login/redirect');
				} else {
					$this->session->set_flashdata('err_message', '- Error in login please try again!');
					redirect(SURL . 'login');
				}
			}
			//pm($data['company_list']);
			$data["filter"] = '';
			#----load view----------#
			$data["title"] = "Login";
			$this->load->view('login', $data);
		} else {
			$this->session->set_flashdata('ok_message', '- Already Login !');
			redirect(SURL . 'login/redirect');
		}
	}
	public function logout()
	{
		$table = 'tbl_admin';
		$where = array('id' => $this->session->userdata('id'));
		$data  = array('logincode' => 0);
		$update_success =  $this->Mod_common->update_table($table, $where, $data);

		// Unset specific session data
		$this->session->unset_userdata('id');
		$this->session->unset_userdata('username');
		$this->session->unset_userdata('logged_in');

		// Alternatively, you can unset all session data by passing an array
		// $this->session->unset_userdata(array('id', 'username', 'logged_in'));

		// Destroy the session
		$this->session->sess_destroy();

		// Redirect to the login page
		redirect(SURL . 'login');
	}

	// public function logout()
	// {
	// 	$table = 'tbl_admin';
	// 	$where = array('id' => $this->session->userdata('id'));
	// 	$data  = array('logincode' => 0);
	// 	$update_success =  $this->Mod_common->update_table($table, $where, $data);
	// 	$this->session->unset_userdata();
	// 	$this->session->sess_destroy();
	// 	redirect(SURL . 'login');
	// }
	public function ses_session()
	{
		// $table='tbl_admin';
		//$where = array('id' => $this->session->userdata('id'));
		//$data  = array('logincode' =>0);
		//$update_success =  $this->Mod_common->update_table($table,$where,$data);
		// $this->session->unset_userdata();
		$this->session->sess_destroy();
		redirect(SURL . 'login');
	}
	public function language()
	{

		$url = SURL;

		$result = $this->Mod_user->get_language();

		if ($result['lang_opt'] == 'both') {

			$table = 'tbl_admin';
			$change_language = 'en';

			$url = $_GET['url'];

			$current_language = $this->session->userdata('language');
			if ($current_language == 'en') {
				$change_language = 'ur';
			}

			$where = array('id' => $this->session->userdata('id'));

			$data  = array('language' => $change_language);

			$this->session->set_userdata('language', $change_language);

			//echo $this->session->userdata('language');

			$update_success =  $this->Mod_common->update_table($table, $where, $data);
		}


		redirect($url);
	}

	public function change_password()
	{
		$table = 'tbl_admin';
		if ($this->session->userdata('loginid') != '') {
			if ($this->input->post('change_password_btn')) {

				$session_array = array('admin_pwd' => base64_encode(trim($this->input->post('old_password'))), 'loginid' => $this->session->userdata('loginid'));

				$login_success =  $this->Mod_common->select_single_records($table, $session_array);
				if ($login_success) {

					if ($this->input->post('new_password') == $this->input->post('con_password')) {
						$where = array('id' => $this->session->userdata('id'));
						$data  = array(
							'admin_pwd' => base64_encode(trim($this->input->post('new_password'))),
							'pwd_change_date' => date('Y-m-d')
						);

						$update_success =  $this->Mod_common->update_table($table, $where, $data);
						if ($update_success) {
							//echo 'ssssssss'; exit;
							$session_array = array('admin_pwd' => $this->input->post('new_password'), 'loginid' => $this->session->userdata('loginid'));

							$login_success =  $this->Mod_common->select_single_records($table, $session_array);

							$this->session->set_userdata($login_success);
							$this->session->set_flashdata('ok_message', '- Password change successfully !');
							//echo $this->session->userdata('logincode');
							// pm($this->session->userdata());
							redirect(SURL . 'login');
						}
					} else {
						$this->session->set_flashdata('err_message', 'Confirm password does not match');
					}
				} else {

					$this->session->set_flashdata('err_message', 'Enter the correct old password');
					redirect(SURL . 'login/change_password');
				}
			}

			$this->load->view('change_password');
		} else {
			$this->session->set_flashdata('err_message', 'First login to change password');
			redirect(SURL . 'login');
		}
	}

	public function se_session($val = '')
	{
		//pm($this->session->userdata('id'));
		if ($val == 1) {
			$table = 'tbl_admin';
			$where = array('id' => $this->session->userdata('id'));
			$data  = array('logincode' => $this->session->userdata('randon_code'));

			$update_success =  $this->Mod_common->update_table($table, $where, $data);

			$session_array = array('id' => $this->session->userdata('id'), 'loginid' => $this->session->userdata('temp_email'));
			if ($update_success) {
				$login_success =  $this->Mod_common->select_single_records($table, $session_array);
				$this->session->set_userdata($login_success);
				$this->session->set_userdata('language', $this->session->userdata('temp_language'));
			}
		} else {
			$this->session->unset_userdata();
			$this->session->sess_destroy();
		}

		redirect(SURL . 'login');
	}

	public function redirect()
	{
		$table = 'tbl_company';
		$data['company_list'] = $this->Mod_common->get_all_records($table, "*");
		//pm($data['company_list']);exit;
		$startDate = $data['company_list'][0]['lic_expiry_dt'];
		$today = date('Y-m-d');
		$days_int = 0;
		// echo ($startDate);exit;

		$start = strtotime($startDate);
		$end = strtotime($today);

		$days_int = ceil(abs($end - $start) / 86400);
		//echo ($days_int);exit;
		if ($days_int <= 10) {

			$this->session->set_flashdata('ok_message', 'Login successfully ! Please note your License will be expired after ' . $days_int . ' days, for renewal contact us :  +92 300 856 7797');
		} else {
			$this->session->set_flashdata('ok_message', '- Login successfully!');
		}

		redirect(SURL . '');
	}
	function randomPassword($length)
	{
		$random = "";
		srand((float)microtime() * 1000000);

		$data = "AbcDE123IJKLMN67QRSTUVWXYZ";
		$data .= "aBCdefghijklmn123opq45rs67tuv89wxyz";
		$data .= "0FGH45OP89";

		for ($i = 0; $i < $length; $i++) {
			$random .= substr($data, (rand() % (strlen($data))), 1);
		}

		return $random;
	}
}
