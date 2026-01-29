<?php

class Mod_promocode extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }
	
	public function get_by_title($title) {
        $query = $this->db->select('*')
        				->from('tbl_promo_code')
        				->where('promo_code', $title)
            			->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
		//return $query->result_array();
        return $query->num_rows();
    }

	public function manage_cities(){
		$this->db->select('*');    
		$this->db->from('tbl_promo_code');
		$this->db->order_by("transid", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}

  	public function edit_by_title($title,$id) {
        $query = $this->db->select('*')
                        ->from('tbl_promo_code')
                        ->where('promo_code', $title)
                        ->where('transid!=', $id)
                        ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }

    public function under_area($promo) {
        $query = $this->db->select('*')
                        ->from('tbl_orderbooking')
                        ->where('promo_code=',$promo)
                        ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }
 
}
