<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mapcities extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_customer","mod_common","mod_salelpg","mod_stockreport","mod_customerledger","mod_bank","mod_customerstockledger"
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
		
		$data['salelpg_list'] = $this->mod_salelpg->manage_salelpg($from_date,$to_date);
		///echo "<pre>";print_r($data['salelpg_list']);exit;
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Sale LPG";
		$this->load->view($this->session->userdata('language')."/map_cities/mapcities",$data);
	}

public function add_map_cities()
	{
		
		$data['banks_list'] = $this->mod_bank->getOnlyBanks();
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		
	

		//$table='tblmaterial_coding';  
         $data['adminlist'] = $this->db->get("tbl_admin")->result_array();	
//echo "<pre>";print_r($data['adminlist']);exit;		 
        $data['city_list'] = $this->db->get("tbl_city")->result_array();
		  $data['area_list'] = $this->db->get("tbl_area")->result_array();
		
		$table='tbl_company';       
		$data['pricing_centralized'] = $this->mod_common->get_all_records($table,"central_pricing");
 
		$this->load->view($this->session->userdata('language')."/map_cities/add_map_cities",$data);
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
 
		$this->load->view($this->session->userdata('language')."/sale_lpg/add_sale_lpg",$data);
	}
	public function add(){

		if($this->input->server('REQUEST_METHOD') == 'POST'){

			$sale_date=$this->input->post('date');
			$date_array = array('post_date>=' => $sale_date);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				$this->session->set_flashdata('err_message', 'Already closed for this date.');
				redirect(SURL . 'SaleLPG/add_sale_lpg');
			}

		 //echo "<pre>";print_r($this->input->post());exit;
			$add=  $this->mod_salelpg->add_sale_lpg($this->input->post());
            //echo "<pre>";print_r($add);exit;
		        if ($add) {
		            $this->session->set_flashdata('ok_message', 'Added Successfully!');
		            redirect(SURL . 'SaleLPG/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in adding please try again!');
		            redirect(SURL . 'SaleLPG/');
		        }
		}
		//$this->add_direct_girn();
	}

	public function add_sale_new(){
	 
		$add = $this->mod_salelpg->add_sale_lpg_new($_POST);
		echo $add;
	}

	public function delete($id) {
 
			$date_array = array('issuenos' => $id);
			$get_rec_date =  $this->mod_common->select_single_records('tbl_issue_goods',$date_array);

			//$sale_date=$this->input->post('date');
			$date_array = array('post_date>=' => $get_rec_date['issuedate']);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'SaleLPG/');
			} 
	
		$saleid=$id."-Sale";
		$receiveid=$id."-Receive";
		$goodsecurity=$id."-Receive Security";
		$goodsidss=$id."-Sale Security";
		$goodsidgasreturn=$id."-Returned Gas";
		
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
 
        $tablems = "tbltrans_master";
        $wherems = "vno = '".$goodsidss."'";
        $deletems = $this->mod_common->delete_record($tablems, $wherems);

        $tableds = "tbltrans_master";
        $whereds = "vno = '".$goodsecurity."'";
        $deleteds = $this->mod_common->delete_record($tableds, $whereds);
 

        $tablems = "tbltrans_detail";
        $wherems = "vno = '".$goodsidss."'";
        $deletems = $this->mod_common->delete_record($tablems, $wherems);

        $tableds = "tbltrans_detail";
        $whereds = "vno = '".$goodsecurity."'";
        $deleteds = $this->mod_common->delete_record($tableds, $whereds);
 
        $tablemr = "tbltrans_master";
        $wheremr = "vno = '".$receiveid."'";
        $deletemr = $this->mod_common->delete_record($tablemr, $wheremr);

        $tabledr = "tbltrans_detail";
        $wheredr = "vno = '".$receiveid."'";
        $deletedr = $this->mod_common->delete_record($tabledr, $wheredr);

        $tablemr = "tbltrans_master";
        $wheremr = "vno = '".$goodsidgasreturn."'";
        $deletemr = $this->mod_common->delete_record($tablemr, $wheremr);

        $tabledr = "tbltrans_detail";
        $wheredr = "vno = '".$goodsidgasreturn."'";
        $deletedr = $this->mod_common->delete_record($tabledr, $wheredr);

        if ($delete) {
            $this->session->set_flashdata('ok_message', 'You have successfully deleted.');
            redirect(SURL . 'SaleLPG/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'SaleLPG/');
        }
    }



    public function delete_row_ajax() {

    	$id = $_POST['id'];
	
		$saleid=$id."-Sale";
		$receiveid=$id."-Receive"; 
		
        // $table = "tbl_issue_goods";
        // $where = "issuenos = '" . $id . "'";
        // $delete = $this->mod_common->delete_record($table, $where);

        $tables = "tbl_issue_goods_detail";
        $wheres = "ig_detail_id = '".$id."'";
        $deletes = $this->mod_common->delete_record($tables, $wheres);


        $tablems = "tbltrans_master";
        $wherems = "vno = '".$saleid."'";
        $deletems = $this->mod_common->delete_record($tablems, $wherems);

        $tableds = "tbltrans_detail";
        $whereds = "vno = '".$saleid."'";
        $deleteds = $this->mod_common->delete_record($tableds, $whereds);

        $tabledr = "tbltrans_detail";
        $wheredr = "vno = '".$receiveid."'";
        $deletedr = $this->mod_common->delete_record($tabledr, $wheredr);

        if ($delete) {
           echo "1";
        } else {
           echo "0";
        }
    }



	public function edit($id){
		if($id){
			$date_array = array('issuenos' => $id);
			$get_rec_date =  $this->mod_common->select_single_records('tbl_issue_goods',$date_array);

			//$sale_date=$this->input->post('date');
			$date_array = array('post_date>=' => $get_rec_date['issuedate']);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'SaleLPG/');
			} 
	
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		$tablem='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($tablem,"*");
		$table='tbl_issue_goods';
		$where = "issuenos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);
 
		$data['edit_list'] = $this->mod_salelpg->edit_salelpg($id); 
		foreach ($data['edit_list'] as $key => $value) {
			$data['filledstock'][]=  $this->mod_salelpg->get_details($value['itemid'],$data['single_edit']['issuedate']);
	 	}
		 $data['banks_list'] = $this->mod_bank->getOnlyBanks();
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Update Sale LPG";
		$this->load->view($this->session->userdata('language')."/sale_lpg/edit",$data);
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

		$data['edit_list'] = $this->mod_salelpg->edit_makeneworder($id);

		foreach ($data['edit_list'] as $key => $value) {
			$data['filledstock'][]=  $this->mod_salelpg->get_details($value['itemid'],$data['single_edit']['issuedate']);
			//$itemids = $value['itemid'];
			//$wherem = "materialcode!='$itemids'";
			//$data['item_lists'] = $this->mod_common->select_array_records($tablem,'*',$wherem);
		}
		//echo '<pre>';print_r($data['edit_list']);exit;
		$data["filter"] = '';
		$data["id"] = $id;
		#----load view----------#
		$data["title"] = "Update Sale LPG";
		$this->load->view($this->session->userdata('language')."/sale_lpg/add_sale_lpg",$data);
		}
	}

	public function update(){
		if($this->input->server('REQUEST_METHOD') == 'POST'){


			$sale_date=$this->input->post('date');

			$date_array = array('post_date>=' => $sale_date);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'SaleLPG');
			}



			$add_salelpg=  $this->mod_salelpg->update_sale_lpg($this->input->post());
            //echo "<pre>";print_r($add_salelpg);exit;
		        if ($add_salelpg || $add_salelpg==0) {
		            $this->session->set_flashdata('ok_message', '- Updated Successfully!');
		            redirect(SURL . 'SaleLPG/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in updating please try again!');
		            redirect(SURL . 'SaleLPG/');
		        }
		}
		//$this->add_direct_girn();
	}

	function record_delete()
	{
		$id = $_POST['parentid'];
		$saleid=$id."-Sale";
		$receiveid=$id."-Receive";

		$this->db->where('ig_detail_id',$id);
		$count = $this->db->count_all_results('tbl_issue_goods_detail');

		$goodsecurity=$id."-Receive Security";
		$goodsidss=$id."-Sale Security";
  
		$goodsidgasreturn=$id."-Returned Gas";
			
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

		// if($count <= "1"){
	       	// $this->db->where(array("issuenos"=>$id));
	       	// $delete_goods = $this->db->delete("tbl_issue_goods");
	    // }


        $table = "tbl_issue_goods_detail";
        $deleteid=	$this->input->post('deleteid');
        $where = "srno = '" . $deleteid . "'";
        $delete_goods = $this->mod_common->delete_record($table, $where);

		
		
		$repost = $this->mod_salelpg->repost_sale($id);

		
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

		$data['edit_list'] = $this->mod_salelpg->edit_salelpg($id);
		//echo '<pre>';print_r($data);
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
		//exit;
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Invoice";
		$this->load->view($this->session->userdata('language')."/sale_lpg/single",$data);
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

		$data['edit_list'] = $this->mod_salelpg->edit_salelpg($id);
		//echo '<pre>';print_r($data);
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
		//exit;
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Invoice";
		$this->load->view($this->session->userdata('language')."/sale_lpg/single_salestax",$data);
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
		

		
		$data['edit_list'] = $this->mod_salelpg->edit_salelpg($id);
		//echo '<pre>';print_r($data);
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
		//exit;
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Invoice";
		
 
		$ftoday='2018-01-01';
	 	 $today=  $issuedate;
		$date_array2 = array('from_date' => $ftoday,'to_date' => $today,'filter' => 'party','acode' => $acode,'id' =>  $id ,'hdate' => '','sort' => 'date','aname_hid' => '');
		  $data['final_bal']=  $this->mod_customerledger->get_report_small($date_array2);

  // pm(	$data['final_bal']);
	 
	 		foreach ($data['final_bal'] as $key => $value) {
			$data['report_new'] = $value['tbalance'];
			//$itemids = $value['itemid'];
			//$wherem = "materialcode!='$itemids'";
			//$data['item_lists'] = $this->mod_common->select_array_records($tablem,'*',$wherem);
		}
		  
			//	$data['total_balance']=  $this->mod_customerstockledger->get_total_customer_stock_one($acode);

	//	 pm($data['total_balance']);


	
	 
			

			if($this->input->post('from_date')=='1947-01-01')
			{
				$data['from_date']='2018-01-01';
			}
			else
			{
				$data['from_date']=$this->input->post('from_date');
			}
			$data['opening']=  $this->mod_customerstockledger->get_opening($date_array2,1);
			$data['itemname'] = $this->mod_common->select_array_records('tblmaterial_coding',"*","catcode='1' "); 
		$total_return = array();
		$total_sale = array();
		$total_return_sale=array();
		 $data['return']=  $this->mod_customerstockledger->getreturn($date_array2);
			foreach ($data['return'] as $key => $value) {
				if(count($value['return']>1))
 				{
			 		foreach ($value['return'] as $key => $value_sub) {
			 			$total_return[$value_sub['itemid']]=$total_return[$value_sub['itemid']]+$value_sub['qty'];
			 		}
				}
			}
			
 

			$data['sale']=  $this->mod_customerstockledger->getsale($date_array2);
			foreach ($data['sale'] as $key => $value) {
				if(count($value['sale']>1))
 				{
			 		foreach ($value['sale'] as $key => $value_sub) {
			 			$total_sale[$value_sub['itemid']]=$total_sale[$value_sub['itemid']]+$value_sub['qty'];
			 		}
				}
			}
 
			for ($i=0; $i <count($data['opening']); $i++) { 
				$item_code=$data['opening'][$i]['itemid'];
				$opening_array[$item_code]=$data['opening'][$i]['opening'];
			}
			
			for ($i=0; $i <count($data['itemname']); $i++) { 
				$item_code= $data['itemname'][$i]['materialcode'];
				$total_return_sale[$item_code]=$total_sale[$item_code]-$total_return[$item_code]+$opening_array[$item_code];
			}
		
 			$data['total_return_sale']=$total_return_sale;
		 
		// pm($data['total_return_sale']);
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	



	
		
		$this->load->view($this->session->userdata('language')."/sale_lpg/single_small",$data);
		}
	}

	function get_filledstock()
	{
		$data['report']=  $this->mod_salelpg->get_details($this->input->post());
		//pm($data['report']);
		foreach ($data['report'] as $key => $value) {
			//pm($value);
		 	//echo $value['filled'];
		 	//print $value;
		 	echo json_encode($value);
		}
		
	}
	function today_amount_recv()
	{
		$data['report']=  $this->mod_salelpg->today_amount_recv($this->input->post());
		$total_recv=0;
		foreach ($data['report'] as $key => $value) {
			 
		 $total_recv+=$value['total_received'];
		}
		echo $total_recv;
	}
	function get_filledstockdate()
	{
		$data['report']=  $this->mod_salelpg->get_details($this->input->post());
		//pm($data['report']);
		foreach ($data['report'] as $key => $value) {
			//pm($value);
		 	echo $value['empty'];
		}
		
	}
}
