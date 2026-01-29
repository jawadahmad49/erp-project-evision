<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customerslist extends CI_Controller {
	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_customerslist","mod_common","mod_customer","mod_salelpg"
        ));   
    }

	public function index()
	{
		
		
			$table='tbl_country';  
			$data['country_list'] = $this->mod_common->get_all_records($table,"*");    			
			$data["title"] = "Region Wise Sale";	
			$this->load->view($this->session->userdata('language')."/customerslist/search",$data);
	}

	public function customerview()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST'){

				$data['report']=  $this->mod_customerslist->get_report($this->input->post());
				//pm($data);
				//exit;
			 	$data['items'] = $this->db->get_where('tblmaterial_coding',array('catcode'=>1))->result();
				
				$table='tbl_company';       
				$data['company'] = $this->mod_common->get_all_records($table,"*");
				$data['from_date'] = trim($this->input->post('from_date'));
				$data['to_date'] = trim($this->input->post('to_date'));
				 $data['type'] = trim($this->input->post('type'));
                $data["title"] = "Customer List  Report";
	            $this->load->view($this->session->userdata('language')."/customerslist/detail_report",$data);
	        
	    }
	}
 
}
?>
