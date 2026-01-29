<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trialbalance extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_customerledger","mod_trialbalance","mod_common","mod_profitreport","mod_customer","mod_customerstockledger","mod_salelpg","mod_vendorledger"
        ));
        
    }
	public function index()
	{
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();

		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
//	pm( $data);
		$data["title"] = "Customer Ledger";	
		$this->load->view($this->session->userdata('language')."/trialbalance/trial_balance",$data);
	}


	public function detail(){
		
 //pm($_POST);
		$data['response'] = $this->mod_trialbalance->get_report_data($_POST);
		
		//$this->load->view($this->session->userdata('language')."/trialbalance/detail");
	}
	public function ledger_report($id=''){
		
	

		if($this->input->server('REQUEST_METHOD') == 'POST' || $id !=''){
			
			$data['one']=2;
			$data['report']=  $this->mod_vendorledger->get_report($this->input->post(),$id);
			if($id !='')
			{
				$data['one']=1;
			}

			if($this->input->post('t_id'))
			{
				$count=1;foreach ($data['report'] as $key => $value) { 
			
				if(!$value['voucherno']){continue;}

					 $total_opngbl=$value['balance']; 
						 
					$total_debit+=$value['debit'];
					$total_credit+=$value['credit'];

		
					 $count++;} 

					$total_opngbl =str_replace(",", "", $total_opngbl);

					 if($this->input->post('edit_amount'))
					{
					  $total_opngbl=$total_opngbl+$this->input->post('edit_amount'); 
					}

					echo $total_opngbl;

					if(($total_opngbl)>0){echo  ' Dr';}else{ echo ' Cr';}

					echo '|';
					echo $total_opngbl;
					echo '|';


					if(($total_opngbl)>0){echo  'Dr';}else{ echo 'Cr';}

					exit();
				}





			//pm($data['report']); exit;
			#----check name already exist---------#
			// if ($this->mod_city->get_by_title($data['city_name'])) {
			// 	$this->session->set_flashdata('err_message', 'Name Already Exist.');
			// 	redirect(SURL . 'city/add_city');
			// 	exit();
			// }
			//pm($data);
			$table='tbl_company';
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
			if ($data['report']) {
			 	//$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            //redirect(SURL . 'vendorledger/detail',$data);
	            $data["title"] = " Ledger Report";
	            $this->load->view($this->session->userdata('language')."/vendorledger/single",$data);
	        } else {
	            //$this->session->set_flashdata('err_message', 'No Record Found.');
	            //redirect(SURL . 'vendorledger/');
	            $data["title"] = " Ledger Report";
	            $this->load->view($this->session->userdata('language')."/vendorledger/single",$data);
	        }
	    }else{
	        //$data["filter"] = 'add';
	        $data["title"] = " Ledger Report";    			
			$this->load->view($this->session->userdata('language')."/vendorledger/single",$data);
		}
	}


	




}
