<?php

class Mod_expensetype extends CI_Model {

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
		return $query->result_array();
    }

	public function manage_cities(){
		$this->db->select('tbl_city.city_id,tbl_city.city_name,tbl_city.status,tbl_country.country_name');    
		$this->db->from('tbl_city');
		$this->db->join('tbl_country', 'tbl_city.country_id = tbl_country.country_id');
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
        return $query->result_array();
    }

    public function used_in_trans($id) {
		
		 
        $result = $this->db->select('acode')
                          ->from('tblacode')
                          ->where('id=',$id)
                          ->get();

        $num_rows = $result->num_rows();
        $line = $result->row_array();
          
			
		
        $query = $this->db->select('*')
                        ->from('tbltrans_detail')
                        ->where('acode=', $line["acode"])
                        ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }

    public function accountcode_forexpensetype($rest_creditors_code){

        //$rest_creditors_code='4001001';
        $result = $this->db->select('COALESCE(max(right(`acode`,3)),0) as code')
                          ->from('tblacode')
                          ->where('LEFT(`acode`,7)',$rest_creditors_code)
                          ->get();

        $num_rows = $result->num_rows();
        $Code = 1;

        $line = $result->row_array();
 
		
		if(intval($line["code"])==0)
			$Code=1;
		else
			 $Code=intval($line["code"])+1;
		if($Code<=99 && $Code >9) $Code="0".$Code;
		if($Code<=9) $Code="00".$Code;
		return $rest_creditors_code .= $Code;
        
    }

     public function accountcode_forincometype(){

        $rest_creditors_code='3002001';
        $result = $this->db->select('COALESCE(max(right(`acode`,3)),0) as code')
                          ->from('tblacode')
                          ->where('LEFT(`acode`,7)',$rest_creditors_code)
                          ->get();

        $num_rows = $result->num_rows();
        $Code = 1;

        $line = $result->row_array();

		
		
		if(intval($line["code"])==0)
			$Code=1;
		else
			 $Code=intval($line["code"])+1;
		if($Code<=99 && $Code >9) $Code="0".$Code;
		if($Code<=9) $Code="00".$Code;
		return $rest_creditors_code .= $Code;
        
    }

    public function getExpenseList(){
        
        //$where = " general='4001001000' OR general='3002001000'";
        $result = $this->db->select('*')
                          ->from('tblacode')
                          ->where('general','4001001000')
                          ->or_where('general','3002001000')
                          //->where($where)
                          ->order_by('acode','desc')
                          ->get();
                          
        //pm($result->result_array());
        return $result->result_array();
        
    }
 
}

?>