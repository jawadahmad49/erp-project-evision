<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Salereturnreport extends CI_Controller {

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
            "mod_salereturnreport","mod_common","mod_customer","mod_salereturn"
        ));
        
    }

	public function index()
	{
		// $table='tblacode';
		// $where = "general='2004001000'";
		// $data['customers'] = $this->mod_common->select_array_records($table,'*',$where);

		// $data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $customer_code=$fix_code['customer_code'];
          if($customer_code !=''){ $where_customer= " and tblacode.general='$customer_code'  "; }else{ $where_customer =""; }
		//$data['vendor_list'] = $this->mod_vendor->getOnlyVendors();
		$data['customer_list'] = $this->db->query("select * from tblacode where atype='Child' $where_customer")->result_array();

		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Sale Return Report";	
		$this->load->view($this->session->userdata('language')."/Salereturnreport/search",$data);
	}

	public function details()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST'){

			$data['report']=  $this->mod_salereturnreport->get_details($this->input->post());
			//pm($data['report']); exit;
			#----check name already exist---------#
			// if ($this->mod_city->get_by_title($data['city_name'])) {
			// 	$this->session->set_flashdata('err_message', 'Name Already Exist.');
			// 	redirect(SURL . 'city/add_city');
			// 	exit();
			// }


						$table='tbl_company';       
        	$data['company'] = $this->mod_common->get_all_records($table,"*");

        	
				$data['from_date'] = trim($this->input->post('from_date'));
				$data['to_date'] = trim($this->input->post('to_date'));

				

			if ($data['report']) {
			 	//$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            //redirect(SURL . 'Salereturnreport/detail',$data);
	            $data["title"] = "Sale Return Report";
	            $this->load->view($this->session->userdata('language')."/Salereturnreport/detail",$data);
	        } else {
	            $this->session->set_flashdata('err_message', 'No Record Found.');
	            redirect(SURL . 'Salereturnreport/');
	        }
	    }else{
	        //$data["filter"] = 'add';
	        $data["title"] = "Sale Return Report";    			
			$this->load->view($this->session->userdata('language')."/Salereturnreport/detail",$data);
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
		//echo '<pre>';print_r($data['edit_list']);exit;
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Sale Return Report";
		$this->load->view($this->session->userdata('language')."/Salereturnreport/single",$data);
		}
	}

	public function edit($id){
		if($id){
			$table='tbltrans_detail';
			$where = "vno='$id'";
			$data['payemetreceipt'] = $this->mod_common->select_single_records($table,$where);
			//pm($data['payemetreceipt']);exit;
	        $data["filter"] = 'edit';
        	$data["title"] = "Update Payment/Receipt";
			$this->load->view($this->session->userdata('language')."/Salereturnreport/add", $data);
		}
		/* Update Data */
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$update=  $this->mod_transaction->update_transaction($this->input->post());
			
			// $transaction = $this->input->post('transaction');
			// if($transaction=="Payment"){ $vtype="CP"; }else{ $vtype="CR";}

			// $data['vtype'] = $vtype;
			// $data['type'] = $this->input->post('types');
			// $data['name'] = mysql_real_escape_string(trim($this->input->post('name')));
			// $data['created_date'] = $this->input->post('date');
			// $data['damount'] = $this->input->post('amount');
			// $data['remarks'] = $this->input->post('remarks');
			// //$data['modify_by'] = $_SESSION['id'];
			// //$data['modify_date']= date('Y-m-d');
			// $editid = $this->input->post('id');

			// // 		#----check name already exist---------#
			// // 			if ($this->mod_city->edit_by_title($cdata['city_name'],$id)) {
			// // 				$this->session->set_flashdata('err_message', 'Name Already Exist.');
			// // 				redirect(SURL . 'city/edit/'.$id);
			// // 				exit();
			// // 			}

			// $table='tbltrans_detail';
			// $where = "id='$editid'";
	 	// 	$res=$this->mod_common->update_table($table,$where,$data);

			if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
	            redirect(SURL . 'Salereturnreport/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Operation Failed.');
	            redirect(SURL . 'Salereturnreport/');
	        }
	    }
	}

	public function delete($id) {
		// if ($this->mod_city->under_area($id)) {
		// 	$this->session->set_flashdata('err_message', 'There are areas under city you can not delete it.');
		// 	redirect(SURL . 'city/');
		// 	exit();
		// } 
		#-------------delete record--------------#
        $table = "tbltrans_detail";
        $where = "vno = '" . $id . "'";
        $delete = $this->mod_common->delete_record($table, $where);

        if ($delete) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Salereturnreport/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Salereturnreport/');
        }
    }

	function get_expensetypename()
	{
	    $table='tbl_exptype_coding';
		$t_id=	$this->input->post('t_id');
		$where = array('type' => $t_id);
		$data['expense_name'] = $this->mod_common->select_array_records($table,"*",$where);

		foreach ($data['expense_name'] as $key => $value) {
			?>
			<option value="<?php echo  $value['id']; ?>"><?php echo  $value['name']; ?></option>
			
		<?php }
		
	}

}
