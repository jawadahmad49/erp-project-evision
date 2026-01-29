<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cylcondition extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_cylcondition","mod_common","mod_item"
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
		
		//error_reporting(E_ALL);
		//// check transactions already made /////
		$table='tbl_posting_stock';
		$data['exist'] = $this->mod_common->get_all_records($table,"*");

		
		//// end check transactions already made /////
		
		$data['copening_list'] = $this->mod_cylcondition->get_itemname($from_date,$to_date);
		//echo "<pre>";print_r($data['copening_list']);exit;
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Cylinder Condition";

 		$this->load->view($this->session->userdata('language')."/Cylcondition/edit_cylcondition",$data);
	}

	public function add_cylcondition()
	{
			$table='tblmaterial_coding';  
			$this->db->where('catcode',1);     
			$data['item_list'] = $this->mod_common->get_all_records($table,"*");
		

			$data["title"] = "Add Record";
			$data["filter"] = 'add';
			$data['id']=$id;

			$data['single_edit']="";

			$this->load->view($this->session->userdata('language')."/Cylcondition/add_cylcondition",$data);
		
	}

	public function edit($id){
		
		 
	//	print 'aaaaaaaaaaaaaaaaa'.$id;
		if($id){
			
			
			
			
				$date_array = array('trans_id' => $id);
		$get_rec_date =  $this->mod_common->select_single_records('tbl_exchange_condition',$date_array);

		//$sale_date=$this->input->post('date');
		$date_array = array('post_date>=' => $get_rec_date['dt']);
		$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

		if(!empty($last_date))
		{
			//echo "string";
			$this->session->set_flashdata('err_message', 'Already closed for this date');
			redirect(SURL . 'Cylcondition/');
		}

		
		
		
			
			$table='tbl_exchange_condition';
			$where = "trans_id='$id'";
			$data['single_edit'] = $this->mod_common->select_single_records($table,$where);

//pm($data['single_edit']);
 				 
		 	$materialcode=$data['cyl_condition']['materialcode'];  
			$table='tblmaterial_coding';       
			$where = "materialcode='$materialcode'";
			$data['selectd_item'] = $this->mod_common->select_single_records($table,$where);
 
			
			$data['postid']=$id;
			
			$table='tblmaterial_coding';
			$this->db->where('catcode',1); 
			$data['item_list'] = $this->mod_common->get_all_records($table,"*");
			
			

			$data["filter"] = 'update';
			$data["title"] = "Update cyl_condition";
			// echo "<pre>";print_r($data);
			$this->load->view($this->session->userdata('language')."/Cylcondition/add_cylcondition",$data);
		}
	}

	public function add(){
		//echo "<pre>";print_r($_POST);exit;
			if($this->input->server('REQUEST_METHOD') == 'POST'){

				$sale_date=$this->input->post('date');
				$date_array = array('post_date' => $sale_date);
				$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

				if(!empty($last_date))
				{
					//echo "string";
					$this->session->set_flashdata('err_message', 'Already closed for this date');
					redirect(SURL . 'Cylcondition/add_cylcondition');
				}


					$qty=$_POST['qty'];
					$stock=$_POST['stock'];
			 
				if($qty>$stock){
					$this->session->set_flashdata('err_message', 'Quantity can not greater then stock !!');
					redirect(SURL . 'Cylcondition/add_cylcondition');
				}
				
				
				$adata['dt']=trim($_POST["date"]);
				$adata['from_itemcode']=trim($_POST["item"]);
				$adata['cyl_type']=trim($_POST["type"]);
				$adata['cyl_condition_from']=trim($_POST["condition"]);
				$adata['qty']=$_POST['qty'];
				$adata['to_itemcode']=trim($_POST["item"]);
				$adata['cyl_condition_to']=trim($_POST["condition1"]);
				$adata['remarks']=$_POST["remarks"];
				$adata['created_by'] = $_SESSION['id'];
				$adata['created_dt']= date('Y-m-d');

				#----check item already exist---------#
				if ($this->mod_cylcondition->check_already($adata['materialcode'],$adata['type'])) {
					$this->session->set_flashdata('err_message', 'Item is Already Exist,Please Update Item');
					redirect(SURL . 'Cylcondition/add_cylcondition');
					exit();
				}
				
				$table='tbl_exchange_condition';
				$res = $this->mod_cylcondition->insert_into_table($table,$adata);

				if ($res) {
				 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
		            redirect(SURL . 'Cylcondition/');
		        } else {
		            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
		            redirect(SURL . 'Cylcondition/');
		        }

		    }
	}

	public function update(){
		//echo "<pre>";print_r($_POST);exit;
			if($this->input->server('REQUEST_METHOD') == 'POST'){

				$sale_date=$this->input->post('date');
				$date_array = array('post_date' => $sale_date);
				$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);
				if(!empty($last_date))
				{
					//echo "string";
					$this->session->set_flashdata('err_message', 'Already closed for this date');
					redirect(SURL . 'CylinderConversion');
				}

			 $id= trim($_POST["postid"]);
			 
					$adata['dt']=trim($_POST["date"]);
				$adata['from_itemcode']=trim($_POST["item"]);
				$adata['cyl_type']=trim($_POST["type"]);
				$adata['cyl_condition_from']=trim($_POST["condition"]);
				$adata['qty']=$_POST['qty'];
				$adata['to_itemcode']=trim($_POST["item1"]);
				$adata['cyl_condition_to']=trim($_POST["condition1"]);
				$adata['remarks']=$_POST["remarks"];
				$adata['created_by'] = $_SESSION['id'];
				$adata['created_dt']= date('Y-m-d');

			
			
			
				$qty=$_POST['qty'];
				$stock=$_POST['stock'];

				if($qty>$stock){
				$this->session->set_flashdata('err_message', 'Quantity can not greater then stock !!');
				redirect(SURL . 'Cylcondition/edit/'.$id);
				}
				
				

					#----check name already exist---------#
				if ($this->mod_cylcondition->check_already_edit($adata['cyl_condition_from'],$id)) {
					$this->session->set_flashdata('err_message', 'Item is Already Exist,Please Update Item');
					redirect(SURL . 'Cylcondition/edit/'.$id);
					exit();
				}
		
				$table='tbl_exchange_condition';
				$where='trans_id="'.$id.'" ';
				$res=$this->mod_cylcondition->update_table($table,$adata,$where);


				if ($res) {
				 	$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
		            redirect(SURL . 'Cylcondition/');
		        } else {
		            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
		            redirect(SURL . 'Cylcondition/');
		        }

		    }
	}

	public function delete($id) {

	
			$date_array = array('trans_id' => $id);
		$get_rec_date =  $this->mod_common->select_single_records('tbl_exchange_condition',$date_array);

		//$sale_date=$this->input->post('date');
		$date_array = array('post_date>=' => $get_rec_date['dt']);
		$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

		if(!empty($last_date))
		{
			//echo "string";
			$this->session->set_flashdata('err_message', 'Already closed for this date');
			redirect(SURL . 'Cylcondition/');
		}

		
		
        $table = "tbl_exchange_condition";
        $where = "trans_id = '" . $id . "'";
        $getrecord = $this->mod_common->select_single_records($table, $where);
        $itemid = $getrecord['materialcode'];
		if ($this->mod_item->get_issue($itemid)) {
			$this->session->set_flashdata('err_message', 'You can not delete it.');
			redirect(SURL . 'Cylcondition/');
			exit();
		}
		#-------------delete record--------------#
        $table = "tbl_exchange_condition";
        $where = "trans_id = '" . $id . "'";
        $delete_country = $this->mod_common->delete_record($table, $where);

        if ($delete_country) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Cylcondition/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Cylcondition/');
        }
    }

    public function enable_disable_type(){

		$table='tblmaterial_coding';
		$item_id=	$this->input->post('item_id');
		$customer=	$this->input->post('customer');
		$where = array('materialcode' => $item_id);
		$data['cat_code'] = $this->mod_common->select_array_records($table,"catcode",$where);
		echo json_encode($data['cat_code']);
		// echo "|";

		// $data['copening_list'] = $this->mod_shopopbal->get_item_stock($item_id,$customer);
		// echo $data['copening_list'];
		exit;

    }
    public function enable_disable_type_customer(){

		$table='tblmaterial_coding';
		$item_id=	$this->input->post('item_id');
		$customer=	$this->input->post('customer');
		$where = array('materialcode' => $item_id);
		$data['cat_code'] = $this->mod_common->select_array_records($table,"catcode",$where);
		echo json_encode($data['cat_code']);
		echo "|";

		$data['copening_list'] = $this->mod_cylcondition->get_item_stock($item_id,$customer);
		echo $data['copening_list'];
		exit;

    }
}
