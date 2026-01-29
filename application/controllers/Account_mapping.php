<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account_mapping extends CI_Controller {


	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_vendor","mod_common","mod_bulkpurchase","mod_salelpg","mod_bank","mod_admin","mod_vendorledger"
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

		  $data['account_mapping'] = $this->db->query("select * from tbl_sales_point inner join tbl_code_mapping on tbl_sales_point.sale_point_id = tbl_code_mapping.sale_point_id where tbl_code_mapping.created_dt between '$from_date' and '$to_date' order by tbl_code_mapping.trans_id desc")->result_array();


	  $data['customer_list'] = $this->db->query("select * from tblacode where atype='Child' and general='2002004000'")->result_array()[0];
	  //pm($data['customer_list']);

		$this->load->view($this->session->userdata('language')."/Account_mapping/bulkpurchase",$data);
	}

	public function add_bulkpurchase()
	{
           $login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '16' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Account_mapping/index/');
			}
		
		  $data['tank_list'] = $this->db->query("select * from tbl_sales_point where sts='Active' and sale_point_id not in (select sale_point_id from tbl_code_mapping)")->result_array();  
//	  pm($data['tank_list'] );exit;

		   $data['acode_list'] = $this->db->query("select * from tblacode where atype='Child'")->result_array();
		    $data['general_list'] = $this->db->query("select * from tblacode where atype='Parent'")->result_array();
		     		 

		$this->load->view($this->session->userdata('language')."/Account_mapping/add_bulkpurchase",$data);
	}

	public function edit($id)
	{        
		      $login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '16' limit 1")->row_array();
		if ($role['edit']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Account_mapping/index/');
			}
		
             $data['tank_list'] = $this->db->query("select * from tbl_sales_point where sale_point_id='$id'")->result_array();

            $data['acode_list'] = $this->db->query("select * from tblacode where atype='Child'")->result_array();
		    $data['general_list'] = $this->db->query("select * from tblacode where atype='Parent'")->result_array();
		    $data['record'] = $this->db->query("select * from tbl_code_mapping where trans_id='$id'")->result_array()[0];   
		// pm($data['record']);    		 

		$this->load->view($this->session->userdata('language')."/Account_mapping/add_bulkpurchase",$data);
	}

	public function add(){

		//pm($this->input->post());exit;

		$login_user=$this->session->userdata('id');
        $array=array(
        	        "sale_point_id"=>$this->input->post("sale_point_id"),
					"cash_code"=>$this->input->post("cash_code"),
					"tax_pay"=>$this->input->post("tax_pay"),
					"tax_receive"=>$this->input->post("tax_receive"),
					"customer_code"=>$this->input->post("customer_code"),
					"vendor_code"=>$this->input->post("vendor_code"),
					"sales_code"=>$this->input->post("sales_code"),
					"stock_code"=>$this->input->post("stock_code"),
					"bank_code"=>$this->input->post("bank_code"),
					"expense_code"=>$this->input->post("expense_code"),
					"cost_of_goods_code"=>$this->input->post("cost_of_goods_code"),
					"frieght_code"=>$this->input->post("frieght_code"),
					"bulk_sales_code"=>$this->input->post("bulk_sales_code"),
					"transporter_code"=>$this->input->post("transporter_code"),
					"security_code"=>$this->input->post("security_code"),
					"gas_return_code"=>$this->input->post("gas_return_code"),
					"empty_stock_code"=>$this->input->post("empty_stock_code"),
					"empty_sale_code"=>$this->input->post("empty_sale_code"),
					"appliances_code"=>$this->input->post("appliances_code"),
					"sale_cylinder_code"=>$this->input->post("sale_cylinder_code"),
					"cost_of_goods_cylinder_code"=>$this->input->post("cost_of_goods_cylinder_code"),
					"cylinder_wo_sec_code"=>$this->input->post("cylinder_wo_sec_code"),
					"gain_loss_code"=>$this->input->post("gain_loss_code"),
					"cylinder_sec_code"=>$this->input->post("cylinder_sec_code"),
					"delivery_charges_code"=>$this->input->post("delivery_charges_code"),
					"discount_code"=>$this->input->post("discount_code"),
					"other_cylinder_stock"=>$this->input->post("other_cylinder_stock"),
					"cost_of_goods_appliances_code"=>$this->input->post("cost_of_goods_appliances_code"),
					"created_by"=>$login_user,
					"created_dt"=>date('Y-m-d'),
					
					
					
			   );

// pm($array);exit();
		$this->db->trans_start();

		if(empty($this->input->post("edit"))){
		    
			$last_id = $this->mod_common->insert_into_table("tbl_code_mapping", $array);
		 
		}else{
		    
		      //pm($this->input->post());exit();
			$last_id = $this->input->post("edit");
             $this->mod_common->update_table("tbl_code_mapping",array("trans_id"=>$last_id), $array);
           	
		}
		
	
		
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->session->set_flashdata('err_message', '- Error in adding please try again!');
            redirect(SURL . 'Account_mapping/');
        }else{
        	$this->session->set_flashdata('ok_message', 'Added Successfully!');
            redirect(SURL . 'Account_mapping/');
        }
		}
public function delete($id) {
			//echo $id;exit();
 $login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '16' limit 1")->row_array();
		if ($role['delete']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Account_mapping/index/');
			}
		$this->db->trans_start();
        $table = "tbl_code_mapping";
        $where = array("trans_id"=>$id);
       	$delete = $this->mod_common->delete_record($table, $where);

        
		$this->db->trans_complete();

		if ($this->db->trans_status() === TRUE)
		{
		    $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Account_mapping/');
		}else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Account_mapping/');
        }
    }


	
	
}
