<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vouchers extends CI_Controller {


	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_voucher","mod_common","mod_admin","mod_customer"
        ));
        
    }

	public function index()
	{

	    $table='tbltrans_master';
		$where = "vtype='JV' AND svtype!='CP'";
		$this->db->order_by("masterid","DESC");
		$data['paymentreceipt_list'] = $this->db->get_where($table,$where)->result_array();

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Voucher";	
		$this->load->view($this->session->userdata('language')."/voucherreceipt/manage_paymentreceipt",$data);
	}


	public function detail($id){

		if($id){
			$table='tblacode';
			$where = array('atype' => 'Child','acode!='=>'2003013001');
			$data['aname'] = $this->mod_common->select_array_records($table,"*",$where);

			$table='tbl_company';       
        	$data['company'] = $this->mod_common->get_all_records($table,"*");


			$table='tbltrans_master';
			$where = "vno='$id'";
			$data['single_edit'] = $this->mod_common->select_single_records($table,$where);
        	//pm($data['company'] )
			
			$tables='tbltrans_detail';
			$wheres = "vno='$id'";
			$data['paymentreceipt_list'] = $this->mod_voucher->select_trans_print_records($wheres);

			//pm($data['paymentreceipt_list']);

	        $data["filter"] = 'edit';
        	$data["title"] = "Voucher Payment/Receipt";
			$this->load->view($this->session->userdata('language')."/voucherreceipt/single",$data);
		}
		else{
			redirect(SURL.'PaymentReceipt');
		}
	}

	public function add()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$this->db->trans_start();
			$add=  $this->mod_voucher->add_transaction($this->input->post());
			$this->db->trans_complete();
			if ($add) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            redirect(SURL . 'voucherreceipt/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'voucherreceipt/');
	        }
	    }
	    $table='tblacode';
		$where = array('atype' => 'Child','acode!='=>'2003013001','ac_status ='=>'Active');
		$data['customer_list'] = $this->mod_common->select_array_records($table,"*",$where);
		


		$userid=$this->session->userdata('id'); 

		$type="JV"; 
		$result = $this->db->get_where('tbltrans_master',array('vtype'=>$type));

		$Sr = array();
		if(count($result->result_array())!=0)
		{
			foreach ($result->result_array() as $key => $value) {
				$parts = explode("-",$value['vno']);
				$Sr[] = $parts[2];
			}

			$billno=max($Sr)+1;
		}
		else
			$billno = 1;
		if($billno <=9)
			$billno = "00000" . $billno;
		else if($billno <=99)
			$billno = "0000" . $billno;
		else if($billno <=999)
			$billno = "000" . $billno;
		else if($billno <=9999)
			$billno = "00" . $billno;
		else if($billno <=99999)
			$billno = "0" . $billno;
		$data['jv']=$userid . "-" . $type . "-" . $billno;


        $data["filter"] = 'add';
        $data["title"] = "Add Voucher";    			
		$this->load->view($this->session->userdata('language')."/voucherreceipt/add",$data);
	}

	public function add_voucher(){
		
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$this->db->trans_start();
			$this->mod_voucher->add_voucher($this->input->post());
			$this->db->trans_complete();
			$this->session->set_flashdata('ok_message', '- Added Successfully!');
			redirect(SURL . 'Vouchers');
		}
	}

	public function edit_voucher($id){
		if($id){

			$table='tblacode';
			$where = array('atype' => 'Child','acode!='=>'2003013001','ac_status ='=>'Active');
			$data['customer_list'] = $this->mod_common->select_array_records($table,"*",$where);

			$table='tbltrans_master';
			$where = "vno='$id'";
			$data['single_edit'] = $this->mod_common->select_single_records($table,$where);

			$data['edit_list'] = $this->mod_voucher->edit_voucher($id);

			$table='tblmaterial_coding';       
        	$data['item_list'] = $this->mod_common->get_all_records($table,"*");

			$data["filter"] = '';
			#----load view----------#
			$data["title"] = "Update Voucher";
			$this->load->view($this->session->userdata('language')."/voucherreceipt/edit",$data);
		}
	}

	public function update_voucher(){
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$this->db->trans_start();
			$update_itn=  $this->mod_voucher->update_voucher($this->input->post());
			$this->db->trans_complete();
            $this->session->set_flashdata('ok_message', '- Updated Successfully!');
            redirect(SURL . '/Vouchers/');
		}
	}



	public function edit($id){

		if($id){
			$table='tblacode';
			$where = array('atype' => 'Child','acode!='=>'2003013001');
			$data['aname'] = $this->mod_common->select_array_records($table,"*",$where);


//pm($data['aname']);
			$tables='tbltrans_detail';
			$wheres = "vno='$id'";
			$data['payemetreceipt'] = $this->mod_common->select_single_records($tables,$wheres);
			//pm($data['payemetreceipt']);
	        $data["filter"] = 'edit';
        	$data["title"] = "Update Payment/Receipt";
			$this->load->view($this->session->userdata('language')."/paymentreceipt/add", $data);
		}
		/* Update Data */
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$this->db->trans_start();
			$update=  $this->mod_voucher->update_transaction($this->input->post());
			$this->db->trans_complete();
			if ($update) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
	            redirect(SURL . 'PaymentReceipt/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Operation Failed.');
	            redirect(SURL . 'PaymentReceipt/');
	        }
	    }
	}

	public function delete($id,$vtype,$date) {


		$this->db->trans_start();
        $table = "tbltrans_master";
        $where = array("vno"=>$id,"vtype"=>$vtype,"created_date"=>$date);
       	$delete = $this->mod_common->delete_record($table, $where);

        $table = "tbltrans_detail";
        $where = array("vno"=>$id,"vtype"=>$vtype,"vdate"=>$date);
        $delete = $this->mod_common->delete_record($table, $where);

        $this->db->trans_complete();

		if ($this->db->trans_status() === TRUE)
		{
		    $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Vouchers/');
		}else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Vouchers/');
        }
    }

}
