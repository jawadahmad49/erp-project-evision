<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_listing extends CI_Controller {



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
	
		$data["title"] = "Transaction Listing Report";	
		$this->load->view($this->session->userdata('language')."/Transaction_listing/search",$data);
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
		   // echo $v2v;exit;
		   
       if($v2v=='v2v'){
	     $data['report'] = $this->db->query("SELECT vno,damount,camount,created_date FROM tbltrans_master WHERE   substr(vno,6,6) >=$fvno  and  substr(vno,6,6) <=$tvno  and vtype ='$vtype' and left(vno,1)=$userid ORDER BY created_date ASC")->result_array();
	     

      }else{
      	  $data['report'] = $this->db->query("SELECT vno,damount,camount,created_date FROM tbltrans_master WHERE created_date BETWEEN '$from_date' AND '$to_date'  and vtype ='$vtype' ORDER BY  created_date asc")->result_array();
      }
		
		 
		 //pm($data['report']);
			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
			if ($data['report']) {
			 	$data["title"] = "Transaction&nbsp;Listing&nbsp;Report";
	            $this->load->view($this->session->userdata('language')."/Transaction_listing/single",$data);
	        } else {
	            $this->session->set_flashdata('err_message', 'No Record Found.');
	            redirect(SURL . 'Transaction_listing/');
	          
	        }
	    }
	}


}
