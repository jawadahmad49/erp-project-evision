<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SaleDateReport extends CI_Controller {


	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_salereport","mod_common","mod_customerstockledger","mod_customer","mod_salelpg"
        ));
        
    }

	public function index()
	{

		$table='tblacode';
		$where = "general='2004001000'";
		$data['customers'] = $this->mod_common->select_array_records($table,'*',$where);

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
		$data['items']=$this->db->query("select * from tblmaterial_coding where catcode='1'")->result_array();
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Cylinders Sale Report";	
		$this->load->view($this->session->userdata('language')."/saledatereport/search_report_item",$data);


		// $table='tblacode';
		// $where = "general='2004001000'";
		// $data['customers'] = $this->mod_common->select_array_records($table,'*',$where);

		// $table='tblmaterial_coding';
		// $data['items'] = $this->mod_common->get_all_records($table,"*");
		// $data["filter"] = '';
		// #----load view----------#
		// $data["title"] = "Sale B/W Date Report";	
		// $this->load->view('saledatereport/search',$data);
	}


	public function search_report()
	{
		$table='tblacode';
		$where = "general='2004001000'";
		$data['customers'] = $this->mod_common->select_array_records($table,'*',$where);

		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Sale B/W Date Report";	
		$this->load->view($this->session->userdata('language')."/saledatereport/search_report",$data);
	}
	// public function search_report_item()
	// {
	// 	$table='tblacode';
	// 	$where = "general='2004001000'";
	// 	$data['customers'] = $this->mod_common->select_array_records($table,'*',$where);

	// 	$table='tblmaterial_coding';
	// 	$data['items'] = $this->mod_common->get_all_records($table,"*");
	// 	$data["filter"] = '';
	// 	#----load view----------#
	// 	$data["title"] = "Sale B/W Date Report";	
	// 	$this->load->view('saledatereport/search_report_item',$data);
	// }

	public function details()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST'){
		

	 
            $data['report1']=  $this->input->post();
		 
			$data['report']=  $this->mod_salereport->get_details($this->input->post());
			
			

			$table='tbl_company';       
        	$data['company'] = $this->mod_common->get_all_records($table,"*");

				$data['from_date'] = date('Y-m-d');
				$data['to_date'] = date('Y-m-d');
				$data["title"] = "Today Sale  Report";


				if($this->input->post('from_date')!='')
				{
				$data['from_date'] = trim($this->input->post('from_date'));
				$data['to_date'] = trim($this->input->post('to_date'));
				$data["title"] = "Sale B/W Date Report";

			}
  
	            $this->load->view($this->session->userdata('language')."/saledatereport/detail",$data);
	       
			 }else{
	       
		    
	$login_user=$this->session->userdata('id');
    $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		  
 	$date_array = array('from_date' =>  date('Y-m-d') , 'to_date' => date('Y-m-d') ,'sale_point_id'=>$sale_point_id);
	$data['report1']=  $date_array;
	$data['report']=  $this->mod_salereport->get_details($date_array);
			

			$table='tbl_company';       
        	$data['company'] = $this->mod_common->get_all_records($table,"*");

				$data['from_date'] = date('Y-m-d');
				$data['to_date'] = date('Y-m-d');
				$data["title"] = "Today Sale  Report";


				if($this->input->post('from_date')!='')
				{
				$data['from_date'] = trim($this->input->post('from_date'));
				$data['to_date'] = trim($this->input->post('to_date'));
				$data["title"] = "Sale B/W Date Report";

			}
  
	            $this->load->view($this->session->userdata('language')."/saledatereport/detail",$data);
	}
	}
	public function item_report()
	{																		
			$data['report']=  $this->mod_salereport->get_details_item_report($this->input->post());
			//echo "<pre>";var_dump($data['report']);
			


			$table='tbl_company';       
			//pm($data['report']);
        	$data['company'] = $this->mod_common->get_all_records($table,"*");

			$data['from_date'] = date('Y-m-d');
			$data['to_date'] = date('Y-m-d');
			$data["title"] = "Today Sale  Report";

			$new_date['from_date']=$this->input->post('from_date');
			$new_date['to_date']=$this->input->post('to_date');
			$new_date['location']=$this->input->post('location');
			$new_date['typee']=$data['typee']=$this->input->post('type');
			$new_date['acode']=$data['acode']=$this->input->post('acode');
			$new_date['sale_type']=$data['sale_type']=$this->input->post('sale_type');
			$data['one_date_report'] = $this->mod_customerstockledger->getdate_stock_report($new_date,2);


			if($this->input->post('from_date')!='')
			{
				$data['from_date'] = trim($this->input->post('from_date'));
				$data['to_date'] = trim($this->input->post('to_date'));
				$data["title"] = "Sale B/W Date Report";
			}
			if ($data['report']) {

	            $this->load->view($this->session->userdata('language')."/saledatereport/detail_report",$data);
	        } else {
	            $this->session->set_flashdata('err_message', 'No Record Found.');
	            redirect(SURL . 'SaleDateReport');
	        }
	}

	public function item_report_detail()
	{																			

		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$data['acode'] = $this->input->post('acode');
				
			if($this->input->post('from_date')!='')
			{
				 $data['daterange'] = trim($this->input->post('from_date').'/'.$this->input->post('to_date'));
				$new_date['from_date']=$this->input->post('from_date');
				 $new_date['to_date']=$this->input->post('to_date');
				 $new_date['location']=$this->input->post('location');
				 $new_date['typee']=$data['typee']=$this->input->post('type');
				 $new_date['acode']=$data['acode']=$this->input->post('acode');
				$data['one_date_report'] = $this->mod_customerstockledger->getdate_stock_report($new_date,2);
				//pm($data['one_date_report']);exit();

				$date_for_item['to_date']=$this->input->post('to_date');
				$data['report_type'] = 2;
				$data['from_date'] = $new_date['from_date'];
				$data['to_date'] = $new_date['to_date'];
			}	
			else 
			{ 
				$day=$this->input->post('day')+1;

                $date_temp = $this->input->post('month') .' '. $day.' '.$this->input->post('year');

                $tdate = date('Y-m-d', strtotime($date_temp));

				$data['daterange'] = trim($tdate);
				$data['report_type'] = 1;
				$data['one_date_report'] = $this->mod_customerstockledger->getdate_stock_report($data['daterange']);
				$date_for_item['to_date']=$tdate;
			}


			$data['name'] = $this->input->post('name');
			$data['single'] = 2;


			$table='tblacode';
			$where = "acode='".$data['acode']."'";
			$data['name'] = $this->mod_common->select_single_records($table,$where);

			$data['report']=  $this->mod_customerstockledger->get_opening($this->input->post());


			$data['sale']=  $this->mod_customerstockledger->getsales($this->input->post(),2);

			
			$data['return']=  $this->mod_customerstockledger->getreturn($this->input->post());
			//pm($data['return']);exit();
			
			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");


			if($this->input->post('items') && $this->input->post('brandname')){
				$itemss=$this->input->post('items');
				$brandnames=$this->input->post('brandname');
				$where_cat_id = "catcode='1' AND brandname='$brandnames' AND materialcode='$itemss'";
				$data['itemname']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_cat_id);

			}elseif($this->input->post('items')){
				$itemss=$this->input->post('items');
				$where_cat_id = "catcode='1' AND  materialcode='$itemss'";
				$data['itemname']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_cat_id);

			}elseif($this->input->post('brandname')){
				$brandnames=$this->input->post('brandname');
				$where_cat_id = "catcode='1' AND brandname='$brandnames'";
				$data['itemname']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_cat_id);

			}else{
				//echo "string"; exit();
				$where_cat_id = array('catcode=' => 1);
				$data['itemname']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_cat_id);
			}

			if($this->input->post('from_date')!='')
			{
				$data['itemname']= $this->mod_common->select_array_records_item('tblmaterial_coding',"*",$date_for_item,$this->input->post(),2);
			}
			else
			{
				$data['itemname']= $this->mod_common->select_array_records_item('tblmaterial_coding',"*",$date_for_item,1);
			}


       		$tables='tblmaterial_coding';       
       		$data['itemname_return'] = $this->mod_common->get_all_records($tables,"*");


			if ($data['sale']) {
	            $data["title"] = "Customer Sale Report";
	            $this->load->view($this->session->userdata('language')."/saledatereport/detail_report_item",$data);
	        }
	        else{
	        	$this->session->set_flashdata('err_message', 'No Record Found.');
	        	 redirect(SURL . 'SaleDateReport');
			}
		}
		else
		{
			 redirect(SURL . 'SaleDateReport');
		}
	}
	
		public function single_report()
	{																			
		if($this->input->server('REQUEST_METHOD') == 'POST'){

			
			$data['acode'] = $this->input->post('acode');
			$data['location'] = $this->input->post('location');
			
			$data['daterange'] = trim($this->input->post('from_date').'/'.$this->input->post('to_date'));

			$data['name'] = $this->input->post('name');
			$data['single'] = 1;

			$table='tblacode';
			$where = "acode='".$data['acode']."'";
			$data['name'] = $this->mod_common->select_single_records($table,$where);

			
			$data['sale']=  $this->mod_customerstockledger->getsaler($this->input->post(),3);

			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");

       	
       		$tables='tblmaterial_coding';       
       		$where='catcode=1';       
       		$data['itemname'] = $this->mod_common->select_array_records($tables,"*",$where);
       		$data['itemname_return'] = $this->mod_common->select_array_records($tables,"*",$where);
			
			$new_date['from_date']=$this->input->post('from_date');
			$new_date['to_date']=$this->input->post('to_date');
			$new_date['location']=$this->input->post('location');
			
			
			$data['one_date_report'] = $this->mod_customerstockledger->getdate_stock_report_customer($new_date,2,$data['acode']);

			
			$data['from_date'] = $new_date['from_date'];
			$data['to_date'] = $new_date['to_date'];
			$data['sale_point_id'] = $new_date['location'];
			$acode = $data['acode'];
			
			if ($data['sale']) {
	            $data["title"] = "Customer Sale Report";
	            $this->load->view($this->session->userdata('language')."/saledatereport/detail_report_item",$data);
	        }
	        else{
	        	$this->session->set_flashdata('err_message', 'No Record Found.');
	        	 redirect(SURL . 'SaleDateReport/single_customer_report');
			}
		}
		else
		{
			 redirect(SURL . 'SaleDateReport/single_customer_report');
		}
	}
	public function single_customer_report()
	{
		
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $customer_code=$fix_code['customer_code'];
          if($customer_code !=''){ $where_customer= " and tblacode.general='$customer_code'  "; }else{ $where_customer ="and tblacode.general in('2004001000','2004002000')"; }
		$data['customer_list'] = $this->db->query("select * from tblacode where atype='Child' $where_customer")->result_array();
		$data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

        if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();

		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "single Party";	
		$this->load->view($this->session->userdata('language')."/saledatereport/single_party_report",$data);
	}

	public function detail($id){
		if($id){
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		$table='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		$table='tbl_issue_goods';

		$where = "issuenos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);


		$data['edit_list'] = $this->mod_salelpg->edit_salelpg($id);
		//echo '<pre>';print_r($data['edit_list']);exit;
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Sale B/W Date Report";
		$this->load->view($this->session->userdata('language')."/saledatereport/single",$data);
		}
	}



}
