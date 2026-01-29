<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Sale_return extends CI_Controller {



	public function __construct() {

        parent::__construct();



        $this->load->model(array(

         "mod_customer","mod_common","Mod_sale_return","mod_stockreport","mod_customerledger","mod_bank","mod_customerstockledger" 

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

		//$data['sale_return_list'] = $this->Mod_sale_return->manage_sale_return($from_date,$to_date,$sale_point_id);

		$data['sale_return_list'] = $this->db->query("SELECT `tbl_goodsreceiving`.*, `tblacode`.* FROM `tbl_goodsreceiving` JOIN `tblacode` ON `tbl_goodsreceiving`.`suppliercode` = `tblacode`.`acode` JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `tbl_goodsreceiving_detail`.`sub_type` in ('filled_return','wo_sec_return','security_return') AND `tbl_goodsreceiving_detail`.`category_id` = '1' AND `tbl_goodsreceiving`.`Purchase_type` = 'salereturn' AND `tbl_goodsreceiving`.`receiptdate` >= '$from_date' AND `tbl_goodsreceiving`.`receiptdate` <= '$to_date' AND `tbl_goodsreceiving`.`sale_point_id` = '$sale_point_id' GROUP BY `receipt_detail_id` ORDER BY `receiptnos` DESC")->result_array();

		$data["filter"] = '';

		#----load view----------#

		$data["title"] = "Manage Sale Return";

		$this->load->view($this->session->userdata('language')."/salereturn/sale_return",$data);

	}



	public function add_sale_return()

	{

		

		  $login_user=$this->session->userdata('id');

	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '601' limit 1")->row_array();

		if ($role['add']!=1) {

			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');

			redirect(SURL . 'Sale_return/index/');

			}

		$login_user=$this->session->userdata('id');

        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

		  if ($sale_point_id=='0') {

	  	$this->session->set_flashdata('err_message', '- Admin Has No Rights To Add Sale!');

			redirect(SURL . 'Sale_return');

			exit();

	  }

        $general = $this->db->query("select customer_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['customer_code'];

        $bank = $this->db->query("select bank_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['bank_code'];

            //$data['vendor_list'] = $this->mod_vendor->getOnlyVendors_only();

        $c_date=date('Y-m-d');

        $data['customer_list'] =$this->db->query("select * from tblacode where general='$general'")->result_array();

        $data['banks_list'] =$this->db->query("select * from tblacode where general='$bank'")->result_array();

	    $data['item_list'] = $this->db->query("select * from tblmaterial_coding where catcode ='1' order by materialcode")->result_array();

	    $data['price'] = $this->db->query("select price from priceconfig where sale_point_id='$sale_point_id' and date>='$c_date'")->row_array()['price'];

	    $data['kg_price']=$data['price']*11.8;

	    

			

		// echo "<pre>";var_dump($data['item_list']);

		$table='tbl_company';       

		$data['pricing_centralized'] = $this->mod_common->get_all_records($table,"*");

		//pm($data['pricing_centralized']);

 

		$this->load->view($this->session->userdata('language')."/salereturn/add_sale_return",$data);

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

				redirect(SURL . 'Sale_return/add_sale_return');

			}



			$myexplode = explode("-",$this->input->post('date'));



			$chkrecord = $this->db->query("select * from close_profit where month='".$myexplode[1]."' and year='".$myexplode[0]."'");

			if($chkrecord->num_rows() > 0){

				$this->session->set_flashdata('err_message', 'Already Profit closed for this date');

				redirect(SURL . 'Sale_return/add_sale_return');

			}



		 //echo "<pre>";print_r($this->input->post());exit;

			

			$this->db->trans_start();

			$add=  $this->Mod_sale_return->add_sale_return($this->input->post());

			$this->db->trans_complete();

			   $same_page = $this->db->query("select same_page from tbl_company")->row_array()['same_page'];

			if($add and $same_page=='true') {

		            $this->session->set_flashdata('ok_message', 'Added Successfully!');

		            redirect(SURL . 'Sale_return/');

		        } elseif ($add || $add==0) {

		           $this->session->set_flashdata('ok_message', 'Added Successfully!');

		            redirect(SURL . 'Sale_return/');

		        } else {

		            $this->session->set_flashdata('err_message', '- Error in updating please try again!');

		            redirect(SURL . 'Sale_return/');

		        }

            //echo "<pre>";print_r($add);exit;

		      

		}

		//$this->add_direct_girn();

	}





	public function delete($id) {

		$login_user=$this->session->userdata('id');

	    $role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '601' limit 1")->row_array();

		if ($role['delete']!=1) {

			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');

			redirect(SURL . 'Sale_return/index/');

			}

		$trans_id=$id;

		$receiptnos=$this->db->query("select receiptnos from tbl_goodsreceiving where trans_id='$trans_id'")->row_array()['receiptnos'];

 //echo $id;exit();

			$date_array = array('receiptnos' => $receiptnos);

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

				redirect(SURL . 'Sale_return/');

			} 

		$login_user=$this->session->userdata('id');

		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

	

		$vno=$sale_point_id."-Return-".$id;

		

		$this->db->trans_start();

        $table = "tbl_goodsreceiving";

        $where = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";

        $delete = $this->mod_common->delete_record($table, $where);



        $tables = "tbl_goodsreceiving_detail";

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

            redirect(SURL . 'Sale_return/');

        } else {

            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');

            redirect(SURL . 'Sale_return/');

        }

    }







    public function delete_row_ajax() {



    	$id = $_POST['id'];

	    $login_user=$this->session->userdata('id');

        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

		$saleid=$sale_point_id."-Return-".$id;

		$receiveid=$id."-Receive"; 

		

        // $table = "tbl_issue_goods";

        // $where = "issuenos = '" . $id . "'";

        // $delete = $this->mod_common->delete_record($table, $where);



        $tables = "tbl_goodsreceiving_detail";

        $wheres = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";

        $deletes = $this->mod_common->delete_record($tables, $wheres);





        $tablems = "tbltrans_master";

        $wherems = "vno = '".$vno."'";

        $deletems = $this->mod_common->delete_record($tablems, $wherems);



        $tableds = "tbltrans_detail";

        $whereds = "vno = '".$vno."'";

        $deleteds = $this->mod_common->delete_record($tableds, $whereds);



        if ($delete) {

           echo "1";

        } else {

           echo "0";

        }

    }







	public function edit($id){

		$login_user=$this->session->userdata('id');

	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '601' limit 1")->row_array();

		if ($role['edit']!=1) {

			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');

			redirect(SURL . 'Sale_return/index/');

			}

		if($id){

			$login_user=$this->session->userdata('id');

            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

			$date_array = array('receiptnos' => $id);

			$get_rec_date =  $this->mod_common->select_single_records('tbl_goodsreceiving',$date_array);



			//$sale_date=$this->input->post('date');

			$date_array = array('post_date>=' => $get_rec_date['receiptdate'],'sale_point_id =' => $sale_point_id);

			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);



			if(!empty($last_date))

			{

				//echo "string";

				$this->session->set_flashdata('err_message', 'Already closed for this date');

				redirect(SURL . 'Sale_return/');

			} 

	    $login_user=$this->session->userdata('id');

        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

		  if ($sale_point_id=='0') {

	  	$this->session->set_flashdata('err_message', '- Admin Has No Rights To Add Sale!');

			redirect(SURL . 'Sale_return');

			exit();

	  }

        $general = $this->db->query("select customer_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['customer_code'];

        $bank = $this->db->query("select bank_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['bank_code'];

            //$data['vendor_list'] = $this->mod_vendor->getOnlyVendors_only();

        $data['customer_list'] =$this->db->query("select * from tblacode where general='$general'")->result_array();

        $data['banks_list'] =$this->db->query("select * from tblacode where general='$bank'")->result_array();

		// $data['customer_list'] = $this->mod_customer->getOnlyCustomers();

		 $data['item_list'] = $this->db->query("select * from tblmaterial_coding order by materialcode")->result_array();

		$table='tbl_goodsreceiving';

		$where = "receiptnos='$id'";

		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);

   //pm($data['single_edit']);exit;

		$data['edit_list'] = $this->Mod_sale_return->edit_salelpg($id); 

		foreach ($data['edit_list'] as $key => $value) {

			$data['filledstock'][]=  $this->Mod_sale_return->get_details($value['itemid'],$data['single_edit']['receiptdate']);

	 	}

		 //$data['banks_list'] = $this->mod_bank->getOnlyBanks();

		$data["filter"] = '';

		$table='tbl_company'; 

		 $c_date=date('Y-m-d'); 

		  $data['price'] = $this->db->query("select price from priceconfig where sale_point_id='$sale_point_id' and date>='$c_date'")->row_array()['price'];     

		$data['pricing_centralized'] = $this->mod_common->get_all_records($table,"*");

		#----load view----------#

		$data["title"] = "Update Sale LPG";

		$this->load->view($this->session->userdata('language')."/salereturn/add_sale_return",$data);

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



		$data['edit_list'] = $this->Mod_sale_return->edit_makeneworder($id);



		foreach ($data['edit_list'] as $key => $value) {

			$data['filledstock'][]=  $this->Mod_sale_return->get_details($value['itemid'],$data['single_edit']['issuedate']);

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



            $login_user=$this->session->userdata('id');

            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

			$sale_date=$this->input->post('date');



			$date_array = array('post_date>=' => $sale_date,'sale_point_id =' => $sale_point_id);

			

			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);



			if(!empty($last_date))

			{

				//echo "string";

				$this->session->set_flashdata('err_message', 'Already closed for this date');

				redirect(SURL . 'Sale_return');

			}





			//$this->db->trans_start();

			$add_salelpg=  $this->Mod_sale_return->update_sale_lpg($this->input->post());

			 //$this->db->trans_complete();

            //echo "<pre>";print_r($add_salelpg);exit;

		        if ($add_salelpg || $add_salelpg==0) {

		            $this->session->set_flashdata('ok_message', '- Updated Successfully!');

		            redirect(SURL . 'Sale_return/');

		        } else {

		            $this->session->set_flashdata('err_message', '- Error in updating please try again!');

		            redirect(SURL . 'Sale_return/');

		        }

		}

		//$this->add_direct_girn();

	}



	// function record_delete()

	// {

	// 	$login_user=$this->session->userdata('id');

 //        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

	// 	$id = $_POST['parentid'];

	// 	$saleid=$sale_point_id."-Return-".$id;



	// 	$this->db->where('trans_id',$id,'sale_point_id',$sale_point_id);

	// 	$count = $this->db->count_all_results('tbl_goodsreceiving_detail');



	// 	$tablems = "tbltrans_master";

 //        $wherems = "vno = '".$saleid."'";

 //        $deletems = $this->mod_common->delete_record($tablems, $wherems);



 //        $tableds = "tbltrans_detail";

 //        $whereds = "vno = '".$saleid."'";

 //        $deleteds = $this->mod_common->delete_record($tableds, $whereds);





 //        $table = "tbl_goodsreceiving_detail";

 //        $deleteid=	$this->input->post('deleteid');

 //        $where = "receipt_id = '" . $deleteid . "'";

 //        $delete_goods = $this->mod_common->delete_record($table, $where);



		

		

	// 	//$repost = $this->Mod_sale_return->repost_sale($id);



		

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

		$purchaseid=$sale_point_id."-Return-".$id;

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

		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();

		$table='tblmaterial_coding';       

        $data['item_list'] = $this->mod_common->get_all_records($table,"*");

		$table='tbl_issue_goods';

		$where = "issuenos='$id'";

		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);



		$acode= $data['single_edit']['issuedto'];

        $issuedate= $data['single_edit']['issuedate'];

		$ftoday='2018-01-01';

	 	 $today=  $issuedate;

		$date_array2 = array('from_date' => $ftoday,'to_date' => $today,'filter' => 'party','acode' => $acode,'id' =>  $id ,'hdate' => '','sort' => 'date','aname_hid' => '');

		  $data['final_bal']=  $this->mod_customerledger->get_report_small($date_array2);



  // pm(	$data['final_bal']);exit;

	 

	 		foreach ($data['final_bal'] as $key => $value) {

			$data['report_new'] = $value['tbalance'];

		}

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

		$data['edit_list'] = $this->Mod_sale_return->edit_salelpg($id);

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



		$data['edit_list'] = $this->Mod_sale_return->edit_salelpg($id);

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

		$table='tbl_goodsreceiving';

		$where = "receiptnos='$id'";

		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);

//pm($data['single_edit']);exit();

	 

 $acode= $data['single_edit']['suppliercode'];

  $scode= $data['single_edit']['scode'];

  $sale_point_id= $data['single_edit']['sale_point_id'];

$receiptdate= $data['single_edit']['receiptdate'];

		



		

		$data['edit_list'] = $this->Mod_sale_return->edit_salelpg($id);

//	echo '<pre>';print_r($data['edit_list']);exit;

		$table='tbl_company';       

        $data['company'] = $this->mod_common->get_all_records($table,"*");

		//exit;

		$data["filter"] = '';

		#----load view----------#

		$data["title"] = "Customer Invoice";

		

 

		$ftoday='2018-01-01';

	 	 $today=  $receiptdate;

		$date_array2 = array('from_date' => $ftoday,'to_date' => $today,'filter' => 'party','acode' => $acode,'id' =>  $id ,'hdate' => '','sort' => 'date','aname_hid' => '','scode' => $scode,'location' => $sale_point_id,);

		  $data['final_bal']=  $this->mod_customerledger->get_report_small($date_array2);



  // pm(	$data['final_bal']);exit;

	 

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

			// pm($data['sale']);exit;

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

		 $data['itemname'] = $this->mod_common->select_array_records('tblmaterial_coding',"*","catcode='1' "); 

		//pm($data['total_return_sale']);exit;

 			 $data['res_record'] = $this->db->query("select  * from  tbl_company")->row_array();

	

		

		$this->load->view($this->session->userdata('language')."/salereturn/invoice",$data);

		}

	}



	function get_filledstock()

	{

		$data['report']=  $this->Mod_sale_return->get_details($this->input->post());

		foreach ($data['report'] as $key => $value) {

		 	echo json_encode($value);

		}

		

	}

	function today_amount_recv()

	{

		$data['report']=  $this->Mod_sale_return->today_amount_recv($this->input->post());

		$total_recv=0;

		foreach ($data['report'] as $key => $value) {

			 

		 $total_recv+=$value['total_received'];

		}

		echo $total_recv;

	}

	function get_filledstockdate()

	{

		$data['report']=  $this->Mod_sale_return->get_details($this->input->post());

		//pm($data['report']);

		foreach ($data['report'] as $key => $value) {

			//pm($value);

		 	echo $value['empty'];

		}

		

	}

	function get_accbal()

	{

		 

		$customer=$this->input->post('customer');



		$balance=$this->db->query("select SUM(damount)-SUM(camount) as balance from tbltrans_detail  where acode='$customer' ")->row_array()['balance'];

		$opening_balance=$this->db->query("select * from tblacode  where acode='$customer' ")->row_array();

		 $opening=$opening_balance['opngbl'];



		if($opening_balance['optype']=='Credit'){ 

			$opening=-1*$opening;

			 } 

			 $acc_balance=$balance+$opening;

		echo $acc_balance;



	}

	public function get_branch()

	{ 

	   

		$customer=$this->input->post('customer');

		$login_user=$this->session->userdata('id');

        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

		//echo $Customer;exit();

		 if($sale_point_id=='0'){

        $where_sale_point_id="";

        }else{

        $where_sale_point_id="and sale_point_id='$sale_point_id'";

        }



		$customer_code=$this->db->query("select * from tblsledger where acode='$customer' $where_sale_point_id")->result_array();	

		$scode =$_SESSION["scode"];



		if ($customer_code[0]['scode']>0) {

		

		



		?>

		<?php

			foreach ($customer_code as $key => $data) {

				?>

				

				

				<option   value="<?php echo $data['scode']; ?>"<?php if($data['scode']==$scode){ ?> selected <?php } ?>><?php echo ucwords($data['stitle']); ?></option>

				

			<?php }

		}else{

			echo 0;

		}



		

		

	}

	public function get_type()

	{ 

	   

		$customer=$this->input->post('customer');



		$record=$this->db->query("select * from tblacode where acode='$customer'")->row_array();

		echo json_encode($record);



	

	}

}

