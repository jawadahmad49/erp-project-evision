<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Emptysale extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_customer","mod_common","mod_emptysale","mod_stockreport","mod_customerledger","mod_bank"
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
		
		$data['salelpg_list'] = $this->mod_emptysale->manage_salelpg($from_date,$to_date,$sale_point_id);
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Empty Sale LPG";
		$this->load->view($this->session->userdata('language')."/Emptysale/sale_lpg",$data);
	}

		public function add_sale_lpg()
	{
		
	      $login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '604' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Emptysale/index/');
			}
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		  if ($sale_point_id=='0') {
	  	$this->session->set_flashdata('err_message', '- Admin Has No Rights To Add Sale!');
			redirect(SURL . 'Emptysale');
			exit();
	  }
        $general = $this->db->query("select customer_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['customer_code'];
        $bank = $this->db->query("select bank_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['bank_code'];
        $data['customer_list'] =$this->db->query("select * from tblacode where general='$general'")->result_array();
        $data['banks_list'] =$this->db->query("select * from tblacode where general='$bank'")->result_array();
		$table='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		
		$table='tbl_company';       
		$data['pricing_centralized'] = $this->mod_common->get_all_records($table,"*");
 
		$this->load->view($this->session->userdata('language')."/Emptysale/add_sale_lpg",$data);
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
				redirect(SURL . 'Emptysale/add_sale_lpg');
			}

		 //echo "<pre>";print_r($this->input->post());exit;
			$add=  $this->mod_emptysale->add_sale_lpg($this->input->post());
            //echo "<pre>";print_r($add);exit;
             $same_page = $this->db->query("select same_page from tbl_company")->row_array()['same_page'];
			if($add and $same_page=='true') {
		            $this->session->set_flashdata('ok_message', 'Added Successfully!');
		            redirect(SURL . 'Emptysale/add_sale_lpg');
		        } else  if ($add || $add==0) {
		            $this->session->set_flashdata('ok_message', 'Added Successfully!');
		            redirect(SURL . 'Emptysale/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in adding please try again!');
		            redirect(SURL . 'Emptysale/');
		        }
		}
		//$this->add_direct_girn();
	}

	public function delete($id) {

		$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '604' limit 1")->row_array();
		if ($role['delete']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Emptysale/index/');
			}

		    $trans_id=$id;
		    $issuenos=$this->db->query("select issuenos from tbl_issue_goods where trans_id='$trans_id'")->row_array()['issuenos'];
			$date_array = array('issuenos' => $issuenos);
			$get_rec_date =  $this->mod_common->select_single_records('tbl_issue_goods',$date_array);

			//$sale_date=$this->input->post('date');
			$login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			$date_array = array('post_date>=' => $get_rec_date['issuedate'],'sale_point_id =' => $sale_point_id);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'Emptysale/');
			}
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
 
	
	
	    $login_user=$this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$saleid=$sale_point_id."-Sale-".$id;
		$receiveid=$sale_point_id."-Receive-".$id;


		$goodsecurity=$sale_point_id."-Receive Security-".$id;
		$goodsidss=$sale_point_id."-Sale Security-".$id;

		
		  
			$goodsidgasreturn=$sale_point_id."-Returned Gas-".$id;
			

		#-------------delete record--------------#
        $table = "tbl_issue_goods";
        $where = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";
        $delete = $this->mod_common->delete_record($table, $where);

        $tables = "tbl_issue_goods_detail";
        $wheres = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";
        $deletes = $this->mod_common->delete_record($tables, $wheres);


        $tablems = "tbltrans_master";
        $wherems = "vno = '".$saleid."'";
        $deletems = $this->mod_common->delete_record($tablems, $wherems);

        $tableds = "tbltrans_detail";
        $whereds = "vno = '".$saleid."'";
        $deleteds = $this->mod_common->delete_record($tableds, $whereds);

// securty


        $tablems = "tbltrans_master";
        $wherems = "vno = '".$goodsidss."'";
        $deletems = $this->mod_common->delete_record($tablems, $wherems);

        $tableds = "tbltrans_master";
        $whereds = "vno = '".$goodsecurity."'";
        $deleteds = $this->mod_common->delete_record($tableds, $whereds);

        //q();


        $tablems = "tbltrans_detail";
        $wherems = "vno = '".$goodsidss."'";
        $deletems = $this->mod_common->delete_record($tablems, $wherems);

        $tableds = "tbltrans_detail";
        $whereds = "vno = '".$goodsecurity."'";
        $deleteds = $this->mod_common->delete_record($tableds, $whereds);

        // end

        $tablemr = "tbltrans_master";
        $wheremr = "vno = '".$receiveid."'";
        $deletemr = $this->mod_common->delete_record($tablemr, $wheremr);

        $tabledr = "tbltrans_detail";
        $wheredr = "vno = '".$receiveid."'";
        $deletedr = $this->mod_common->delete_record($tabledr, $wheredr);
   // end

        $tablemr = "tbltrans_master";
        $wheremr = "vno = '".$goodsidgasreturn."'";
        $deletemr = $this->mod_common->delete_record($tablemr, $wheremr);

        $tabledr = "tbltrans_detail";
        $wheredr = "vno = '".$goodsidgasreturn."'";
        $deletedr = $this->mod_common->delete_record($tabledr, $wheredr);

        if ($delete) {
            $this->session->set_flashdata('ok_message', 'You have successfully deleted.');
            redirect(SURL . 'Emptysale/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Emptysale/');
        }
    }
	public function edit($id){

		$login_user=$this->session->userdata('id');
	    $role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '604' limit 1")->row_array();
		if ($role['edit']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Emptysale/index/');
			}

		if($id){
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
			//$sale_date=$this->input->post('date');
			$date_array = array('issuenos' => $id);
			$get_rec_date =  $this->mod_common->select_single_records('tbl_issue_goods',$date_array);

			//$sale_date=$this->input->post('date');
			$login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			$date_array = array('post_date>=' => $get_rec_date['issuedate'],'sale_point_id =' => $sale_point_id);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'Emptysale/');
			}
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
 
	
	    $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $general = $this->db->query("select customer_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['customer_code'];
        $bank = $this->db->query("select bank_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['bank_code'];
        $data['customer_list'] =$this->db->query("select * from tblacode where general='$general'")->result_array();
        $data['banks_list'] =$this->db->query("select * from tblacode where general='$bank'")->result_array();
		//$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		$tablem='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($tablem,"*");
		$table='tbl_issue_goods';
		$where = "issuenos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);
//echo '<pre>';print_r($data['single_edit']);exit;
		$data['edit_list'] = $this->mod_emptysale->edit_salelpg($id);
		//pm($data['edit_list'] );
		//echo '<pre>';print_r($data['customer_list']);exit;
		foreach ($data['edit_list'] as $key => $value) {
			$data['filledstock'][]=  $this->mod_emptysale->get_details($value['itemid'],$data['single_edit']['issuedate']);
	 	}
		 //$data['banks_list'] = $this->mod_bank->getOnlyBanks();
		 $table='tbl_company';       
		$data['pricing_centralized'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Update Sale LPG";
		$this->load->view($this->session->userdata('language')."/Emptysale/edit",$data);
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
		$this->load->view($this->session->userdata('language')."/Emptysale/add_sale_lpg",$data);
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
				redirect(SURL . 'Emptysale');
			}



			$add_salelpg=  $this->mod_emptysale->update_sale_lpg($this->input->post());
           
		        if ($add_salelpg || $add_salelpg==0) {
		            $this->session->set_flashdata('ok_message', '- Updated Successfully!');
		            redirect(SURL . 'Emptysale/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in updating please try again!');
		            redirect(SURL . 'Emptysale/');
		        }
		}
	}

	// function record_delete()
	// {
	// 	$login_user=$this->session->userdata('id');
 //        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
	// 	$id = $_POST['parentid'];
	// 	$saleid=$sale_point_id."-Sale-".$id;
		
		

	// 	$this->db->where('trans_id',$id,'sale_point_id',$sale_point_id);
	// 	$count = $this->db->count_all_results('tbl_issue_goods_detail');
			
 //        $tablems = "tbltrans_master";
 //        $wherems = "vno = '".$saleid."'";
 //        $deletems = $this->mod_common->delete_record($tablems, $wherems);

 //        $tableds = "tbltrans_detail";
 //        $whereds = "vno = '".$saleid."'";
 //        $deleteds = $this->mod_common->delete_record($tableds, $whereds);		#-------------delete record ajax--------------#
 
	// 	if($count <= 1){
	//        	$this->db->where(array("trans_id"=>$id,"sale_point_id"=>$sale_point_id));
	//        	$delete_goods = $this->db->delete("tbl_issue_goods");
	//     }

 //        $table = "tbl_issue_goods_detail";
 //        $deleteid=	$this->input->post('deleteid');
 //        $where = "srno = '" . $deleteid . "'";
 //        $delete_goods = $this->mod_common->delete_record($table, $where);

 //        //$repost = $this->mod_emptysale->repost_sale($id);

 //        if ($delete_goods) {
 //            echo '1';
	// 	 	exit;
	// 	 }
	// 	 else {
	// 	 	echo '0';
	// 	 	exit;
	// 	 }
	// }
	function record_delete()
	{   
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$id = $_POST['parentid'];
		$saleid=$sale_point_id."-Sale-".$id;
		
		$this->db->where('trans_id',$id,'sale_point_id',$sale_point_id);
		 
		$count = $this->db->query("SELECT COUNT(ig_detail_id) as count FROM tbl_issue_goods_detail where trans_id='$id' and sale_point_id='$sale_point_id'")->row_array()['count'];
 
        $table = "tbl_issue_goods_detail";
        $deleteid=	$this->input->post('deleteid');
        $where = "srno = '" . $deleteid . "'";
        $delete_goods = $this->mod_common->delete_record($table, $where);
       
        if ($count==1) {
        $table = "tbl_issue_goods";
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
		$this->load->view($this->session->userdata('language')."/Emptysale/single",$data);
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
		$this->load->view($this->session->userdata('language')."/Emptysale/single_salestax",$data);
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
		
		
		$this->load->view($this->session->userdata('language')."/Emptysale/single_small",$data);
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
