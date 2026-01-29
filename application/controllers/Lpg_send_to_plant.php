<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lpg_send_to_plant extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_vendorwise","mod_common","mod_vendor","mod_girndirect"
        ));
        
    }

	public function index()
	{  

	     $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $customer_code=$fix_code['customer_code'];
        if($customer_code !=''){ $where_customer= " and tblacode.general='$customer_code'  "; }else{ $where_customer =""; }
		$data['customer_list'] = $this->db->query("select * from tblacode where atype='Child' $where_customer")->result_array();
		$data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

        if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Detail Of LPG Refund";	
		$this->load->view($this->session->userdata('language')."/Lpg_send_to_plant/search",$data);
	}

	public function details()
	{
		 if($this->input->server('REQUEST_METHOD') == 'POST'){

		 		$data['from_date'] =$from_date= trim($this->input->post('from_date'));
				$data['to_date'] = $to_date=trim($this->input->post('to_date'));
				$data['location'] = $location=trim($this->input->post('location'));
				$data['customer'] = $customer=trim($this->input->post('customer'));


	        
		
			$data['issue']=$this->db->query("select  sum(return_gas) as return_gas,issuedate from tbl_issue_goods where issuedate between '$from_date' and '$to_date' and sale_point_id='$location' group by issuedate")->result_array();
//pm($data['issue']);exit();

			$data['receive']=$this->db->query("select sum(return_gas) as return_gas,receiptdate from tbl_goodsreceiving where receiptdate between '$from_date' and '$to_date' and sale_point_id='$location' group by receiptdate")->result_array();

			$data['receive_return']=$this->db->query("select sum(return_gas) as return_gas,irdate from tbl_issue_return where irdate between '$from_date' and '$to_date' and sale_point_id='$location' group by irdate")->result_array();
			//pm($data['receive']);exit();
		
			
			$table='tbl_company';       
        	$data['company'] = $this->mod_common->get_all_records($table,"*");

        	

				
 
			if ($data['issue'] || $data['receive']) {
	            $data["title"] = "Detail Of LPG Refund";
	            $this->load->view($this->session->userdata('language')."/Lpg_send_to_plant/detail",$data);
	        } else {
	            $this->session->set_flashdata('err_message', 'No Record Found.');
	            redirect(SURL . 'Lpg_send_to_plant/');
	        }
	    }
	}

public function newpdf(){

		if($this->input->server('REQUEST_METHOD') == 'POST'){
      	$data['from_date'] =$from_date= trim($this->input->post('from_date'));
				$data['to_date'] = $to_date=trim($this->input->post('to_date'));
				$data['location'] = $location=trim($this->input->post('location'));
				$data['customer'] = $customer=trim($this->input->post('customer'));


	        
		
			$data['issue']=$this->db->query("select  sum(return_gas) as return_gas,issuedate from tbl_issue_goods where issuedate between '$from_date' and '$to_date' and sale_point_id='$location' group by issuedate")->result_array();
//pm($data['issue']);exit();

			$data['receive']=$this->db->query("select sum(return_gas) as return_gas,receiptdate from tbl_goodsreceiving where receiptdate between '$from_date' and '$to_date' and sale_point_id='$location' group by receiptdate")->result_array();

			$data['receive_return']=$this->db->query("select sum(return_gas) as return_gas,irdate from tbl_issue_return where irdate between '$from_date' and '$to_date' and sale_point_id='$location' group by irdate")->result_array();
			//pm($data['receive']);exit();
		
		// pm($data['report']);
			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
			
		
			
	    }

	  
	    	 $profilename =  $from_date;
	    	 // $profilename1 =  $to_date;
	    	 // $profilename2 =  $type;
	  
	  //pm($data);


		$this->load->view($this->session->userdata('language')."/Lpg_send_to_plant/pdffile",$data);

		$this->load->library('pdf');
			 $html = $this->output->get_output();
			 $this->dompdf->loadHtml($html);
			 $this->dompdf->setPaper('A4', 'landscape');
	        $this->dompdf->render();


	        
	        $this->dompdf->stream( $profilename.".pdf", array("Attachment"=>0));	
	}


	

}
