<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ShopOpeningBalance extends CI_Controller {

	 
	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_shopopbal","mod_common","mod_item","mod_customerstockledger"
        ));
    }
	public function index()
	{ 
		// $table='tbl_posting_stock';
		// $data['exist'] = $this->mod_common->get_all_records($table,"*");
		// $data['name'] =$this->db->query("select * from tbl_sales_point")->result_array();
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
         if($sale_point_id=='0'){
       $where_sale_point_id="";
        }else{
       $where_sale_point_id="where location='$sale_point_id'";
        }
        $data['copening_list'] =$this->db->query("select * from tbl_shop_opening $where_sale_point_id order by trans_id desc")->result_array();

		//$data['copening_list'] = $this->mod_shopopbal->get_itemname(); 
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Shop Opening Balance";

 		$this->load->view($this->session->userdata('language')."/shop_opening_balance/shop_opening_balance",$data);
	}

	public function add_shopopeningbalance()
	{        
		    $login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '12' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'ShopOpeningBalance/index/');
			}
			// $table='tblmaterial_coding';       
			// $data['item_list'] = $this->mod_common->get_all_records($table,"*");
			$data['item_list'] =$this->db->query("select * from tblmaterial_coding where catcode!='1'")->result_array();

			
	        $login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
            $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
            $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

            if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		     $data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();
 
			
			$data["title"] = "Add Shop Opening Balance";
			$data["filter"] = 'add';
			$data['id']=$id;
			$this->load->view($this->session->userdata('language')."/shop_opening_balance/add_shopopeningbalance",$data);
		
	}

	public function edit($id){
		 $login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '12' limit 1")->row_array();
		if ($role['edit']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'ShopOpeningBalance/index/');
			}
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
        $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

        if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();
		if($id){
			$table='tbl_shop_opening';
			$where = "trans_id='$id'";
			$data['shop_opening'] = $this->mod_common->select_single_records($table,$where);

 				 
		 	$materialcode=$data['shop_opening']['materialcode'];  
			$table='tblmaterial_coding';       
			$where = "materialcode='$materialcode'";
			$data['selectd_item'] = $this->mod_common->select_single_records($table,$where);
 
			
			$table='tbl_shop_opening';       
			$data['last_date_is'] = $this->mod_common->select_last_records($table,"","*");
 
			
			// $table='tblmaterial_coding';       
			// $data['item_list'] = $this->mod_common->get_all_records($table,"*");
			$data['item_list'] =$this->db->query("select * from tblmaterial_coding where catcode!='1'")->result_array();
			$data['name'] =$this->db->query("select * from tbl_sales_point")->result_array();

			$data["filter"] = 'update';
			$data["title"] = "Update Shop Opening Balance";
			// echo "<pre>";print_r($data);
			$this->load->view($this->session->userdata('language')."/shop_opening_balance/add_shopopeningbalance",$data);
		}
	}

	public function add(){
		//echo "<pre>";print_r($_POST);exit;
			if($this->input->server('REQUEST_METHOD') == 'POST'){
                $login_user=$this->session->userdata('id');
                $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
				$sale_date=$this->input->post('date');
				$date_array = array('post_date' => $sale_date,'sale_point_id =' => $sale_point_id);
				$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);
				$materialcode=$_POST["item"];

				if(!empty($last_date))
				{
					//echo "string";
					$this->session->set_flashdata('err_message', 'Already closed for this date');
					redirect(SURL . 'ShopOpeningBalance/add_shopopeningbalance');
				}

	$catcode = $this->db->query("select catcode from tblmaterial_coding where materialcode='$materialcode'")->row_array()['catcode'];

				//echo $catcode;exit();
				if($catcode!=1){
					$type='Other';
				}else{
					$type=trim($_POST["type"]);
				} 
				

				$adata['location']=trim($_POST["location"]);

				$adata['materialcode']=trim($_POST["item"]);
				$adata['type']=$type;
				$adata['qty']=trim($_POST["quantity"]);
				$adata['cost_price']=trim($_POST["cost_price"]);
				$adata['date']=$_POST["date"];
				$adata['created_by'] = $_SESSION['id'];
				$adata['created_date']= date('Y-m-d');
					
			

				

				#----check item already exist---------#
				if ($this->mod_shopopbal->check_already($adata['materialcode'],$adata['type'])) {
					$this->session->set_flashdata('err_message', 'Item is Already Exist,Please Update Item');
					redirect(SURL . 'ShopOpeningBalance/add_shopopeningbalance');
					exit();
				}

				$udata['cost_price'] = trim($_POST['cost_price']);

				$this->db->where("materialcode",$_POST['item']);
				$this->db->update("tblmaterial_coding",$udata);

				
				$table='tbl_shop_opening';
				$res = $this->mod_common->insert_into_table($table,$adata);

				if ($res) {
				 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
		            redirect(SURL . 'ShopOpeningBalance/');
		        } else {
		            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
		            redirect(SURL . 'ShopOpeningBalance/');
		        }

		    }
	}

	public function update(){
	 //echo "<pre>";print_r($_POST);exit;
			if($this->input->server('REQUEST_METHOD') == 'POST'){
                $login_user=$this->session->userdata('id');
                $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
				$sale_date=$this->input->post('date');
				$date_array = array('post_date' => $sale_date,'sale_point_id =' => $sale_point_id);
			    $materialcode=$_POST["item"];
				$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);
				if(!empty($last_date))
				{
					//echo "string";
					$this->session->set_flashdata('err_message', 'Already closed for this date');
					redirect(SURL . 'CylinderConversion');
				}
$catcode = $this->db->query("select catcode from tblmaterial_coding where materialcode='$materialcode'")->row_array()['catcode'];

				//echo $catcode;exit();
				if($catcode!=1){
					$type='Other';
				}else{
					$type=trim($_POST["type"]);
				}
				$adata['location']=trim($_POST["location"]);
				$adata['materialcode']=trim($_POST["item"]);
				$adata['type']=$type;
				$adata['qty']=trim($_POST["quantity"]);
				$adata['date']=$_POST["date"];
				$adata['cost_price']=trim($_POST["cost_price"]);
				$adata['modify_by'] = $_SESSION['id'];
				$adata['modify_date']= date('Y-m-d');

		 		$id = trim($_POST["id"]);
				$where = "trans_id='$id'";

					#----check name already exist---------#
				if ($this->mod_shopopbal->check_already_edit($adata['materialcode'],$adata['type'],$id)) {
					$this->session->set_flashdata('err_message', 'Item is Already Exist,Please Update Item');
					redirect(SURL . 'ShopOpeningBalance/edit/'.$id);
					exit();
				}

				$udata['cost_price'] = trim($_POST['cost_price']);

				$this->db->where("materialcode",$_POST['item']);
				$this->db->update("tblmaterial_coding",$udata);
		
				$table='tbl_shop_opening';
				$res=$this->mod_common->update_table($table,$where,$adata);

				if ($res) {
				 	$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
		            redirect(SURL . 'ShopOpeningBalance/');
		        } else {
		            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
		            redirect(SURL . 'ShopOpeningBalance/');
		        }

		    }
	}

	public function delete($id) {

		 $login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '12' limit 1")->row_array();
		if ($role['delete']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'ShopOpeningBalance/index/');
			}

        $table = "tbl_shop_opening";
        $where = "trans_id = '" . $id . "'";
        $getrecord = $this->mod_common->select_single_records($table, $where);
        $itemid = $getrecord['materialcode'];
		if ($this->mod_item->get_issue($itemid)) {
			$this->session->set_flashdata('err_message', 'You can not delete it.');
			redirect(SURL . 'ShopOpeningBalance/');
			exit();
		}
		#-------------delete record--------------#
        $table = "tbl_shop_opening";
        $where = "trans_id = '" . $id . "'";
        $delete_country = $this->mod_common->delete_record($table, $where);

        if ($delete_country) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'ShopOpeningBalance/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'ShopOpeningBalance/');
        }
    }

    public function enable_disable_type(){

		$table='tblmaterial_coding';
		$item=	$this->input->post('item');
		$customer=	$this->input->post('customer');
		$where = array('materialcode' => $item);
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
		$scode=	$this->input->post('scode');
		
		$where = array('materialcode' => $item_id);
		$data['cat_code'] = $this->mod_common->select_array_records($table,"catcode",$where);
		echo json_encode($data['cat_code']);
		echo "|";

		

		$totalgivenqty = 0;
		$totalrecvqty = 0;

		$query12 = $this->db->query("select sum(qty) as totalgivenqty from tbltransfercylinder where fromcustomer='$customer' and itemid='$item_id'");
		if($query12->num_rows() > 0){
			foreach ($query12->result() as $key => $value) {
				 $totalgivenqty = $value->totalgivenqty;
				 
			}

		}

		$query12 = $this->db->query("select sum(qty) as totalrecvqty from tbltransfercylinder where tocustomer='$customer' and itemid='$item_id'");
		if($query12->num_rows() > 0){
			foreach ($query12->result() as $key => $value) {
				 $totalrecvqty = $value->totalrecvqty;
				 
			}

		}

		  $totalgivenqty;
		$totalrecvqty;

		
		
		 $today=date('Y-m-d');
		 $login_user=$this->session->userdata('id');
        $location = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
 
		 	$date_array2 = array('from_date' => '2018-01-01','to_date' => $today, 'acode' => $customer,'location'=>$location, 'scode' => $scode);
	
			$data['opening']=  $this->mod_customerstockledger->get_opening($date_array2,1);
		// print_r($data['opening']);
		 


			$data['return']=  $this->mod_customerstockledger->getreturn($date_array2);
			$data['sale']=  $this->mod_customerstockledger->getsale($date_array2);
  
 		  // print_r($data['return']);
		 
		 $total_return_value=0;
			foreach ($data['return'] as $key => $value) {

				if(count($value['return']>1))
 				{
			 		foreach ($value['return'] as $key => $value_sub) {

			 			$total_return[$value_sub['itemid']]=$total_return[$value_sub['itemid']]+$value_sub['qty'];
			 			 
						 if($item_id==$value_sub['itemid']){
						 $total_return_value+=$value_sub['qty'];
						 }
			 		}
				}
			}
			
  $total_sale_value=0;
			foreach ($data['sale'] as $key => $value) {
				// echo "string";
				// exit;


				if(count($value['sale']>1))
 				{
			 		foreach ($value['sale'] as $key => $value_sub) {

			 			$total_sale[$value_sub['itemid']]=$total_sale[$value_sub['itemid']]+$value_sub['qty'];
						if($item_id==$value_sub['itemid']){
						 $total_sale_value+=$value_sub['qty'];
						 }
			 		}
				}
			}
 

			for ($i=0; $i <count($data['opening']); $i++) { 

				$item_code=$data['opening'][$i]['itemid'];
				$opening_array[$item_code]=$data['opening'][$i]['opening'];
				
				if($item_id==$item_code){
						 $total_open_value+=$data['opening'][$i]['opening'];
						 }

			}
			 
	 
		 
		// print 's:'.$total_sale_value;
		// print 'r:'.$total_return_value;
		// print 'o:'.$total_open_value;
			

			
		  print $total_val= $total_sale_value-$total_return_value+$total_open_value-$totalgivenqty+$totalrecvqty;
	 
			
		
		
		   
		exit;

    }
}
