<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Voucher_listing extends CI_Controller {



	public function __construct() {
        parent::__construct();

        $this->load->model(array(
           "mod_common","mod_salelpg"
        ));
        
    }

	public function index()
	{
		
		#----load view----------#
		$data['supplier_list'] = $this->db->query("select * from tblacode where  atype='child'")->result_array();
		$data['user_list'] = $this->db->query("select * from tbl_admin where  status='Active' and id!='2'")->result_array();
	
		$data["title"] = "Voucher Listing Report";	
		$this->load->view($this->session->userdata('language')."/Voucher_listing/search",$data);
	}


	public function report()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST'){
        $from_date =$data['from_date']=$this->input->post("from_date");
		 $to_date=$data['to_date']=$this->input->post("to_date");
		  $vtype=$data['vtype']=$this->input->post("vtype");
		   $fvno=$data['fvno']=$this->input->post("fvno");
		    $tvno=$data['tvno']=$this->input->post("tvno");
		    $v2v=$data['filter']=$this->input->post("filter");
		      $userid=$data['userid']=$this->input->post("userid");
		
		
		
       if($v2v=='v2v'){
	      $data['report'] = $this->db->query("SELECT vno,damount,camount,created_date FROM tbltrans_master WHERE   substr(vno,6,6) >=$fvno  and  substr(vno,6,6) <=$tvno  and vtype ='$vtype' and left(vno,1)=$userid ORDER BY created_date ASC")->result_array();
       // echo "SELECT vno,damount,camount,created_date FROM tbltrans_master WHERE   substr(vno,6,6) >='$fvno'  and  substr(vno,6,6) <='$tvno'  and vtype ='$vtype' and left(vno,1)='$userid' ORDER BY created_date ASC";exit;

      }else{
      	  $data['report'] = $this->db->query("SELECT vno,damount,camount,created_date FROM tbltrans_master WHERE created_date BETWEEN '$from_date' AND '$to_date' and vtype='$vtype' ORDER BY created_date ASC")->result_array();
      }
		
		 
		 //pm($data['report']);
			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
       		if ($data['report']) {
			if ($vtype=='JV') {
			 	$data["title"] = "Journal&nbsp;Voucher";
	            $this->load->view($this->session->userdata('language')."/Voucher_listing/single_jv",$data);
	        } else if ($vtype=='JVM') {
			 	$data["title"] = "Journal&nbsp;Voucher&nbsp;Madni";
	            $this->load->view($this->session->userdata('language')."/Voucher_listing/single_jv",$data);
	        }else if ($vtype=='CP') {
			 	$data["title"] ='Cash&nbsp;Payment&nbsp;Voucher';
	            $this->load->view($this->session->userdata('language')."/Voucher_listing/single_cp",$data);
	        }else if ($vtype=='CR') {
			 	$data["title"] ='Cash&nbsp;Receipt&nbsp;Voucher';
	            $this->load->view($this->session->userdata('language')."/Voucher_listing/single_cp",$data);
	        }else if ($vtype=='BR') {
			 	$data["title"] ='Bank&nbsp;Receipt&nbsp;Voucher';
	            $this->load->view($this->session->userdata('language')."/Voucher_listing/single_br",$data);
	        }else if ($vtype=='BP') {
			 	$data["title"] ='Bank&nbsp;Payment&nbsp;Voucher';
	            $this->load->view($this->session->userdata('language')."/Voucher_listing/single_br",$data);
	        }else if ($vtype=='JVD') {
			 	$data["title"] ='Journal&nbsp;Voucher&nbsp;Dollar';
	            $this->load->view($this->session->userdata('language')."/Voucher_listing/single_dollar",$data);
	        }else if ($vtype=='HDP') {
			 	$data["title"] ='Dollar&nbsp;Payments&nbsp;';
	            $this->load->view($this->session->userdata('language')."/Voucher_listing/single_dollar",$data);
	        }else{
	        	
	        	 redirect(SURL . 'Voucher_listing/');
	        }
	    }else{
	    	redirect(SURL.'Voucher_listing');

	    }
	    }
	}


}
