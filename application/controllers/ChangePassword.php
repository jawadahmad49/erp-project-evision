<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ChangePassword extends CI_Controller {

	
	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_login","mod_common"
        ));
    }
    public function index() {
    	echo "helo";exit;
	}
	
	
}
