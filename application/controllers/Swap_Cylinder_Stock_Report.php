<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Swap_Cylinder_Stock_Report extends CI_Controller {



	public function __construct() {
        parent::__construct();

        $this->load->model(array(
           "mod_common","mod_salelpg"
        ));
        
    }

	public function index()
	{ 
		// pm($this->input->post());
		$data['itemss'] = $this->db->query("select * from tblmaterial_coding where catcode='7'")->result_array();
		// pm($this->input->post($data['itemss']));
		 $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
        $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

        if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();

		#----load view----------#
		$data["title"] = "Swap Cylinder Stock Report ";	
		$this->load->view($this->session->userdata('language')."/Swap_Cylinder_Stock_Report/search",$data);
	}

	public function report()
	{

		//pm($this->input->post());
		    if($this->input->server('REQUEST_METHOD') == 'POST'){
            $from_date =$data['from_date']=$this->input->post("from_date");
		    $to_date=$data['to_date']=$this->input->post("to_date");
		    $location=$data['location']=$this->input->post("location");
		    $item=$this->input->post("item");
		    if($item!='All'){ $where= "and tblmaterial_coding.materialcode='$item'"; }else{ $where= ""; }
          	$data['report'] = $this->db->query("select * from tblmaterial_coding where catcode='7' $where")->result_array();
            $data["title"]="Swap&nbsp;Cylinder&nbsp;Stock&nbsp;Report";
		 //pm($data['report']);
			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
			if ($data['report']) {
			$this->load->view($this->session->userdata('language')."/Swap_Cylinder_Stock_Report/single",$data);
	        } else {
	        $this->session->set_flashdata('err_message', 'No Record Found.');
	        redirect(SURL . 'Swap_Cylinder_Stock_Report/');
	          
	        }
	    }
	}


}
