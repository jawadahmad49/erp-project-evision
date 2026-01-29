<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VendorLedger extends CI_Controller {

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
            "mod_vendorledger","mod_common","mod_vendor","mod_salelpg"
        ));
        
    }

	public function index()
	{
		
	   $login_user=$this->session->userdata('id');
       $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
       $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
       $customer_code=$fix_code['customer_code'];
       $vendor_code=$fix_code['vendor_code'];
       $cash_code=$fix_code['cash_code'];
       $sale_point_id=$fix_code['sale_point_id'];
       $tax_pay=$fix_code['tax_pay'];
       $tax_receive=$fix_code['tax_receive'];
       $sales_code=$fix_code['sales_code'];
       $stock_code=$fix_code['stock_code'];
       $bank_code=$fix_code['bank_code'];
       $expense_code=$fix_code['expense_code'];
       $cost_of_goods_code=$fix_code['cost_of_goods_code'];
       $empty_stock_code=$fix_code['empty_stock_code'];
       $empty_sale_code=$fix_code['empty_sale_code'];
       $security_code=$fix_code['security_code'];
       
       
       if($sale_point_id !=''){ $where_codes = " and tblacode.general in('$customer_code','$vendor_code','$bank_code','$expense_code','$empty_stock_code','$empty_sale_code','$security_code') or left(acode,6) in('400200','400500','400600') or tblacode.acode in ('$cash_code','$sale_point_id','$tax_pay','$tax_receive','$sales_code','$stock_code','$cost_of_goods_code') "; }else{ $where_codes =""; }
       $data['vendor_list'] =  $this->db->query("select * from tblacode  where atype='Child' $where_codes")->result_array();


		//$data['vendor_list'] = $this->mod_vendor->getOnlyVendors_with_customer();
		//pm($data['vendor_list']);
		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		
		$user_id=$this->session->userdata('id');
		$where_right = array('uid' => $user_id,'pageid' => '10');
	    $data['bank_right']= $this->mod_common->select_array_records('tbl_user_rights',"*",$where_right);

	    if(!empty($data['bank_right']))
		{
			$data['bank_flage']='yes';
		}
		else
		{
			$data['bank_flage']='no';
		}


		#----load view----------#
		$data["title"] = "General Ledger";	
		$this->load->view($this->session->userdata('language')."/vendorledger/search",$data);
		



	}

	public function report($id='')
	{
		//pm($this->input->post());

		$data['myfrmdate'] = $this->input->post("from_date");
		$data['myto_date'] = $this->input->post("to_date");
		$data['myfilter'] = $this->input->post("filter");
		$data['myacode'] = $this->input->post("acode");
		$data['myid'] = $this->input->post("id");
		$data['myhdate'] = $this->input->post("hdate");
		$data['mysort'] = $this->input->post("sort");
		$data['myaname_hid'] = $this->input->post("aname_hid");


		if($this->input->server('REQUEST_METHOD') == 'POST' || $id !=''){
			
			$data['one']=2;
			$data['report']=  $this->mod_vendorledger->get_report($this->input->post(),$id);
			//pm($data['report']);exit;
			if($id !='')
			{
				$data['one']=1;
			}

			if($this->input->post('t_id'))
			{
				$count=1;foreach ($data['report'] as $key => $value) { 
			
				if(!$value['voucherno']){continue;}

					 $total_opngbl=$value['balance']; 
						 
					$total_debit+=$value['debit'];
					$total_credit+=$value['credit'];

		
					 $count++;} 

					$total_opngbl =str_replace(",", "", $total_opngbl);

					 if($this->input->post('edit_amount'))
					{
					  $total_opngbl=$total_opngbl+$this->input->post('edit_amount'); 
					}

					echo $total_opngbl;

					if(($total_opngbl)>0){echo  ' Dr';}else{ echo ' Cr';}

					echo '|';
					echo $total_opngbl;
					echo '|';


					if(($total_opngbl)>0){echo  'Dr';}else{ echo 'Cr';}

					exit();
				}





			//pm($data['report']); exit;
			#----check name already exist---------#
			// if ($this->mod_city->get_by_title($data['city_name'])) {
			// 	$this->session->set_flashdata('err_message', 'Name Already Exist.');
			// 	redirect(SURL . 'city/add_city');
			// 	exit();
			// }
			//pm($data);
			$table='tbl_company';
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
			if ($data['report']) {
				// if(!empty($this->input->post("pdf"))){

				// }
				
			 // 	$this->newpdf($data['report']);
	            $data["title"] = "General Ledger Report";
	            $this->load->view($this->session->userdata('language')."/vendorledger/single",$data);
	        } else {
	            //$this->session->set_flashdata('err_message', 'No Record Found.');
	            //redirect(SURL . 'vendorledger/');
	            $data["title"] = "General Ledger Report";
	            $this->load->view($this->session->userdata('language')."/vendorledger/single",$data);
	        }
	    }else{
	        //$data["filter"] = 'add';
	        $data["title"] = "General Ledger Report";    			
			$this->load->view($this->session->userdata('language')."/vendorledger/single",$data);
		}



	}
	public function report_old($id='')
	{
		//pm($this->input->post());

		$data['myfrmdate'] = $this->input->post("from_date");
		$data['myto_date'] = $this->input->post("to_date");
		$data['myfilter'] = $this->input->post("filter");
		$data['myacode'] = $this->input->post("acode");
		$data['myid'] = $this->input->post("id");
		$data['myhdate'] = $this->input->post("hdate");
		$data['mysort'] = $this->input->post("sort");
		$data['myaname_hid'] = $this->input->post("aname_hid");


		if($this->input->server('REQUEST_METHOD') == 'POST' || $id !=''){
			
			$data['one']=2;
			$data['report']=  $this->mod_vendorledger->get_report($this->input->post(),$id);
			//pm($data['report']);exit;
			if($id !='')
			{
				$data['one']=1;
			}

			if($this->input->post('t_id'))
			{
				$count=1;foreach ($data['report'] as $key => $value) { 
			
				if(!$value['voucherno']){continue;}

					 $total_opngbl=$value['balance']; 
						 
					$total_debit+=$value['debit'];
					$total_credit+=$value['credit'];

		
					 $count++;} 

					$total_opngbl =str_replace(",", "", $total_opngbl);

					 if($this->input->post('edit_amount'))
					{
					  $total_opngbl=$total_opngbl+$this->input->post('edit_amount'); 
					}

					echo $total_opngbl;

					if(($total_opngbl)>0){echo  ' Dr';}else{ echo ' Cr';}

					echo '|';
					echo $total_opngbl;
					echo '|';


					if(($total_opngbl)>0){echo  'Dr';}else{ echo 'Cr';}

					exit();
				}





			//pm($data['report']); exit;
			#----check name already exist---------#
			// if ($this->mod_city->get_by_title($data['city_name'])) {
			// 	$this->session->set_flashdata('err_message', 'Name Already Exist.');
			// 	redirect(SURL . 'city/add_city');
			// 	exit();
			// }
			//pm($data);
			$table='tbl_company';
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
			if ($data['report']) {
				// if(!empty($this->input->post("pdf"))){

				// }
				
			 // 	$this->newpdf($data['report']);
	            $data["title"] = "General Ledger Report";
	            $this->load->view($this->session->userdata('language')."/vendorledger/single_old",$data);
	        } else {
	            //$this->session->set_flashdata('err_message', 'No Record Found.');
	            //redirect(SURL . 'vendorledger/');
	            $data["title"] = "General Ledger Report";
	            $this->load->view($this->session->userdata('language')."/vendorledger/single_old",$data);
	        }
	    }else{
	        //$data["filter"] = 'add';
	        $data["title"] = "General Ledger Report";    			
			$this->load->view($this->session->userdata('language')."/vendorledger/single_old",$data);
		}



	}
	public function newpdf(){

		if($this->input->server('REQUEST_METHOD') == 'POST' || $id !=''){
			
			$data['one']=2;
			$data['report']=  $this->mod_vendorledger->get_report($this->input->post(),$id);
			
			if($id !='')
			{
				$data['one']=1;
			}

			if($this->input->post('t_id'))
			{
				$count=1;foreach ($data['report'] as $key => $value) { 
			
				if(!$value['voucherno']){continue;}

					 $total_opngbl=$value['balance']; 
						 
					$total_debit+=$value['debit'];
					$total_credit+=$value['credit'];

		
					 $count++;} 

					$total_opngbl =str_replace(",", "", $total_opngbl);

					 if($this->input->post('edit_amount'))
					{
					  $total_opngbl=$total_opngbl+$this->input->post('edit_amount'); 
					}

					echo $total_opngbl;

					if(($total_opngbl)>0){echo  ' Dr';}else{ echo ' Cr';}

					echo '|';
					echo $total_opngbl;
					echo '|';


					if(($total_opngbl)>0){echo  'Dr';}else{ echo 'Cr';}

					exit();
				}

            $from_date=$this->input->post('from_date');
           $to_date=$this->input->post('to_date');
           
			$table='tbl_company';
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
			
	    }

	    foreach ($data['report'] as $key => $value) {
	    	 $profilename =  $value['accountname'].' From '.$from_date.' To '.$to_date;
	    }

	  


		$this->load->view($this->session->userdata('language')."/vendorledger/pdffile",$data);

		$this->load->library('pdf');
			 $html = $this->output->get_output();
			 $this->dompdf->loadHtml($html);
			 $this->dompdf->setPaper('A4', 'landscape');
	        $this->dompdf->render();


	        
	        $this->dompdf->stream( $profilename.".pdf", array("Attachment"=>0));	
	}




}
