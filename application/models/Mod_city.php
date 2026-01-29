<?php

class Mod_city extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }
	
	public function get_by_title($title) {
        $query = $this->db->select('*')
        				->from('tbl_city')
        				->where('city_name', $title)
            			->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
		//return $query->result_array();
        return $query->num_rows();
    }

	public function manage_cities(){
		$this->db->select('tbl_city.city_id,tbl_city.city_name,tbl_city.status');    
		$this->db->from('tbl_city');
		$this->db->order_by("city_id", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}

  	public function edit_by_title($title,$id) {
        $query = $this->db->select('*')
                        ->from('tbl_city')
                        ->where('city_name', $title)
                        ->where('city_id!=', $id)
                        ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }

    public function under_area($id) {
        $query = $this->db->select('*')
                        ->from('tbl_area')
                        ->where('city_id=', $id)
                        ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }
 
}

?>