<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Privacy_policy extends CI_Controller
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
        $data['record'] = $this->db->query("SELECT * FROM tbl_privacy_policy")->row_array();

        $data["filter"] = '';
        #----load view----------#
        $data["title"] = "Privacy Policy";
        $this->load->view("app/Privacy_policy/manage", $data);
    }
    public function view()
    {
        $data['description'] = $this->db->query("SELECT description FROM tbl_privacy_policy")->row_array()['description'];
       
        $data["title"] = "Privacy Policy";
        $this->load->view("app/Privacy_policy/view", $data);
    }
    public function add()
    {

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $array['description']  = $this->input->post("description");
            $array['created_by'] =  $this->session->userdata('id');
            $array['created_date'] = date('Y-m-d');

            if (empty($this->input->post("id"))) {
                $add = $this->mod_common->insert_into_table("tbl_privacy_policy", $array);
            } else {
                $id = $this->input->post("id");
                $add = $this->mod_common->update_table("tbl_privacy_policy", array("id" => $id), $array);
            }
        }
        redirect(SURL . 'app/Privacy_policy/');
    }
}
