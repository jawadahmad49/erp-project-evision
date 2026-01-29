<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VendorWiseReport extends CI_Controller {

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
        $vendor_code=$fix_code['vendor_code'];
          if($vendor_code !=''){ $where_vendor= " and tblacode.general='$vendor_code'  "; }else{ $where_vendor =""; }
		//$data['vendor_list'] = $this->mod_vendor->getOnlyVendors();
		$data['vendor_list'] = $this->db->query("select * from tblacode where atype='Child' $where_vendor")->result_array();
		$data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

        if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();

		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Purchases Report";	
		$this->load->view($this->session->userdata('language')."/vendorwisereport/search",$data);
	}

	public function details()
	{
		     $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $vendor_code=$fix_code['vendor_code'];
          if($vendor_code !=''){ $where_vendor= " and tblacode.general='$vendor_code'  "; }else{ $where_vendor =""; }
		//$data['vendor_list'] = $this->mod_vendor->getOnlyVendors();
		$data['vendor_list'] = $this->db->query("select * from tblacode where atype='Child' $where_vendor")->result_array();
		$data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

        if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();
		 if($this->input->server('REQUEST_METHOD') == 'POST'){
		

	      $data['report1']=  $this->input->post();
		
			$data['report']=  $this->mod_vendorwise->get_details($this->input->post());
			// pm($data['report']);
			$table='tbl_company';       
        	$data['company'] = $this->mod_common->get_all_records($table,"*");

        			   	$data['cyl_list'] = $this->db->query("select * from tblmaterial_coding where catcode='1'")->result_array();
				$data['from_date'] = trim($this->input->post('from_date'));
				$data['to_date'] = trim($this->input->post('to_date'));
				$data['location'] = trim($this->input->post('location'));
				$data['vendor'] = trim($this->input->post('vendor'));
				$data['type'] = trim($this->input->post('type'));
				
 
			if ($data['report']) {
	            $data["title"] = "Purchases Report";
	            $this->load->view($this->session->userdata('language')."/vendorwisereport/detail",$data);
	        } else {
	            $this->session->set_flashdata('err_message', 'No Record Found.');
	            redirect(SURL . 'VendorWiseReport/');
	        }
	    }else{
			 $date_array = array('from_date' =>  date('Y-m-d') , 'to_date' => date('Y-m-d') );
	   $data['report1']=  $date_array;
	   	$data['report']=  $this->mod_vendorwise->get_details($date_array);
	   $table='tbl_company';       
        	$data['company'] = $this->mod_common->get_all_records($table,"*");

        	
				$data['from_date'] = trim($this->input->post('from_date'));
				$data['to_date'] = trim($this->input->post('to_date'));


 
			if ($data['report']) {
			 	//$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            //redirect(SURL . 'vendorwisereport/detail',$data);
	            $data["title"] = "Purchases Report";
	            $this->load->view($this->session->userdata('language')."/vendorwisereport/detail",$data);
	        } else {
	            $this->session->set_flashdata('err_message', 'No Record Found.');
	            redirect(SURL . 'VendorWiseReport/');
	        }
	       
		}
	}

public function detail($id){
		if($id){
		$data['vendor_list'] = $this->mod_vendor->getOnlyVendors();
		$table='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		$table='tbl_goodsreceiving';
		$where = "receiptnos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);

		$data['edit_list'] = $this->mod_girndirect->edit_directgirn($id);
		//echo '<pre>';print_r($data);
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Vendor Wise Report";
		//pm($data['edit_list']);
		$this->load->view($this->session->userdata('language')."/vendorwisereport/single",$data);
		}
	}

	

}
