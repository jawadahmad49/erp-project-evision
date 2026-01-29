<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_wise_summary extends CI_Controller {

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
		$data["title"] = "Invoice Wise Summary";	
		$this->load->view($this->session->userdata('language')."/Invoice_wise_summary/search",$data);
	}

	public function details()
	{
		 if($this->input->server('REQUEST_METHOD') == 'POST'){

		 		$data['from_date'] =$from_date= trim($this->input->post('from_date'));
				$data['to_date'] = $to_date=trim($this->input->post('to_date'));
				$data['location'] = $location=trim($this->input->post('location'));
				$data['customer'] = $customer=trim($this->input->post('customer'));
		 if($customer!=''){ $where_acode= " AND `tbl_issue_goods`.`issuedto`='$customer'"; }else{ $where_acode =""; }

	        $data['report1']=  $this->input->post();
		
			$data['report']=$this->db->query("select * from tbl_issue_goods where issuedate between '$from_date' and '$to_date' and sale_point_id='$location' $where_acode")->result_array();
			// $data['report'] =$this->db->query("SELECT tbl_issue_goods.*,tbl_issue_goods_detail.* FROM `tbl_issue_goods`INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` where `tbl_issue_goods`.`issuedate` between '$from_date' and '$to_date' and `tbl_issue_goods`.`sale_point_id`='$location' $where_acode")->result_array();
			
			$table='tbl_company';       
        	$data['company'] = $this->mod_common->get_all_records($table,"*");

        	

				
 
			if ($data['report']) {
	            $data["title"] = "Invoice Wise Summary";
	            $this->load->view($this->session->userdata('language')."/Invoice_wise_summary/detail",$data);
	        } else {
	            $this->session->set_flashdata('err_message', 'No Record Found.');
	            redirect(SURL . 'Invoice_wise_summary/');
	        }
	    }
	}



	

}
