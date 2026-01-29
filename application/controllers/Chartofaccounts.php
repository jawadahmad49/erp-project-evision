<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chartofaccounts extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_coa","mod_common","mod_customer"
        ));
        
    }
	public function index()
	{
		$table='tblacode';
		$loccode= $this->session->userdata('loccode');
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Customers";
		$this->load->view($this->session->userdata('language')."/coa_coding/coa_manage",$data);
	}

	public function get_coa()
	{	  
	  $data=  $this->mod_coa->get_coa();	 
	}
	
	public function add_customer()
	{
		$table='tbl_country';       
        $data['country_list'] = $this->mod_common->get_all_records($table,"*"); 
		$table='tbl_city';       
        $data['city_list'] = $this->mod_common->get_all_records($table,"*"); 
		$data["filter"] = 'add';

		$table='tbl_admin';
		$where = array('status'=>"Active");
        $data['salesman'] = $this->mod_common->select_array_records($table,"*",$where);
 
		$loccode= $this->session->userdata('loccode');
		$table='tbl_sales_point';
		if($loccode=='1000'){ $where = "sts='Active'"; }else{$where = "sts='Active' and sale_point_id='$loccode'";}
        $data['sections'] = $this->mod_common->select_array_records($table,"*",$where);
		$this->load->view($this->session->userdata('language')."/coa_coding/add_customer",$data);
	}

	public function add(){
		
		 $login_user=$this->session->userdata('id');
		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '307' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Chartofaccounts/index/');
			}

	  	$data['datas'] = $this->mod_customer->accountcode_forcustomer($_POST['loccode']);
		
		 
		$arrr= explode("|",$data['datas']);
		if($arrr[1]){
		$adata['acode']=$arrr[0];
		$adata['aname']=trim($_POST["customername"]);
		$adata['email']=trim($_POST["email"]);
		$adata['address']=trim($_POST["address"]);
		$adata['saleman_id']=trim($_POST['salemanid']);
		$adata['loccode']=trim($_POST['loccode']);
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
		$adata['segment']=trim($_POST["segment"]);
		$adata['country_id']=trim($_POST["country"]);
		$adata['city_id']=trim($_POST["city"]);
		$adata['area_id']=trim($_POST["area"]);
		/* fixed values */
		$adata['general']=$arrr[1];
		$adata['atype']="Child";
		$adata['family']="L";
		$adata['sledger']="No";
		$adata['dlimit']=0;
		$adata['climit']=0;
 
		
		$table='tblacode';
		$res = $this->mod_common->insert_into_table($table,$adata);

		//For Customer Log
		$last_id = $this->db->insert_id();

		$get_cus_data = $this->db->get_where($table,array('id'=>$last_id))->row();
		$log_data['acode'] = $get_cus_data->acode;
		$log_data['dt'] = $_POST['regdate'];

		$get_saleman = $this->db->get_where($table,array('id'=>$get_cus_data->saleman_id))->row();
		$log_data['salesman_code'] = $get_saleman->acode;
		$log_data['created_by'] = $this->session->userdata('id');
		$log_data['created_dt'] = date("Y-m-d");

		$this->db->insert('tbl_customer_log',$log_data);

		
			$loccode= $this->session->userdata('loccode');
			$table='tbl_sales_point';
			if($loccode=='1000'){
				$fold_name='sp';
				$where = "sts='Active'";}else{$where = "sts='Active' and sale_point_id='$loccode'";}
			$data['sections'] = $this->mod_common->select_array_records($table,"*",$where);

if($data['sections']['sp_type']=='Warehouse'){ $fold_name='whouse';}
if($data['sections']['sp_type']=='Salepoint'){ $fold_name='sp';}
		
		
		if ($res) {
		 	$this->session->set_flashdata('ok_message', 'You have successfully added.');
            redirect(SURL.'/Customer/');
        } else {
            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
            redirect(SURL.'/Customer/');
        }
		
		
	}else{
		
			$this->session->set_flashdata('err_message', 'Adding Operation Failed.');
            redirect(SURL . $fold_name.'/Customer/');
		
	}
	}

	public function delete($id) {
		
		$login_user=$this->session->userdata('id');
		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '307' limit 1")->row_array();
		if ($role['delete']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Chartofaccounts/index/');
			}

		if ($this->mod_customer->under_transactions($id)) {
			$this->session->set_flashdata('err_message', 'Transaction is recorded for this account , you can not delete it.');
			redirect(SURL . '/Chartofaccounts/');
			exit();
		}
		
	 
		 
		if ($this->mod_customer->under_childs($id)) {
			$this->session->set_flashdata('err_message', 'Childes are created under this code, can not delete it.');
			redirect(SURL . '/Chartofaccounts/');
			exit();
		}
		
		
		
		
		#-------------delete record--------------#
        $table = "tblacode";
        $where = "acode = '" . $id . "'";
        $delete_area = $this->mod_common->delete_record($table, $where);

		
		
		
		
		
		
        if ($delete_area) {
            $this->session->set_flashdata('ok_message', 'You have successfully deleted.');
            redirect(SURL.'Chartofaccounts/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL .'Chartofaccounts/');
        }
    }

    public function edit($id){

    	$login_user=$this->session->userdata('id');
		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '307' limit 1")->row_array();
		if ($role['edit']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Chartofaccounts/index/');
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

		
		
		$table='tbl_sales_point';
		$where = "sts='Active'";
        $data['sections'] = $this->mod_common->select_array_records($table,"*",$where);
		
		
		$table='tblacode';
		$where = "acode='$id'";
		$data['customer'] = $this->mod_common->select_single_records($table,$where);
		$data["filter"] = 'update';
 
	 
			$table='tbl_admin';
		$where = array('status'=>"Active");
        $data['salesman'] = $this->mod_common->select_array_records($table,"*",$where);
 
		//echo "<pre>";print_r($data);
		$this->load->view($this->session->userdata('language')."/coa_coding/add_customer", $data);
	}
	public function update(){

		$adata['aname']=trim($_POST["customername"]);
		$adata['email']=trim($_POST["email"]);
		$adata['address']=trim($_POST["address"]);
		$adata['saleman_id']=trim($_POST['salemanid']);
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
		$adata['segment']=trim($_POST["segment"]);
		$adata['country_id']=trim($_POST["country"]);
		$adata['city_id']=trim($_POST["city"]);
		$adata['area_id']=trim($_POST["area"]);
		 
		 
		$adata['atype']="Child";
		$adata['family']="L";
		$adata['sledger']="No";
		$adata['dlimit']=0;
		$adata['climit']=0;
 		
 		$id = trim($_POST["id"]);
		$where = "acode='$id'";
		
		$table='tblacode';
		$res=$this->mod_common->update_table($table,$where,$adata);

		$get_cus_data = $this->db->get_where($table,array('acode'=>$id))->row();

		$log_data['acode'] = $id;
		$log_data['dt'] = $_POST['regdate'];

		$get_saleman = $this->db->get_where($table,array('id'=>$get_cus_data->saleman_id))->row();
		$log_data['salesman_code'] = $get_saleman->acode;

		$log_data['created_by'] = $this->session->userdata('id');
		$log_data['created_dt'] = date("Y-m-d");

		$this->db->insert('tbl_customer_log',$log_data);

		
		

			$loccode= $this->session->userdata('loccode');
			$table='tbl_sales_point';
			if($loccode=='1000'){
			$fold_name='sp';
			$where = "sts='Active'";}else{$where = "sts='Active' and sale_point_id='$loccode'";}
			$data['sections'] = $this->mod_common->select_array_records($table,"*",$where);

		 
			if($data['sections'][0]['sp_type']=='Warehouse'){ $fold_name='whouse';}
			if($data['sections'][0]['sp_type']=='Salepoint'){ $fold_name='sp';}

	 
		
		if ($res) {
		 	$this->session->set_flashdata('ok_message', 'You have successfully updated.');
            redirect(SURL.'/Customer/');
        } else {
            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
            redirect(SURL.'/Customer/');
        }
	}

	function get_rec($ac)
	{
	    $table='tblacode';
 
		$where = array('acode' => $ac);
		$data['city_list'] = $this->mod_common->select_array_records($table,"*",$where);

		 foreach ($data['city_list'] as $key => $line) {
			 print $line['acode']."|".$line['aname']."|".$line['address']."|".$line['general']."|".$line['atype']
		."|".$line['family']."|".$line['sledger']."|".$line['dlimit']."|".$line['climit']."|".$line['opngbl']."|".$line['optype']
		."|".$line['stregno']."|".$line['isplaccount']."|".$line['level']."|".$line['phone_no'].'|'.$line['fax']."|".$line['reg_no']
		."|".$line['vat_no']."|".$line['cont_person']."|".$line['email']."|".$line['cell']."|";
		 }
		
	}
	
	
	
	
function add_update()
	{
		
		
 
 
		$pcode_hidden=trim($_POST["pcode_hidden"]);
		$isedit=trim($_POST["isedit"]);
		$adata['pname']=trim($_POST["pname"]);
		$adata['email']=trim($_POST["email"]);
		$adata['address']=trim($_POST["adres"]);
		$adata['gen']=trim($_POST['gen']);
		$adata['family']=trim($_POST["family"]);
		$adata['sledger']=trim($_POST['sledger']);
		$adata['dlimit']=trim($_POST['dlimit']);
		$adata['climit']=trim($_POST['climit']);
		$adata['opbalance']=trim($_POST["opbalance"]);
		$adata['optype']=trim($_POST["optype"]);
		$adata['streg']=trim($_POST["streg"]);
	    $adata['ispl']=trim($_POST["ispl"]); 
		$adata['phone']=trim($_POST["phone"]);
		$adata['cont_person']=trim($_POST["cont_person"]);
		$adata['fax']=trim($_POST["fax"]);
		$adata['ntn']=trim($_POST["ntn"]);
		$adata['pcode_hidden']=trim($_POST["pcode_hidden"]);
		
		if($isedit==1){
		$add=$this->mod_coa->update_account($adata);
		}else{
		$add = $this->mod_coa->add_new_account($adata);
		}
		 
		if ($add) {
            print 'Added/updated  Operation Failed';
        } else {
         
		 	print 'Added/updated successfully';
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
