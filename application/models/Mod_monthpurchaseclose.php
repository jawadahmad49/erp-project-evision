<?php

class Mod_monthpurchaseclose extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }
	public function get_by_title($month,$year) {
        $query = $this->db->select('*')
        				->from('tbl_monthly_closing')
        				->where('month_no', $month)
						->where('year_no=', $year)
            			->get();
       
        return $query->num_rows();
    }
	
	public function get_by_title_update($month,$year,$id) {
        $query = $this->db->select('*')
        				->from('tbl_monthly_closing')
        				->where('month_no', $month)
						->where('year_no=', $year)
						->where('trans_id!=',$id)
            			->get();
       
        return $query->num_rows();
    }


	public function manage_monthpurchaseclose(){
		$this->db->select('*');    
		$this->db->from('tbl_monthly_closing');
		//$this->db->join('tblclass', 'tblcategory.classcode = tblclass.classcode');
		$this->db->order_by("trans_id", "desc");
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