<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Swap_cylinder_receive extends CI_Controller {


	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_customer","mod_common","Mod_swap_receive","mod_stockreport","mod_customerledger","mod_transaction","mod_bank","mod_customerstockledger"
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
		$data['swap_cylinder_receive'] = $this->Mod_swap_receive->manage_swap_receive($from_date,$to_date,$sale_point_id);
	
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Swap Cylinder Receive";
		$this->load->view($this->session->userdata('language')."/Swap_cylinder_receive/manage_swap_receive",$data);
	}

		public function add_swap_receive()
	{    
		$login_user=$this->session->userdata('id');
	    $role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '201' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Swap_cylinder_receive/index/');
			}
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        //$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		  if ($sale_point_id=='0') {
	  	$this->session->set_flashdata('err_message', '- Admin Has No Rights To Add Sale Return!');
			redirect(SURL . 'Swap_cylinder_receive');
			exit();
	  }

        $general = $this->db->query("select customer_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['customer_code'];
        $bank = $this->db->query("select bank_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['bank_code'];
        $data['customer_list'] =$this->db->query("select * from tblacode where general='$general'")->result_array();
        $data['banks_list'] =$this->db->query("select * from tblacode where general='$bank'")->result_array();

		$table='tbl_company';       
		$data['pricing_centralized'] = $this->mod_common->get_all_records($table,"*");

		$table='tblmaterial_coding';       
        $data['item_list'] = $this->db->query("select * from tblmaterial_coding where catcode='7'")->result_array();
		$this->load->view($this->session->userdata('language')."/Swap_cylinder_receive/add_swap_receive",$data);
	}
	

	
	
	
	public function add(){

		if($this->input->server('REQUEST_METHOD') == 'POST'){


			$sale_date=$this->input->post('date');
			$date_array = array('post_date>=' => $sale_date,'sale_point_id =' => $sale_point_id);
			$login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'Swap_cylinder_receive/add_swap_receive');
			}
			
			//echo "<pre>";print_r($this->input->post());exit;
			$this->db->trans_start();
			$add=  $this->Mod_swap_receive->add_swap_receive($this->input->post());
			 $this->db->trans_complete();
            //echo "<pre>";print_r($add);exit;
			  $same_page = $this->db->query("select same_page from tbl_company")->row_array()['same_page'];
			if($add and $same_page=='true') {
		            $this->session->set_flashdata('ok_message', '- Added Successfully!');
		            redirect(SURL . 'Swap_cylinder_receive/');
		        } else if ($add) {
		            $this->session->set_flashdata('ok_message', '- Added Successfully!');
		            redirect(SURL . 'Swap_cylinder_receive/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in adding please try again!');
		            redirect(SURL . 'Swap_cylinder_receive/');
		        }
		}
		//$this->add_direct_girn();
	}

	public function delete($id) {

	
	    $login_user=$this->session->userdata('id');
	    $role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '201' limit 1")->row_array();
		if ($role['delete']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Swap_cylinder_receive/index/');
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
				redirect(SURL . 'Swap_cylinder_receive/');
			}
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
 
        $login_user=$this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
 
		$vno=$sale_point_id."-SCR-".$id;

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
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Swap_cylinder_receive/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Swap_cylinder_receive/');
        }
    }
	public function edit($id){
		$login_user=$this->session->userdata('id');
	    $role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '201' limit 1")->row_array();
		if ($role['edit']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Swap_cylinder_receive/index/');
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
			$get_rec_date =  $this->mod_common->select_single_records('tbl_swap_recv',$date_array);

			//$sale_date=$this->input->post('date');
			$date_array = array('post_date>=' => $get_rec_date['irdate'],'sale_point_id =' => $sale_point_id);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);
			$table='tbl_company';       
		$data['pricing_centralized'] = $this->mod_common->get_all_records($table,"*");

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'Swap_cylinder_receive/');
			}
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
 
 
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		$tablem='tblmaterial_coding';       
        $data['item_list'] = $this->db->query("select * from tblmaterial_coding where catcode='7'")->result_array();
		$table='tbl_swap_recv';
		$where = "irnos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);
//echo '<pre>';print_r($data['single_edit']);exit;
		$data['edit_list'] = $this->Mod_swap_receive->edit_swap_recv($id);
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
		$data["title"] = "Update Swap Cylinder Receive";
		$this->load->view($this->session->userdata('language')."/Swap_cylinder_receive/edit",$data);
		}
	}



	public function update(){
		if($this->input->server('REQUEST_METHOD') == 'POST'){

			$sale_date=$this->input->post('date');
			
			$date_array = array('post_date' => $sale_date,'sale_point_id =' => $sale_point_id);
			$login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'Swap_cylinder_receive');
			}


			$this->db->trans_start();
			$add_salereturn=  $this->Mod_swap_receive->update_swap_recv($this->input->post());
			 $this->db->trans_complete();
            //echo "<pre>";print_r($add_salereturn);exit;
		        if ($add_salereturn || $add_salereturn==0) {
		            $this->session->set_flashdata('ok_message', '- Updated Successfully!');
		            redirect(SURL . 'Swap_cylinder_receive/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in updating please try again!');
		            redirect(SURL . 'Swap_cylinder_receive/');
		        }
		}
		//$this->add_direct_girn();
	}

	function record_delete()
	{   
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$id = $_POST['parentid'];
		$saleid=$sale_point_id."-SCR-".$id;
		
		

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

		$data['edit_list'] = $this->Mod_swap_receive->edit_swap_recv($id);
		//echo '<pre>';print_r($data);
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
		//exit;
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Invoice";
		$this->load->view($this->session->userdata('language')."/Swap_cylinder_receive/single",$data);
		}
	}


}
