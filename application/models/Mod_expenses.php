<?php

class Mod_expenses extends CI_Model {

    function __construct() { 

        parent::__construct();
        error_reporting(0);
    
    }
    
 
	
	
	
	
    public function get_total_balance_expenses($data){
		
		  $date=$data['to_date'];
		   $from_date=$data['from_date'];
          
            $query1 = "SELECT opngbl,optype,phone_no,address,aname,acode,reg_date FROM `tblacode` WHERE LEFT(acode,6)= '400100' AND acode !='4001002001' AND acode !='4001001000'  AND reg_date<'$date'";

            $result = $this->db->query($query1);
            $line = $result->result_array();
   
    for ($i=0; $i<count($line); $i++) {


   $acode= $line[$i]['acode'];
   $query2 = $this->db->query("SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' AND `vdate` BETWEEN '$from_date' AND '$date'");

 
    $credit_debit = $query2->result_array();
    
    
     $change_difference=$credit_debit[0]['op_camount']-$credit_debit[0]['op_damount'];

 
 
$opngbl_new=$line[$i]['opngbl'];
if($line[$i]['optype']=='Credit')
{
$opngbl_new=-$line[$i]['opngbl'];
   $line[$i]['new_balance_pay']=$line[$i]['opngbl']+ $change_difference;
}else{
	
	   $line[$i]['new_balance_pay']=-$line[$i]['opngbl']+ $change_difference;
}


    

if($line[$i]['new_balance_pay']<=0){$line[$i]['optype']='Debit'; }else{$line[$i]['optype']='Credit';}
  

}

 //  pm($line);
    return $line;
 
    }

	
  
   
}

?>