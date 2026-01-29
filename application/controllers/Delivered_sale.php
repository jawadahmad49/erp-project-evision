<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delivered_sale extends CI_Controller {



	public function __construct() {
        parent::__construct();
		

        $this->load->model(array(
            "mod_salereport","mod_common","mod_customerstockledger","mod_customer","mod_salelpg"
        ));
        
    } 

	public function index()
	{

		//$data['deal'] = $this->db->query("select * from deal")->result_array();
		//pm($data['deal']);
		#----load view----------#
		$data["title"] = "Delivered sale journal Enteries";	
		$this->load->view($this->session->userdata('language')."/Delivered_sale_report/delivered_sale.php",$data);

	}
	

	public function item_report_detail($vno)
	{
		
	$login_user=$this->session->userdata('id');
									
		//pm($this->input->post());
		//error_reporting(E_All);
     if($vno==''){
		$vno = $this->input->post("voucher_no");
		//$vtype= $this->input->post("voucher_type");
		$vno=$vno;
		//echo $vno;
	 }else{
		 $vno=$vno;
		 //$vtypee=(explode("-",$vno));
		// $vtype=$vtypee[1];
	 }
		//  echo $vno;
		
         $data['vno'] =$vno;
		$data['record'] = $this->db->query("select tbltrans_detail.*,tblacode.aname from tbltrans_detail inner join tblacode on tblacode.acode=tbltrans_detail.acode where vno='$vno'  order by tbltrans_detail.testid asc")->result_array();
	$table='tbl_company';       
        	$data['company'] = $this->mod_common->get_all_records($table,"*");
		$this->load->view($this->session->userdata('language')."/Delivered_sale_report/delivered_sale_report",$data);
		
	}
	

}

