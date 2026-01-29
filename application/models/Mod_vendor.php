<?php



class Mod_vendor extends CI_Model {



    function __construct() {



        parent::__construct();

        error_reporting(0);

    

    }



	public function accountcode_forvendor($rest_creditors_code){



		//$rest_creditors_code='1001001';//2001002

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





	public function getOnlyVendors_with_customer(){



	 



 		$codess=array('1001001000','2004002000');

		$result = $this->db->select('*')

                          ->from('tblacode')

                          ->where('atype','Child')

                            

                          

                          ->order_by('acode','desc')

                          ->get();



               //   pm($result->result_array());  

		return $result->result_array();

		

	}







	public function getOnlyVendors(){



	 

 $codess=array('1001001000','2004002000');

		$result = $this->db->select('*')

                          ->from('tblacode')

                          ->where('atype','Child')

                          ->where('general!=','2004001000')

                            

                          

                          ->order_by('acode','desc')

                          ->get();



               //   pm($result->result_array());  

		return $result->result_array();

		

	}



	public function getOnlyVendors_only(){



		$rest_creditors_code='1001001';

		$result = $this->db->select('*')

                          ->from('tblacode')

                          ->where('atype','Child')

                          ->where('ac_status','Active')

                          ->where('LEFT(`acode`,7)',$rest_creditors_code)

                          ->order_by('acode','desc')

                          ->get();



                          

		return $result->result_array();

		

	}



	public function getOnlyVendor(){



		$rest_creditors_code='1001001';

		$result = $this->db->select('*')

                          ->from('tblacode')

                          ->where('LEFT(`acode`,7)',$rest_creditors_code)

                          ->where('atype','Child')

                          ->order_by('acode','desc')

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

                        ->from('tbl_goodsreceiving')

                        ->where('suppliercode=', $id)

                        ->get();

        //return $query->num_rows > 0 ? $query->row_array() : FALSE;

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