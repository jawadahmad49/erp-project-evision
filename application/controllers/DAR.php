<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DAR extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
          "mod_common","mod_dar" , "mod_plantreport"
        ));
        
    }

	public function index()
	{   
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
		$data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

        if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();
		$data["title"] = "Daily Activity Report";
		$this->load->view($this->session->userdata('language')."/dailyactivityreport/search_report_item",$data);
	}
 
	public function detail_report()
	{					 
				$table='tbl_company';       

				$data['company'] = $this->mod_common->get_all_records($table,"*");


				$data["title"] = "Daily Activity Report";

				$data['from_date'] = trim($this->input->post('from_date'));
				$data["title"] = "Daily Activity Report";


				$table='tbl_company';       
				$data['company'] = $this->mod_common->get_all_records($table,"*");

				 
				$to_date=$this->input->post('to_date');
				$from_date = $_POST['from_date'];
				$data_posted = array('from_date' => $from_date, 'to_date' => $to_date);
				$data['daterange'] = trim($from_date." / ".$_POST['to_date']);
				$new_date['from_date']=$from_date;
				$new_date['to_date']=$to_date;
 				$date_for_item['to_date']=$to_date;
				$data['report_type'] = 2;
				$data['sale']=  $this->mod_dar->getsales($data_posted,2);
 
				$where_cat_id =''; // array('catcode=' => 1);
				$data['itemname']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_cat_id);
				 
				$data['sale_return']=  $this->mod_dar->getsales_return($data_posted,2);
				 
				$data['purchases']=  $this->mod_dar->getpurchases($data_posted,2);
				 
				$data['purchases_return']=  $this->mod_dar->getpurchases_return($data_posted,2);
			 
				$data['payments']=  $this->mod_dar->getpayments($data_posted,2);
				 
				$data['receipts']=  $this->mod_dar->getreceipts($data_posted,2);
				// pm($data['receipts']);
			$this->load->view($this->session->userdata('language')."/dailyactivityreport/detail_report_new",$data);
	        

	         
	}
	
	
	
	
	
	
	
	
	
	
	
	public function details()
	{ 
		if($this->input->server('REQUEST_METHOD') == 'POST'){

	 $data['report']=  $this->input->post();
  	$show_hide=$this->input->post('show_hide');
 
	 $data['show_hide']=  $show_hide;
  
 	$data['report']=  $this->mod_plantreport->get_details($this->input->post());
	 
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
			if ($data['report']) {
			 	//$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            //redirect(SURL . 'saledatereport/detail',$data);
	            $data["title"] = "DAR";
	            $this->load->view($this->session->userdata('language')."/dailyactivityreport/detail",$data);
	        } else {
	            $this->session->set_flashdata('err_message', 'No Record Found.');
	            redirect(SURL . 'DAR/');
	        }
	    }else{
	       
		    
		   
		  
 	$date_array = array('from_date' =>  date('Y-m-d') , 'to_date' => date('Y-m-d'), 'baseon' =>'Overall', 'show_hide' =>'', 'id' =>'' );
			 
			$data['report']=  $date_array;
			$data['report']=  $this->mod_plantreport->get_details($date_array);
			$table='tbl_company';       
			$data['company'] = $this->mod_common->get_all_records($table,"*");
			
	        $data["title"] = "Plant Report";    		
 	 
			$this->load->view($this->session->userdata('language')."/dailyactivityreport/detail",$data);
		}
	}


	 
}
