<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lpg_refund extends CI_Controller {

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
		$data["title"] = "Lpg Refund";	
		$this->load->view($this->session->userdata('language')."/Lpg_refund/search",$data);
	}

	public function details()
	{
		 if($this->input->server('REQUEST_METHOD') == 'POST'){

		 		$data['from_date'] =$from_date= trim($this->input->post('from_date'));
				$data['to_date'] = $to_date=trim($this->input->post('to_date'));
				$data['location'] = $location=trim($this->input->post('location'));
				$data['customer'] = $customer=trim($this->input->post('customer'));
		 if($customer!=''){ $where_acode= " AND `tbl_issue_goods`.`issuedto`='$customer'"; }else{ $where_acode =""; }
		 if($customer!=''){ $where_acodee= " AND `tbl_issue_return`.`scode`='$customer'"; }else{ $where_acodee =""; }
		  if($customer!=''){ $where_acodeee= " AND `tbl_goodsreceiving`.`suppliercode`='$customer'"; }else{ $where_acodeee =""; }

	        $data['report1']=  $this->input->post();
		
			$data['report']=$this->db->query("select * from tbl_issue_goods where issuedate between '$from_date' and '$to_date' and sale_point_id='$location' $where_acode")->result_array();

			$data['report_return']=$this->db->query("select * from tbl_issue_return where irdate between '$from_date' and '$to_date' and sale_point_id='$location' $where_acodee")->result_array();

			$data['report_return_filled']=$this->db->query("select * from tbl_goodsreceiving where receiptdate between '$from_date' and '$to_date' and sale_point_id='$location' $where_acodeee")->result_array();
		
			
			$table='tbl_company';       
        	$data['company'] = $this->mod_common->get_all_records($table,"*");

        	

				
 
			if ($data['report']) {
	            $data["title"] = "LPG Refund To Customer";
	            $this->load->view($this->session->userdata('language')."/Lpg_refund/detail",$data);
	        } else {
	            $this->session->set_flashdata('err_message', 'No Record Found.');
	            redirect(SURL . 'Lpg_refund/');
	        }
	    }
	}


public function newpdf(){

		if($this->input->server('REQUEST_METHOD') == 'POST'){
       	$data['from_date'] =$from_date= trim($this->input->post('from_date'));

				$data['to_date'] = $to_date=trim($this->input->post('to_date'));

		 	$data['location'] = $location=trim($this->input->post('location'));
		 		
				$data['customer'] = $customer=trim($this->input->post('customer'));
		 if($customer!=''){ $where_acode= " AND `tbl_issue_goods`.`issuedto`='$customer'"; }else{ $where_acode =""; }
		 if($customer!=''){ $where_acodee= " AND `tbl_issue_return`.`scode`='$customer'"; }else{ $where_acodee =""; }
		  if($customer!=''){ $where_acodeee= " AND `tbl_goodsreceiving`.`suppliercode`='$customer'"; }else{ $where_acodeee =""; }

		
			$data['report']=$this->db->query("select * from tbl_issue_goods where issuedate between '$from_date' and '$to_date' and sale_point_id='$location' $where_acode")->result_array();

			$data['report_return']=$this->db->query("select * from tbl_issue_return where irdate between '$from_date' and '$to_date' and sale_point_id='$location' $where_acodee")->result_array();

			$data['report_return_filled']=$this->db->query("select * from tbl_goodsreceiving where receiptdate between '$from_date' and '$to_date' and sale_point_id='$location' $where_acodeee")->result_array();
		
		 
	
			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
			
		
			
	    }

	  
	    	 $profilename =  $from_date;
	    	 // $profilename1 =  $to_date;
	    	 // $profilename2 =  $type;
	  
	  //pm($data);


		$this->load->view($this->session->userdata('language')."/Lpg_refund/pdffile",$data);

		$this->load->library('pdf');
			 $html = $this->output->get_output();
			 $this->dompdf->loadHtml($html);
			 $this->dompdf->setPaper('A4', 'landscape');
	        $this->dompdf->render();


	        
	        $this->dompdf->stream( $profilename.".pdf", array("Attachment"=>0));	
	}

	

}
