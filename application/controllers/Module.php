<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Module extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		if ($this->session->userdata('loginid') == '') {
			redirect(SURL . 'login');
		}
		$data["title"] = " Admin ";

		$this->load->view("app/home", $data);
	}


	public function crm()
	{

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Noor LPG CRM";

		$this->load->view("crm/home", $data);
	}


	public function app()
	{

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Mobile App";

		$this->load->view("app/home", $data);
	}
}
