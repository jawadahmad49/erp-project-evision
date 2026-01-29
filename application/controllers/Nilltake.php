<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nilltake extends CI_Controller {


	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_customerstockledger","mod_common","mod_admin","mod_customer","mod_customerledger","mod_salelpg"
        ));
    }

	public function index()
	{
		/*$table='tblacode';
		$where = "general='2001001000'";
		$data['customers'] = $this->mod_common->select_array_records($table,'*',$where);*/

		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();

		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Stock Ledger";	
		$this->load->view($this->session->userdata('language')."/Nilltake/search",$data);
	}

	
	public function openBalance_expenses()
	{      if($this->input->server('REQUEST_METHOD') == 'POST'){
		

	      $data['report']=  $this->input->post();
		$data['total_balance']=  $this->mod_customerstockledger->get_total_balance_expenses($this->input->post());
		//pm($data);
		//die;

		 
		$table='tbl_company';       
       	$data['company'] = $this->mod_common->get_all_records($table,"*");

		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Expenses Balance";	
		$this->load->view($this->session->userdata('language')."/Nilltake/net_balance_expenses",$data);
		 }else{
	         
 	   $date_array = array('from_date' =>  date('Y-m-d') , 'to_date' => date('Y-m-d') );
	   $data['report']=  $date_array;
	   $data['total_balance']=  $this->mod_customerstockledger->get_total_balance_expenses($date_array);

		 
		$table='tbl_company';       
       	$data['company'] = $this->mod_common->get_all_records($table,"*");

		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Expenses Balance";	
		$this->load->view($this->session->userdata('language')."/Nilltake/net_balance_expenses",$data);
		 }

	}
	
	public function customerSale()
	{
	
	 
		$data['total_balance']=  $this->mod_customerstockledger->get_total_customer_stock();

		  
		$table='tbl_company';       
       	$data['company'] = $this->mod_common->get_all_records($table,"*");

		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->select_array_records($table,"*","catcode='1' ");
		
		//pm($data['items']);
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Sale";	
			
			//pm($data);

		$this->load->view($this->session->userdata('language')."/Nilltake/customer_sale",$data);
		

	}

	public function openBalance()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST'){
		

	 $data['report']=  $this->input->post();
		$data['total_balance']=  $this->mod_customerstockledger->get_total_balancenew($this->input->post());

		//pm($data['total_balance']);
		
		$table='tbl_company';       
       	$data['company'] = $this->mod_common->get_all_records($table,"*");

		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Receivable";	
		$this->load->view($this->session->userdata('language')."/Nilltake/net_balance",$data);

	  }else{
	       
		    
		   
		  
 	$date_array = array('from_date' =>  date('Y-m-d') , 'to_date' => date('Y-m-d') );
	$data['report']=  $date_array;
	// pm($data);
	// die;
	 
		$data['total_balance']=  $this->mod_customerstockledger->get_total_balancenew($date_array);

		//pm($data['total_balance']);
		
		$table='tbl_company';       
       	$data['company'] = $this->mod_common->get_all_records($table,"*");

		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Receivable";	
		$this->load->view($this->session->userdata('language')."/Nilltake/net_balance",$data);

	}
	}
	
		public function openBalance_pay()
	{
		
		if($this->input->server('REQUEST_METHOD') == 'POST'){
		

	 $data['report']=  $this->input->post();
	
  
  
 	$data['total_balance']=  $this->mod_customerstockledger->get_total_balance_pay($this->input->post());
	
		$table='tbl_company';       
       	$data['company'] = $this->mod_common->get_all_records($table,"*");

		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		
			 	
	           $data["title"] = "Payables";	
		$this->load->view($this->session->userdata('language')."/Nilltake/net_balance_pay",$data);
	         
		
	    }else{
	       
		    
		   
		  
 	$date_array = array('from_date' =>  date('Y-m-d') , 'to_date' => date('Y-m-d') );
	$data['report']=  $date_array;
	// pm($data);
	// die;
	 
		$data['total_balance']=  $this->mod_customerstockledger->get_total_balance_pay($date_array);

		 
		$table='tbl_company';       
       	$data['company'] = $this->mod_common->get_all_records($table,"*");

		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Payables";	
		$this->load->view($this->session->userdata('language')."/Nilltake/net_balance_pay",$data);

	}
	}
	public function report()
	{
		// echo "string";
		// exit();


		// pm($this->input->post()); exit();



		if($this->input->server('REQUEST_METHOD') == 'POST'){

			$data['customer_ledger_report']=  $this->mod_customerledger->get_report($this->input->post());

			
 			//echo "<pre>"; var_dump($data['customer_ledger_report']);

			$total_debit_view=0;
			$total_credit_view=0;
			$total_balance_view=0;
			$total_record= count($data['customer_ledger_report'])-1;
			 
			foreach ($data['customer_ledger_report'] as $key => $value) { 

					$total_debit_view+=$value['debit'];
					$total_credit_view+=$value['credit'];
					$total_balance_view+=$value['balance'];
			}
			
			

			$data['total_debit_view'] = $total_debit_view;
			$data['total_credit_view'] = $total_credit_view;
			$data['total_balance_view'] =  $data['customer_ledger_report'][$total_record]['tbalance'];

			$data['acode'] = $this->input->post('acode');
			
			$data['daterange'] = trim($this->input->post('from_date').'/'.$this->input->post('to_date'));

			$data['name'] = $this->input->post('name');

			$table='tblacode';
			$where = "acode='".$data['acode']."'";
			$data['name'] = $this->mod_common->select_single_records($table,$where);


			 
			$data['report']=  $this->mod_customerstockledger->get_opening($this->input->post(),1);

  
 // pm($data['report']);
			$data['sale']=  $this->mod_customerstockledger->getsale($this->input->post());
 	
	  
			$data['return']=  $this->mod_customerstockledger->getreturn($this->input->post());

			if($data['sale'] && $data['return']){
				$data['salereturn']=array_merge($data['sale'],$data['return']);
			}elseif($data['sale']){
				$data['salereturn']=$data['sale'];
			}else{
				$data['salereturn']=$data['return'];
			}

			
			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");

       		$tables='tblmaterial_coding';       

      		$where_cat_id = array('catcode' => 1);

        	$data['itemname']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_cat_id);

//pm($data['itemname']);
       		$data['itemname_return'] = $this->mod_common->get_all_records($tables,"*");
 //print_r($data['itemname_return'] );

			if ($data['opening']) {
			 	//$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            //redirect(SURL . 'vendorledger/detail',$data);
	            $data["title"] = "Customer Stock Ledger Report";
	            $this->load->view($this->session->userdata('language')."/Nilltake/single",$data);
	        } else {
	            //$this->session->set_flashdata('err_message', 'No Record Found.');
	            //redirect(SURL . 'Customerstockledger/');
             	$data["title"] = "Customer Stock Ledger Report";
	            $this->load->view($this->session->userdata('language')."/Nilltake/single",$data);
	        }
	    }else{
	        //$data["filter"] = 'add';
	        $data["title"] = "Customer Stock Ledger Report";    			
			$this->load->view($this->session->userdata('language')."/Nilltake/single",$data);
		}
	}

	public function detail($id){
		if($id){
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		$table='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		$table='tbl_issue_goods';
		$where = "issuenos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);

		$data['edit_list'] = $this->mod_salelpg->edit_salelpg($id);
		//echo '<pre>';print_r($data['edit_list']);exit;
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Return Report Detail";
		$this->load->view($this->session->userdata('language')."/Nilltake/single",$data);
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
			$this->load->view($this->session->userdata('language')."/Nilltake/add", $data);
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
	            redirect(SURL . 'Nilltake/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Operation Failed.');
	            redirect(SURL . 'Nilltake/');
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
            redirect(SURL . 'Nilltake/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Nilltake/');
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
