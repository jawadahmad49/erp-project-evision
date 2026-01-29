<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserLog extends CI_Controller {


	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_salereport","mod_common","mod_userlog","mod_customer","mod_salelpg"
        ));
        
    }

	public function index()
	{

$table='tbl_admin';       
        $data['user_list'] = $this->mod_common->get_all_records($table,"*");
		
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "User Log Report";	
		$this->load->view($this->session->userdata('language')."/userlog/search",$data);


	
	}



	

	public function details()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST'){
		

	 
          $id=  $this->input->post('user');
         $data['report']= $this->db->query("select * from tbl_user_log where user_id='$id'")->result_array();
        // pm($data['report']);
			
		  $data['user_id']=$id;
		  $table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");

		 	
  
	     $this->load->view($this->session->userdata('language')."/userlog/detail_report",$data);
	       
		
	}
}
	

}
