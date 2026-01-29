<?php

class Mod_customeropbal extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }


	public function get_itemname(){
		$this->db->select('*');    
		$this->db->from('tbl_customer_opening');
		$this->db->join('tblmaterial_coding', 'tbl_customer_opening.materialcode = tblmaterial_coding.materialcode');
		$this->db->order_by("trans_id", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function customeropening_balance_list($id){
		$this->db->select('tbl_customer_opening.*,tblmaterial_coding.*');
		$this->db->from('tbl_customer_opening');
		$this->db->join('tblmaterial_coding', 'tbl_customer_opening.materialcode = tblmaterial_coding.materialcode');
		$this->db->where('tbl_customer_opening.acode=',$id);
		$this->db->order_by("trans_id", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function check_already($item,$acode){
		//echo $item;
		//echo $type;
		//$this->db->select('*');    
		//$this->db->from('tbl_shop_opening');
		//$this->db->join('tblmaterial_coding', 'tbl_shop_opening.materialcode = tblmaterial_coding.materialcode');
		//$this->db->order_by("trans_id", "desc");
		//$query = $this->db->get();
		$query = $this->db->select('*')
                        ->from('tbl_customer_opening')
                        ->where('materialcode', $item)
                        ->where('acode', $acode)
                        ->get();
        //pm($query->num_rows());
		return $query->num_rows();
	}

  	public function check_already_edit($item,$acode,$id) {
  		//echo $item;echo $type;echo $id;exit;
        $query = $this->db->select('*')
                        ->from('tbl_customer_opening')
                        ->where('materialcode', $item)
                        ->where('acode', $acode)
                        ->where('trans_id!=', $id)
                        ->get();
              //pm($query->result_array());          
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }
 
}

?>