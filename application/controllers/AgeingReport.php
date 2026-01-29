<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AgeingReport extends CI_Controller {

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
            "mod_ageingreport","mod_common"
        ));
        
    }

	public function index()
	{
		$table='tbltrans_detail';
		$data['stock_report_list'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Ageing Report";	
		$table='tblcategory';       
        $data['category_list'] = $this->mod_common->get_all_records($table,"*");
        $this->load->view($this->session->userdata('language')."/ageing_report/search",$data);       	
	}

	public function details()
	{

		if($this->input->server('REQUEST_METHOD') == 'POST'){

			$data['report']=  $this->mod_ageingreport->get_details($this->input->post());
			$data['daterange'] = $this->input->post('to_date');

			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
			if ($data['report']) {

	            $data["title"] = "Ageing Report";
	            $this->load->view($this->session->userdata('language')."/ageing_report/detail",$data);
	        } else {
	            $this->session->set_flashdata('err_message', 'No Record Found.');
	            redirect(SURL . 'AgeingReport/');
	        }
	    }else{
	        $data["title"] = "Ageing Report";    			
			$this->load->view($this->session->userdata('language')."/ageing_report/detail",$data);
		}
	}


}
