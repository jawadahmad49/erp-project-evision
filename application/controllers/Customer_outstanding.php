<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_outstanding extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_cashbookreport","mod_common","mod_customer","mod_salelpg"
        ));
        
    }

	public function index()
	{
		$table='tblacode';
		$where = "general='2001001000'";
		$data['customers'] = $this->mod_common->select_array_records($table,'*',$where);
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $cash_code=$fix_code['cash_code'];
         if($cash_code !=''){ $where_code = " and tblacode.acode='$cash_code' "; }else{ $where_code =" and tblacode.general='2003013000' "; }
        // $data['result1'] = $this->db->query("select * from tblacode where general='2003013000' and atype='Child'")->result_array();
        $data['result1'] = $this->db->query("select * from tblacode where atype='Child' $where_code")->result_array();
		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Outstanding Report";	
		$this->load->view($this->session->userdata('language')."/Customer_outstanding/search",$data);
	}

	public function report()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST'){

        
		 $data['from_date'] = $this->input->post('from_date');
		 $data['to_date']= $this->input->post('to_date');
		 $data['sale_point_id']= $this->input->post('sale_point_id');

			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
			if ($data['report']) {
			       $data["title"] = "Customer&nbsp;Outstanding";
				   
	            $this->load->view($this->session->userdata('language')."/Customer_outstanding/single",$data);
	        } else {
	            //$this->session->set_flashdata('err_message', 'No Record Found.');
	            //redirect(SURL . 'Customer_outstanding/');
	            $data["title"] = "Customer&nbsp;Outstanding";
	            $this->load->view($this->session->userdata('language')."/Customer_outstanding/single",$data);
	        }
	    }else{
	        //$data["filter"] = 'add';
	        $data["title"] = "Customer&nbsp;Outstanding";    			
			$this->load->view($this->session->userdata('language')."/Customer_outstanding/single",$data);
		}
	} 
	} 
 
