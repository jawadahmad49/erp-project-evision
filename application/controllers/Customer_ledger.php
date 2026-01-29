<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_ledger extends CI_Controller {

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
            "mod_cashbookreport","mod_common","mod_customer","mod_salelpg"
        ));
        
    }

	public function index()
	{
		$table='tblacode';
		$where = "general='2001001000'";
		$data['customers'] = $this->mod_common->select_array_records($table,'*',$where);
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $cash_code=$fix_code['cash_code'];
         if($cash_code !=''){ $where_code = " and tblacode.acode='$cash_code'  "; }else{ $where_code =" and tblacode.general='2003013000' "; }
        // $data['result1'] = $this->db->query("select * from tblacode where general='2003013000' and atype='Child'")->result_array();
        $data['result1'] = $this->db->query("select * from tblacode where atype='Child' $where_code")->result_array();
		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Ledger";	
		$this->load->view($this->session->userdata('language')."/customerledger/search",$data);
	}

	public function report()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST'){

			$detailrec=$this->mod_cashbookreport->get_report($this->input->post());
    //pm($detailrec); exit;
			  $userid=$this->session->userdata('id'); 
			  $this->db->query("delete from cash_book_detail_temp where userid='$userid'");
			   foreach ($detailrec as $key => $value) {
			 $array = array(
							"userid"=>$userid,
							"acnumber"=>$value['acnumber'],
							"fromdate"=>$value['fromdate'],
							"todate"=>$value['todate'],
							"openingreceipt"=>$value['openingreceipt'],
							"openingbalance"=>$value['openingbalance'],
							"voucherno"=>$value['voucherno'],
							"voucherdate"=>$value['voucherdate'],
							"accountcode"=>$value['accountcode'],
							"acname"=>$value['acname'],
							"description"=>$value['description'],
							"receipt"=>$value['receipt'],
							"payment"=>$value['payment'],
							"balance"=>$value['balance'],
						   
							
						  );

				$this->mod_common->insert_into_table("cash_book_detail_temp", $array);
}
         $data['report'] = $this->db->query("SELECT * FROM `cash_book_detail_temp` where userid='$userid' order by voucherdate")->result_array();
			//pm($data['report']); exit;
			
			
			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
			if ($data['report']) {
			 	//$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            //redirect(SURL . 'cashbookreport/detail',$data);
	            $data["title"] = "Customer Ledger";
	            $this->load->view($this->session->userdata('language')."/customerledgerreport/single",$data);
	        } else {
	            //$this->session->set_flashdata('err_message', 'No Record Found.');
	            //redirect(SURL . 'cashbookreport/');
	            $data["title"] = "Customer Ledger";
	            $this->load->view($this->session->userdata('language')."/customerledger/single",$data);
	        }
	    }else{
	        //$data["filter"] = 'add';
	        $data["title"] = "Customer Ledger";    			
			$this->load->view($this->session->userdata('language')."/customerledger/single",$data);
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
		$data["title"] = "Customer Ledger";
		$this->load->view($this->session->userdata('language')."/customerledger/single",$data);
		}
	}

	function export()
	{
		if(isset($_POST["Export"])){
		 
      header('Content-Type: text/csv; charset=utf-8');  
      header('Content-Disposition: attachment; filename=data.csv');  
      $output = fopen("php://output", "w");
	  
      fputcsv($output, array('Voucher No', 'Voucher Date', 'Account Code', 'Account Name', 'Description', 'Receipt', 'Payment', 'Balance'));  
      $query = "SELECT * from tbltrans_detail between '$fromdate' and '$todate' ORDER BY testid DESC";  
      $result = mysqli_query($con, $query);  
      while($row = mysqli_fetch_assoc($result))  
      {  
           fputcsv($output, $row);  
      }  
      fclose($output);  
 }
	} 

}
