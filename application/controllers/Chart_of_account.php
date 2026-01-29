<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chart_of_account extends CI_Controller {



	public function __construct() {
        parent::__construct();
	

        $this->load->model(array(
            "mod_salereport","mod_common","mod_customerstockledger","mod_customer","mod_salelpg"
        ));
        
    }

	public function index()
	{

			$data["title"] = "Chart of account";
			  $this->load->view($this->session->userdata('language')."/chart_of_account/search",$data);	
		

	}
	public function report()
	{   

			$level=$data["level"] = $this->input->post("level");
			 if($level=='All'){
			 $where_acode =""; 
			}else if($level=='A'){
			  $where_acode = "where right(`acode`,9)='000000000'"; 
			}else if($level=='B'){
			  $where_acode = "where right(`acode`,6)='000000' and right(`acode`,9)!='000000000'"; 
			}else if($level=='C'){
			  $where_acode = "where right(`acode`,3)='000' and right(`acode`,6)!='000000' and right(`acode`,9)!='000000000'"; 
			}else if($level=='D'){
			  $where_acode = "where atype='Child'"; 
			}
		



		$data['report'] = $this->db->query("select * from tblacode  $where_acode")->result_array();
		//pm($data['deal']);
		#----load view----------#
		$data["title"] = "Chart of account";
		$data["Level"] =$level;
		if ($data['report']) {
			 	
	            $this->load->view($this->session->userdata('language')."/chart_of_account/detail",$data);
	        } else {
	            $this->session->set_flashdata('err_message', 'No Record Found.');
	            redirect(SURL . 'chart_of_account/');
	          
	        }
        
	}

	

}

