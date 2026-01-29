<?php

class Mod_servicelevel extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }
	
	public function get_complains(){
		$this->db->order_by("level_name", "desc");
		$query = $this->db->get('tbl_alert');
		return $query->result_array();
	}

    public function get_users(){
        $this->db->order_by("id", "desc");
        $query = $this->db->get('tbl_admin');
        return $query->result_array();
    }
 
}

?>