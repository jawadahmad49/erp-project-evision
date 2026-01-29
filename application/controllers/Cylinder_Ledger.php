<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cylinder_Ledger extends CI_Controller { 

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
            "mod_customerledger","mod_admin","mod_common","mod_customer","mod_customerstockledger","mod_salelpg"
        ));
        
    }
	public function index()
	{
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();

	
		$data["title"] = "Cylinder Ledger";	
		$this->load->view($this->session->userdata('language')."/Cylinder_Ledger/search",$data);
	}

	public function report()
	{
	 
		//pm($this->input->post());
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			//$data['report']=  $this->mod_customerledger->get_report($this->input->post());

			// pm($data['report']);
			$data['from_date']=$from_date=$this->input->post('from_date');
			$data['to_date']=$to_date=$this->input->post('to_date');
			$data['acode']=$acode=$this->input->post('acode');

			$data['opening_balance'] = $this->db->query("select sum(damount-camount) as balance  from tbltrans_detail where  acode='$acode' and vdate<'$from_date'")->row_array()['balance'];
			$data['report'] = $this->db->query("select tbl_issue_goods.*,tbl_issue_goods_detail.* from tbl_issue_goods inner join tbl_issue_goods_detail on tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id where  issuedto='$acode' and issuedate between '$from_date' and '$to_date'")->result_array();
		


			$data['itemname'] = $this->mod_common->select_array_records('tblmaterial_coding',"*","catcode='1' ");
 



			// pm($data['report']);exit;

			//$data['sale']=  $this->mod_customerstockledger->getsale($this->input->post());

 //pm($data['sale']);

			// foreach ($data['sale'] as $key => $value) {
			// 	// echo "string";
			// 	// exit;


			// 	if(count($value['sale']>1))
 		// 		{
			//  		foreach ($value['sale'] as $key => $value_sub) {

			//  			$total_sale[$value_sub['itemid']]=$total_sale[$value_sub['itemid']]+$value_sub['qty'];

			//  		}
			// 	}
			// }
			
			
		
			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
		//	if ($data['report']) {

				//pm($data['report']);
			 	//$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            //redirect(SURL . 'vendorledger/detail',$data);
	            $data["title"] = "Cylinder Ledger Report";
	            $this->load->view($this->session->userdata('language')."/Cylinder_Ledger/single",$data);
	      //  }
	    }
	}




}
