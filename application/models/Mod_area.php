<?php

class Mod_area extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }

	
	public function get_by_title($title) {
        $query = $this->db->select('*')
        				->from('tbl_area')
        				->where('aname', $title)
            			->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
		return $query->num_rows();
    }

	public function manage_areas(){
		$this->db->select('tbl_area.area_id,tbl_area.aname,tbl_area.status,tbl_city.city_name');    
		$this->db->from('tbl_area');
		$this->db->join('tbl_city', 'tbl_area.city_id = tbl_city.city_id');
		$this->db->order_by("area_id", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}

  	public function edit_by_title($title,$id) {
        $query = $this->db->select('*')
                        ->from('tbl_area')
                        ->where('aname', $title)
                        ->where('area_id!=', $id)
                        ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }

    public function edit_record($id){      
        #------------ get record------------#
        $table = "tbl_area";
        $where = "area_id='" . $id . "'";
        $result = $this->mod_common->select_single_records($table, $where);
        return $result;
    }
 
}

?>