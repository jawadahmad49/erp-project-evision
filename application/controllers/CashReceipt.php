<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CashReceipt extends CI_Controller {
 
	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_transaction","mod_common","mod_admin","mod_customerledger"
        ));
        
    }

	public function index()
	{
		if(isset($_POST['submit'])){			
			$from_date = date("Y-m-d", strtotime($_POST['from']));
			
			$to_date = date("Y-m-d", strtotime($_POST['to']));
			
		}else{
			$from_date = date('Y-m-d');
			$to_date = date('Y-m-d');
		}
		

 

		$sum = " SUM(d.camount) as camount";
		$where = "d.vtype='CR' AND d.svtype!='CP'";
		$data['paymentreceipt_list_rec'] = $this->mod_transaction->get_all_transaction($table,"*",$where,$from_date,$to_date,$sum);
		

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Receipt";	
		$this->load->view($this->session->userdata('language')."/cashreceipt/manage_paymentreceipt",$data);
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
			$data['paymentreceipt_list'] = $this->mod_transaction->select_trans_print_records($wheres);

			//pm($data['paymentreceipt_list']);

	        $data["filter"] = 'edit';
        	$data["title"] = "Update Receipt";
			$this->load->view($this->session->userdata('language')."/cashreceipt/single",$data);
		}
		else{
			redirect(SURL.'CashReceipt');
		}
	}
	public function detail_small($id){
		if($id){ 
		
				//pm(	$data['aname']);
		$table='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		
		//pm(	$data);
		$table='tbltrans_detail';
		//$where = "vno='$id' and ORDER BY testid DESC";
		$data['single_edit'] = $this->db->order_by("testid","DESC")->limit(1)->get_where($table,array("vno"=>$id))->row_array();
		 
			//$data['single_edit'] = $this->mod_common->select_single_records($table,$where);
			
			$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
			
		 //pm(	$data['single_edit']);
          $acode= $data['single_edit']['acode'];
           $issuedate= $data['single_edit']['vdate'];
           $camount= $data['single_edit']['camount'];
		   $wheres = "vno='$id'";

		$data['edit_list'] = $this->mod_transaction->select_trans_print_records($wheres);
//	echo '<pre>';print_r($data['edit_list']);
		
		//exit; 
		 
		
		$table='tblacode';
		$where = array( 'acode'=>$acode);
		$data['aname'] = $this->mod_common->select_array_records($table,"*",$where);


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
			//  pm(	$data['report_new']);
			//$itemids = $value['itemid'];
			//$wherem = "materialcode!='$itemids'";
			//$data['item_lists'] = $this->mod_common->select_array_records($tablem,'*',$wherem);
		}
		
 
		
		$this->load->view($this->session->userdata('language')."/cashreceipt/single_small",$data);
		}
	}
	
	
	public function add()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			
			$this->db->trans_start();
			$add=  $this->mod_transaction->add_transaction($this->input->post());
			 $this->db->trans_complete();

			if ($add) {
			 	$this->session->set_flashdata('ok_message', 'You have successfully added.');
	            redirect(SURL . 'CashReceipt/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'CashReceipt/');
	        }
	    }
	    $table='tblacode';
		$where = array('atype' => 'Child','acode!='=>'2003013001','ac_status ='=>'Active');
		$data['aname'] = $this->mod_common->select_array_records($table,"*",$where);
		//q();


        $data["filter"] = 'add';
        $data["title"] = "Add Receipt";    			
		$this->load->view($this->session->userdata('language')."/cashreceipt/add",$data);
	}

	function add_daily_paymentreceipt(){
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$this->db->trans_start();
			$add=  $this->mod_transaction->add_single_transaction($this->input->post());
			$this->db->trans_complete();

			echo $add;
		}
	}


	public function add_singleday_paymentreceipt()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$this->db->trans_start();
			$add=  $this->mod_transaction->add_transaction($this->input->post());
			 $this->db->trans_complete();
			if ($add) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            redirect(SURL . 'CashReceipt/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'CashReceipt/');
	        }
	    }
	    $table='tblacode';
		$where = array('atype' => 'Child','acode!='=>'2003013001','ac_status ='=>'Active');
		$data['aname'] = $this->mod_common->select_array_records($table,"*",$where);
		//q();


        $data["filter"] = 'add';
        $data["title"] = "Add Single Day Receipt";    			
		$this->load->view($this->session->userdata('language')."/cashreceipt/add_single_day",$data);
	}



	public function edit($id){

		if($id){
			$table='tblacode';
			$where = array('atype' => 'Child','acode!='=>'2003013001');
			$data['aname'] = $this->mod_common->select_array_records($table,"*",$where);

 
			$tables='tbltrans_detail';
			$wheres = "vno='$id'";
			$data['payemetreceipt'] = $this->mod_common->select_single_records($tables,$wheres);
			//pm($data['payemetreceipt']);
	        $data["filter"] = 'edit';
        	$data["title"] = "Update Receipt";
			$this->load->view($this->session->userdata('language')."/cashreceipt/add", $data);
		}
		/* Update Data */
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			
			$this->db->trans_start();	
			$update=  $this->mod_transaction->update_transaction($this->input->post());
			$this->db->trans_complete();

			if ($update) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
	            redirect(SURL . 'CashReceipt/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Operation Failed.');
	            redirect(SURL . 'CashReceipt/');
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
            redirect(SURL . 'CashReceipt/');
		}else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'CashReceipt/');
        }
    }


    public function delete_row() {

    	$id = $_POST['vno'];
    	
		$this->db->trans_start();
        $table = "tbltrans_master";
        $where = array("vno"=>$id);
       	$delete = $this->mod_common->delete_record($table, $where);

        $table = "tbltrans_detail";
        $where = array("vno"=>$id);
        $delete = $this->mod_common->delete_record($table, $where);
		$this->db->trans_complete();

		if ($this->db->trans_status() === TRUE)
		{
		    echo "Successfully deleted.";
		}else {
            echo "Sorry there is something wrong!";
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

	function get_cashhand()
	{
		$data['cash_position'] = $this->mod_admin->cash_position();

		  foreach ($data['cash_position'] as $key=>$datas) {
														
			$opening= $datas[opngbl]; 
			if($datas[optype]=='Credit'){ $opening=-1*$opening; } 
			
			$bal=$datas[damount]-$datas[camount]+$opening;
			echo $bal;
			echo "|";
			echo $bal;
			
			exit();
																
		}
	}	

	function get_accbal($acode='')
	{
		 
		$data['acc_bal'] = $this->mod_admin->get_account_balance($acode);

		  foreach ($data['acc_bal'] as $key=>$datas) {
														
			$opening= $datas[opngbl]; 
			if($datas[optype]=='Credit'){ $opening=-1*$opening; } 
			
			$bal=$datas[damount]-$datas[camount]+$opening;
			echo $bal;
				if(($bal)>0){echo  ' Dr';}else{ echo ' Cr';}
			echo "|";
			echo $bal; 	if(($bal)>0){echo  ' Dr';}else{ echo ' Cr';}
			
			exit();
																
		}
	}	

	function getVendors(){
		$table='tblacode';
		$where = array('atype' => 'Child','acode!='=>'2003013001','ac_status ='=>'Active');
		$c = $this->mod_common->select_array_records($table,"*",$where);
		?>
		<option value="">Choose a Vendor....</option>
		<?php
		foreach ($c as $key => $value) {
			?>
			<option value="<?php echo  $value['acode']; ?>"><?php echo  $value['aname']; ?></option>
			<?php
		}
	}
	

}
