<?php

class Mod_category extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }


	public function manage_categories(){
		$this->db->select('tblcategory.id,tblcategory.catname,tblcategory.status,tblclass.classcode,tblclass.classname');    
		$this->db->from('tblcategory');
		$this->db->join('tblclass', 'tblcategory.classcode = tblclass.classcode');
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