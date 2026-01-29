<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CustomerLedger extends CI_Controller { 

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
            "mod_customerledger","mod_admin","mod_common","mod_customer","mod_customerstockledger","mod_salelpg"
        ));
        
    }
	public function index()
	{
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $customer_code=$fix_code['customer_code'];
          if($customer_code !=''){ $where_customer= " and tblacode.general='$customer_code'  "; }else{ $where_customer =""; }
		//$data['vendor_list'] = $this->mod_vendor->getOnlyVendors();
		$data['customer_list'] = $this->db->query("select * from tblacode where atype='Child' $where_customer")->result_array();
		$data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

        if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();

		//$data['customer_list'] = $this->mod_customer->getOnlyCustomers();

		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Ledger";	
		$this->load->view($this->session->userdata('language')."/customerledger/search",$data);
	}

	public function report()
	{
	 
		//pm($this->input->post());
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$data['report']=  $this->mod_customerledger->get_report($this->input->post());
         if($this->input->post('from_date')=='1947-01-01')
			{
				$data['from_date']='2018-01-01';
			}
			else
			{
				$data['from_date']=$this->input->post('from_date');
			}
 
		 //pm($this->input->post());exit();
			$data['opening']=  $this->mod_customerstockledger->get_opening($this->input->post(),1);
			//pm($data['opening']);exit();
		 
			$data['itemname'] = $this->mod_common->select_array_records('tblmaterial_coding',"*","catcode='1' ");
 

			$total_return = array();
			$total_sale = array();
			$total_return_sale=array();
			$total_sale_ledger=array();
			$total_sale_ledger_security=array();
			$total_sale_ledger_wo_security=array();

		 $data['return']=  $this->mod_customerstockledger->getreturn($this->input->post());
			foreach ($data['return'] as $key => $value) {

				if(count($value['return']>1))
 				{
			 		foreach ($value['return'] as $key => $value_sub) {

			 			$total_return[$value_sub['itemid']]=$total_return[$value_sub['itemid']]+$value_sub['qty'];
			 			 
			 		}
				}
			}
			$data['sale']=  $this->mod_customerstockledger->getsale($this->input->post());
            foreach ($data['sale'] as $key => $value) {
				 if(count($value['sale']>1))
 				{
			 		foreach ($value['sale'] as $key => $value_sub) {

			 			$total_sale[$value_sub['itemid']]=$total_sale[$value_sub['itemid']]+$value_sub['qty'];

			 		}
				}
			}
			
			
			$data['getsale_ledger']=  $this->mod_customerstockledger->getsale_ledger($this->input->post());
			foreach ($data['getsale_ledger'] as $key => $value) {
			 			$total_sale_ledger[$value['itemid']]=$value['qty'];
 				 
			}
			$data['total_sale_ledger']=$total_sale_ledger;
			
			$data['getsale_ledger_security']=  $this->mod_customerstockledger->getsale_ledger_security($this->input->post());
			foreach ($data['getsale_ledger_security'] as $key => $value) {
			 			$total_sale_ledger_security[$value['itemid']]=$value['qty'];
 				 
			}
			$data['total_sale_ledger_security']=$total_sale_ledger_security;

			$data['getsale_ledger_wo_security']=  $this->mod_customerstockledger->getsale_ledger_wo_security($this->input->post());
			foreach ($data['getsale_ledger_wo_security'] as $key => $value) {
			 			$total_sale_ledger_wo_security[$value['itemid']]=$value['qty'];
 				 
			}
			$data['total_sale_ledger_wo_security']=$total_sale_ledger_wo_security;
			
			
			
			$data['getreturn_ledger']=  $this->mod_customerstockledger->getreturn_ledger($this->input->post());
			foreach ($data['getreturn_ledger'] as $key => $value) {
			 			$total_return_ledger[$value['itemid']]=$value['qty'];
			 			
			}
			$data['total_return_ledger']=$total_return_ledger;

			$data['getreturn_wo_sec_ledger']=  $this->mod_customerstockledger->getreturn_wo_sec_ledger($this->input->post());
			foreach ($data['getreturn_wo_sec_ledger'] as $key => $value) {
			 			$total_return_wo_sec_ledger[$value['itemid']]=$value['qty'];
			 			
			}
			$data['total_return_wo_sec_ledger']=$total_return_wo_sec_ledger;

			for ($i=0; $i <count($data['opening']); $i++) { 

				$item_code=$data['opening'][$i]['itemid'];
				$opening_array[$item_code]=$data['opening'][$i]['opening'];

			}
			for ($i=0; $i <count($data['itemname']); $i++) { 

				   $item_code= $data['itemname'][$i]['materialcode'];
			 
			$total_return_sale[$item_code]=$opening_array[$item_code]+$total_sale_ledger[$item_code]+$total_sale_ledger_wo_security[$item_code]-($total_return_ledger[$item_code]+$total_return_wo_sec_ledger[$item_code]);
				
		}
		$data['total_return_sale']=$total_return_sale;
			$data['total_sale']=$total_sale;
			
			$data['total_return']=$total_return;
			 $data['opening_return_sale']=  $this->mod_customerstockledger->get_opening($this->input->post(),1);
			$check_box = $_POST['check'];
			if($check_box!=""){
				$data['hide'] = $check_box;
			}

			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
			if ($data['report']) {
				$data["title"] = "Customer Ledger Report";
	            $this->load->view($this->session->userdata('language')."/customerledger/single",$data);
	        } else {
	        	$data["title"] = "Customer Ledger Report";
	            $this->load->view($this->session->userdata('language')."/customerledger/single",$data);
	        }
	    }else{
	    	 $data["title"] = "Customer Ledger Report";    			
			$this->load->view($this->session->userdata('language')."/customerledger/single",$data);
		}
	}





}
