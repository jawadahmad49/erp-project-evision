<?php

class Mod_coa extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }

 
	public function add_new_account($data){

 


$acode=trim($data["pcode"]);
 $aname=trim($data["pname"]);
$address=trim($data["address"]);
$gen=trim($data["gen"]);
$family=trim($data["family"]);
$level=trim($data["level"]);		      
 
$atype=trim($data["atype"]);
$sledger=trim($data["sledger"]);
$dlimit=trim($data["dlimit"]);
$climit=trim($data["climit"]);
$opbalance=trim($data["opbalance"]);
$optype=trim($data["optype"]);
$streg=trim($data["streg"]);
$ispl=trim($data["ispl"]);
$cont_person=trim($data["cont_person"]);
$phone=trim($data["phone"]);
$fax=trim($data["fax"]);
$ntn=trim($data["ntn"]);
$email=trim($data["email"]);
 
	 
$acode='';
$acode_new='';
$qressql_inult = "select max(acode) as acode from tblacode where general='$gen'"  ;


			
				$resul = $this->db->query($qressql_inult);
				$rw = $resul->result_array();
				foreach($rw as $key=>$value) {
				 
					$acode=$value['acode'];
				}
 

$div_name='';
 
		if(substr($gen,-9) == '000000000')
		{

		$div_name='extra_for_main_';
		if($acode){
				$rest = substr($acode,0,4)+1;
				$acode_new=$rest.'000000'; 
			}else{
					$rest = substr($gen,0,3);
					$acode_new=$rest.'1000000'; 
			}
			  $atype='Parent';
		}
		elseif(substr($gen,-6) == '000000')
		{
		  if($acode){
		 	$rest = substr($acode,0,7);
	 
		   	$rest_in =$rest+1;
			 
			$acode_new=$rest_in.'000'; 
		  }else{
			  
				$rest = substr($gen,0,6);
				$acode_new=$rest.'1000'; 
			 
		  }
			$atype='Parent';
		}
		elseif(substr($gen,-3) == '000')
		{
			 
			if($acode){
			$rest = substr($acode,0,7);

			  $rest_in = substr($acode,7,3)+1;


			if($rest_in<=9){$rest_in_n='00'.$rest_in;}
			if($rest_in>9 && $rest_in<=99){$rest_in_n='0'.$rest_in;}
			if($rest_in>99){$rest_in_n=$rest_in;}
			$acode_new=$rest.$rest_in_n; 
			}else{
					$rest = substr($gen,0,7);
					$acode_new=$rest.'001'; 
			}
			
			 $atype='Child';
		}
		
	 
 
 
 if(substr($gen, 0, 1)=='1'){ $family='L'; }
 if(substr($gen, 0, 1)=='2'){ $family='A'; }
 if(substr($gen, 0, 1)=='3'){ $family='S'; }
 if(substr($gen, 0, 1)=='4'){ $family='E'; } 

// $qresult = mysql_query("SELECT * FROM tblacode where general='$gen' and aname='$aname'" );
// if(mysql_num_rows($qresult)>0){
	// print 'already_name';
	// exit;
// }

     $query="insert into tblacode (acode ,aname ,address ,general, atype ,family, sledger, dlimit, climit, opngbl, optype, vat_no,
  isplaccount,level,phone_no,cell,reg_no,email,cont_person,ac_status)
values
('$acode_new' ,'$aname' ,'$address' ,'$gen', '$atype' ,'$family' ,'$sledger' ,'$dlimit' ,'$climit' ,'$opbalance',
'$optype' ,'$streg' ,$ispl,'$level','$phone','$fax','$ntn','$email','$cont_person','Active')";
 	$this->db->query($query);						 
		
 
 
 
 }
 
  
		





	public function update_account($data){
		
		
		
	$acode=trim($data["pcode"]);
	$acode_hidden=trim($data["pcode_hidden"]);
	$aname=trim($data["pname"]);
	$address=trim($data["address"]);
	$gen=trim($data["gen"]);
	$family=trim($data["family"]);
	$level=trim($data["level"]);
	$atype=trim($data["atype"]);
	$sledger=trim($data["sledger"]);
	$dlimit=trim($data["dlimit"]);
	$climit=trim($data["climit"]);
	$opbalance=trim($data["opbalance"]);
	$optype=trim($data["optype"]);
	$streg=trim($data["streg"]);
	$ispl=trim($data["ispl"]);
	$cont_person=trim($data["cont_person"]);
	$phone=trim($data["phone"]);
	$fax=trim($data["fax"]);
	$ntn=trim($data["ntn"]);
	$email=trim($data["email"]);



	$query ="update tblacode set
	aname='$aname' ,address='$address' , sledger='$sledger' ,dlimit='$dlimit', ac_status='Active'  , climit='$climit', opngbl='$opbalance' ,
	optype='$optype' , vat_no='$streg', isplaccount=$ispl,  phone_no='$phone',reg_no='$ntn',email='$email',cell='$fax',
	cont_person='$cont_person'
	where acode='$acode_hidden'";
	$this->db->query($query);

}



		
}

?>