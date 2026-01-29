<?php

class Mod_cashbookreport extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }

    public function get_report($data){


        // $daterange= $data['daterange'];
        // $sr=explode("/",($daterange));
        // $fdate=trim($sr[0]);
        // $tdate=trim($sr[1]);

           $fdate=$data['from_date'];
            $tdate=$data['to_date'];

        $acode_cash =$data['acode'];
    $aname = $this->db->query("select aname from tblacode where acode= '$acode_cash'")->row_array()['aname'];


$opngbl = ''; $optype = ''; $op_damount=0;    $op_camount = 0;
$result = "SELECT opngbl,optype FROM `tblacode` WHERE acode ='$acode_cash'";

$query = $this->db->query($result);
$line = $query->row_array();

$opngbl = $line['opngbl'];
$optype = $line['optype'];


$results = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode_cash' and vdate <'$fdate'";
$querys = $this->db->query($results);
           

foreach($querys->result_array() as $key=>$value){
    $op_damount = $value['op_damount'];
    $op_camount = $value['op_camount'];
}
     
$total_opngbl = '';
if($optype=='Credit'){
    $opngbl = -1*($opngbl) ;
}

$total_opngbl =    ($op_damount  -$op_camount )+($opngbl);

$opngbl_receipt = $total_opngbl; 
$opngbl_balance = $total_opngbl;               
    
    

$total_bal= $total_damount=$total_camount=$i=0;
 
$tdr=0;
$tcr=0;
$blnc = $total_opngbl;

$query_main = "SELECT distinct vno from tbltrans_detail where acode  = '$acode_cash'   And vdate BETWEEN '$fdate' AND '$tdate' ORDER by vdate,ig_detail_id asc";
$result_main = $this->db->query($query_main);
  


$result_mains = $result_main->result_array();


foreach($result_mains as $key=>$values){

$this->db->select('*');    
$this->db->from('tbltrans_detail');
$this->db->where('vno', $values['vno']);
$this->db->where_not_in('acode', $acode_cash);
$this->db->where("(vtype='CR' OR vtype='CP'  OR vtype='JV' OR vtype='BR' OR vtype='BP' OR svtype='CR' OR svtype='BR')", NULL, FALSE);
$this->db->order_by("vdate", "testid ");
$query = $this->db->get();


      foreach($query->result_array() as $keys=>$val){
         $srno = $val['srno'];
         $vno = $val['vno'];
         $vdate = $val['vdate'];
          $vtype = $val['vtype'];
       
    $cash_amt = $this->db->query("SELECT sum(damount) as cash_amt FROM `tbltrans_detail` WHERE `vno` ='$vno'   AND  acode='$acode_cash'")->row_array()['cash_amt'];
      $cash_amt_camount = $this->db->query("SELECT sum(camount) as cash_amt_camount FROM `tbltrans_detail` WHERE `vno` ='$vno'")->row_array()['cash_amt_camount'];
  
  
     if($cash_amt!==$cash_amt_camount and $vtype=='JV')continue;
       $acode = $val['acode'];
        
        
        $qresult_sub = "SELECT aname from tblacode where acode= '".$val['acode']."'";
        $query = $this->db->query($qresult_sub);
        $line_sub = $query->row_array();
        $aname= $line_sub['aname'];

        $remarks = $val['remarks'];
        $remarks=  str_replace(",","-",$remarks);
        $damount = $val['damount'];
        $camount = $val['camount'];
//echo"<pre>";
            $vnoo= strtoupper($vno); 
            $vdatee= $vdate; 
            $acodee= $acode; 
            $anamee= $aname; 
            $remarkss= $remarks; 
            $camounts= $camount;
            $damounts= $damount;

//print_r($line_sub);
        
            if($camount > 0){
                $blnc=$camount+$blnc;
            }else{
                $blnc=$blnc-$damount;
                }
      
            $blncc= $blnc; 

        $datas[] = array(
                "acnumber" =>$acode_cash,
                "fromdate" =>$fdate,
                "todate" =>$tdate,
                "openingreceipt"=>$opngbl_receipt,
                "openingbalance"=>$opngbl_balance,
                "voucherno" =>$vnoo,
                "voucherdate" =>$vdatee,
                "accountcode" =>$acodee,
                "acname" =>$anamee,
                "description" =>$remarkss, 
                "receipt" =>$camounts, 
                "payment" =>$damounts,
                "balance" =>$blncc,
             

            );

        } 






    } 
    $result_main = $this->db->query("SELECT * from tbltrans_detail where acode  = '$acode_cash' and vtype='JV'  And vdate BETWEEN '$fdate' AND '$tdate' ORDER by vdate,ig_detail_id asc ")->result_array();

 foreach ($result_main as $key => $value) {
    $vno=$value['vno'];
    $acode = $this->db->query("SELECT acode from tbltrans_detail where vno = '$vno'  and camount>'0'")->row_array()['acode'];
   
  $aname = $this->db->query("SELECT aname from tblacode where acode='$acode'")->row_array()['aname'];
       
         $remarks = $value['remarks'];
        $remarks=  str_replace(",","-",$remarks);
        $damount = $value['damount'];
        $camount = $value['camount'];
           $cash_amt = $this->db->query("SELECT sum(damount) as cash_amt FROM `tbltrans_detail` WHERE `vno` = '$vno'  AND  acode='$acode_cash'")->row_array()['cash_amt'];
            $cash_amt_camount = $this->db->query("SELECT sum(camount) as cash_amt_camount FROM `tbltrans_detail` WHERE `vno` = '$vno' ")->row_array()['cash_amt_camount'];
         if($cash_amt==$cash_amt_camount)continue;
           if($damount > 0){
                $blnc=$damount+$blnc;
            }else{
                $blnc=$blnc-$camount;
                }
      
            $blncc= $blnc; 
  $vno= $value['vno'];
  if(  $vno=='')continue;
  $datas[] = array(
                  "acnumber" =>$acode_cash,
                "fromdate" =>$fdate,
                "todate" =>$tdate,
                "openingreceipt"=>$opngbl_receipt,
                "openingbalance"=>$opngbl_balance,
                "voucherno" =>$vno,
                "voucherdate" =>$value['vdate'],
                "accountcode" =>$value['acode'],
                "acname" =>$aname,
                "description" =>$remarkss, 
                "receipt" =>$damount, 
                "payment" =>$camount,
                "balance" =>$blncc,

            );
            




}


    
        if($datas){
            return $datas;
        }else{
             $datas[] = array(
                "acnumber" =>$acode_cash,
                "fromdate" =>$fdate,
                "todate" =>$tdate,
                "openingreceipt"=>$opngbl_receipt,
                "openingbalance"=>$opngbl_balance,
                "receipt" =>$camounts, 
                "payment" =>$damounts,
                "balance" =>$total_opngbl,  

            );
             return $datas;

        }
    }
   
}

?>