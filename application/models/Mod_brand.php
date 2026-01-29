<?php

class Mod_brand extends CI_Model {

    function __construct() {

        parent::__construct();
    }

	
	public function get_by_title($title) {
        $query = $this->db->select('*')
        				->from('tbl_brand')
        				->where('brand_name', $title)
            			->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
		//return $query->result_array();
        return $query->num_rows();
    }
    
    public function edit_by_title($title,$id) {
        $query = $this->db->select('*')
                        ->from('tbl_brand')
                        ->where('brand_name', $title)
                        ->where('brand_id!=', $id)
                        ->get();

        return $query->num_rows();
    }

    public function under_item($id) {
        $query = $this->db->select('*')
                        ->from('tblmaterial_coding')
                        ->where('brandname=', $id)
                        ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }
}

?>