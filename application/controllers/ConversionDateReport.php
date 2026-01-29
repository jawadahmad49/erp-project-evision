<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ConversionDateReport extends CI_Controller {

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
            "mod_cylinderconversion","mod_common"
        ));
        
    }

	public function index()
	{

		$data["filter"] = '';
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
        $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

        if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();
		#----load view----------#
		$data["title"] = "Conversion B/W Date Report";	
		$this->load->view($this->session->userdata('language')."/conversiondatereport/search",$data);

	}

	public function details()
	{
		

		$from_date=$this->input->post('from_date');
		$to_date=$this->input->post('to_date');
		$location=$this->input->post('location');


		$wheredr = "`trans_date` BETWEEN '$from_date' AND '$to_date' AND type = 'from' AND tbl_cylinderconversion_master.sale_point_id='$location'";
		$data['list_from'] = $this->mod_cylinderconversion->select_from_records($wheredr);
		//pm($data['list_from']);
		$data['from_date'] =$from_date;
		$data['to_date'] =$to_date;
		$data['location'] =$location;
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
		
		foreach ($data['list_from'] as $key => $value) {

				$id=$value['trans_id'];

				$wheredr = "tbl_cylinderconversion_detail.trans_id = '".$id."' AND type = 'to' AND tbl_cylinderconversion_detail.sale_point_id='$location'";
				$data['list_from'][$key]['to_list'] = $this->mod_cylinderconversion->select_to_records($wheredr);
		}
		
			//pm($data);

			$data["title"] = "Conversion B/W Date Report";	

			if ($data['list_from']) {
			 	//$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            //redirect(SURL . 'saledatereport/detail',$data);
	            $this->load->view($this->session->userdata('language')."/conversiondatereport/detail",$data);
	        } else {
	            $this->session->set_flashdata('err_message', 'No Record Found.');
	            redirect(SURL . 'ConversionDateReport/');
	        }
	}

}
