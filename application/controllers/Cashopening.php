<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cashopening extends CI_Controller {

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

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_cashopening","mod_common"
        ));
        
    }

	public function index($id)
	{
		$table='tblacode';       
        $where = "acode='2003013001'";
		$data['Cashopening'] = $this->mod_common->select_single_records($table,$where);
	//echo "<pre>";var_dump($data['Cashopening']);exit;
        if($data['Cashopening']){
			//// check transactions already made /////
			$table='tbltrans_master';
			$data['exist'] = $this->mod_common->get_all_records($table,"*");
			//// end check transactions already made /////
        	$data["filter"] = 'edit';
        }else{
        	$data["filter"] = 'add';
        }
		#----load view----------#
		$data["title"] = "Cashopening";	
		$this->load->view($this->session->userdata('language')."/Cashopening/add",$data);
	}

	public function add()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			// "<pre>";var_dump($this->input->post());exit;

			$add=  $this->mod_cashopening->add_Cashopening($this->input->post());

			if ($add) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            redirect(SURL . 'Cashopening/edit/'.$add);
	            //$this->load->view('Cashopening/add',$add);
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Cashopening/');
	        }
	    }
	   	$data["filter"] = 'Cashopening';
		#----load view----------#
		$data["title"] = "Cashopening";	
		$this->load->view($this->session->userdata('language')."/Cashopening/",$data);
	}


	public function edit($id){
		//// check transactions already made /////
		$table='tbltrans_master';
		$data['exist'] = $this->mod_common->get_all_records($table,"*");
		//// end check transactions already made /////

			$table='tblacode';
			$where = "acode='2003013001'";
			$data['Cashopening'] = $this->mod_common->select_single_records($table,$where);
			$data["filter"] = 'edit';
			$data["title"] = "Cashopening";	
			$this->load->view($this->session->userdata('language')."/Cashopening/add",$data);
		
		/* Update Data */
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$update=  $this->mod_cashopening->update_Cashopening($this->input->post());

			if ($update) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
	            redirect(SURL . 'Cashopening/edit/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Operation Failed.');
	            redirect(SURL . 'Cashopening/edit/');
	        }
	    }
	}

}
