<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CustomerOpeningBalance extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_customeropbal","mod_common"
        ));
        
    }
    
	public function index()
	{
		
	    $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
         if($sale_point_id=='0'){
        $where_sale_point_id="";
        }else{
        $where_sale_point_id="where sale_point_id='$sale_point_id'";
        }
        $data['copening_list'] =$this->db->query("select * from tbl_customer_opening $where_sale_point_id order by trans_id desc")->result_array();
			$data["filter"] = '';
			#----load view----------#
			$data["title"] = "Manage Customer Opening Balance";
			$this->load->view($this->session->userdata('language')."/customer_opening_balance/customer_opening_balance",$data);
		
	}

	public function add_customeropeningbalance()
	{
		

		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];
        $customer_code=$fix_code['customer_code'];
        if($customer_code !=''){ $where_customer_code= " and tblacode.general='$customer_code'  "; }else{ $where_customer_code =""; }
        

        if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['sale_point_id']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();
		
		 $data['custumer'] =$this->db->query("select * from tblacode where atype='Child'  $where_customer_code ")->result_array();

			$table='tblmaterial_coding';       
			$data['item_list'] = $this->mod_common->get_all_records($table,"*");
			//pm($data['item_list'] );

			$data["title"] = "Add Customer Opening Balance";
			$data["filter"] = 'add';
			$data['id']=$id;
			$this->load->view($this->session->userdata('language')."/customer_opening_balance/add_customeropeningbalance",$data);
		
	}

	public function edit($id){
		if($id){
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];
        $customer_code=$fix_code['customer_code'];
        if($customer_code !=''){ $where_customer_code= " and tblacode.general='$customer_code'  "; }else{ $where_customer_code =""; }
        

        if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['sale_point_id']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();
		
		 $data['custumer'] =$this->db->query("select * from tblacode where atype='Child'  $where_customer_code ")->result_array();
			$table='tbl_customer_opening';
			$where = "trans_id='$id'";
			$data['customer_opening'] = $this->mod_common->select_single_records($table,$where);

			$table='tblmaterial_coding';       
			$data['item_list'] = $this->mod_common->get_all_records($table,"*");

			$data["filter"] = 'update';
			//echo "<pre>";print_r($data);
			$this->load->view($this->session->userdata('language')."/customer_opening_balance/edit",$data);
		}
	}

	public function add(){
		//echo "<pre>";print_r($_POST);exit;
			if($this->input->server('REQUEST_METHOD') == 'POST'){

				 $sale_date=$this->input->post('date');
				$date_array = array('post_date' => $sale_date);
				$login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			$date_array = array('sale_point_id =' => $sale_point_id);
				 $last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);
				 $login_user=$this->session->userdata('id');
			

				if(!empty($last_date))
				{
					//echo "string";
					$this->session->set_flashdata('err_message', 'Already closed for this date');
					redirect(SURL . 'CustomerOpeningBalance/add_customeropeningbalance/'.$_POST["id"]);
				}

				$adata['acode']=trim($_POST["Customer"]);
				$adata['sale_point_id']=trim($_POST["sale_point_id"]);
				$adata['materialcode']=trim($_POST["item"]);
				$adata['qty']=trim($_POST["quantity"]);
				$adata['scode']=trim($_POST["scode"]);
				$adata['date']=$_POST["date"];
				$adata['created_by'] = $login_user;
				$adata['created_date']= date('Y-m-d');

				#----check item already exist---------#
				if ($this->mod_customeropbal->check_already($adata['materialcode'],$adata['acode'])) {
					$this->session->set_flashdata('err_message', 'Item is Already Exist,Please Update Item');
					redirect(SURL . 'CustomerOpeningBalance/add_customeropeningbalance/'.$adata['acode']);
					exit();
				}
				
				$table='tbl_customer_opening';
				$res = $this->mod_common->insert_into_table($table,$adata);

				if ($res) {
				 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
		            redirect(SURL . 'CustomerOpeningBalance/add_customeropeningbalance/'.$adata['acode']);
		        } else {
		            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
		            redirect(SURL . 'CustomerOpeningBalance/add_customeropeningbalance/'.$adata['acode']);
		        }

		    }
	}

	public function update(){
		//echo "<pre>";print_r($_POST);exit;
			if($this->input->server('REQUEST_METHOD') == 'POST'){


			$sale_date=$this->input->post('date');
			$acode=$this->input->post('acode');

			$date_array = array('post_date' => $sale_date);
			$login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			$date_array = array('sale_point_id =' => $sale_point_id);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);
			$login_user=$this->session->userdata('id');
			
			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'CustomerOpeningBalance/index/'.$acode);
			}



				$adata['materialcode']=trim($_POST["item"]);
				$adata['sale_point_id']=trim($_POST["sale_point_id"]);
				$adata['qty']=trim($_POST["quantity"]);
				$adata['date']=$_POST["date"];
				$adata['scode']=trim($_POST["scode"]);
				$adata['modify_by'] = $login_user;
				$adata['modify_date']= date('Y-m-d');

		 		$id = trim($_POST["id"]);
				$where = "trans_id='$id'";

					#----check name already exist---------#
				if ($this->mod_customeropbal->check_already_edit($adata['materialcode'],$_POST['acode'],$id)) {
					$this->session->set_flashdata('err_message', 'Item is Already Exist,Please Update Item');
					redirect(SURL . 'CustomerOpeningBalance/edit/'.$id);
					exit();
				}
		
				$table='tbl_customer_opening';
				$res=$this->mod_common->update_table($table,$where,$adata);

				if ($res) {
				 	$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
		            redirect(SURL . 'CustomerOpeningBalance/index/'.$_POST["acode"]);
		        } else {
		            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
		            redirect(SURL . 'CustomerOpeningBalance/index/'.$_POST["acode"]);
		        }

		    }
	}

	public function delete($id) {
		#-------------delete record--------------#
        $table = "tbl_customer_opening";
        $where = "trans_id = '" . $id . "'";
        $delete_country = $this->mod_common->delete_record($table, $where);

        if ($delete_country) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'CustomerOpeningBalance/index/'.$acode);
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'CustomerOpeningBalance/index/'.$acode);
        }
    }
    public function get_branch()
	{ 
	   
		$Customer=$this->input->post('Customer');
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		//echo $Customer;exit();
		 if($sale_point_id=='0'){
        $where_sale_point_id="";
        }else{
        $where_sale_point_id="and sale_point_id='$sale_point_id'";
        }

		$customer_code=$this->db->query("select * from tblsledger where acode='$Customer' $where_sale_point_id")->result_array();	
		$scode =$_SESSION["scode"];

		if ($customer_code[0]['id']>0) {
		
		

		?>
			<!-- <option value="0">Choose a Branch...</option> -->
		<?php
			foreach ($customer_code as $key => $data) {
				?>
				
				
				<option   value="<?php echo $data['scode']; ?>"<?php if($data['id']==$scode){ ?> selected <?php } ?>><?php echo ucwords($data['stitle']); ?></option>
				
			<?php }
		}else{
			echo 0;
		}

		
		
	}
}
