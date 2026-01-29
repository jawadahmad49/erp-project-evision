<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SecurityReceipt extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "Mod_securityreceipt","mod_bank","mod_common","mod_admin","mod_vendorledger","mod_customer","mod_customerstockledger"
        ));
      //  $this->load->library('../controllers/ShopOpeningBalance');
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
		
	$data['paymentreceipt_list'] = $this->Mod_securityreceipt->all_bank_transaction($from_date,$to_date);
 
		$data["filter"] = ''; 
		#----load view----------#
		$data["title"] = "Manage Security Receipt";	
		$this->load->view($this->session->userdata('language')."/securityreceipts/manage_paymentreceipt",$data);
	}


	
	public function details()
	{
	
	  	$to_date=	$this->input->post('to_date');
	
 
 if($to_date==''){$to_date=date('Y-m-d');}
		$data['total_balance']=  $this->mod_admin->getcurrent_security_cylinder('All','All',$to_date,'Market');
 //pm($data['total_balance']);exit;
		$table='tbl_company';       
       	$data['company'] = $this->mod_common->get_all_records($table,"*");

		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->select_array_records($table,"*","catcode='1' ");
		
		//pm($data['items']);
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Sale";	
			
			//pm($data);

		$this->load->view($this->session->userdata('language')."/securityreceipts/detail",$data);

	}
	public function bank_balance($id='')
	{
	 
		if($this->input->server('REQUEST_METHOD') == 'POST' || $id !=''){
 
			$data['one']=2;
			$data['report']=  $this->mod_vendorledger->get_report($this->input->post(),$id);
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

		 
			$table='tbl_company';       
        	$data['company'] = $this->mod_common->get_all_records($table,"*");
 
	 
			$wheres =  $id;
			$data['paymentreceipt_list'] = $this->Mod_securityreceipt->select_trans_print_records($wheres);

			

	        $data["filter"] = 'edit';
        	$data["title"] = "Update Bank/Receipt";
			$this->load->view($this->session->userdata('language')."/securityreceipts/single",$data);
		}
		else{
			redirect(SURL.'SecurityReceipt');
		}
	}

	public function add()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			



			$sale_date=$this->input->post('date');
			$date_array = array('post_date>=' => $sale_date);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
			$this->session->set_flashdata('err_message', 'Already closed for this date.');
			redirect(SURL . 'SecurityReceipt/add');
			}

			
			
			$this->db->trans_start();
			
			$add=  $this->Mod_securityreceipt->add_transaction($this->input->post());
	 

			$this->db->trans_complete();
			if ($add) {
			 	$this->session->set_flashdata('ok_message', 'You have successfully added.');
	            redirect(SURL . 'SecurityReceipt/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'SecurityReceipt/');
	        }
	    }
	    $table='tblacode';
		$where = array('atype' => 'Child');
 
		$data['aname'] = $this->mod_customer->getOnlyCustomers();
		
		
        $where_cat_id = array('catcode' => 1);

        $data['item_list']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_cat_id);
		
		$data['bank_list'] = $this->mod_bank->getOnlyBanks();

        $data["filter"] = 'add';
        $data["title"] = "Add Security Receipt";    			
		$this->load->view($this->session->userdata('language')."/securityreceipts/add",$data);
	}



	public function edit($id){ 
		if($id){
			$table='tblacode'; 
			$where = array('atype' => 'Child'); 
					$data['aname'] = $this->mod_customer->getOnlyCustomers();

			$data['bank_list'] = $this->mod_bank->getOnlyBanks();
		 
			$where_cat_id = array('catcode' => 1);
			$data['item_list']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_cat_id);
		
		
		
		
			$tables='tbl_security_receipt';
			$wheres = "trans_id='$id'";
			$data['payemetreceipt'] = $this->mod_common->select_single_records($tables,$wheres);
			 //pm($data['payemetreceipt']);
			 
			 $qty_issued=$data['payemetreceipt']['qty'];
			 $item_id=$data['payemetreceipt']['itemid'];
			 $customer=$data['payemetreceipt']['customercode'];
			
		 	 /////////////////////////////////////STOCK LOGIC /////////////////////////////////////////
			/////////////////////////////////////STOCK LOGIC /////////////////////////////////////////
			
			 

		$table='tblmaterial_coding';

		$where = array('materialcode' => $item_id);
		$data['cat_code'] = $this->mod_common->select_array_records($table,"catcode",$where);
		//echo json_encode($data['cat_code']);
		//echo "|";
		$today=date('Y-m-d');
		$date_array2 = array('from_date' => '2018-01-01','to_date' => $today, 'acode' => $customer);
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
						if($item_id==$value_sub['itemid']){ $total_return_value+=$value_sub['qty']; }
			 		}
				}
			}
		$total_sale_value=0;
			foreach ($data['sale'] as $key => $value) {
				if(count($value['sale']>1))
 				{
			 		foreach ($value['sale'] as $key => $value_sub) {
			 			$total_sale[$value_sub['itemid']]=$total_sale[$value_sub['itemid']]+$value_sub['qty'];
						if($item_id==$value_sub['itemid']){ $total_sale_value+=$value_sub['qty']; }
			 		}
				}
			}
 		for ($i=0; $i <count($data['opening']); $i++) { 
				$item_code=$data['opening'][$i]['itemid'];
				$opening_array[$item_code]=$data['opening'][$i]['opening'];
				if($item_id==$item_code){ $total_open_value+=$data['opening'][$i]['opening']; }

			}
		  $data['stock']= $total_sale_value-$total_return_value+$total_open_value+$qty_issued;
	 
			
		
		
	 
			/////////////////////////////////////STOCK LOGIC /////////////////////////////////////////
			/////////////////////////////////////STOCK LOGIC /////////////////////////////////////////
			/////////////////////////////////////STOCK LOGIC /////////////////////////////////////////
			
	        $data["filter"] = 'edit';
        	$data["title"] = "Update Security Receipt";
			$this->load->view($this->session->userdata('language')."/securityreceipts/add", $data);
		}
		
		
		
		
		/* Update Data */
		if($this->input->server('REQUEST_METHOD') == 'POST'){

			$id=$this->input->post('id');
			$sale_date=$this->input->post('date');
			$date_array = array('post_date>=' => $sale_date);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
			$this->session->set_flashdata('err_message', 'Already closed for this date.');
			redirect(SURL . 'SecurityReceipt/edit/'.$id);
			}
			$this->db->trans_start();
			
		//	pm($this->input->post());
			$update=  $this->Mod_securityreceipt->update_transaction($this->input->post());
			 $this->db->trans_complete();


			if ($update) {
			 	$this->session->set_flashdata('ok_message', 'You have successfully updated.');
	            redirect(SURL . 'SecurityReceipt/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Operation Failed.');
	            redirect(SURL . 'SecurityReceipt/');
	        }
	    }
	}

	public function delete($id) {

$vno=$id.'-SecurityReceipt';



	 
		 
			$date_array = array('trans_id=' => $id);
		 	$sale_date =  $this->mod_common->select_single_records('tbl_security_receipt',$date_array);
			 
		  	$dt=$sale_date['dt'];
		 
			$date_array = array('post_date>=' => $dt);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
			$this->session->set_flashdata('err_message', 'Already closed for this date.');
			redirect(SURL . 'SecurityReceipt');
			}
			
		$this->db->trans_start();
		
		
        $table = "tbl_security_receipt";
        $where = array("trans_id"=>$id);
       	$delete = $this->mod_common->delete_record($table, $where);

        $table = "tbltrans_master";
        $where = array("vno"=>$vno);
       	$delete = $this->mod_common->delete_record($table, $where);

        $table = "tbltrans_detail";
        $where = array("vno"=>$vno);
        $delete = $this->mod_common->delete_record($table, $where);

        $this->db->trans_complete();

		if ($this->db->trans_status() === TRUE)
		{
		    $this->session->set_flashdata('ok_message', 'You have successfully deleted.');
            redirect(SURL . 'SecurityReceipt/');
		}else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'SecurityReceipt/');
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
