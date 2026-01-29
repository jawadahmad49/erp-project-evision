<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nill_take_report extends CI_Controller {
	public function __construct() {
        parent::__construct();

        $this->load->model(array(
           "mod_common","mod_salelpg"
        ));
        
    }

	public function index()
	{
	$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
     $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
             $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];
if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
       
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $customer_code=$fix_code['customer_code'];
         
          if($customer_code !=''){ $where_customer= " and tblacode.general='$customer_code'  "; }else{ $where_customer ="and tblacode.general in('2004001000','2004002000')"; }
		$data['supplier_list'] = $this->db->query("select * from tblacode where atype='Child' $where_customer")->result_array();
		//$data['supplier_list'] = $this->db->query("select * from tblacode where  atype='child'")->result_array();
		$data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();

		$data["title"] = "Nill Take Report";	
		$this->load->view($this->session->userdata('language')."/Nill_take_report/search",$data);
	}
// public function report()
// 	{
// 		$login_user=$this->session->userdata('id');
//         $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
//      $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
//              $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];
// if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
       
//         $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
//         $customer_code=$fix_code['customer_code'];
         
//           if($customer_code !=''){ $where_customer= " and tblacode.general='$customer_code'  "; }else{ $where_customer ="and tblacode.general in('2004001000','2004002000')"; }
// 		$data['supplier_list'] = $this->db->query("select * from tblacode where atype='Child' $where_customer")->result_array();
// 		//$data['supplier_list'] = $this->db->query("select * from tblacode where  atype='child'")->result_array();
// 		$data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();

	
// 		if($this->input->server('REQUEST_METHOD') == 'POST'){
//         $from_date =$data['from_date']=$this->input->post("from_date");
// 		 $to_date=$data['to_date']=$this->input->post("to_date");
// 		 $data['supplier']=$supplier = $this->input->post("supplier");
// 		 $data['sale_point_id']=$sale_point_id=$this->input->post('location');
// 		  $plant=$data['plant'] = $this->input->post("plant");

// 		 if($supplier !='All'){ $where_supplier = " and tbl_issue_goods.issuedto='$supplier'  "; }else{ $where_supplier =""; }
		
// 		 // if($plant !='All'){ $where_plant = " and tbl_issue_goods.tank_id='$plant'  "; }else{ $where_plant =""; }
	
// 		   	$data['report'] = $this->db->query("SELECT tbl_issue_goods_detail.*,tblmaterial_coding.*,tbl_issue_goods.* FROM tbl_issue_goods_detail

// 	  	 inner join tbl_issue_goods on tbl_issue_goods_detail.ig_detail_id=tbl_issue_goods.issuenos inner join tblmaterial_coding on tbl_issue_goods_detail.itemid=tblmaterial_coding.materialcode  $where_supplier  and  tbl_issue_goods.issuedate Between '$from_date' and '$to_date' and tbl_issue_goods.sale_point_id='$sale_point_id' and tblmaterial_coding.catcode='1' group by tbl_issue_goods.issuedto order by tbl_issue_goods.issuenos")->result_array();
// 		   	$data['cyl_list'] = $this->db->query("select * from tblmaterial_coding where catcode='1'")->result_array();
// 		   	$data['apl_list'] = $this->db->query("select * from tblmaterial_coding where catcode!='1'")->result_array();
// 	 //pm($data['cyl_list']);
// 			$table='tbl_company';       
//        		$data['company'] = $this->mod_common->get_all_records($table,"*");
// 			if ($data['report']) {
// 			 	$data["title"] = "Retail&nbsp;Sale&nbsp;Register";
// 	            $this->load->view($this->session->userdata('language')."/Nill_take_report/single",$data);
// 	        } else {
// 	            $this->session->set_flashdata('err_message', 'No Record Found.');
// 	            redirect(SURL . 'Nill_take_report/');
	          
// 	        }
// 	    }
// 	}
	public function report()
	{

			$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
     $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
             $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];
if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
       
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $customer_code=$fix_code['customer_code'];
         
          if($customer_code !=''){ $where_customer= " and tblacode.general='$customer_code'  "; }else{ $where_customer ="and tblacode.general in('2004001000','2004002000')"; }
		
 $data['supplier']=$supplier = $this->input->post("supplier");

if($supplier !='All' ){ $where_acode = "and tblacode.acode='$supplier'  "; }else{ $where_acode =""; }
		


 $data['supplier']=$supplier = $this->input->post("supplier");

if($supplier !='All' ){ $where_acode = "and tblacode.acode='$supplier'  "; }else{ $where_acode =""; }
		


		$data['supplier_list'] = $this->db->query("select * from tblacode where atype='Child' $where_customer $where_acode")->result_array();
$from_date =$data['from_date']=$this->input->post("from_date");
		 // $to_date=$data['to_date']=$this->input->post("to_date");
		
		 $data['sale_point_id']=$sale_point_id=$this->input->post('location');

		if($this->input->server('REQUEST_METHOD') == 'POST'){



			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
			if ($data['supplier_list']) {
			 $data["title"] = "Nill Take Report";	
	            $this->load->view($this->session->userdata('language')."/Nill_take_report/single",$data);
	        } else {
	            $this->session->set_flashdata('err_message', 'No Record Found.');
	            redirect(SURL . 'Nill_take_report/');
	          
	        }
	    }
	}
	public function newpdf(){
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
     $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
             $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];
if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
       
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $customer_code=$fix_code['customer_code'];
         
          if($customer_code !=''){ $where_customer= " and tblacode.general='$customer_code'  "; }else{ $where_customer ="and tblacode.general in('2004001000','2004002000')"; }
		
 $data['supplier']=$supplier = $this->input->post("supplier");

if($supplier !='All' ){ $where_acode = "and tblacode.acode='$supplier'  "; }else{ $where_acode =""; }
		


 $data['supplier']=$supplier = $this->input->post("supplier");

if($supplier !='All' ){ $where_acode = "and tblacode.acode='$supplier'  "; }else{ $where_acode =""; }
		


		$data['supplier_list'] = $this->db->query("select * from tblacode where atype='Child' $where_customer $where_acode")->result_array();
$from_date =$data['from_date']=$this->input->post("from_date");
		 // $to_date=$data['to_date']=$this->input->post("to_date");
		
		 $data['sale_point_id']=$sale_point_id=$this->input->post('location');

		if($this->input->server('REQUEST_METHOD') == 'POST'){

			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");


	  
	    	 $profilename =  $from_date;
	


		$this->load->view($this->session->userdata('language')."/Nill_take_report/pdffile",$data);

		$this->load->library('pdf');
			 $html = $this->output->get_output();
			 $this->dompdf->loadHtml($html);
			 $this->dompdf->setPaper('A4', 'landscape');
	        $this->dompdf->render();


	        
	        $this->dompdf->stream( $profilename.".pdf", array("Attachment"=>0));	
	}

}
}
