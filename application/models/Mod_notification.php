<?php

class Mod_notification extends CI_Model
{

    function __construct()
    {

        parent::__construct();
        error_reporting(0);
    }
    public function add_notification($data)
    {

        $filename = "";

        if ($_FILES['company_image']['name'] != "") {

            //$projects_folder_path = './assets/images/notification/'; 
            // $projects_folder_path = './lpgapp/images/';
            $projects_folder_path = './assets/images/notification/';
            $projects_folder_path1 = 'assets/images/notification/';


            $orignal_file_name = $_FILES['company_image']['name'];

            $file_ext = ltrim(strtolower(strrchr($_FILES['company_image']['name'], '.')), '.');

            $rand_num = rand(1, 1000);

            $config['upload_path'] = $projects_folder_path;
            $config['allowed_types'] = 'jpg|jpeg|gif|tiff|tif|png';
            $config['overwrite'] = false;
            $config['encrypt_name'] = TRUE;
            //$config['file_name'] = $file_name;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('company_image')) {

                $error_file_arr = array('error' => $this->upload->display_errors());
                //print_r($error_file_arr); exit;
                //return $error_file_arr;
            } else {

                $data_image_upload = array('upload_image_data' => $this->upload->data());
                $filename = $projects_folder_path1 . $data_image_upload['upload_image_data']['file_name'];
                $full_path =   $data_image_upload['upload_image_data']['full_path'];
            }
        }

        //$imp_days = implode(",",$data['opening_days']);
        //var_dump($data);



        $in_array_master = array(
            "promo_code" => $data['pcode'],
            "title" => $data['title'],
            "start_date" => $data['start_date'],
            "expiry_date" => $data['end_date'],
            "sts" => $data['status'],
            "details" => $data['remarks'],
            "short_detail" => $data['short'],
            "logo" => $filename,
            "created_dt" => date('Y-m-d'),
            "created_by" => $this->session->userdata('id')
            // "start_time"=>$data['start_time'],
            //"end_time"=>$data['end_time'],

        );

        $table = "tbl_notifications";
        $add = $this->mod_common->insert_into_table($table, $in_array_master);

        if ($add) {
            return $add;
        } else {
            return false;
        }
    }



    public function update_notification($data)
    {
        $id = $data['id'];
        $filename = $data['old_image'];

        if ($_FILES['company_image']['name'] != "") {

            $projects_folder_path = './assets/images/notification/';

            $orignal_file_name = $_FILES['company_image']['name'];

            $file_ext = ltrim(strtolower(strrchr($_FILES['company_image']['name'], '.')), '.');

            $rand_num = rand(1, 1000);

            $config['upload_path'] = $projects_folder_path;
            $config['allowed_types'] = 'jpg|jpeg|gif|tiff|tif|png';
            $config['overwrite'] = false;
            $config['encrypt_name'] = TRUE;
            //$config['file_name'] = $file_name;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('company_image')) {

                $error_file_arr = array('error' => $this->upload->display_errors());
                //print_r($error_file_arr); exit;
                //return $error_file_arr;
            } else {

                $data_image_upload = array('upload_image_data' => $this->upload->data());
                $filename = $data_image_upload['upload_image_data']['file_name'];
                $full_path =   $data_image_upload['upload_image_data']['full_path'];
            }
        }
        //$imp_days = implode(",",$data['opening_days']);


        #----------- update record trans master---------------#
        $in_array_master = array(
            "promo_code" => $data['pcode'],
            "title" => $data['title'],
            "start_date" => $data['start_date'],
            "expiry_date" => $data['end_date'],
            "sts" => $data['status'],
            "details" => $data['remarks'],
            "short_detail" => $data['short'],
            "logo" => $filename,
            "modify_dt" => date('Y-m-d'),
            "modify_by" => $this->session->userdata('id'),
            // "start_time"=>$data['start_time'],
            // "end_time"=>$data['end_time'],

        );

        $table = "tbl_notifications";
        $where = "transid='$id'";
        $update = $this->mod_common->update_table($table, $where, $in_array_master);

        if ($update) {
            return $id;
        } else {
            return $id;
        }
    }








    public function get_by_title($title)
    {
        $query = $this->db->select('*')
            ->from('tbl_promo_code')
            ->where('promo_code', $title)
            ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        //return $query->result_array();
        return $query->num_rows();
    }

    public function manage_notification()
    {
        $this->db->select('*');
        $this->db->from('tbl_notifications');
        //$this->db->join('tbl_promo_code', 'tbl_notifications.promo_code = tbl_promo_code.transid');
        //$this->db->order_by("transid", "desc");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function edit_by_title($title, $id)
    {
        $query = $this->db->select('*')
            ->from('tbl_promo_code')
            ->where('promo_code', $title)
            ->where('transid!=', $id)
            ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }

    public function under_area($promo)
    {
        $query = $this->db->select('*')
            ->from('tbl_orderbooking')
            ->where('promo_code=', $promo)
            ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }
}
