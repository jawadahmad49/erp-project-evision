<?php

class Mod_complaint extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }
	

	public function get_complaints(){
		$this->db->order_by("trans_id", "desc");
		$query = $this->db->get('tbl_complaint_query');
		return $query->result_array();
	}

	public function get_all_complaints(){
		$this->db->order_by("reg_id", "desc");
		$this->db->where(array('sts!='=>"Closed",'forwarded_to'=>$this->session->userdata('id')));
		$query = $this->db->get('tbl_complaint_registration');
		return $query->result_array();
	}
		public function accountcode_forcustomer(){

		$rest_creditors_code='2004001';//2001001
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
}

?>