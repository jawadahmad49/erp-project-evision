<?php



class Mod_customer extends CI_Model {



    function __construct() {



        parent::__construct();

        error_reporting(0);

    

    }



	public function accountcode_forcustomer($rest_creditors_code){



		//$rest_creditors_code='2004001';//2001001

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



	public function getOnlyCustomers(){



		$rest_creditors_code='2004001';

		$result = $this->db->select('*')

                          ->from('tblacode')

             //             ->where('LEFT(`acode`,7)',$rest_creditors_code)

                          ->where('atype','Child')

                          ->where('ac_status','Active')

                          ->order_by('aname','ASC')

                          ->get();



		return $result->result_array();

		

	}



	public function getOnlytanks(){

 

		$result = $this->db->select('*')

                          ->from('tbl_tank')

                          ->where('status','Active')

                          ->order_by('tank_id','desc')

                          ->get();



		return $result->result_array();

		

	}

	public function getOnlytankss($tank){

 

		$result = $this->db->select('*')

                          ->from('tbl_tank')

                         // ->where('status','Active')

						    ->where('tank_id',$tank)

						  

                          ->order_by('tank_id','desc')

                          ->get();



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

                        ->from('tbl_issue_goods')

                        ->where('issuedto=', $id)

                        ->get();

        return $query->num_rows();

    }

		

    public function under_transactions($id) {

        $query = $this->db->select('*')

                        ->from('tbltrans_detail')

                        ->where('acode=', $id)

                        ->get();

        return $query->num_rows();

    }

		

    public function under_childs($id) {

        $query = $this->db->select('*')

                        ->from('tblacode')

                        ->where('general=', $id)

                        ->get();

        return $query->num_rows();

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

		 

}



?>