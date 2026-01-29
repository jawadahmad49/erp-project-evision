<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expensereport extends CI_Controller {


	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_expenses","mod_common"
        ));
    }

	public function index()
	{
		
	$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Expenses Report";	
		$this->load->view($this->session->userdata('language')."/Expensereport/search",$data);
	}

	
	public function openBalance_expenses_detail()
	{      if($this->input->server('REQUEST_METHOD') == 'POST'){
		
	//pm($this->input->post());exit;
	        $data['report']=  $this->input->post();
		$data['total_balance']=  $this->mod_expenses->get_total_balance_expenses($this->input->post());
	    // pm($data['total_balance']);exit;
		 $table='tbl_company'; 
$data['from_date']=$this->input->post('from_date');
$data['to_date']=$this->input->post('to_date');		 
       	$data['company'] = $this->mod_common->get_all_records($table,"*");

		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Expenses Balance";	
		$this->load->view($this->session->userdata('language')."/Expensereport/net_balance_expenses",$data);
		
		
		}
	}

	



	

}
