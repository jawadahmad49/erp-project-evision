<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profit_loss_report extends CI_Controller {



	public function __construct() {
        parent::__construct();

        $this->load->model(array(
           "mod_common","mod_salelpg","mod_profitreport"
        ));
        
    }

	public function index()
	{

        $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
        $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

        if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();
	$data["title"] = "Profit & Loss Statement";	
		$this->load->view($this->session->userdata('language')."/Profit_loss_report/search",$data);
	}

	public function report()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST'){
        $from_date =$data['from_date']=$this->input->post("from_date");
		$to_date=$data['to_date']=$this->input->post("to_date");
		$sale_point_id=$data['sale_point_id']=$this->input->post("location");
		 $data_posted['from_date']=$from_date;
		 $data_posted['to_date']=$to_date;
		 $data_posted['to_date']=$to_date;
		 $data_posted['sale_point_id']=$sale_point_id;
		$data['expenses']=  $this->mod_profitreport->getpayments_new_old($data_posted,2)->expense;
	    $table='tbl_company';       
       $data['company'] = $this->mod_common->get_all_records($table,"*");
		$data["title"] = "Profit&nbsp;&&nbsp;Loss&nbsp;Statement";
	    $this->load->view($this->session->userdata('language')."/Profit_loss_report/single",$data);
	        
	    }
	}
	public function newpdf(){

		if($this->input->server('REQUEST_METHOD') == 'POST'){
  $from_date =$data['from_date']=$this->input->post("from_date");
		$to_date=$data['to_date']=$this->input->post("to_date");
		$sale_point_id=$data['sale_point_id']=$this->input->post("location");
		
		 $data_posted['from_date']=$from_date;
		 $data_posted['to_date']=$to_date;
		 $data_posted['to_date']=$to_date;
		 $data_posted['sale_point_id']=$sale_point_id;

		$data['expenses']=  $this->mod_profitreport->getpayments_new_old($data_posted,2)->expense;
					$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
			
		
			
	    }

	  
	    	 $profilename =  $from_date;
	    	 // $profilename1 =  $to_date;
	    	 // $profilename2 =  $type;
	  
	  //pm($data);


		$this->load->view($this->session->userdata('language')."/Profit_loss_report/pdffile",$data);

		$this->load->library('pdf');
			 $html = $this->output->get_output();
			 $this->dompdf->loadHtml($html);
			 $this->dompdf->setPaper('A4', 'landscape');
	        $this->dompdf->render();


	        
	        $this->dompdf->stream( $profilename.".pdf", array("Attachment"=>0));	
	}


}
