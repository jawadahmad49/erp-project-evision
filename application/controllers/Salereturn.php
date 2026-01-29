<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Salereturn extends CI_Controller {





	public function __construct() {

        parent::__construct();



        $this->load->model(array(

            "mod_customer","mod_common","mod_salereturn","mod_stockreport","mod_customerledger","mod_transaction","mod_bank","mod_customerstockledger"

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

		$data['salereturn_list'] = $this->mod_salereturn->manage_salereturn($from_date,$to_date,$sale_point_id);

	

		$data["filter"] = '';

		#----load view----------#

		$data["title"] = "Manage Sale Return";

		$this->load->view($this->session->userdata('language')."/sale_return/sale_return",$data);

	}



		public function add_sale_return()

	{    

		$login_user=$this->session->userdata('id');

	    $role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '201' limit 1")->row_array();

		if ($role['add']!=1) {

			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');

			redirect(SURL . 'Salereturn/index/');

			}

		$login_user=$this->session->userdata('id');

        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

        //$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

		  if ($sale_point_id=='0') {

	  	$this->session->set_flashdata('err_message', '- Admin Has No Rights To Add Sale Return!');

			redirect(SURL . 'Salereturn');

			exit();

	  }



        $general = $this->db->query("select customer_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['customer_code'];

        $bank = $this->db->query("select bank_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['bank_code'];

        $data['customer_list'] =$this->db->query("select * from tblacode where general='$general'")->result_array();

        $data['banks_list'] =$this->db->query("select * from tblacode where general='$bank'")->result_array();

		// $data['banks_list'] = $this->mod_bank->getOnlyBanks();

		// $data['customer_list'] = $this->mod_customer->getOnlyCustomers();

		$table='tbl_company';       

		$data['pricing_centralized'] = $this->mod_common->get_all_records($table,"*");

		//echo "<pre>";print_r($data['vendor_list']);exit;

		$table='tblmaterial_coding';       

        $data['item_list'] = $this->mod_common->get_all_records($table,"*");

		$this->load->view($this->session->userdata('language')."/sale_return/add_sale_return",$data);

	}

	

	public function detail_small($id){

		if($id){ 

		 

			

		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();

		$table='tblmaterial_coding';       

        $data['item_list'] = $this->mod_common->get_all_records($table,"*");

		$table='tbl_issue_return';

		$where = "irnos='$id'";

		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);



	 

 $acode= $data['single_edit']['scode'];

 $sale_point_id= $data['single_edit']['sale_point_id'];

 $scode= $data['single_edit']['branch_code'];

$issuedate= $data['single_edit']['irdate'];

		



		$data['edit_list'] = $this->mod_salereturn->edit_salereturn($id);

		//$data['edit_list'] = $this->mod_salelpg->edit_salelpg($id);

//	echo '<pre>';print_r($data['edit_list']);exit;

		$table='tbl_company';       

        $data['company'] = $this->mod_common->get_all_records($table,"*");

		//exit;

		$data["filter"] = '';

		#----load view----------#

		$data["title"] = "Customer Invoice";

		

 

		$ftoday='2018-01-01';

	 	 $today=  $issuedate;

		$date_array2 = array('from_date' => $ftoday,'to_date' => $today,'filter' => 'party','acode' => $acode,'id' =>  $id ,'hdate' => '','sort' => 'date','aname_hid' => '','location' => $sale_point_id,'scode' => $scode);

		  $data['final_bal']=  $this->mod_customerledger->get_report_small($date_array2);



   //pm(	$data['final_bal']);exit;

	 

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

			 //pm($data['sale']);exit();

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

		 

		// echo '<pre>';print_r($data['total_return_sale']);exit;

		 

		 

		 

		 

		 

		 

		 

		 

		 

		 

		//echo '<pre>';print_r($data);

		$table='tbl_company';       

        $data['company'] = $this->mod_common->get_all_records($table,"*");

		//exit;

		$data["filter"] = '';

		#----load view----------#

		$data["title"] = "Customer Invoice";

		$this->load->view($this->session->userdata('language')."/sale_return/single_small",$data);

		

		

		

				

		}

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

				redirect(SURL . 'Salereturn/add_sale_return');

			}

			

			//echo "<pre>";print_r($this->input->post());exit;

			$this->db->trans_start();

			$add=  $this->mod_salereturn->add_sale_return($this->input->post());

			 $this->db->trans_complete();

            //echo "<pre>";print_r($add);exit;

			  $same_page = $this->db->query("select same_page from tbl_company")->row_array()['same_page'];

			if($add and $same_page=='true') {

		            $this->session->set_flashdata('ok_message', '- Added Successfully!');

		            redirect(SURL . 'salereturn/');

		        } else if ($add) {

		            $this->session->set_flashdata('ok_message', '- Added Successfully!');

		            redirect(SURL . 'salereturn/');

		        } else {

		            $this->session->set_flashdata('err_message', '- Error in adding please try again!');

		            redirect(SURL . 'salereturn/');

		        }

		}

		//$this->add_direct_girn();

	}



	public function delete($id) {



	

	    $login_user=$this->session->userdata('id');
	     $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

	    $role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '201' limit 1")->row_array();

		if ($role['delete']!=1) {

			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');

			redirect(SURL . 'Salereturn/index/');

			}

		/////////////////////////////////////////////////////////////////////////////////////////////////////////

			//$sale_date=$this->input->post('date');

		    $trans_id=$id;

		    $irnos=$this->db->query("select irnos from tbl_issue_return where trans_id='$trans_id' and sale_point_id='$sale_point_id'")->row_array()['irnos'];

			$date_array = array('irnos' => $irnos);

			$get_rec_date =  $this->mod_common->select_single_records('tbl_issue_return',$date_array);



			//$sale_date=$this->input->post('date');

			

			$login_user=$this->session->userdata('id');

           

            $date_array = array('post_date>=' => $get_rec_date['irdate'],'sale_point_id =' => $sale_point_id);

			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);



			if(!empty($last_date))

			{

				//echo "string";

				$this->session->set_flashdata('err_message', 'Already closed for this date');

				redirect(SURL . 'salereturn/');

			}

			/////////////////////////////////////////////////////////////////////////////////////////////////////////

 

        $login_user=$this->session->userdata('id');

		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

 

		$saleid=$sale_point_id."-Return-".$id;

		//$receiveid=$id."-Receive";

		$receiveidd=$sale_point_id."-Return Payment-".$id;



		$this->db->trans_start();

		#-------------delete record--------------#

        $table = "tbl_issue_return";

        $where = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";

        $delete = $this->mod_common->delete_record($table, $where);



        $tables = "tbl_issue_return_detail";

        $wheres = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";

        $deletes = $this->mod_common->delete_record($tables, $wheres);



        $tablems = "tbltrans_master";

        $wherems = "vno = '".$saleid."'";

        $deletems = $this->mod_common->delete_record($tablems, $wherems);



        $tableds = "tbltrans_detail";

        $whereds = "vno = '".$saleid."'";

        $deleteds = $this->mod_common->delete_record($tableds, $whereds);



   



        $this->db->trans_complete();



        if ($delete) {

            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');

            redirect(SURL . 'salereturn/');

        } else {

            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');

            redirect(SURL . 'salereturn/');

        }

    }

	public function edit($id){

		$login_user=$this->session->userdata('id');

	    $role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '201' limit 1")->row_array();

		if ($role['edit']!=1) {

			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');

			redirect(SURL . 'Salereturn/index/');

			}

		if($id){

			

			

				/////////////////////////////////////////////////////////////////////////////////////////////////////////

			//$sale_date=$this->input->post('date');

			$login_user=$this->session->userdata('id');

            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

            $bank = $this->db->query("select bank_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['bank_code'];

            $data['banks_list'] =$this->db->query("select * from tblacode where general='$bank'")->result_array();

			//$data['banks_list'] = $this->mod_bank->getOnlyBanks();

			$date_array = array('irnos' => $id);

			$get_rec_date =  $this->mod_common->select_single_records('tbl_issue_return',$date_array);



			//$sale_date=$this->input->post('date');

			$date_array = array('post_date>=' => $get_rec_date['irdate'],'sale_point_id =' => $sale_point_id);

			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			$table='tbl_company';       

		$data['pricing_centralized'] = $this->mod_common->get_all_records($table,"*");



			if(!empty($last_date))

			{

				//echo "string";

				$this->session->set_flashdata('err_message', 'Already closed for this date');

				redirect(SURL . 'salereturn/');

			}

			/////////////////////////////////////////////////////////////////////////////////////////////////////////

 

 

		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();

		$tablem='tblmaterial_coding';       

        $data['item_list'] = $this->mod_common->get_all_records($tablem,"*");

		$table='tbl_issue_return';

		$where = "irnos='$id'";

		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);

//echo '<pre>';print_r($data['single_edit']);exit;

		$data['edit_list'] = $this->mod_salereturn->edit_salereturn($id);

		//echo '<pre>';print_r($data['edit_list']);exit;

		foreach ($data['edit_list'] as $key => $value) {

			//$data['filledstock'][]=  $this->mod_salereturn->get_details($value['itemid'],$data['single_edit']['issuedate']);

			//$itemids = $value['itemid'];

			//$wherem = "materialcode!='$itemids'";

			//$data['item_lists'] = $this->mod_common->select_array_records($tablem,'*',$wherem);

		}

		//echo '<pre>';print_r($data['item_list']);exit;

		//pm($data['filledstock']);



		$data["filter"] = '';

		#----load view----------#

		$data["title"] = "Update Sale Return";

		$this->load->view($this->session->userdata('language')."/sale_return/edit",$data);

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



		$data['edit_list'] = $this->mod_salereturn->edit_makeneworder($id);

		//echo '<pre>';print_r($data['edit_list']);exit;

		$data["filter"] = '';

		$data["id"] = $id;

		#----load view----------#

		$data["title"] = "Update Sale Return";

		$this->load->view('sale_return/add_sale_return',$data);

		}

	}



	public function update(){

		if($this->input->server('REQUEST_METHOD') == 'POST'){



			$sale_date=$this->input->post('date');

			

			$date_array = array('post_date' => $sale_date,'sale_point_id =' => $sale_point_id);

			$login_user=$this->session->userdata('id');

            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

			//$date_array = array('sale_point_id =' => $sale_point_id);

			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);



			if(!empty($last_date))

			{

				//echo "string";

				$this->session->set_flashdata('err_message', 'Already closed for this date');

				redirect(SURL . 'Salereturn');

			}





			$this->db->trans_start();

			$add_salereturn=  $this->mod_salereturn->update_sale_return($this->input->post());

			 $this->db->trans_complete();

            //echo "<pre>";print_r($add_salereturn);exit;

		        if ($add_salereturn || $add_salereturn==0) {

		            $this->session->set_flashdata('ok_message', '- Updated Successfully!');

		            redirect(SURL . 'salereturn/');

		        } else {

		            $this->session->set_flashdata('err_message', '- Error in updating please try again!');

		            redirect(SURL . 'salereturn/');

		        }

		}

		//$this->add_direct_girn();

	}



	// function record_delete()

	// {

	// 	$parentid=	$this->input->post('parentid');



	// 	$this->db->where('irnos',$parentid);

	// 	$count = $this->db->count_all_results('tbl_issue_return_detail');



 //        $saleid=$parentid."-Return";

	// 	//$receiveid=$parentid."-Receive";



 //        $tablems = "tbltrans_master";

 //        $wherems = "vno = '".$saleid."'";

 //        $deletems = $this->mod_common->delete_record($tablems, $wherems);



 //        $tableds = "tbltrans_detail";

 //        $whereds = "vno = '".$saleid."'";

 //        $deleteds = $this->mod_common->delete_record($tableds, $whereds);



 //        $tablemr = "tbltrans_master";

 //        $wheremr = "vno = '".$receiveid."'";

 //        $deletemr = $this->mod_common->delete_record($tablemr, $wheremr);



 //        $tabledr = "tbltrans_detail";

 //        $wheredr = "vno = '".$receiveid."'";

 //        $deletedr = $this->mod_common->delete_record($tabledr, $wheredr);



 //        // if($count <= 1){

	//        	// $this->db->where(array("irnos"=>$parentid));

	//        	// $delete_goods = $this->db->delete("tbl_issue_return");

	//     // }



 //        $table = "tbl_issue_return_detail";

 //        $deleteid=	$this->input->post('deleteid');

 //        $where = "sr_no = '" . $deleteid . "'";

 //        $delete_goods = $this->mod_common->delete_record($table, $where);



 //        $repost = $this->mod_salereturn->repost_return($parentid);



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
		$vno=$sale_point_id."-Return-".$id;
		
		

		$this->db->where('trans_id',$id,'sale_point_id',$sale_point_id);
		 
		$count = $this->db->query("SELECT COUNT(irnos) as count FROM tbl_issue_return_detail where trans_id='$id' and sale_point_id='$sale_point_id'")->row_array()['count'];
 

        $this->db->trans_start();

   
        $table = "tbl_issue_return_detail";
        $deleteid=	$this->input->post('deleteid');
        $where = "sr_no = '" . $deleteid . "'";
        $delete_goods = $this->mod_common->delete_record($table, $where);
 

        if ($count==1) {
        $table = "tbl_issue_return";
        $where = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";
        $delete_goods = $this->mod_common->delete_record($table, $where);
 

        $tablems = "tbltrans_master";
        $wherems = "vno = '".$vno."'";
        $deletems = $this->mod_common->delete_record($tablems, $wherems);
 
		
        $tableds = "tbltrans_detail";
        $whereds = "vno = '".$vno."'";
        $deleteds = $this->mod_common->delete_record($tableds, $whereds);
 
        }

		$this->db->trans_complete();
		
		//$repost = $this->mod_salelpg->repost_sale($id);

		
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

		$table='tbl_issue_return';

		$where = "irnos='$id'";

		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);



		$data['edit_list'] = $this->mod_salereturn->edit_salereturn($id);

		//echo '<pre>';print_r($data);

		$table='tbl_company';       

        $data['company'] = $this->mod_common->get_all_records($table,"*");

		//exit;

		$data["filter"] = '';

		#----load view----------#

		$data["title"] = "Customer Invoice";

		$this->load->view($this->session->userdata('language')."/sale_return/single",$data);

		}

	}



	function get_filledstock()

	{

		$data['report']=  $this->mod_salereturn->get_details($this->input->post());

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

		$data['report']=  $this->mod_salereturn->get_details($this->input->post());

		//pm($data['report']);

		foreach ($data['report'] as $key => $value) {

			//pm($value);

		 	echo $value['empty'];

		}

		

	}

}

