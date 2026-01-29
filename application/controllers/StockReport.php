<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StockReport extends CI_Controller {

	public function __construct() {
        parent::__construct();
       
        $this->load->model(array(
            "mod_stockreport","mod_common"
        ));
    }

	public function index()
	{
		 //error_reporting(E_ALL);
		 ini_set('memory_limit','2048M');

		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
        $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

        if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Stock Report";	
		// $table='tblcategory';       
  //       $data['category_list'] = $this->mod_common->get_all_records($table,"*");
        $data['category_list']=$this->db->query("select * from tblcategory where catcode='1'")->result_array();
 
        $data['openging_date'] = $this->mod_common->select_last_records('tbl_shop_opening')['date'];
        $this->load->view($this->session->userdata('language')."/stock_report/search",$data);       	
	}

	public function details()
	{
		 
		if($this->input->server('REQUEST_METHOD') == 'POST'){
       //pm($this->input->post());exit;
			   $data['from_date']= $from_date=$this->input->post('from_date');

			  $data['to_date']= $to_date=$this->input->post('to_date');

			 $data['category_id']=$category_id=$this->input->post('category');
			 $location=$this->input->post('location');
			 $data['location']=$location;

 

			  $last_post_date=$this->mod_common->select_last_records('tbl_posting_stock')['post_date'];

			   $where_last = "post_date = '" . $last_post_date . "' AND itemcode = '" . $id . "'";

			$last_day=$this->mod_common->select_single_records('tbl_posting_stock',$where_last);
//pm($last_day);
			//pm();
			$data['report']=  $this->mod_stockreport->get_details($this->input->post());
       //pm($data['report']);exit;
			$report_count=0;
			if($category_id==1){
			foreach ($data['report'] as $key => $value) {

				$id=$value['materialcode'];
			 
			 
				 $today_stock=$this->mod_common->stock($id,'empty',$from_date,1,$location); 
		//pm($data['today_stock']);exit;
		 

				$empty_filled= explode('_', $today_stock); 
				$data['report'][$report_count]['filled']=$empty_filled[0];
				$data['report'][$report_count]['empty']=$empty_filled[1];
		
			 
				$report_count++;
 
			}
			}else{
					foreach ($data['report'] as $key => $value) {

				$id=$value['materialcode'];
			 
			 
				 $today_stock=$this->mod_common->other_stock($id,'empty',$from_date, 1); 
		        $empty_filled= explode('_', $today_stock); 
				$data['report'][$report_count]['filled']=$empty_filled[0];
				$data['report'][$report_count]['empty']=$empty_filled[1];
		
			 
				$report_count++;
 
			}
			}
				
			
  	//pm($data['report']);
		//pm($empty_filled);
			$data['category_id']=  $this->input->post('category');


			$data['daterange'] = trim($this->input->post('from_date').'/'.$this->input->post('to_date'));
			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
			
			
			
			 
			if ($data['report']) {
			     $data["title"] = "Stock Report";
	            $this->load->view($this->session->userdata('language')."/stock_report/detail",$data);
	        } else {
	            $this->session->set_flashdata('err_message', 'No Record Found.');
	            redirect(SURL . 'StockReport/');
	        }
	    }else{
	        //$data["filter"] = 'add';
	        $data["title"] = "Stock Report";    			
			$this->load->view($this->session->userdata('language')."/stock_report/detail",$data);
		}
	}
		public function newpdf(){

		if($this->input->server('REQUEST_METHOD') == 'POST'){
       //pm($this->input->post());exit;
			   $data['from_date']= $from_date=$this->input->post('from_date');

			   $data['to_date']=$to_date=$this->input->post('to_date');

			 $category_id=$this->input->post('category');
			 $location=$this->input->post('location');
			 $data['location']=$location;

 

			  $last_post_date=$this->mod_common->select_last_records('tbl_posting_stock')['post_date'];

			   $where_last = "post_date = '" . $last_post_date . "' AND itemcode = '" . $id . "'";

			$last_day=$this->mod_common->select_single_records('tbl_posting_stock',$where_last);
//pm($last_day);
			//pm();
			$data['report']=  $this->mod_stockreport->get_details($this->input->post());
       //pm($data['report']);exit;
			$report_count=0;
			if($category_id==1){
			foreach ($data['report'] as $key => $value) {

				$id=$value['materialcode'];
			 
			 
				 $today_stock=$this->mod_common->stock($id,'empty',$from_date,1,$location); 
		// pm($data['today_stock']);exit;
		 

				$empty_filled= explode('_', $today_stock); 
				$data['report'][$report_count]['filled']=$empty_filled[0];
				$data['report'][$report_count]['empty']=$empty_filled[1];
		
			 
				$report_count++;
 
			}
			}else{
					foreach ($data['report'] as $key => $value) {

				$id=$value['materialcode'];
			 
			 
				 $today_stock=$this->mod_common->other_stock($id,'empty',$from_date, 1); 
				 
		        $empty_filled= explode('_', $today_stock); 
				$data['report'][$report_count]['filled']=$empty_filled[0];
				$data['report'][$report_count]['empty']=$empty_filled[1];
		
			 
				$report_count++;
 
			}
			}
				
			
  	//pm($data['report']);
		//pm($empty_filled);
			$data['category_id']=  $this->input->post('category');


			$data['daterange'] = trim($this->input->post('from_date').'/'.$this->input->post('to_date'));
			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
			
	    }

	  
	    	 $profilename =  $from_date;
	    	 // $profilename1 =  $to_date;
	    	 // $profilename2 =  $type;
	  
	  //pm($data);


		$this->load->view($this->session->userdata('language')."/stock_report/pdffile",$data);

		$this->load->library('pdf');
			 $html = $this->output->get_output();
			 $this->dompdf->loadHtml($html);
			 $this->dompdf->setPaper('A4', 'landscape');
	        $this->dompdf->render();


	        
	        $this->dompdf->stream( $profilename.".pdf", array("Attachment"=>0));	
	}


}
