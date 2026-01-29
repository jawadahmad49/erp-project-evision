<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor extends CI_Controller {


	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_vendor","mod_common","mod_customer"
        ));
        
    }
	public function index()
	{   
        $login_user=$this->session->userdata('id');
       $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
       $general = $this->db->query("select vendor_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['vendor_code'];
       if($sale_point_id=='0'){
       $where_general="where left(acode,6)='100100' and atype='Child'";
        }else{
       $where_general="where general='$general'";
        }

       $data['vendor_list'] = $this->db->query("select * from tblacode $where_general")->result_array();

       
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Vendors";
		$this->load->view($this->session->userdata('language')."/vendor_coding/manage_vendor",$data);
	}

	public function add_vendor()
	{   
		$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '10' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Vendor/index/');
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

        //pm($data['city_list']);

        $table='tbl_brand';       
        $data['brand_list'] = $this->mod_common->get_all_records($table,"*");
        

		$data["filter"] = 'add';
		$this->load->view($this->session->userdata('language')."/vendor_coding/add_vendor",$data);
	}

	public function add(){
		 //$login_user=$this->session->userdata('id');
       //$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
	   $sale_point_id=$_POST["location"];
       $general = $this->db->query("select vendor_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['vendor_code'];
       $rest_creditors_code=$general[0].$general[1].$general[2].$general[3].$general[4].$general[5].$general[6];

		//echo "<pre>";print_r($_POST);exit;
		$data['datas'] = $this->mod_vendor->accountcode_forvendor($rest_creditors_code);
  //echo $data['datas'];exit;
		//pm($_POST["bname"]);



		$adata['acode']=$data['datas'];
		$adata['aname']=trim($_POST["vendorname"]);
		$adata['address']=trim($_POST["address"]);
		$adata['phone_no']=trim($_POST["phoneno"]);
		$adata['cell']=trim($_POST["cellno"]);
		$adata['cont_person']=trim($_POST["contactperson"]);
		$adata['reg_date']=$_POST["regdate"];
		$adata['credit_days']=trim($_POST["creditdays"]);
		$adata['reg_no']=trim($_POST["regno"]);
		$adata['opngbl']=trim($_POST["openingbalance"]);
		$adata['optype']=trim($_POST["openingtype"]);
		$adata['vat_no']=trim($_POST["vatno"]);
		$adata['ac_status']=trim($_POST["status"]);
		$adata['country_id']=trim($_POST["country"]);
		$adata['city_id']=trim($_POST["city"]);
		$adata['area_id']=trim($_POST["area"]);
		$adata['bname']=trim($_POST["bname"]);

		if(isset($_POST["brand1"]) && $_POST["brand1"]!='')
		$adata['brand1']=trim($_POST["brand1"]);

		if(isset($_POST["brand2"]) && $_POST["brand2"]!='')
		$adata['brand2']=trim($_POST["brand2"]);

		if(isset($_POST["brand3"]) && $_POST["brand3"]!='')
		$adata['brand3']=trim($_POST["brand3"]);

		if(isset($_POST["brand4"]) && $_POST["brand4"]!='')
		$adata['brand4']=trim($_POST["brand4"]);

		if(isset($_POST["brand5"]) && $_POST["brand5"]!='')
		$adata['brand5']=trim($_POST["brand5"]);

		if(isset($_POST["brand6"]) && $_POST["brand6"]!='')
		$adata['brand6']=trim($_POST["brand6"]);

		if(isset($_POST["brand7"]) && $_POST["brand7"]!='')
		$adata['brand7']=trim($_POST["brand7"]);

		if(isset($_POST["brand8"]) && $_POST["brand8"]!='')
		$adata['brand8']=trim($_POST["brand8"]);

		if(isset($_POST["brand9"]) && $_POST["brand9"]!='')
		$adata['brand9']=trim($_POST["brand9"]);

		if(isset($_POST["brand10"]) && $_POST["brand10"]!='')
		$adata['brand10']=trim($_POST["brand10"]);
		/* fixed values */
		$adata['general']=$general;
		$adata['atype']="Child";
		$adata['family']="L";
		$adata['sledger']="No";
		$adata['dlimit']=0;
		$adata['climit']=0;

		$j=1;
		for ($i=0; $i <count($_POST["bname"]); $i++) {
			$adata['brand'.$j++]=$_POST["bname"][$i];
		}
 		
		$table='tblacode';
		$res = $this->mod_common->insert_into_table($table,$adata);
		
		if ($res) {
		 	$this->session->set_flashdata('ok_message', 'You have successfully added.');
            redirect(SURL . 'Vendor/');
        } else {
            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
            redirect(SURL . 'Vendor/');
        }
	}

	public function delete($id) {

		$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '10' limit 1")->row_array();
		if ($role['delete']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Vendor/index/');
			}
		
			
		 if ($this->mod_vendor->under_items($id)) {
			$this->session->set_flashdata('err_message', 'Purchases are made for this vendor, you can not delete it.');
			redirect(SURL . 'Vendor/');
			exit();
		}
		if ($this->mod_vendor->used_in_trans($id)) {
			
	 
			$this->session->set_flashdata('err_message', 'There are transactions recorded against this Vendor, you can not delete it.');
			redirect(SURL . 'ExpenseType/');
			exit();
		     }
		
		#-------------delete record--------------#
        $table = "tblacode";
        $where = "acode = '" . $id . "'";
        $delete_area = $this->mod_common->delete_record($table, $where);

        if ($delete_area) {
            $this->session->set_flashdata('ok_message', 'You have successfully deleted.');
            redirect(SURL . 'Vendor/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Vendor/');
        }
    }

    public function edit($id){
    	$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '10' limit 1")->row_array();
		if ($role['edit']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Vendor/index/');
			}
    	$table='tbl_country';       
        $data['country_list'] = $this->mod_common->get_all_records($table,"*");

        $table='tbl_brand';       
        $data['brand_list'] = $this->mod_common->get_all_records($table,"*");
        
        $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
        $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

        if($sale_point_id !=''){ $where_sale_point_id= "and sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['location']=$this->db->query("select * from tbl_sales_point where sale_point_id in (select sale_point_id from tbl_code_mapping) $where_sale_point_id")->result_array();

        $data['city'] = $this->mod_customer->edit_record($id);
			if(empty($data['city']))
			{
	 			$this->session->set_flashdata('err_message', 'Record Not Exist!');			
				redirect(SURL.'Customer');
			}
    	$table='tbl_city';       
        $data['city_list'] = $this->mod_common->get_all_records($table,"*");

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
		$data['vendor'] = $this->mod_common->select_single_records($table,$where);




        $where_cat_id = array('acode' => $id);
        $data['item_list']= $this->mod_common->select_array_records('tblacode',"*",$where_cat_id);
        
        $brand_array='';
        $j=0;

        for ($i=1; $i <=10 ; $i++) { 

        	if($data['item_list'][0]["brand$i"])
        	{
        		$brand_array[$j++]=$data['item_list'][0]["brand$i"];
        	}
        }

        for ($k=0; $k <count($brand_array); $k++) {

        	$brand_id=$brand_array[$k];

        	$where_br_id = array('brandname' => $brand_id);
        	$data['new_item_list']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_br_id);
        	
        	//pm($data['new_item_list']);
        	$flag=0;

        	if(!empty($data['new_item_list']))
        	{
        		foreach ($data['new_item_list'] as $key => $value) {
        			
	        		 $brand_id_materail=$value['materialcode'];

	        		$where_mt_id = array('itemid' => $brand_id_materail);
	        		$data['good_item_list']= $this->mod_common->select_array_records('tbl_goodsreceiving_detail',"*",$where_mt_id);

	        		if(!empty($data['good_item_list']))
	        		{
	        			$flag=1;
	        		}
        		}
        		if($flag==1)
        		{
        			$selected[$brand_id]='disable';
        		}
        	}
        }
        //pm($brand_array);


		$data["selected"] = $selected;

		$data["filter"] = 'update';
		//echo "<pre>";print_r($data);
		$this->load->view($this->session->userdata('language')."/vendor_coding/add_vendor", $data);
	}
	public function update(){
       //$login_user=$this->session->userdata('id');
       //$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$sale_point_id=$_POST["location"];
       $general = $this->db->query("select vendor_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['vendor_code'];
		$adata['aname']=trim($_POST["vendorname"]);
		$adata['address']=trim($_POST["address"]);
		$adata['phone_no']=trim($_POST["phoneno"]);
		$adata['cell']=trim($_POST["cellno"]);
		$adata['cont_person']=trim($_POST["contactperson"]);
		$adata['reg_date']=$_POST["regdate"];
		$adata['credit_days']=trim($_POST["creditdays"]);
		$adata['reg_no']=trim($_POST["regno"]);
		$adata['opngbl']=trim($_POST["openingbalance"]);
		$adata['optype']=trim($_POST["openingtype"]);
		$adata['vat_no']=trim($_POST["vatno"]);
		$adata['ac_status']=trim($_POST["status"]);
		$adata['country_id']=trim($_POST["country"]);
		$adata['city_id']=trim($_POST["city"]);
		$adata['area_id']=trim($_POST["area"]);
		$adata['bname']=trim($_POST["bname"]);

		$adata['brand1']='';
		$adata['brand2']='';
		$adata['brand3']='';
		$adata['brand4']='';
		$adata['brand5']='';
		$adata['brand6']='';
		$adata['brand7']='';
		$adata['brand8']='';
		$adata['brand9']='';
		$adata['brand10']='';

		if(isset($_POST["brand1"]) && $_POST["brand1"]!='')
		$adata['brand1']=trim($_POST["brand1"]);

		if(isset($_POST["brand2"]) && $_POST["brand2"]!='')
		$adata['brand2']=trim($_POST["brand2"]);

		if(isset($_POST["brand3"]) && $_POST["brand3"]!='')
		$adata['brand3']=trim($_POST["brand3"]);

		if(isset($_POST["brand4"]) && $_POST["brand4"]!='')
		$adata['brand4']=trim($_POST["brand4"]);

		if(isset($_POST["brand5"]) && $_POST["brand5"]!='')
		$adata['brand5']=trim($_POST["brand5"]);

		if(isset($_POST["brand6"]) && $_POST["brand6"]!='')
		$adata['brand6']=trim($_POST["brand6"]);

		if(isset($_POST["brand7"]) && $_POST["brand7"]!='')
		$adata['brand7']=trim($_POST["brand7"]);

		if(isset($_POST["brand8"]) && $_POST["brand8"]!='')
		$adata['brand8']=trim($_POST["brand8"]);

		if(isset($_POST["brand9"]) && $_POST["brand9"]!='')
		$adata['brand9']=trim($_POST["brand9"]);

		if(isset($_POST["brand10"]) && $_POST["brand10"]!='')
		$adata['brand10']=trim($_POST["brand10"]);
		/* fixed values */
		$adata['general']=$general;
		$adata['atype']="Child";
		$adata['family']="L";
		$adata['sledger']="No";
		$adata['dlimit']=0;
		$adata['climit']=0;
 		
 		$id = trim($_POST["id"]);
		$where = "acode='$id'";
		
		$table='tblacode';
		$res=$this->mod_common->update_table($table,$where,$adata);

		if ($res) {
		 	$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
            redirect(SURL . 'Vendor/');
        } else {
            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
            redirect(SURL . 'Vendor/');
        }
	}
	 public function get_detail(){
	
		$location = $this->input->post("location");
	
		
       $sale_point_id = $this->db->query("select sale_point_id from tbl_code_mapping where sale_point_id='$location'")->row_array()['sale_point_id'];
      
      if ($sale_point_id=='') {
      	echo "1";
      }
	 
		
	}

}
