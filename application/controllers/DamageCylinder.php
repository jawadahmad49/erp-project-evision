<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DamageCylinder extends CI_Controller {

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
		
		$data['salelpg_list'] = $this->mod_emptysale->manage_damagesalelpg($from_date,$to_date);
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Damage Sale LPG";
		$this->load->view($this->session->userdata('language')."/DamageCylinder/sale_lpg",$data);
	}

		public function add_sale_lpg()
	{
		
				$data['banks_list'] = $this->mod_bank->getOnlyBanks();
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		//echo "<pre>";print_r($data['vendor_list']);exit;
		$table='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		
		$table='tbl_company';       
		$data['pricing_centralized'] = $this->mod_common->get_all_records($table,"central_pricing");
 
		$this->load->view($this->session->userdata('language')."/DamageCylinder/add_sale_lpg",$data);
	}
	public function add(){

		if($this->input->server('REQUEST_METHOD') == 'POST'){

			$sale_date=$this->input->post('date');
			$date_array = array('post_date>=' => $sale_date);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				$this->session->set_flashdata('err_message', 'Already closed for this date.');
				redirect(SURL . 'DamageCylinder/add_sale_lpg');
			}

		 //echo "<pre>";print_r($this->input->post());exit;
			$add=  $this->mod_emptysale->add_sale_lpg($this->input->post());
            //echo "<pre>";print_r($add);exit;
		        if ($add) {
		            $this->session->set_flashdata('ok_message', 'Added Successfully!');
		            redirect(SURL . 'DamageCylinder/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in adding please try again!');
		            redirect(SURL . 'DamageCylinder/');
		        }
		}
		//$this->add_direct_girn();
	}

	public function delete($id) {

	
	
		
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
			//$sale_date=$this->input->post('date');
			$date_array = array('issuenos' => $id);
			$get_rec_date =  $this->mod_common->select_single_records('tbl_issue_goods',$date_array);

			//$sale_date=$this->input->post('date');
			$date_array = array('post_date>=' => $get_rec_date['issuedate']);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'DamageCylinder/');
			}
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
 
	
	
	
		$saleid=$id."-Sale";
		$receiveid=$id."-Receive";


		$goodsecurity=$id."-Receive Security";
		$goodsidss=$id."-Sale Security";

		
		  
			$goodsidgasreturn=$id."-Returned Gas";
			

		#-------------delete record--------------#
        $table = "tbl_issue_goods";
        $where = "issuenos = '" . $id . "'";
        $delete = $this->mod_common->delete_record($table, $where);

        $tables = "tbl_issue_goods_detail";
        $wheres = "ig_detail_id = '".$id."'";
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
            redirect(SURL . 'DamageCylinder/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'DamageCylinder/');
        }
    }
	public function edit($id){
		if($id){
			
			
			
		
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
			//$sale_date=$this->input->post('date');
			$date_array = array('issuenos' => $id);
			$get_rec_date =  $this->mod_common->select_single_records('tbl_issue_goods',$date_array);

			//$sale_date=$this->input->post('date');
			$date_array = array('post_date>=' => $get_rec_date['issuedate']);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'DamageCylinder/');
			}
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
 
	
	
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
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
		 $data['banks_list'] = $this->mod_bank->getOnlyBanks();
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Update Damage Sale LPG";
		$this->load->view($this->session->userdata('language')."/DamageCylinder/edit",$data);
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
		$this->load->view($this->session->userdata('language')."/DamageCylinder/add_sale_lpg",$data);
		}
	}

	public function update(){
		if($this->input->server('REQUEST_METHOD') == 'POST'){


			$sale_date=$this->input->post('date');

			$date_array = array('post_date>=' => $sale_date);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'DamageCylinder');
			}



			$add_salelpg=  $this->mod_emptysale->update_sale_lpg($this->input->post());
           
		        if ($add_salelpg || $add_salelpg==0) {
		            $this->session->set_flashdata('ok_message', '- Updated Successfully!');
		            redirect(SURL . 'DamageCylinder/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in updating please try again!');
		            redirect(SURL . 'DamageCylinder/');
		        }
		}
	}

	function record_delete()
	{
		$id = $_POST['parentid'];
		$saleid=$id."-Sale";
		$receiveid=$id."-Receive";


		$goodsecurity=$id."-Receive Security";
		$goodsidss=$id."-Sale Security";
  
		$goodsidgasreturn=$id."-Returned Gas";

		$this->db->where('ig_detail_id',$id);
		$count = $this->db->count_all_results('tbl_issue_goods_detail');
			
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
		#-------------delete record ajax--------------#
 
		if($count <= 1){
	       	$this->db->where(array("issuenos"=>$id));
	       	$delete_goods = $this->db->delete("tbl_issue_goods");
	    }

        $table = "tbl_issue_goods_detail";
        $deleteid=	$this->input->post('deleteid');
        $where = "srno = '" . $deleteid . "'";
        $delete_goods = $this->mod_common->delete_record($table, $where);

        $repost = $this->mod_emptysale->repost_sale($id);

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
		$this->load->view($this->session->userdata('language')."/DamageCylinder/single",$data);
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
		$this->load->view($this->session->userdata('language')."/DamageCylinder/single_salestax",$data);
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
		
		
		$this->load->view($this->session->userdata('language')."/DamageCylinder/single_small",$data);
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
