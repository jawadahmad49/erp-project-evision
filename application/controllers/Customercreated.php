<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customercreated extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_customercreated","mod_common"
        ));
    }

	public function index()
	{
		$data["title"] = "Customer Created Report";	

        $this->load->view($this->session->userdata('language')."/customercreated/search",$data);       	
	}

	public function details()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			//pm();
			$data['report']=  $this->mod_customercreated->get_details($this->input->post());

			$data['daterange'] = $_POST['from_date']." / ".$_POST['to_date'];

			if ($data['report']) {

				$table='tbl_company';       
       			$data['company'] = $this->mod_common->get_all_records($table,"*");

				$data["title"] = "Customer Created Report";
	            $this->load->view($this->session->userdata('language')."/customercreated/detail",$data);
	        } else {

	            $this->session->set_flashdata('err_message', 'No Record Found.');
	            redirect(SURL . 'Customercreated/');
	        }
	    }else{
	        $data["title"] = "Customer Created Report";    			
			$this->load->view($this->session->userdata('language')."/customercreated/detail",$data);
		}
	}
}
