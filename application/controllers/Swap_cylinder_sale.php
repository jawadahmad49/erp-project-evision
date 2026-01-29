<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Swap_cylinder_sale extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_customer","mod_common","Mod_swap_sale","mod_stockreport","mod_customerledger","mod_bank"
        ));
        
    }
	public function index()
	{
		if(isset($_POST['submit'])){			
			$from_date = date("Y-m-d", strtotime($_POST['from']));
			
			$to_date = date("Y-m-d", strtotime($_POST['to']));
			
		}else{
			$from_date = date('Y-m-d', strtotime('-15 day'));
			$to_date = date('Y-m-d');
		}
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		
		$data['swap_sale_list'] = $this->Mod_swap_sale->manage_swap_sale($from_date,$to_date,$sale_point_id);
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Swap Cylinder Sale";
		$this->load->view($this->session->userdata('language')."/Swap_cylinder_sale/manage_swap_sale",$data);
	}

		public function add_swap_sale()
	{
		
	      $login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '604' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Swap_cylinder_sale/index/');
			}
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		  if ($sale_point_id=='0') {
	  	$this->session->set_flashdata('err_message', '- Admin Has No Rights To Add Sale!');
			redirect(SURL . 'Swap_cylinder_sale');
			exit();
	  }
        $general = $this->db->query("select customer_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['customer_code'];
        $bank = $this->db->query("select bank_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['bank_code'];
        //$data['customer_list'] =$this->db->query("select * from tblacode where general='$general'")->result_array();
        $data['banks_list'] =$this->db->query("select * from tblacode where general='$bank'")->result_array();
		$table='tblmaterial_coding';       
        $data['item_list'] = $this->db->query("select * from tblmaterial_coding where catcode='7'")->result_array();

       $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
       $customer_code=$fix_code['customer_code'];
       $vendor_code=$fix_code['vendor_code'];
       $cash_code=$fix_code['cash_code'];
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

       $sale_point_id=$fix_code['sale_point_id'];
       $exp_code=$expense_code[0].$expense_code[1].$expense_code[2].$expense_code[3].$expense_code[4].$expense_code[5];
       $data['customer_list'] =$this->db->query("select * from tblacode  where atype='Child' and general in('$customer_code','$vendor_code','$bank_code','$expense_code','$empty_stock_code','$empty_sale_code','$security_code') or left(acode,6)='$exp_code' or left(acode,6)='200100' or general in ('1002003000','2006001000') or tblacode.acode in ('$cash_code','$sale_point_id','$tax_pay','$tax_receive','$sales_code','$stock_code','$cost_of_goods_code')")->result_array();
		
		$table='tbl_company';       
		$data['pricing_centralized'] = $this->mod_common->get_all_records($table,"*");
 
		$this->load->view($this->session->userdata('language')."/Swap_cylinder_sale/add_swap_sale",$data);
	}
	public function add(){

		if($this->input->server('REQUEST_METHOD') == 'POST'){

            $login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			$sale_date=$this->input->post('date');
			$date_array = array('post_date>=' => $sale_date,'sale_point_id =' => $sale_point_id);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				$this->session->set_flashdata('err_message', 'Already closed for this date.');
				redirect(SURL . 'Swap_cylinder_sale/add_swap_sale');
			}

		 //echo "<pre>";print_r($this->input->post());exit;
			$add=  $this->Mod_swap_sale->add_swap_sale($this->input->post());
            //echo "<pre>";print_r($add);exit;
             $same_page = $this->db->query("select same_page from tbl_company")->row_array()['same_page'];
			if($add and $same_page=='true') {
		            $this->session->set_flashdata('ok_message', 'Added Successfully!');
		            redirect(SURL . 'Swap_cylinder_sale/add_swap_sale');
		        } else  if ($add || $add==0) {
		            $this->session->set_flashdata('ok_message', 'Added Successfully!');
		            redirect(SURL . 'Swap_cylinder_sale/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in adding please try again!');
		            redirect(SURL . 'Swap_cylinder_sale/');
		        }
		}
		//$this->add_direct_girn();
	}

	public function delete($id) {

	  $login_user=$this->session->userdata('id');
	    $role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '201' limit 1")->row_array();
		if ($role['delete']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Swap_cylinder_sale/index/');
			}
		/////////////////////////////////////////////////////////////////////////////////////////////////////////
			//$sale_date=$this->input->post('date');
		    $trans_id=$id;
		    $irnos=$this->db->query("select irnos from tbl_swap_recv where trans_id='$trans_id'")->row_array()['irnos'];
			$date_array = array('irnos' => $irnos);
			$get_rec_date =  $this->mod_common->select_single_records('tbl_swap_recv',$date_array);

			//$sale_date=$this->input->post('date');
			
			$login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
            $date_array = array('post_date>=' => $get_rec_date['irdate'],'sale_point_id =' => $sale_point_id);

			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'Swap_cylinder_sale/');
			}
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
 
        $login_user=$this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
 
		$vno=$sale_point_id."-SCS-".$id;

		$this->db->trans_start();
		#-------------delete record--------------#
        $table = "tbl_swap_recv";
        $where = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";
        $delete = $this->mod_common->delete_record($table, $where);

        $tables = "tbl_swap_recv_detail";
        $wheres = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";
        $deletes = $this->mod_common->delete_record($tables, $wheres);

        $tablems = "tbltrans_master";
        $wherems = "vno = '".$vno."'";
        $deletems = $this->mod_common->delete_record($tablems, $wherems);

        $tableds = "tbltrans_detail";
        $whereds = "vno = '".$vno."'";
        $deleteds = $this->mod_common->delete_record($tableds, $whereds);


        $this->db->trans_complete();

        if ($delete) {
            $this->session->set_flashdata('ok_message', 'You have successfully deleted.');
            redirect(SURL . 'Swap_cylinder_sale/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Swap_cylinder_sale/');
        }
    }
	public function edit($id){

		$login_user=$this->session->userdata('id');
	    $role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '604' limit 1")->row_array();
		if ($role['edit']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Swap_cylinder_sale/index/');
			}

		if($id){
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
			//$sale_date=$this->input->post('date');
			$date_array = array('irnos' => $id);
			$get_rec_date =  $this->mod_common->select_single_records('tbl_swap_recv',$date_array);

			//$sale_date=$this->input->post('date');
			$login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			$date_array = array('post_date>=' => $get_rec_date['irdate'],'sale_point_id =' => $sale_point_id);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'Swap_cylinder_sale/');
			}
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
 
	
	    $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $general = $this->db->query("select customer_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['customer_code'];
        $bank = $this->db->query("select bank_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['bank_code'];
        //$data['customer_list'] =$this->db->query("select * from tblacode where general='$general'")->result_array();
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
       $customer_code=$fix_code['customer_code'];
       $vendor_code=$fix_code['vendor_code'];
       $cash_code=$fix_code['cash_code'];
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

       $sale_point_id=$fix_code['sale_point_id'];
       $exp_code=$expense_code[0].$expense_code[1].$expense_code[2].$expense_code[3].$expense_code[4].$expense_code[5];
       $data['customer_list'] =$this->db->query("select * from tblacode  where atype='Child' and general in('$customer_code','$vendor_code','$bank_code','$expense_code','$empty_stock_code','$empty_sale_code','$security_code') or left(acode,6)='$exp_code' or left(acode,6)='200100' or general in ('1002003000','2006001000') or tblacode.acode in ('$cash_code','$sale_point_id','$tax_pay','$tax_receive','$sales_code','$stock_code','$cost_of_goods_code')")->result_array();
       
        $data['banks_list'] =$this->db->query("select * from tblacode where general='$bank'")->result_array();     
        $data['item_list'] = $this->db->query("select * from tblmaterial_coding where catcode='7'")->result_array();
		$table='tbl_swap_recv';
		$where = "irnos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);
		$data['edit_list'] = $this->Mod_swap_sale->edit_swap_sale($id);

		// foreach ($data['edit_list'] as $key => $value) {
		// 	$data['filledstock'][]=  $this->mod_emptysale->get_details($value['itemid'],$data['single_edit']['issuedate']);
	 // 	}
		 $table='tbl_company';       
		$data['pricing_centralized'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Update Swap Cylinder Sale";
		$this->load->view($this->session->userdata('language')."/Swap_cylinder_sale/edit",$data);
		}
	}

	public function makenew($id){
		if($id){
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		$table='tblmaterial_coding';
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		$table='tbl_orderbooking';
		$where = "id='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);

		$data['edit_list'] = $this->mod_emptysale->edit_makeneworder($id);

		foreach ($data['edit_list'] as $key => $value) {
			$data['filledstock'][]=  $this->mod_emptysale->get_details($value['itemid'],$data['single_edit']['issuedate']);
			//$itemids = $value['itemid'];
			//$wherem = "materialcode!='$itemids'";
			//$data['item_lists'] = $this->mod_common->select_array_records($tablem,'*',$wherem);
		}
		//echo '<pre>';print_r($data['edit_list']);exit;
		$data["filter"] = '';
		$data["id"] = $id;
		#----load view----------#
		$data["title"] = "Update Sale LPG";
		$this->load->view($this->session->userdata('language')."/Swap_cylinder_sale/add_sale_lpg",$data);
		}
	}

	public function update(){
		if($this->input->server('REQUEST_METHOD') == 'POST'){

            $login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			$sale_date=$this->input->post('date');
			$date_array = array('post_date>=' => $sale_date,'sale_point_id =' => $sale_point_id);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'Swap_cylinder_sale');
			}



			$add_salelpg=  $this->Mod_swap_sale->update_swap_sale($this->input->post());
           
		        if ($add_salelpg || $add_salelpg==0) {
		            $this->session->set_flashdata('ok_message', '- Updated Successfully!');
		            redirect(SURL . 'Swap_cylinder_sale/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in updating please try again!');
		            redirect(SURL . 'Swap_cylinder_sale/');
		        }
		}
	}

function record_delete()
	{   
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$id = $_POST['parentid'];
		$saleid=$sale_point_id."-SCS-".$id;
		
		

		$this->db->where('trans_id',$id,'sale_point_id',$sale_point_id);
		$count = $this->db->count_all_results('tbl_swap_recv_detail');

        $table = "tbl_swap_recv_detail";
        $deleteid=	$this->input->post('deleteid');
        $where = "sr_no = '" . $deleteid . "'";
        $delete_goods = $this->mod_common->delete_record($table, $where);
       
        if ($count==1) {
        $table = "tbl_swap_recv";
        $where = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";
        $delete_goods = $this->mod_common->delete_record($table, $where);
			
        $tablems = "tbltrans_master";
        $wherems = "vno = '".$saleid."'";
        $deletems = $this->mod_common->delete_record($tablems, $wherems);

        $tableds = "tbltrans_detail";
        $whereds = "vno = '".$saleid."'";
        $deleteds = $this->mod_common->delete_record($tableds, $whereds);
        
        }


		
        if ($delete_goods) {
            echo '1';
		 	exit;
		 }
		 else {
		 	echo '0';
		 	exit;
		 }
	}

	public function detail($id){
		if($id){
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		$table='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		$table='tbl_swap_recv';
		$where = "irnos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);

		$data['edit_list'] = $this->Mod_swap_sale->edit_swap_sale($id);
		//echo '<pre>';print_r($data);
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
		//exit;
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Invoice";
		$this->load->view($this->session->userdata('language')."/Swap_cylinder_sale/single",$data);
		}
	}

	public function detail_salestax($id){
		if($id){
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		$table='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		$table='tbl_issue_goods';
		$where = "issuenos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);

		$data['edit_list'] = $this->mod_emptysale->edit_salelpg($id);
		//echo '<pre>';print_r($data);
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
		//exit;
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Invoice";
		$this->load->view($this->session->userdata('language')."/Swap_cylinder_sale/single_salestax",$data);
		}
	}

	public function detail_small($id){
		if($id){
			
			
 

			
			
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		$table='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		$table='tbl_issue_goods';
		$where = "issuenos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);

	 
$acode= $data['single_edit']['issuedto'];
$issuedate= $data['single_edit']['issuedate'];
		

		
		$data['edit_list'] = $this->mod_emptysale->edit_salelpg($id);
		//echo '<pre>';print_r($data);
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
		//exit;
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Invoice";
		
 
		$ftoday='2018-01-01';
	 	 $today= date("Y-m-d", strtotime($issuedate."-1 days" ));
		$date_array2 = array('from_date' => $ftoday,'to_date' => $today,'filter' => 'party','acode' => $acode,'id' => '','hdate' => '','sort' => 'date','aname_hid' => '');
		  $data['final_bal']=  $this->mod_customerledger->get_report($date_array2);

 
	 
	 		foreach ($data['final_bal'] as $key => $value) {
			$data['report_new'] = $value['tbalance'];
			//$itemids = $value['itemid'];
			//$wherem = "materialcode!='$itemids'";
			//$data['item_lists'] = $this->mod_common->select_array_records($tablem,'*',$wherem);
		}
		//pm(	$data['report_new']);
		
		
		$this->load->view($this->session->userdata('language')."/Swap_cylinder_sale/single_small",$data);
		}
	}

	function get_filledstock()
	{
		$data['report']=  $this->mod_emptysale->get_details($this->input->post());
		//pm($data['report']);
		foreach ($data['report'] as $key => $value) {
			//pm($value);
		 	//echo $value['filled'];
		 	//print $value;
		 	echo json_encode($value);
		}
		
	}
	function get_filledstockdate()
	{
		$data['report']=  $this->mod_emptysale->get_details($this->input->post());
		//pm($data['report']);
		foreach ($data['report'] as $key => $value) {
			//pm($value);
		 	echo $value['empty'];
		}
		
	}
}
