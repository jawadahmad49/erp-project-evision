<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExpenseType extends CI_Controller {

 
	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_expensetype","mod_common"
        ));
        
    }

	public function index()
	{
		$login_user=$this->session->userdata('id');
      $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
       $general = $this->db->query("select expense_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['expense_code'];
        if($sale_point_id=='0'){
       $where_general="and left(acode,6)='400100' and atype='Child'";
        }else{
       $where_general="and general='$general'";
        }
      
		$data['expense_list'] = $this->db->query("select * from tblacode where ac_status='Active' $where_general")->result_array(); 
		

		//$data['expense_list'] = $this->mod_expensetype->getExpenseList();

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Expense Type";	
		$this->load->view($this->session->userdata('language')."/expensetype/manage_expensetype",$data);
	}

	public function add()
	{   
		$login_user=$this->session->userdata('id');
	    $role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '303' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'ExpenseType/index/');
			}
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
        $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

        if($sale_point_id !=''){ $where_sale_point_id= "and sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['location']=$this->db->query("select * from tbl_sales_point where sale_point_id in (select sale_point_id from tbl_code_mapping) $where_sale_point_id")->result_array();
		
		if($this->input->server('REQUEST_METHOD') == 'POST'){

$this->db->trans_start();
			// $types = trim($this->input->post('types'));
	  //       if($types=="Expense"){
			// 	$edata['datas'] = $this->mod_expensetype->accountcode_forexpensetype();
			// 	$data['acode']=$edata['datas'];
			// 	$data['general']="4001001000";

	  //       }else{
			// 	$idata['datas'] = $this->mod_expensetype->accountcode_forincometype();
			// 	$data['acode']=$idata['datas'];
			// 	$data['general']="3002001000";
	           
	  //       }
       // $login_user=$this->session->userdata('id');
       // $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
      $sale_point_id=$this->input->post('location');
       $general = $this->db->query("select expense_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['expense_code'];
       // echo $general;exit();

       $rest_creditors_code=$general[0].$general[1].$general[2].$general[3].$general[4].$general[5].$general[6];
       $edata['datas'] = $this->mod_expensetype->accountcode_forexpensetype($rest_creditors_code);

		    $udata['acode']=$edata['datas'];
			$udata['aname'] = trim($this->input->post('expensename'));
			$udata['segment'] = $this->input->post('types');
			$udata['ac_status'] = $this->input->post('status');
			$udata['general']=$general;
			$udata['atype']="Child";
			$udata['family']="L";
			$udata['sledger']="No";
			$udata['dlimit']=0;
			$udata['climit']=0;
 
            //pm($udata);exit();
			$table='tblacode';
			$res = $this->mod_common->insert_into_table($table,$udata);

			$this->db->trans_complete();

			if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            redirect(SURL . 'ExpenseType/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'ExpenseType/');
	        }
	    }
        $data["filter"] = 'add';
        $data["title"] = "Add Expense Type";    			
		$this->load->view($this->session->userdata('language')."/expensetype/add",$data);
	}


	public function edit($id){
		$login_user=$this->session->userdata('id');
	    $role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '303' limit 1")->row_array();
		if ($role['edit']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'ExpenseType/index/');
			}
		if($id){
			$table='tblacode';
			$where = "id='$id'";
			$data['expensetype'] = $this->mod_common->select_single_records($table,$where);
			
			$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
        $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

        if($sale_point_id !=''){ $where_sale_point_id= "and sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['location']=$this->db->query("select * from tbl_sales_point where sale_point_id in (select sale_point_id from tbl_code_mapping) $where_sale_point_id")->result_array();
	        $data["filter"] = 'edit';
        	$data["title"] = "Update Expense Type";
			$this->load->view($this->session->userdata('language')."/expensetype/add", $data);
		}
		/* Update Data */
		if($this->input->server('REQUEST_METHOD') == 'POST'){
$this->db->trans_start();
			// $types = trim($this->input->post('types'));
	  //       if($types=="Expense"){
			// 	$edata['datas'] = $this->mod_expensetype->accountcode_forexpensetype();
			// 	$data['acode']=$edata['datas'];
			// 	$data['general']="4001001000";

	  //       }else{
			// 	$idata['datas'] = $this->mod_expensetype->accountcode_forincometype();
			// 	$data['acode']=$idata['datas'];
			// 	$data['general']="3002001000";
	           
	  //       }
       // $login_user=$this->session->userdata('id');
       // $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
       $sale_point_id=$this->input->post('location');
       $general = $this->db->query("select expense_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['expense_code'];
       $rest_creditors_code=$general[0].$general[1].$general[2].$general[3].$general[4].$general[5].$general[6];
      $edata['datas'] = $this->mod_expensetype->accountcode_forexpensetype($rest_creditors_code);

            $udata['acode']=$edata['datas'];
			$udata['aname'] = trim($this->input->post('expensename'));
			$udata['segment'] = $this->input->post('types');
			$udata['ac_status'] = $this->input->post('status');
			//$data['modify_by'] = $_SESSION['id'];
			//$data['modify_date']= date('Y-m-d');
			$editid = $this->input->post('id');
			$udata['general']=$general;
 
			$table='tblacode';
			$where = "id='$editid'";
	 		$res=$this->mod_common->update_table($table,$where,$udata);
$this->db->trans_complete();
			if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
	            redirect(SURL . 'ExpenseType/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Operation Failed.');
	            redirect(SURL . 'ExpenseType/');
	        }
	    }
	}

	public function delete($id) {
		$login_user=$this->session->userdata('id');
	    $role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '303' limit 1")->row_array();
		if ($role['delete']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'ExpenseType/index/');
			}
		if ($this->mod_expensetype->used_in_trans($id)) {
			
	 
			$this->session->set_flashdata('err_message', 'There are transactions recorded for this expense/income type, you can not delete it.');
			redirect(SURL . 'ExpenseType/');
			exit();
		} 
		 
		#-------------delete record--------------#
        $table = "tblacode";
        $where = "id = '" . $id . "'";
        $delete = $this->mod_common->delete_record($table, $where);

        if ($delete) {
            $this->session->set_flashdata('ok_message', 'You have successfully deleted.');
            redirect(SURL . 'ExpenseType/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'ExpenseType/');
        }
    }

}
