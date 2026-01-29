<?php

class Mod_country extends CI_Model {

    function __construct() {

        parent::__construct();
    }

	
	public function get_by_title($title) {
        $query = $this->db->select('*')
        				->from('tbl_country')
        				->where('country_name', $title)
            			->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
		//return $query->result_array();
        return $query->num_rows();
    }
    
    public function edit_by_title($title,$id) {
        $query = $this->db->select('*')
                        ->from('tbl_country')
                        ->where('country_name', $title)
                        ->where('country_id!=', $id)
                        ->get();

        return $query->num_rows();
    }

    public function under_country($id) {
        $query = $this->db->select('*')
                        ->from('tbl_city')
                        ->where('country_id=', $id)
                        ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }
}

?>