<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchaseother extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_vendor","mod_common","mod_purchaseother","mod_salelpg","mod_admin","mod_bank","mod_vendorledger"
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
		
		$data['purchaseother_list'] = $this->mod_purchaseother->manage_purchaseother($from_date,$to_date,$sale_point_id);
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Purchase Other";
		$this->load->view($this->session->userdata('language')."/purchase_other/purchase_other",$data);
	}

	public function add_purchase_other()
	{
             $login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '103' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Purchaseother/index/');
			}
		    $login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
             if ($sale_point_id=='0') {
	           	$this->session->set_flashdata('err_message', '- Admin Has No Rights To Add Purchase!');
			    redirect(SURL . 'Purchaseother');
			    exit(); }
            $general = $this->db->query("select vendor_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['vendor_code'];
            $bank = $this->db->query("select bank_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['bank_code'];
            //$data['vendor_list'] = $this->mod_vendor->getOnlyVendors_only();
            $data['vendor_list'] =$this->db->query("select * from tblacode where general='$general'")->result_array();
            $data['bank_list'] =$this->db->query("select * from tblacode where general='$bank'")->result_array();
        $where_cat = array('id!=' => 1);
        $data['category_list']= $this->mod_common->select_array_records('tblcategory',"*",$where_cat);
        
        $where_cat_id = array('catcode!=' => 1);
        $data['item_list']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_cat_id);

        $where_cat_id = array('catcode!=' => 1);
        $data['item_list']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_cat_id);

				$data['cash_position'] = $this->mod_admin->cash_position();
				foreach ($data['cash_position'] as $key=>$datas) {
				$opening= $datas[opngbl]; 
				if($datas[optype]=='Credit'){ $opening=-1*$opening; } 
				$bal=$datas[damount]-$datas[camount]+$opening;
				}
				$data['cash_balance']=$bal;

		//$data['bank_list'] = $this->mod_bank->getOnlyBanks();
		$this->load->view($this->session->userdata('language')."/purchase_other/add_purchase_other",$data);

	}

public function get_bank_bal($bankcode,$dt,$ids)
	{
		   
				$bal=0;
				$data['bank_position']=  $this->mod_vendorledger->get_report($dt,$bankcode);  
				$bal=str_replace(",", "", $data['bank_position'][0]['tbalance']);   	  
		   
	

				$table='tbl_goodsreceiving';
				$where = "receiptnos='$ids'";
				$data['single_edit'] = $this->mod_common->select_single_records($table,$where);
				$total_paid=$data['single_edit']['total_paid'];
				if($data['single_edit']['bank_code']==$bankcode) {

				$bal=$bal+$total_paid;

				}
// exit;

				 print $bal;
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
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'Purchaseother/add_purchase_other');
			}

			$check_date=$this->input->post('date');
			$date = explode("-",$check_date);
			$year=$date[0];
			$month=$date[1];
			$datee=$date[2];
			$new_date=$year."-".$month."-".$datee;
			 
			if ($check_date!=$new_date) {
				$this->session->set_flashdata('err_message', 'Date Format IS Not Correct !');
				redirect(SURL . 'Purchaseother/add_purchase_other');
			}
			
			$add_purchaseother=  $this->mod_purchaseother->add_purchase_other($this->input->post());
            //echo "<pre>";print_r($add_purchaseempty);exit;
             $same_page = $this->db->query("select same_page from tbl_company")->row_array()['same_page'];
			if($add_purchaseother and $same_page=='true') {
		            $this->session->set_flashdata('ok_message', 'Added Successfully!');
		            redirect(SURL . 'Purchaseother/');
		        } else if ($add_purchaseother) {
		            $this->session->set_flashdata('ok_message', 'Added Successfully!');
		            redirect(SURL . 'Purchaseother/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in adding please try again!');
		            redirect(SURL . 'Purchaseother/');
		        }
		}
	}

		public function delete($id) {
			
			 $login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '103' limit 1")->row_array();
		if ($role['delete']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Purchaseother/index/');
			}
				
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
			//$sale_date=$this->input->post('date');
			$date_array = array('trans_id' => $id);
			$get_rec_date =  $this->mod_common->select_single_records('tbl_goodsreceiving',$date_array);

			//$sale_date=$this->input->post('date');
			$login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			$date_array = array('post_date>=' => $get_rec_date['receiptdate'],'sale_point_id =' => $sale_point_id);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'Purchaseother');
			}
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
 
			
			$login_user=$this->session->userdata('id');
			$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			$purchaseid=$sale_point_id."-Purchase-".$id;
			//$purchase_payment_id=$sale_point_id."-Purchase Payment-".$id;
		#-------------delete record--------------#
        $table = "tbl_goodsreceiving";
        $where = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";
        $delete_goods = $this->mod_common->delete_record($table, $where);

        $tables = "tbl_goodsreceiving_detail";
        $wheres = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";
        $deletes = $this->mod_common->delete_record($tables, $wheres);

        $tablems = "tbltrans_master";
        $wherems = "vno = '".$purchaseid."'";
        $deletems = $this->mod_common->delete_record($tablems, $wherems);

        $tableds = "tbltrans_detail";
        $whereds = "vno = '".$purchaseid."'";
        $deleteds = $this->mod_common->delete_record($tableds, $whereds);



        if ($delete_goods) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Purchaseother/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Purchaseother/');
        }
    }

	public function edit($id){
		  $login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '103' limit 1")->row_array();
		if ($role['edit']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Purchaseother/index/');
			}
		if($id){ 
			$date_array = array('receiptnos' => $id);
			$get_rec_date =  $this->mod_common->select_single_records('tbl_goodsreceiving',$date_array);

			//$sale_date=$this->input->post('date');
			$login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
            $date_array = array('post_date>=' => $get_rec_date['receiptdate'],'sale_point_id =' => $sale_point_id);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{ 
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'Purchaseother');
			} 
		// $data['vendor_list'] = $this->mod_vendor->getOnlyVendors_only();
			$login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
            $general = $this->db->query("select vendor_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['vendor_code'];
            $bank = $this->db->query("select bank_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['bank_code'];
            //$data['vendor_list'] = $this->mod_vendor->getOnlyVendors_only();
            $data['vendor_list'] =$this->db->query("select * from tblacode where general='$general'")->result_array();
            $data['bank_list'] =$this->db->query("select * from tblacode where general='$bank'")->result_array();
		$tablem='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($tablem,"*");
		$table='tbl_goodsreceiving';
		$where = "receiptnos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);

		$where_cat = array('id!=' => 1);
        $data['category_list']= $this->mod_common->select_array_records('tblcategory',"*",$where_cat);
		//echo '<pre>';print_r($data['single_edit']);exit;
		$data['edit_list'] = $this->mod_purchaseother->edit_purchaseother($id);
		// echo '<pre>';print_r($data['single_edit']);exit;
		// echo $data['single_edit']['total_paid'];
		
		foreach ($data['edit_list'] as $key => $value) {
			$data['otherstock'][]=  $this->mod_salelpg->get_details($value['itemid'],$data['single_edit']['receiptdate']);

			 } 
			if($data['single_edit']['pay_mode']=='cash'){
				 
				$data['cash_position'] = $this->mod_admin->cash_position();

				foreach ($data['cash_position'] as $key=>$datas) {

				$opening= $datas[opngbl]; 
				if($datas[optype]=='Credit'){ $opening=-1*$opening; } 

				$bal=$datas[damount]-$datas[camount]+$opening;
				 
			  $data['single_edit']['Cash_Balance']=$bal+$data['single_edit']['total_paid'];
				} 	
			}
			
			 
			if($data['single_edit']['pay_mode']=='bank'){
					$date=  $data['single_edit']['receiptdate'];			 
				  	$data['bank_position']=  $this->mod_vendorledger->get_report($date,$data['single_edit']['bank_code']);					
					  // $bal=str_replace(",", "", $data['bank_position'][0]['tbalance']); 

					 
			
				  // echo $data['single_edit']['Bank_Balance']=$bal+$data['single_edit']['total_paid'];


					   //waqas written method starts here
					
					 $bal=str_replace(",", "", $data['bank_position'][count($data['bank_position']) - 1]['tbalance']); 
					  $data['single_edit']['Bank_Balance']=$bal;

					  //waqas written method ends here
				  		   
			}
	 
		
		
		//$data['bank_list'] = $this->mod_bank->getOnlyBanks();
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Update Purchase Other";
		$this->load->view($this->session->userdata('language')."/purchase_other/edit",$data);
		}
	}
	
	
	public function get_cash_in_hand_bal($acc)
	{
		
		
			$bal=0;
		 
				$data['cash_position'] = $this->mod_admin->cash_position();

				foreach ($data['cash_position'] as $key=>$datas) {

				$opening= $datas[opngbl]; 
				if($datas[optype]=='Credit'){ $opening=-1*$opening; } 

				$bal=$datas[damount]-$datas[camount]+$opening;
				 
			  
				}
				print $bal;
	}
	
	
	
	
	function trans_delete()
	{
		
		#-------------delete record--------------#
		$parentid=	$this->input->post('parentid');
        $purchaseid=$parentid."-Purchase";
        $purchaseid_payments=$parentid."-Purchase Payment";
        $tablems = "tbltrans_master";
        $wherems = "vno = '".$purchaseid."'";
        $deletems = $this->mod_common->delete_record($tablems, $wherems);
        $tableds = "tbltrans_detail";
        $whereds = "vno = '".$purchaseid."'";
        $deleteds = $this->mod_common->delete_record($tableds, $whereds);
		
		
		$tablems = "tbltrans_master";
		$wherems = "vno = '".$purchaseid_payments."'";
		$deletems = $this->mod_common->delete_record($tablems, $wherems);
		$tableds = "tbltrans_detail";
		$whereds = "vno = '".$purchaseid_payments."'";
		$deleteds = $this->mod_common->delete_record($tableds, $whereds);

        if ($delete_goods) {
            echo '1';
		 	exit;
		 }
		 else {
		 	echo '0';
		 	exit;
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
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'Purchaseother');
			}
			$check_date=$this->input->post('date');
			$date = explode("-",$check_date);
			$year=$date[0];
			$month=$date[1];
			$datee=$date[2];
			$new_date=$year."-".$month."-".$datee;
			if ($check_date!=$new_date) {
				$this->session->set_flashdata('err_message', 'Date Format IS Not Correct !');
				redirect(SURL . 'Purchaseother/add_purchase_other');
			}
		//	pm($this->input->post());
			$add_purchaseother=  $this->mod_purchaseother->update_purchase_other($this->input->post());
		        if ($add_purchaseother || $add_purchaseother==0) {
		            $this->session->set_flashdata('ok_message', '- Updated Successfully!');
		            redirect(SURL . 'Purchaseother/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in updating please try again!');
		            redirect(SURL . 'Purchaseother/');
		        }
		}
	}

	// function record_delete()
	// {
		 
	// 	$login_user=$this->session->userdata('id');
 //        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
	// 	$parentid=	$this->input->post('parentid');
	// 	$purchaseid=$sale_point_id."-Purchase-".$parentid;

 //         $this->db->where('receipt_detail_id',$parentid,'sale_point_id',$sale_point_id);
	//      $count = $this->db->count_all_results('tbl_goodsreceiving_detail');

		  
 //        $tablems = "tbltrans_master";
 //        $wherems = "vno = '".$purchaseid."'";
 //        $deletems = $this->mod_common->delete_record($tablems, $wherems);

 //        $tableds = "tbltrans_detail";
 //        $whereds = "vno = '".$purchaseid."'";
 //        $deleteds = $this->mod_common->delete_record($tableds, $whereds);


 //         if($count <= 1){
	//        	$this->db->where(array("trans_id"=>$parentid,"sale_point_id"=>$sale_point_id));
	//        	$delete_goods = $this->db->delete("tbl_goodsreceiving");
	//     }

		
	// 	$table = "tbl_goodsreceiving_detail";
 //        $deleteid=	$this->input->post('deleteid');
 //        $where = "receipt_id = '" . $deleteid . "'";
 //        $delete_goods = $this->mod_common->delete_record($table, $where);


 //       // $repost = $this->mod_purchaseother->repost_purchase($parentid);
		
		
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
		$purchaseid=$sale_point_id."-Purchase-".$id;
		$this->db->where('trans_id',$id,'sale_point_id',$sale_point_id);
		$count = $this->db->query("SELECT COUNT(receipt_detail_id) as count FROM tbl_goodsreceiving_detail where trans_id='$id' and sale_point_id='$sale_point_id'")->row_array()['count'];
        $table = "tbl_goodsreceiving_detail";
        $deleteid=	$this->input->post('deleteid');
        $where = "receipt_id = '" . $deleteid . "'";
        $delete_goods = $this->mod_common->delete_record($table, $where);
        if ($count==1) {
        $table = "tbl_goodsreceiving";
        $where = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";
        $delete_goods = $this->mod_common->delete_record($table, $where);
			
        $tablems = "tbltrans_master";
        $wherems = "vno = '".$purchaseid."'";
        $deletems = $this->mod_common->delete_record($tablems, $wherems);

        $tableds = "tbltrans_detail";
        $whereds = "vno = '".$purchaseid."'";
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
		$data['vendor_list'] = $this->mod_vendor->getOnlyVendors();
		$table='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		$table='tbl_goodsreceiving';
		$where = "receiptnos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);

		$data['edit_list'] = $this->mod_purchaseother->edit_purchaseother($id);
		//echo '<pre>';print_r($data);
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Vendor Invoice";
		$this->load->view($this->session->userdata('language')."/purchase_other/single",$data);
		}
	}
	function get_item($cate_id)
	{
	    $table='tblmaterial_coding';
		$cate_id=	$this->input->post('category_id');
		$where = array('catcode' => $cate_id);
		$data['item_list'] = $this->mod_common->select_array_records($table,"*",$where);
		if(empty($data['item_list']))
		{?>
			<option value="">No item found</option>
		<?php }
		else{?>

		<option value="">Choose item </option>

		<?php foreach ($data['item_list'] as $key => $value) {
			?>
			<option value="<?php echo  $value['materialcode']; ?>"><?php echo  $value['itemname']; ?></option>
			
		<?php }
		}
		
	}

}
