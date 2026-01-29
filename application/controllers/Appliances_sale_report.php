<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Appliances_sale_report extends CI_Controller {


	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_salereport","mod_common","mod_customerstockledger","mod_customer","mod_salelpg"
        ));
        
    }

	public function index()
	{
		$table='tblacode';
		$where = array('general' =>1001001000);
		$data['brand'] = $this->mod_common->select_array_records($table,"*",$where);
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
        $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

        if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $customer_code=$fix_code['customer_code'];
          if($customer_code !=''){ $where_customer= " and tblacode.general='$customer_code'  "; }else{ $where_customer ="and tblacode.general in('2004001000','2004002000')"; }
		$data['customer_list'] = $this->db->query("select * from tblacode where atype='Child' $where_customer")->result_array();
		$data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();

		// $table='tblmaterial_coding';
		// $data['items'] = $this->mod_common->get_all_records($table,"*");
		$data['items']=$this->db->query("select * from tblmaterial_coding where catcode!='1'")->result_array();
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Appliances Sale Report";	
		$this->load->view($this->session->userdata('language')."/appliances_sale_report/search",$data);

	}

	public function item_report()
	{																		
			$data['report']=  $this->mod_salereport->get_appliances_report($this->input->post());
			//echo "<pre>";var_dump($data['report']);
			


			$table='tbl_company';       
        	$data['company'] = $this->mod_common->get_all_records($table,"*");

			$data['from_date'] = date('Y-m-d');
			$data['to_date'] = date('Y-m-d');
			$data["title"] = "Today Sale  Report";

			$new_date['from_date']=$this->input->post('from_date');
			$new_date['to_date']=$this->input->post('to_date');
			$new_date['location']=$this->input->post('location');
			$new_date['typee']=$data['typee']=$this->input->post('type');
			$new_date['acode']=$data['acode']=$this->input->post('acode');
			$data['one_date_report'] = $this->mod_customerstockledger->getdate_stock_report($new_date,2);


			if($this->input->post('from_date')!='')
			{
				$data['from_date'] = trim($this->input->post('from_date'));
				$data['to_date'] = trim($this->input->post('to_date'));
				$data["title"] = "Appliances Sale Report";
			}
			if ($data['report']) {

	            $this->load->view($this->session->userdata('language')."/appliances_sale_report/single",$data);
	        } else {
	            $this->session->set_flashdata('err_message', 'No Record Found.');
	            redirect(SURL . 'Appliances_sale_report');
	        }
	}




}
