<?php

class Mod_customercreated extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }
    public function get_details($data){
       $from_date = $data['from_date'];
       $to_date = $data['to_date'];
       $segment = $data['segment'];

       $this->db->where('reg_date >=',$from_date);
       $this->db->where('reg_date <=',$to_date);
       if($segment!="all"){
       	$this->db->where('segment',$segment);
       }
       return $this->db->get('tblacode')->result();
    }
}

?>