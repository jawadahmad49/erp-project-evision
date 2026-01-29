<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accesscylinders extends CI_Controller {

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
            "mod_accesscylinders","mod_common" ,"mod_admin"  ,"mod_customerstockledger" 
        ));
        
    }

	public function index()
	{
		$table='tbltrans_detail';
		$data['stock_report_list'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Access Cylinders Report";	
		$table='tblcategory';       
        $data['category_list'] = $this->mod_common->get_all_records($table,"*");
        $this->load->view($this->session->userdata('language')."/accesscylinders/search",$data);       	
	}

	public function details()
	{
	
	  	$to_date=	$this->input->post('to_date');
 
 
 if($to_date==''){$to_date=date('Y-m-d');}
		$data['total_balance']=  $this->mod_admin->getcurrent_stock_new_access('All','All',$to_date,'Access');
 
		$table='tbl_company';       
       	$data['company'] = $this->mod_common->get_all_records($table,"*");

		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->select_array_records($table,"*","catcode='1' ");
		
		//pm($data['items']);
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Sale";	
			
			//pm($data);

		$this->load->view($this->session->userdata('language')."/accesscylinders/detail",$data);

	}
	public function details_own()
	{
		
		
		
		$data['total_balance']=  $this->mod_customerstockledger->get_total_customer_stock();

		  
		$table='tbl_company';       
       	$data['company'] = $this->mod_common->get_all_records($table,"*");

		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->select_array_records($table,"*","catcode='1' ");
		
		//pm($data['items']);
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Sale";	
			
			//pm($data);

		$this->load->view($this->session->userdata('language')."/Customerstockledger/customer_sale",$data);

		  	// $to_date=	$this->input->post('to_date');
	 
		// $data['total_balance']=  $this->mod_accesscylinders->get_total_business_stock($to_date);

		 

		
		// $table='tbl_company';       
       	// $data['company'] = $this->mod_common->get_all_records($table,"*");

		// $table='tblmaterial_coding';
		// $data['items'] = $this->mod_common->select_array_records($table,"*","catcode='1' ");
		
		// //pm($data['items']);
		// $data["filter"] = '';
		// #----load view----------#
		// $data["title"] = "Customer Sale";	
			
			// //pm($data);

		// $this->load->view($this->session->userdata('language')."/accesscylinders/details_own",$data);

	}


}
