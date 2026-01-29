<?php

class Mod_bank extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }

	public function accountcode_forbank($rest_creditors_code){

		//$rest_creditors_code='2004002';//2001001
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
		if($Code<=99 && $Code>9) $Code="0".$Code;
		if($Code<=9) $Code="00".$Code;

		return $rest_creditors_code .= $Code;
		
	}

	public function getOnlyBanks(){

		$rest_creditors_code='2004002';
		$result = $this->db->select('*')
                          ->from('tblacode')
                          ->where('LEFT(`acode`,7)',$rest_creditors_code)
                          ->where('atype','Child')
                  
                          ->order_by('aname','ASC')
                          ->get();

		return $result->result_array();
		
	}

	public function getnotBanks($where){

		$rest_creditors_code='2004002';
		$result = $this->db->select('*')
                          ->from('tblacode')
                   //       ->where('LEFT(`acode`,7)!=',$rest_creditors_code)
                          ->where('atype','Child')
                          ->where('ac_status','Active')
                           ->where($where)
                          ->order_by('aname','ASC')
                          ->get();
 


		return $result->result_array();
		
	}
	public function getOnlyBank(){

		$rest_creditors_code='2004002';
		$result = $this->db->select('*')
                          ->from('tblacode')
                          ->where('LEFT(`acode`,7)',$rest_creditors_code)
                          ->where('atype','Child')
                          ->order_by('acode','desc')
                          ->get();

		return $result->result_array();
		
	}

	public function checkOnlyBank($where){

		$rest_creditors_code='2004002';
		$result = $this->db->select('*')
                          ->from('tblacode')
                          ->where('LEFT(`acode`,7)',$rest_creditors_code)
                          ->where('atype','Child')
                          ->where($where)
                          ->order_by('acode','desc')
                          ->get();
                     //q();     

		return $result->result_array();
		
	}

	public function edit_record($id){      
	    #------------ get record------------#
	    $table = "tblacode";
	    $where = "acode='" . $id . "'";
	    $result = $this->mod_common->select_single_records($table, $where);
	    return $result;
	}

	
    public function under_items($id) {
        $query = $this->db->select('*')
                        ->from('tbltrans_detail')
                        ->where('acode=', $id)
                        ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }
		 
}

?>