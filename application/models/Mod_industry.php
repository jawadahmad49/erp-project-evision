<?php

class Mod_industry extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }


	public function manage_categories(){
		$this->db->select('tbl_lead_generation.id,tbl_lead_generation.industry_name,tbl_lead_generation.industry_status');    
		$this->db->from('tbl_lead_generation');
		//$this->db->join('tblclass', 'tbl_lead_generation.classcode = tblclass.classcode');
		$this->db->order_by("id", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}

    public function under_items($id) {
        $query = $this->db->select('*')
                        ->from('tblmaterial_coding')
                        ->where('catcode=', $id)
                        ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }
 
}

?>