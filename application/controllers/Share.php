<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Share extends CI_Controller {
 
	public function __construct() {
        parent::__construct();
    }

	public function index()
	{
        $_SESSION["menuclass"]='About';
		$this->load->view("en/Share/Share");
	}

 
 

}
