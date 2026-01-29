<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_return_filled extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_admin", "mod_vendor","mod_common","Mod_purchase_return_filled","mod_salelpg","mod_bank","mod_vendorledger","mod_user","mod_customerledger"
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
		$data['purchase_return'] = $this->Mod_purchase_return_filled->manage_return_filled($from_date,$to_date,$sale_point_id);
		//pm($data['directgirn_list']);exit();


		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Purchase Filled";
		$this->load->view($this->session->userdata('language')."/Purchase_return_filled/purchase_return",$data);
	}

	public function add_pur_rtn_filled()
	{
		    $login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '102' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Purchase_return_filled/index/');
			}
		    $login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
             if ($sale_point_id=='0') {
	  	$this->session->set_flashdata('err_message', '- Admin Has No Rights To Add Purchase!');
			redirect(SURL . 'Purchase_return_filled');
			exit();
	  }
            $general = $this->db->query("select vendor_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['vendor_code'];
            $bank = $this->db->query("select bank_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['bank_code'];
            //$data['vendor_list'] = $this->mod_vendor->getOnlyVendors_only();
            $data['vendor_list'] =$this->db->query("select * from tblacode where general='$general'")->result_array();
            $data['bank_list'] =$this->db->query("select * from tblacode where general='$bank'")->result_array();
		
			$data['cash_position'] = $this->mod_admin->cash_position();

			foreach ($data['cash_position'] as $key=>$datas) {

			$opening= $datas[opngbl]; 
			if($datas[optype]=='Credit'){ $opening=-1*$opening; } 

			$bal=$datas[damount]-$datas[camount]+$opening;
				 
			  
		}
			 
		$data['cash_balance']=$bal;
  
        $where_cat_id = array('catcode' => 1);

        $data['item_list']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_cat_id);
         $table='tbl_company';       
		$data['pricing_centralized'] = $this->mod_common->get_all_records($table,"*");

        //pm($data['item_list']);
        //$data['bank_list'] = $this->mod_bank->getOnlyBanks();
       		

		// $table='tblmaterial_coding';       
  //       $data['item_list'] = $this->mod_common->get_all_records($table,"*");

		$this->load->view($this->session->userdata('language')."/Purchase_return_filled/add_pur_rtn_filled",$data);
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
				redirect(SURL . 'Purchase_return_filled/add_direct_girn');
			}
			$add_directgirn=  $this->Mod_purchase_return_filled->add_purchase_return_filled($this->input->post());
			 //echo "<pre>";print_r($add_directgirn);exit;
			$same_page = $this->db->query("select same_page from tbl_company")->row_array()['same_page'];
			if($add_directgirn and $same_page=='true') {
		            $this->session->set_flashdata('ok_message', 'Added Successfully!');
		            redirect(SURL . 'Purchase_return_filled/');
		        } elseif($add_directgirn) {
		            $this->session->set_flashdata('ok_message', 'Added Successfully!');
		            redirect(SURL . 'Purchase_return_filled/');
		        }else {
		            $this->session->set_flashdata('err_message', '- Error in adding please try again!');
		            redirect(SURL . 'Purchase_return_filled/');
		        }
		}
		//$this->add_direct_girn();
	}

		public function delete($param) {
			 $login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '102' limit 1")->row_array();
		if ($role['delete']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Purchase_return_filled/index/');
			}
			
			$arr=explode("_",$param);
			$id=$arr[0];
			$sale_date=$arr[1];
			$login_user=$this->session->userdata('id');
			$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			 
			//$sale_date=$this->input->post('date');
			$date_array = array('post_date>=' => $sale_date,'sale_point_id =' => $sale_point_id);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'Purchase_return_filled');
			}
 
		$purchaseid=$sale_point_id."-PRF-".$id;
		#-------------delete record--------------#
        $table = "tbl_issue_goods";
        $where = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";
        $delete_goods = $this->mod_common->delete_record($table, $where);

        $tables = "tbl_issue_goods_detail";
        $where = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";
        $deletes = $this->mod_common->delete_record($tables, $where);

        $tablems = "tbltrans_master";
        $wherems = "vno = '".$purchaseid."'";
        $deletems = $this->mod_common->delete_record($tablems, $wherems);

        $tableds = "tbltrans_detail";
        $whereds = "vno = '".$purchaseid."'";
        $deleteds = $this->mod_common->delete_record($tableds, $whereds);


		

        if ($delete_goods) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Purchase_return_filled/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Purchase_return_filled/');
        }
    }

	public function edit($param){
		 $login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '102' limit 1")->row_array();
		if ($role['edit']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Purchase_return_filled/index/');
			}


			$arr=explode("_",$param);
			$id=$arr[0];
			$sale_date=$arr[1];

			//$sale_date=$this->input->post('date');
			
			$login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
            $date_array = array('post_date>=' => $sale_date,'sale_point_id =' => $sale_point_id);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
			//echo "string";
			$this->session->set_flashdata('err_message', 'Already closed for this date');
			redirect(SURL . 'Purchase_return_filled');
			}


		if($id){
		//$data['vendor_list'] = $this->mod_vendor->getOnlyVendors_only();
		    $login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
            $general = $this->db->query("select vendor_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['vendor_code'];
            $bank = $this->db->query("select bank_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['bank_code'];
            //$data['vendor_list'] = $this->mod_vendor->getOnlyVendors_only();
            $data['vendor_list'] =$this->db->query("select * from tblacode where general='$general'")->result_array();
            $data['bank_list'] =$this->db->query("select * from tblacode where general='$bank'")->result_array();

		// $tablem='tblmaterial_coding';       
  //       $data['item_list'] = $this->mod_common->get_all_records($tablem,"*");

        $where_cat_id = array('catcode' => 1);
        $data['item_list']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_cat_id);
        $table='tbl_company';       
		$data['pricing_centralized'] = $this->mod_common->get_all_records($table,"*");


		$table='tbl_issue_goods';
		$where = "issuenos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);
		$data['edit_list'] = $this->Mod_purchase_return_filled->edit_purchase_return($id);
		//echo '<pre>';print_r($data);exit;
		foreach ($data['edit_list'] as $key => $value) {
			$data['emptystock'][]=  $this->mod_salelpg->get_details($value['itemid'],$data['single_edit']['issuedate']);
		}



		$data["filter"] = '';
		$data["edit_id"] = $id;
		#----load view----------#
		$data["title"] = "Update Purchase Return Filled";
		$this->load->view($this->session->userdata('language')."/Purchase_return_filled/edit",$data);
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
public function get_bank_bal($bankcode,$dt,$ids)
	{
		
		   
				$bal=0;
				// $data['bank_position']=  $this->mod_vendorledger->get_report($dt,$bankcode);  
				// $bal=str_replace(",", "", $data['bank_position'][0]['tbalance']);   	  
		   
		   
		   
		 
		   
				$data['one']=2;
				$data['bank_position']=  $this->mod_vendorledger->get_report($bankcode,$bankcode,$dt);
				//pm($data['bank_position']);
				if($id !='')
				{
					$data['one']=1;
				}
 
				foreach ($data['bank_position'] as $key => $value) { 
			 
					 $tbalance=$value['tbalance']; 
						 
					 } 

					$total_opngbl =str_replace(",", "", $tbalance);

					 

					$bal= $total_opngbl;
 
				 



	

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
				redirect(SURL . 'Purchase_return_filled');
			}

			$add_directgirn=  $this->Mod_purchase_return_filled->update_purchase_return($this->input->post());
            //echo "<pre>";print_r($add_directgirn);exit;
		        if ($add_directgirn || $add_directgirn==0) {
		            $this->session->set_flashdata('ok_message', '- Updated Successfully!');
		            redirect(SURL . 'Purchase_return_filled/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in updating please try again!');
		            redirect(SURL . 'Purchase_return_filled/');
		        }
		}
		//$this->add_direct_girn();
	}
	public function get_cylinder($vendor_id){

        $where_cat_id = array('acode' => $vendor_id);
        $data['item_list']= $this->mod_common->select_array_records('tblacode',"*",$where_cat_id);

        
        $brand_array='';

        for ($i=1; $i <=10 ; $i++) { 

        	if($data['item_list'][0]["brand$i"])
        	{
        		$brand_array["brand$i"]=$data['item_list'][0]["brand$i"];
        	}
        }
        $where_cat_id = array('catcode' => 1);
        $data['item_list_new']= $this->Mod_purchase_return_filled->get_brand_item($where_cat_id,$brand_array);
        ?>
        
        	<option value="">Choose a Item...</option>
        
        <?php 	
        foreach ($data['item_list_new'] as $key => $value) { ?>
        	<option value="<?php echo $value['materialcode']; ?>"><?php echo $value['itemname']; ?></option>
        <?php }
        exit;

	}

	// function record_delete()
	// {   
	// 	$login_user=$this->session->userdata('id');
 //        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
	// 	$parentid=	$this->input->post('parentid');
	// 	$purchaseid=$sale_point_id."-PRF-".$id;
		

	// 	 $this->db->where('receipt_detail_id',$parentid,'sale_point_id',$sale_point_id);
	//      $count = $this->db->count_all_results('tbl_goodsreceiving_detail');

  
 //        $tablems = "tbltrans_master";
 //        $wherems = "vno = '".$purchaseid."'";
 //        $deletems = $this->mod_common->delete_record($tablems, $wherems);



 //        $tableds = "tbltrans_detail";
 //        $whereds = "vno = '".$purchaseid."'";
 //        $deleteds = $this->mod_common->delete_record($tableds, $whereds);



 //        $table = "tbl_goodsreceiving_detail";
 //        $deleteid=	$this->input->post('deleteid');
 //        $where = "receipt_id = '" . $deleteid . "'";
 //        $delete_goods = $this->mod_common->delete_record($table, $where);

 //       // $repost = $this->Mod_purchase_return_filled->repost_purchase($parentid);

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
		$saleid=$sale_point_id."-PRF-".$id;
 
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

	public function detail($id){
		if($id){
		$data['vendor_list'] = $this->mod_vendor->getOnlyVendors();
		$table='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		$table='tbl_goodsreceiving';
		$where = "receiptnos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);

		$data['edit_list'] = $this->Mod_purchase_return_filled->edit_directgirn($id);
		//echo '<pre>';print_r($data);
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Vendor Invoice";
		$this->load->view($this->session->userdata('language')."/Purchase_return_filled/single",$data);
		}
	}
	public function detail_small($id){
		if($id){
			
		$data['vendor_list'] = $this->mod_vendor->getOnlyVendors();
		$table='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		$table='tbl_goodsreceiving';
		$where = "receiptnos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);
		//pm(	$data['single_edit']);
          $acode= $data['single_edit']['suppliercode'];
           $issuedate= $data['single_edit']['receiptdate'];

		$data['edit_list'] = $this->Mod_purchase_return_filled->edit_directgirn($id);
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
		  //pm(	$date_array2);
		  $data['final_bal']=  $this->mod_customerledger->get_report_small($date_array2);

  // pm(	$data['final_bal']);
	 
	 		foreach ($data['final_bal'] as $key => $value) {
			$data['report_new'] = $value['tbalance'];
			//$itemids = $value['itemid'];
			//$wherem = "materialcode!='$itemids'";
			//$data['item_lists'] = $this->mod_common->select_array_records($tablem,'*',$wherem);
		}
		
 
		
		$this->load->view($this->session->userdata('language')."/Purchase_return_filled/single_small",$data);
		}
	}
}
