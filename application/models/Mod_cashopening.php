<?php

class mod_cashopening extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }

    
    public function add_cashopening($data){
       
        $in_array_master = array(
            "business_name" =>$data['bname'],
            "logo" =>$filename,
            "owner_name" =>$data['oname'],
            "address" =>$data['address'],
            "phone" =>$data['phoneno'],
            "email" =>$data['email'],
            "created_date" =>date('Y-m-d'),
            "created_by" =>$this->session->userdata('id')    
        );
        
        $table = "tblacode";
        $add = $this->mod_common->insert_into_table($table, $in_array_master);

        if($add){
            return $add;
        }else{
            return false;
        }
    }

    public function update_cashopening($data){
        
        $acode = $data['id'];

        #----------- update record trans master---------------#
         $in_array_master = array(
            "opngbl" =>$data['amount'],
            "optype" =>$data['type'],  
        );
        
        $table = "tblacode";
        $where = "acode='$acode'";
        $update=$this->mod_common->update_table($table,$where,$in_array_master);
        
        if($update){
            return $acode;
        }else{
            return $acode;

        }

     }

  
}

?>