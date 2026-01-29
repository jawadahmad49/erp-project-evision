<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Safety_instruction extends CI_Controller
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
        $data['record'] = $this->db->query("SELECT * FROM tbl_safety_instruction")->row_array();
    
        $data["filter"] = '';
        #----load view----------#
        $data["title"] = "Safety Instruction";
        $this->load->view("app/safety_instruction/terms", $data);
    }
    public function add()
    {
        
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $array['safety_instruction'] =  $this->input->post("safety_instruction");
            $array['description']  = $this->input->post("description");
            $array['created_by'] =  $this->session->userdata('id');
            $array['created_date'] = date('Y-m-d');

            if(empty($this->input->post("id"))){
                $add = $this->mod_common->insert_into_table("tbl_safety_instruction",$array);
            }else{
                 //pm($this->input->post());
                $id = $this->input->post("id");
                $add=$this->mod_common->update_table("tbl_safety_instruction",array("id"=>$id), $array);
            }
        }
        $data["title"] = "Safety Instruction";
        redirect(SURL . 'app/Safety_instruction/');
    }
}
