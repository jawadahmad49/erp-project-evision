<?php

class Mod_company extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }

    
    public function add_company($data){

        $filename = "";

        if ($_FILES['company_image']['name'] != "") {

             $projects_folder_path = './assets/images/company/';

             // $projects_folder_path = 'http://192.168.10.92:8012/logo/';



            $orignal_file_name = $_FILES['company_image']['name'];

            $file_ext = ltrim(strtolower(strrchr($_FILES['company_image']['name'], '.')), '.');

            $rand_num = rand(1, 1000);

            $config['upload_path'] = $projects_folder_path;
            $config['allowed_types'] = 'jpg|jpeg|gif|tiff|tif|png';
            $config['overwrite'] = false;
            $config['encrypt_name'] = TRUE;
            $config['file_name'] = $file_name;

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
		
		$imp_days = implode(",",$data['opening_days']);
		 
        $in_array_master = array(
            "business_name" =>$data['bname'],
            "logo" =>$filename,
            "owner_name" =>$data['oname'],
            "address" =>$data['address'],
            "phone" =>$data['phoneno'],
            "central_pricing" =>$data['central_pricing'],
            "email" =>$data['email'],
			"ntn" =>$data['ntn'],
			"gst" =>$data['gst'],
            "created_date" =>date('Y-m-d'),
            "created_by" =>$this->session->userdata('id'),
			 "start_time"=>$data['start_time'],
			 "end_time"=>$data['end_time'],
			 "opening_days"=>$imp_days,
             "show_default_date"=>$data['default_date'],
              "stock_check"=>$data['stock_check'],
              "same_page"=>$data['same_page'],
              "empty_return"=>$data['empty_return'],
               "complete_day"=>$data['complete_day'],
              
             
        );
        
        $table = "tbl_company";
        $add = $this->mod_common->insert_into_table($table, $in_array_master);

        if($add){
            return $add;
        }else{
            return false;
        }
    }

    public function update_company($data){  
        $id = $data['id'];
        $filename = $data['old_image'];

        if ($_FILES['company_image']['name'] != "") {

            $projects_folder_path = './assets/images/company/';

           // $projects_folder_path = 'http://192.168.10.92:8012/logo/';

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
		$imp_days = implode(",",$data['opening_days']);
		
		
        #----------- update record trans master---------------#
         $in_array_master = array(
            "business_name" =>$data['bname'],
            "logo" =>$filename,
            "owner_name" =>$data['oname'],
            "address" =>$data['address'],
            "phone" =>$data['phoneno'],
            "central_pricing" =>$data['central_pricing'],
            "email" =>$data['email'],
			"ntn" =>$data['ntn'],
			"gst" =>$data['gst'],
            "lat" =>$data['lat'],
            "longitude" =>$data['long'],
            "modify_date" =>date('Y-m-d'),
            "modify_by" =>$this->session->userdata('id'),
			 "start_time"=>$data['start_time'],
			 "end_time"=>$data['end_time'],
			 "opening_days"=>$imp_days,
             "show_default_date"=>$data['default_date'],
             "stock_check"=>$data['stock_check'],
              "same_page"=>$data['same_page'],
               "empty_return"=>$data['empty_return'],
              "complete_day"=>$data['complete_day'],
             

        );
        
        $table = "tbl_company";
        $where = "id='$id'";
        $update=$this->mod_common->update_table($table,$where,$in_array_master);
        
        if($update){
            return $id;
        }else{
            return $id;

        }

     }

  
}

?>