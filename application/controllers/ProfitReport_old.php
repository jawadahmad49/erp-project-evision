<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProfitReport_old extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_profitreport_old","mod_common"
        ));
        
    }

	public function index()
	{
		$table='tbltrans_detail';
		$data['stock_report_list'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Profit Loss Report";	
		$table='tblcategory';       
        $data['category_list'] = $this->mod_common->get_all_records($table,"*");
        $this->load->view($this->session->userdata('language')."/profit_report_old/search",$data);       	
	}


	public function detail_report()
	{							
		$month = $_POST["month"];
		$year = $_POST["year"];
		
		if($year == date('Y')){
			$first_date = date('Y-m-01', strtotime("$month, $year"));
			$last_date = date('Y-m-t', strtotime("$month, $year"));
		}elseif($year==date('Y')+1){
			$first_date = date('Y-m-01', strtotime("$month, +1 year"));
			$last_date = date('Y-m-t', strtotime("$month, +1 year"));
		}
		
				$table='tbl_company';       
				$data['company'] = $this->mod_common->get_all_records($table,"*");

				$data["title"] = "Profit Loss for Period";
		 		$data['from_date'] = $first_date;
				$data['to_date'] = $last_date;
				$data["title"] = "Profit Loss for Period";
				$data['c_month'] = $month;
		
				$data['c_year'] = $year;


				$table='tbl_company';       
				$data['company'] = $this->mod_common->get_all_records($table,"*");

				//$from_date=$data['from_date'];
				//$to_date=$data['to_date'];
				$from_date = $first_date;
				$to_date = $last_date;
		
				$data_posted = array('from_date' => $from_date, 'to_date' => $to_date);
				$data['daterange'] =  $from_date.' to '.$to_date;
				$new_date['from_date']=$from_date;
				$new_date['to_date']=$to_date;
				//$data['one_date_report'] = $this->mod_profitreport_old->getdate_stock_report($new_date,2);
				$date_for_item['to_date']=$to_date;
				$data['report_type'] = 2;
				$data['sale']=  $this->mod_profitreport_old->getsales($data_posted,2);
				//	 pm($data['sale']);
				$where_cat_id =''; // array('catcode=' => 1);
				$data['itemname']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_cat_id);
				//$tables='tblmaterial_coding';       
				//$data['itemname'] = $this->mod_common->get_all_records($tables,"*");
				//$data['itemname_return'] = $this->mod_common->get_all_records($tables,"*");
				//////////////////////////////////    SALES //////////////////////////////////////////////////////////////////////////////////////////
				//////////////////////////////////    SALES //////////////////////////////////////////////////////////////////////////////////////////
		 
		 
				//////////////////////////////////    SALES RETURN //////////////////////////////////////////////////////////////////////////////////////////
				$data['sale_return']=  $this->mod_profitreport_old->getsales_return($data_posted,2);
				//////////////////////////////////    PURCHASES //////////////////////////////////////////////////////////////////////////////////////////
				
				
				///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//////////////////////////////////    PURCHASES //////////////////////////////////////////////////////////////////////////////////////////
				$data['purchases']=  $this->mod_profitreport_old->getpurchases($data_posted,2);
				//////////////////////////////////    PURCHASES //////////////////////////////////////////////////////////////////////////////////////////
				
				
				
				//////////////////////////////////    PURCHASES RETURN //////////////////////////////////////////////////////////////////////////////////////////
				$data['purchases_return']=  $this->mod_profitreport_old->getpurchases_return($data_posted,2);
				//////////////////////////////////    PURCHASES RETURN //////////////////////////////////////////////////////////////////////////////////////////

				
				
				//////////////////////////////////    PURCHASES RETURN //////////////////////////////////////////////////////////////////////////////////////////
				$data['payments']=  $this->mod_profitreport_old->getpayments_new($data_posted,2);
				//////////////////////////////////    PURCHASES RETURN //////////////////////////////////////////////////////////////////////////////////////////
			
				//////////////////////////////////    receipts RETURN //////////////////////////////////////////////////////////////////////////////////////////
				$data['receipts']=  $this->mod_profitreport_old->getreceipts($data_posted,2);
				//////////////////////////////////    receipts RETURN //////////////////////////////////////////////////////////////////////////////////////////
		// pm($data['payments']);
			$this->load->view($this->session->userdata('language')."/profit_report_old/detail_report",$data);
	        

	         
	}

}

?>
