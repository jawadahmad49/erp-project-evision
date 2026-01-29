<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BankReceipt extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_trabank","mod_bank","mod_common","mod_admin","mod_vendorledger","mod_customerledger"
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
		

	    $table='tbltrans_master'; 
		$where = " svtype !='BP' AND (vtype='BP' OR vtype='BR')";
		
		$data['paymentreceipt_list'] = $this->mod_trabank->all_bank_transaction_receipt($where,$from_date,$to_date);
 
		$data["filter"] = ''; 
		#----load view----------#
		$data["title"] = "Manage Bank Receipt";	
		$this->load->view($this->session->userdata('language')."/bankreceipt/manage_paymentreceipt",$data);
	}


	
	
	public function bank_balance($id='',$date)
	{
	 
		if($this->input->server('REQUEST_METHOD') == 'POST' || $id !=''){
			
			$date=	$this->input->post('date');
	//	echo $date;
	
 
			$data['one']=2;
			$data['report']=  $this->mod_vendorledger->get_report($this->input->post(),$id,$date);
			if($id !='')
			{
				$data['one']=1;
			}
 
				foreach ($data['report'] as $key => $value) { 
			 
					 $tbalance=$value['tbalance']; 
						 
					 } 

					$total_opngbl =str_replace(",", "", $tbalance);

					 if($this->input->post('edit_amount'))
					{
					  $total_opngbl=$total_opngbl+$this->input->post('edit_amount'); 
					}

					echo $total_opngbl;

					if(($total_opngbl)>0){echo  ' Dr';}else{ echo ' Cr';}

					echo '|';
					echo $total_opngbl;
					echo '|';


					if(($total_opngbl)>0){echo  'Dr';}else{ echo 'Cr';}

					exit();
				  
		}
	}
	
	
	
	public function detail($id){
 
		if($id){

			$table='tblacode';
			$where = array('atype' => 'Child','acode!='=>'2003013001');
			$data['aname'] = $this->mod_common->select_array_records($table,"*",$where);
 
			$table='tbl_company';       
        	$data['company'] = $this->mod_common->get_all_records($table,"*");

			$table='tbltrans_detail';
			$where = "vno='$id'";
			$data['single_edit'] = $this->mod_common->select_single_records($table,$where);
        	//pm($data['company'] )
			
			$tables='tbltrans_detail';
			$wheres = "vno='$id'";
			$data['paymentreceipt_list'] = $this->mod_trabank->select_trans_print_records($wheres);

			

	        $data["filter"] = 'edit';
        	$data["title"] = "Update Bank Receipt";
			$this->load->view($this->session->userdata('language')."/bankreceipt/single",$data);
		}
		else{
			redirect(SURL.'BankReceipt');
		}
	}
public function detail_small($id){
		if($id){
			//echo $id;
		$table='tblacode';
			$where = array('atype' => 'Child','acode!='=>'2003013001');
			$data['aname'] = $this->mod_common->select_array_records($table,"*",$where);
			
			
			$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
				//pm(	$data['aname']);
		
		
		//pm(	$data);
		$table='tbltrans_detail';
		$data['single_edit'] = $this->db->order_by("testid","DESC")->limit(1)->get_where($table,array("vno"=>$id))->row_array();
			//$where = "vno='$id'";
			//$data['single_edit'] = $this->mod_common->select_single_records($table,$where);
			
		$tables='tbltrans_detail';
			$wheres = "vno='$id'";
			$data['paymentreceipt_list'] = $this->mod_trabank->select_trans_print_records($wheres);	
			//echo '<pre>';print_r($data);
			
			
		//pm(	$data['single_edit']);
		
          $acode= $data['single_edit']['acode'];
		 
		  
           $issuedate= $data['single_edit']['vdate'];
		   $wheres = "vno='$id'";

		$data['edit_list'] = $this->mod_trabank->select_trans_print_records($wheres);
	
		
		//exit;
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Invoice";
		$ftoday='2018-01-01';
	 	 $today=  $issuedate;
		$date_array2 = array('from_date' => $ftoday,'to_date' => $today,'filter' => 'party','acode' => $acode,'id' =>  $id ,'hdate' => '','sort' => 'date','aname_hid' => '');
		  //pm(	$date_array2);
		  $data['final_bal']=  $this->mod_customerledger->get_report_small($date_array2);

   //pm(	$data['final_bal']);
	 
	 		foreach ($data['final_bal'] as $key => $value) {
			$data['report_new'] = $value['tbalance'];
			//$itemids = $value['itemid'];
			//$wherem = "materialcode!='$itemids'";
			//$data['item_lists'] = $this->mod_common->select_array_records($tablem,'*',$wherem);
		}
		
 
		
		$this->load->view($this->session->userdata('language')."/bankreceipt/single_small",$data);
		}
	}	
	
	

	public function add()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$this->db->trans_start();
			$add=  $this->mod_trabank->add_transaction($this->input->post());

			$this->db->trans_complete();
			if ($add) {
			 	$this->session->set_flashdata('ok_message', 'You have successfully added.');
	            redirect(SURL . 'BankReceipt/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'BankReceipt/');
	        }
	    }
	    $table='tblacode';
		$where = array('atype' => 'Child');
 
		$data['aname'] = $this->mod_bank->getnotBanks($where);
		
		$data['bank_list'] = $this->mod_bank->getOnlyBanks();

        $data["filter"] = 'add';
        $data["title"] = "Add Bank Receipt";    			
		$this->load->view($this->session->userdata('language')."/bankreceipt/add",$data);
	}



	public function edit($id){ 
		if($id){
			$table='tblacode'; 
			$where = array('atype' => 'Child'); 
			$data['aname'] = $this->mod_bank->getnotBanks($where);

			$data['bank_list'] = $this->mod_bank->getOnlyBanks();
		 
			$tables='tbltrans_detail';
			$wheres = "vno='$id'";
			$data['payemetreceipt'] = $this->mod_common->select_single_records($tables,$wheres);
			 
	        $data["filter"] = 'edit';
        	$data["title"] = "Update Bank/Receipt";
			$this->load->view($this->session->userdata('language')."/bankreceipt/add", $data);
		}
		/* Update Data */
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$this->db->trans_start();
			$update=  $this->mod_trabank->update_transaction($this->input->post());
			 $this->db->trans_complete();


			if ($update) {
			 	$this->session->set_flashdata('ok_message', 'You have successfully updated.');
	            redirect(SURL . 'BankReceipt/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Operation Failed.');
	            redirect(SURL . 'BankReceipt/');
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
		    $this->session->set_flashdata('ok_message', 'You have successfully deleted.');
            redirect(SURL . 'BankReceipt/');
		}else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'BankReceipt/');
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
