<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_customer","mod_common"
        ));
        
    }
	public function index()
	{ 	
		$login_user=$this->session->userdata('id');
       $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
       $general = $this->db->query("select customer_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['customer_code'];
		//$data['customer_list'] = $this->mod_customer->getOnlyCustomers(); 
       if($sale_point_id=='0'){
       $where_general="and left(acode,6)='200400' and atype='Child'";
        }else{
       $where_general="and general='$general'";
        }
		$data['customer_list'] = $this->db->query("select * from tblacode where ac_status='Active' $where_general")->result_array(); 
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Customers";
		$this->load->view($this->session->userdata('language')."/customer_coding/manage_customer",$data);
	}

	public function add_customer()
	{    
		$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '9' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Customer/index/');
			}
		$table='tbl_country';       
        $data['country_list'] = $this->mod_common->get_all_records($table,"*"); 
		
		$table='tbl_city';       
        $data['city_list'] = $this->mod_common->get_all_records($table,"*"); 
        
        $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
        $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

        if($sale_point_id !=''){ $where_sale_point_id= "and sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['location']=$this->db->query("select * from tbl_sales_point where sale_point_id in (select sale_point_id from tbl_code_mapping) $where_sale_point_id")->result_array();

		
		$data["filter"] = 'add';
		$this->load->view($this->session->userdata('language')."/customer_coding/add_customer",$data);
	}

	public function add(){
		//echo "<pre>";print_r($_POST);exit;
		 // $login_user=$this->session->userdata('id');
   //     $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$sale_point_id=$_POST["location"];
       $general = $this->db->query("select customer_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['customer_code'];
       $rest_creditors_code=$general[0].$general[1].$general[2].$general[3].$general[4].$general[5].$general[6];
		$data['datas'] = $this->mod_customer->accountcode_forcustomer($rest_creditors_code);

		$adata['acode']=$data['datas'];
		$adata['aname']=trim($_POST["customername"]);
		$adata['email']=trim($_POST["email"]);
		$adata['address']=trim($_POST["address"]);
		$adata['phone_no']=trim($_POST["phoneno"]);
		$adata['cell']=trim($_POST["cell"]);
		$adata['cont_person']=trim($_POST["contactperson"]);
		$adata['reg_date']=$_POST["regdate"];
		$adata['credit_days']=trim($_POST["creditdays"]);
		$adata['reg_no']=trim($_POST["regno"]);
		$adata['opngbl']=trim($_POST["openingbalance"]);
		$adata['cnic']=trim($_POST["cnic"]);
		$adata['optype']=trim($_POST["openingtype"]);
		$adata['vat_no']=trim($_POST["vatno"]);
		$adata['ac_status']=trim($_POST["status"]);
		$adata['segment']=trim($_POST["segment"]);
		$adata['country_id']=trim($_POST["country"]);
		$adata['city_id']=trim($_POST["city"]);
		$adata['area_id']=trim($_POST["area"]);
		/* fixed values */
		$adata['general']=$general;
		$adata['atype']="Child";
		$adata['family']="L";
		$adata['sledger']=trim($_POST["sledger"]);
		$adata['dlimit']=0;
		$adata['climit']=0;
 
		
		$table='tblacode';
		$res = $this->mod_common->insert_into_table($table,$adata);

		//q();


		if ($res) {
		 	$this->session->set_flashdata('ok_message', 'You have successfully added.');
            redirect(SURL . 'Customer/');
        } else {
            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
            redirect(SURL . 'Customer/');
        }
	}

	public function delete($id) {
		$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '9' limit 1")->row_array();
		if ($role['delete']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Customer/index/');
			}
		
				if ($this->mod_customer->under_items($id)) {
			$this->session->set_flashdata('err_message', 'Sale is recorded for this customer , you can not delete it.');
			redirect(SURL . 'Customer/');
			exit();
		}
		if ($this->mod_customer->used_in_trans($id)) {
			
	 
			$this->session->set_flashdata('err_message', 'There are transactions recorded against this Customer, you can not delete it.');
			redirect(SURL . 'ExpenseType/');
			exit();
		     }
		
		#-------------delete record--------------#
        $table = "tblacode";
        $where = "acode = '" . $id . "'";
        $delete_area = $this->mod_common->delete_record($table, $where);

        if ($delete_area) {
            $this->session->set_flashdata('ok_message', 'You have successfully deleted.');
            redirect(SURL . 'Customer/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Customer/');
        }
    }

    public function edit($id){
    	$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '9' limit 1")->row_array();
		if ($role['edit']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Customer/index/');
			}
		$table='tbl_country';       
        $data['country_list'] = $this->mod_common->get_all_records($table,"*");
        

        $data['city'] = $this->mod_customer->edit_record($id);

			if(empty($data['city']))
			{
	 			$this->session->set_flashdata('err_message', 'Record Not Exist!');			
				redirect(SURL.'Customer');
			}
    	$where_id = array('country_id' => $data['city']['country_id']);
    	$table='tbl_city';       
    	$data['city_list']= $this->mod_common->select_array_records($table,"*");

        $data['area'] = $this->mod_customer->edit_record($id);
			if(empty($data['area']))
			{
	 			$this->session->set_flashdata('err_message', 'Record Not Exist!');			
				redirect(SURL.'Customer');
			}
    	$where_id = array('city_id' => $data['area']['city_id']);
    	$table='tbl_area';       
    	$data['area_list']= $this->mod_common->select_array_records($table,"*",$where_id);

		$table='tblacode';
		$where = "acode='$id'";
		$data['customer'] = $this->mod_common->select_single_records($table,$where);
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
        $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

        if($sale_point_id !=''){ $where_sale_point_id= "and sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['location']=$this->db->query("select * from tbl_sales_point where sale_point_id in (select sale_point_id from tbl_code_mapping) $where_sale_point_id")->result_array();
		$data["filter"] = 'update';
		//echo "<pre>";print_r($data);
		$this->load->view($this->session->userdata('language')."/customer_coding/add_customer", $data);
	}
	public function update(){
		 // $login_user=$this->session->userdata('id');
   //     $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
       $sale_point_id=$_POST["location"];
       $general = $this->db->query("select customer_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['customer_code'];

		$adata['aname']=trim($_POST["customername"]);
		$adata['email']=trim($_POST["email"]);
		$adata['address']=trim($_POST["address"]);
		$adata['phone_no']=trim($_POST["phoneno"]);
		$adata['cell']=trim($_POST["cell"]);
		$adata['cont_person']=trim($_POST["contactperson"]);
		$adata['reg_date']=$_POST["regdate"];
		$adata['credit_days']=trim($_POST["creditdays"]);
		$adata['reg_no']=trim($_POST["regno"]);
		$adata['cnic']=trim($_POST["cnic"]);
		$adata['opngbl']=trim($_POST["openingbalance"]);
		$adata['optype']=trim($_POST["openingtype"]);
		$adata['vat_no']=trim($_POST["vatno"]);
		$adata['ac_status']=trim($_POST["status"]);
		$adata['segment']=trim($_POST["segment"]);
		$adata['country_id']=trim($_POST["country"]);
		$adata['city_id']=trim($_POST["city"]);
		$adata['area_id']=trim($_POST["area"]);
		/* fixed values */
		$adata['general']=$general;
		$adata['atype']="Child";
		$adata['family']="L";
		$adata['sledger']=trim($_POST["sledger"]);
		$adata['dlimit']=0;
		$adata['climit']=0;
 		
 		$id = trim($_POST["id"]);
		$where = "acode='$id'";
		
		$table='tblacode';
		$res=$this->mod_common->update_table($table,$where,$adata);

		if ($res) {
		 	$this->session->set_flashdata('ok_message', 'You have successfully updated.');
            redirect(SURL . 'Customer/');
        } else {
            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
            redirect(SURL . 'Customer/');
        }
	}

	function get_city()
	{
	    $table='tbl_city';
		$country_id=	$this->input->post('country_id');
		$where = array('country_id' => $country_id);
		$data['city_list'] = $this->mod_common->select_array_records($table,"*",$where);

		if($data['city_list']){?>
			<option value="">Choose a City...</option>
		<?php
			foreach ($data['city_list'] as $key => $value) {
				?>
				
				<option value="<?php echo  $value['city_id']; ?>"><?php echo  $value['city_name']; ?></option>
				
			<?php }
		}	
		
	}

	function get_area()
	{
	    $table='tbl_area';
		$city_id=	$this->input->post('city_id');
		$where = array('city_id' => $city_id);
		$data['area_list'] = $this->mod_common->select_array_records($table,"*",$where);

		foreach ($data['area_list'] as $key => $value) {
			?>
			<option value="<?php echo  $value['area_id']; ?>"><?php echo  $value['aname']; ?></option>
			
		<?php }
		
	}

}
