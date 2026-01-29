<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DAR_old extends CI_Controller {

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
          "mod_common","mod_dar_old" 
        ));
        
    }

	public function index()
	{
		$data["title"] = "Daily Activity Report";	
		$this->load->view($this->session->userdata('language')."/dailyactivityreportold/search_report_item",$data);
	}

	public function detail_report()
	{							
			 
				$table='tbl_company';       

				$data['company'] = $this->mod_common->get_all_records($table,"*");


				$data["title"] = "Daily Activity Report";

				$data['from_date'] = trim($this->input->post('from_date'));
				$data["title"] = "Daily Activity Report";


				$table='tbl_company';       
				$data['company'] = $this->mod_common->get_all_records($table,"*");

				
				///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//////////////////////////////////    SALES //////////////////////////////////////////////////////////////////////////////////////////
				//////////////////////////////////    SALES //////////////////////////////////////////////////////////////////////////////////////////
				$to_date=$from_date=$this->input->post('from_date');
				$data_posted = array('from_date' => $from_date, 'to_date' => $to_date);
				$data['daterange'] = trim($from_date);
				$new_date['from_date']=$from_date;
				$new_date['to_date']=$to_date;
				//$data['one_date_report'] = $this->mod_dar_old->getdate_stock_report($new_date,2);
				$date_for_item['to_date']=$to_date;
				$data['report_type'] = 2;
				$data['sale']=  $this->mod_dar_old->getsales($data_posted,2);
				//	 pm($data['sale']);
				$where_cat_id =''; // array('catcode=' => 1);
				$data['itemname']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_cat_id);
				//$tables='tblmaterial_coding';       
				//$data['itemname'] = $this->mod_common->get_all_records($tables,"*");
				//$data['itemname_return'] = $this->mod_common->get_all_records($tables,"*");
				//////////////////////////////////    SALES //////////////////////////////////////////////////////////////////////////////////////////
				//////////////////////////////////    SALES //////////////////////////////////////////////////////////////////////////////////////////
		 
		 
				//////////////////////////////////    SALES RETURN //////////////////////////////////////////////////////////////////////////////////////////
				$data['sale_return']=  $this->mod_dar_old->getsales_return($data_posted,2);
				//////////////////////////////////    PURCHASES //////////////////////////////////////////////////////////////////////////////////////////
				
				
				///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//////////////////////////////////    PURCHASES //////////////////////////////////////////////////////////////////////////////////////////
				$data['purchases']=  $this->mod_dar_old->getpurchases($data_posted,2);
				//////////////////////////////////    PURCHASES //////////////////////////////////////////////////////////////////////////////////////////
				
				
				
				//////////////////////////////////    PURCHASES RETURN //////////////////////////////////////////////////////////////////////////////////////////
				$data['purchases_return']=  $this->mod_dar_old->getpurchases_return($data_posted,2);
				//////////////////////////////////    PURCHASES RETURN //////////////////////////////////////////////////////////////////////////////////////////

				
				
				//////////////////////////////////    PURCHASES RETURN //////////////////////////////////////////////////////////////////////////////////////////
				$data['payments']=  $this->mod_dar_old->getpayments($data_posted,2);
				//////////////////////////////////    PURCHASES RETURN //////////////////////////////////////////////////////////////////////////////////////////
			
				//////////////////////////////////    receipts RETURN //////////////////////////////////////////////////////////////////////////////////////////
				$data['receipts']=  $this->mod_dar_old->getreceipts($data_posted,2);
				//////////////////////////////////    receipts RETURN //////////////////////////////////////////////////////////////////////////////////////////
		// pm($data['payments']);
			$this->load->view($this->session->userdata('language')."/dailyactivityreportold/detail_report",$data);
	        

	         
	}

	 
}
