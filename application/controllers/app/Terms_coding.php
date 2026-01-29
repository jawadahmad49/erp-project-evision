<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Terms_coding extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array(
            "mod_common",
        ));
       
    }

    public function index()
    {
        $today = date('Y-m-d');

        // $data['item'] = $this->db->query("SELECT * FROM `tbl_feedback` where f_date ='$today'")->result_array();
        $data['record'] = $this->db->query("SELECT * FROM tbl_terms_condition")->row_array();
    
        $data["filter"] = '';
        #----load view----------#
        $data["title"] = "Terms and conditions";
        $this->load->view("app/Terms/terms", $data);
    }
    public function add()
    {
        
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $array['terms_condition'] =  $this->input->post("terms_condition");
            $array['description']  = $this->input->post("description");
            $array['created_by'] =  $this->session->userdata('id');
            $array['created_date'] = date('Y-m-d');

            if(empty($this->input->post("id"))){
                $add = $this->mod_common->insert_into_table("tbl_terms_condition",$array);
            }else{
                 //pm($this->input->post());
                $id = $this->input->post("id");
                $add=$this->mod_common->update_table("tbl_terms_condition",array("id"=>$id), $array);
            }
        }
        $data["title"] = "Terms and Conditions";
        redirect(SURL . 'app/Terms_coding/');
    }
}
