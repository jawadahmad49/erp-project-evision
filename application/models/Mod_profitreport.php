<?php

class Mod_profitreport extends CI_Model { 

 

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }
    
    public function getdate_stock_report($data,$type=''){

        $sql="SELECT * from `tblmaterial_coding` where catcode='1'";
        $query = $this->db->query($sql);
        $count=0;
         
            foreach($query->result_array() as $key => $value) {

                $itemid=$value['materialcode'];
                if($type==2)
                {
                      $fdate=$data['from_date'];      
                      $tdate=$data['to_date'];    

                     // pm($data);


                    $sqlcot = "SELECT COALESCE(sum((select itemnameint from tblmaterial_coding where catcode='1' AND materialcode=d.itemid) *d.qty),0)/1000 as totala from tbl_issue_goods_detail d , tbl_issue_goods m where d.ig_detail_id= m.issuenos AND issuedate  >= '$fdate' AND issuedate  <= '$tdate' AND d.itemid = '$itemid' ";
                }
                else
                {
                    $sqlcot = "SELECT COALESCE(sum((select itemnameint from tblmaterial_coding where catcode='1' AND materialcode=d.itemid) *d.qty),0)/1000 as totala from tbl_issue_goods_detail d , tbl_issue_goods m where d.ig_detail_id= m.issuenos AND issuedate  = '$data' AND d.itemid = '$itemid' ";   
                }

            $querycot = $this->db->query($sqlcot);
            $result=$querycot->row_array();
            
            $new_data[]=$result;
             $new_data[$count]['materialcode']=$value['materialcode'];
             $new_data[$count++]['itemname']=$value['itemname'];
            
         }
         return $new_data;
    }

    public function getdar_stock_report($data,$type=''){

        $sql="SELECT * from `tblmaterial_coding` where catcode='1'";
        $query = $this->db->query($sql);
        $count=0;
         
            foreach($query->result_array() as $key => $value) {

                $itemid=$value['materialcode'];
                if($type==2)
                {
                      $fdate=$data['from_date'];      
                      $tdate=$data['to_date'];    

                     // pm($data);


                    $sqlcot = "SELECT COALESCE(sum((select itemnameint from tblmaterial_coding where catcode='1' AND materialcode=d.itemid) *d.qty),0)/1000 as totala from tbl_issue_goods_detail d , tbl_issue_goods m where d.ig_detail_id= m.issuenos AND issuedate  >= '$fdate' AND issuedate  <= '$tdate' AND d.itemid = '$itemid' ";
                }
                else
                {
                    $sqlcot = "SELECT COALESCE(sum((select itemnameint from tblmaterial_coding where catcode='1' AND materialcode=d.itemid) *d.qty),0)/1000 as totala from tbl_issue_goods_detail d , tbl_issue_goods m where d.ig_detail_id= m.issuenos AND issuedate  = '$data' AND d.itemid = '$itemid' ";   
                }

            $querycot = $this->db->query($sqlcot);
            $result=$querycot->row_array();
            
            $new_data[]=$result;
             $new_data[$count]['materialcode']=$value['materialcode'];
             $new_data[$count++]['itemname']=$value['itemname'];
            
         }
         return $new_data;
    }

    public function getdaily_activity_report($data,$type=''){

                    $sqlcot = "SELECT * FROM tbl_goodsreceiving WHERE receiptdate = '$data'";   
                
            $querycot = $this->db->query($sqlcot);
            $result=$querycot->row_array();
            
            $new_data[]=$result;
             // $new_data[$count]['materialcode']=$value['materialcode'];
             // $new_data[$count++]['itemname']=$value['itemname'];
            
         
         return $new_data;
    }

    public function get_total_customer_stock(){

            $query1 = "SELECT opngbl,optype,phone_no,address,aname,acode FROM `tblacode`
			WHERE LEFT(acode,7)= '2004001' AND acode !='2004001000'"; 

			// $query1 = "SELECT opngbl,optype,phone_no,address,aname,acode FROM `tblacode`
			// WHERE   acode  ='20040010050'";

            $result = $this->db->query($query1);
            $line = $result->result_array();
            
            $fdate=date('Y-m-d');
            

    for ($i=0; $i<count($line); $i++) {

        $acode=$line[$i]['acode'];

        $coding_query1 = "SELECT * FROM `tblmaterial_coding` where catcode='1' ";

        $coding_result = $this->db->query($coding_query1);
        $coding_line = $coding_result->result_array();
        $total_opening_balance=0;
        for ($j=0; $j<count($coding_line); $j++) {



            $itemid=$coding_line[$j]['materialcode'];
           
                $sqlcot = "SELECT qty as opening FROM `tbl_customer_opening` WHERE acode ='$acode' AND materialcode ='$itemid'";// AND materialcode ='$itemid'COALESCE(SUM(`qty`),0) as opening
                $querycot = $this->db->query($sqlcot);
                $rowcot = $querycot->row_array();

           // exit();

			      	$sqls = " SELECT   (SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as sale 
				FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` 
				ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` 
				WHERE `issuedto` ='$acode' AND `tbl_issue_goods_detail`.`wrate`>0 
				AND `tbl_issue_goods_detail`.`returns`=0 AND `tbl_issue_goods_detail`.`itemid`='$itemid') 
				+ 
				(SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as sale   
				FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` 
				WHERE `issuedto` ='$acode' AND `tbl_issue_goods_detail`.`wrate`=0 AND `tbl_issue_goods_detail`.`returns`=0 
				AND `tbl_issue_goods_detail`.`itemid`='$itemid')
				+ 
				(SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as sale   FROM `tbl_issue_goods` 
				INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` 
				WHERE `issuedto` ='$acode' AND `tbl_issue_goods_detail`.`wrate`>0 AND `tbl_issue_goods_detail`.`returns`>0 
				AND `tbl_issue_goods_detail`.`itemid`='$itemid')

				+ 
				(SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`-`tbl_issue_goods_detail`.`returns`),0) as sale   FROM `tbl_issue_goods` 
				INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` 
				WHERE  `issuedto` ='$acode' AND `tbl_issue_goods_detail`.`wrate`=0 
				AND `tbl_issue_goods_detail`.`returns`>0 AND `tbl_issue_goods_detail`.`itemid`='$itemid'
				) as sale" ;
				
				
				 
 
            $querys = $this->db->query($sqls)->row_array();
 
            $sqlr = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returns
			FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` 
			ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `scode` ='$acode' 
			AND `tbl_issue_return_detail`.`itemid`='$itemid' ";
             
            $queryr = $this->db->query($sqlr)->row_array();

 
		// print '---opening:'.$rowcot['opening'];
		// print '---returns:'.$queryr['returns'];
		// print '---sale:'.$querys['sale'];
                $opening_balance= $rowcot['opening']+$querys['sale']-$queryr['returns'];

                $opening_balance_sum=$opening_balance;

                $line[$i]['stock'][$itemid]=$opening_balance;
                
                    if($opening_balance<0)
                    {
                        $opening_balance_sum=$opening_balance_sum*-1;
                        $total_opening_balance=$total_opening_balance+$opening_balance_sum;
                    }
                    else
                    {
                        $total_opening_balance=$total_opening_balance+$opening_balance_sum;

                    }
				

                }
            $line[$i]['total']=$total_opening_balance;
		}
        		return $line;

    }
 
    public function get_total_customer_balance(){
 

            $query1 = "SELECT opngbl,optype,phone_no,address,aname,acode FROM `tblacode` WHERE LEFT(acode,7)= '2004001' AND acode !='2004001000'";

            $result = $this->db->query($query1);
            $line = $result->result_array();
       

    for ($i=0; $i<count($line); $i++) {

 
        $acode= $line[$i]['acode'];

    $query2 = $this->db->query("SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode'");


    //pm($result->result_array());
    $credit_debit = $query2->result_array();

    
     $change_difference=$credit_debit[0]['op_damount']-$credit_debit[0]['op_camount'];


    
    if($change_difference >= 0 AND $line[$i]['optype']=='Debit')
    {
       // echo "string";
       $line[$i]['new_balance']=$line[$i]['opngbl']+ $change_difference;
    }
    else if($change_difference <= 0 AND $line[$i]['optype']=='Credit')
    {
       $line[$i]['new_balance']=-$line[$i]['opngbl']+ $change_difference;

    }

	$new_balance+= $line[$i]['new_balance'];
}

 
  
 
    return $new_balance;

    }
    public function get_total_balance($data){

            $query1 = "SELECT opngbl,optype,phone_no,address,aname,acode FROM `tblacode` WHERE LEFT(acode,7)= '2004001' AND acode !='2004001000'";

            $result = $this->db->query($query1);
            $line = $result->result_array();
            //pm($line);

    for ($i=0; $i<count($line); $i++) {


    //echo "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode'";

        $acode= $line[$i]['acode'];


   //echo  "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode'";
   $query2 = $this->db->query("SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode'");


    //pm($result->result_array());
    $credit_debit = $query2->result_array();
    //     echo $acode;
    //     echo "<br>";
    // pm($credit_debit);


    // pm($credit_debit);


    
     $change_difference=$credit_debit[0]['op_damount']-$credit_debit[0]['op_camount'];


    // if($value['optype']=='Credit')
    // {

    // }
    //$value['optype']=='Debit')
 
$opngbl_new=$line[$i]['opngbl'];
if($line[$i]['optype']=='Credit')
{
$opngbl_new=-$line[$i]['opngbl'];
   $line[$i]['new_balance']=-$line[$i]['opngbl']+ $change_difference;
}else{
	
	   $line[$i]['new_balance']=$line[$i]['opngbl']+ $change_difference;
}


    

if($line[$i]['new_balance']<=0){$line[$i]['optype']='Credit'; }else{$line[$i]['optype']='Debit';}
 
    // if($change_difference >= 0 AND $line[$i]['optype']=='Debit')
    // {

    //    // echo "string";
    //    $line[$i]['new_balance']=$line[$i]['opngbl']+ $change_difference;
    // }
    // else if($change_difference <= 0 AND $line[$i]['optype']=='Credit')
    // {

    //    $line[$i]['new_balance']=-$line[$i]['opngbl']+ $change_difference;

    // }
	

}

 
  //  pm($line);
    return $line;
 
    }


    public function get_opening($data,$category_id=''){
       
        print_r($data);
        if($category_id==1)
        {
            $category_id='WHERE catcode=1';
        }

        $fdate=$data['from_date'];
        $tdate=$data['to_date'];

        $acode= $data['acode'];


       if($data['day']!='')
       {
            //$fdate='2000-01-01';
            $day=$data['day']+1;

            $date_temp = $data['month'] .' '. $day.' '.$data['year'];

            $tdate = date('Y-m-d', strtotime($date_temp));
            $fdate=$tdate;

       }
    

        //pm($data);


        $sql="SELECT * from `tblmaterial_coding` $category_id    ";
        $query = $this->db->query($sql);
         
        if($query->num_rows()>0){
            foreach($query->result_array() as $key => $value) {
                $itemid=$value['materialcode'];

            ///////// opening start    ////

               $sqlcot = "SELECT qty as opening FROM `tbl_customer_opening` WHERE `date` <= '$fdate'  AND acode ='$acode' AND materialcode ='$itemid'";// AND materialcode ='$itemid'COALESCE(SUM(`qty`),0) as opening
            $querycot = $this->db->query($sqlcot);
            $rowcot = $querycot->row_array();


                       $sqls = " SELECT sum(sale) as sale from  (SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as sale  
				FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` 
				ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
				WHERE `issuedate`<'$fdate'  AND `issuedto` ='$acode' AND `tbl_issue_goods_detail`.`wrate`>0 
				AND `tbl_issue_goods_detail`.`returns`=0 AND `tbl_issue_goods_detail`.`itemid`='$itemid' 
               UNION 
               SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as sale   FROM `tbl_issue_goods` 
			   INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` 
			   WHERE `issuedate`<'$fdate'  AND `issuedto` ='$acode' AND `tbl_issue_goods_detail`.`wrate`=0 
			   AND `tbl_issue_goods_detail`.`returns`=0 AND `tbl_issue_goods_detail`.`itemid`='$itemid'
               UNION 
               SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as sale   FROM `tbl_issue_goods` 
			   INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` 
			   WHERE `issuedate`<'$fdate'  AND `issuedto` ='$acode' AND `tbl_issue_goods_detail`.`wrate`>0 
			   AND `tbl_issue_goods_detail`.`returns`>0 AND `tbl_issue_goods_detail`.`itemid`='$itemid'
			   
			    UNION 
               SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`-`tbl_issue_goods_detail`.`returns`),0) as sale   FROM `tbl_issue_goods` 
			   INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` 
			   WHERE `issuedate`<'$fdate'  AND `issuedto` ='$acode' AND `tbl_issue_goods_detail`.`wrate`=0 
			   AND `tbl_issue_goods_detail`.`returns`>0 AND `tbl_issue_goods_detail`.`itemid`='$itemid'
			   ) sale" ;
			   
		//  print                        $sqls;
			   
         // print '<br>';
            $querys = $this->db->query($sqls)->row_array();
 

            $sqlr = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returns  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `irdate`<'$fdate' AND `scode` ='$acode' AND `tbl_issue_return_detail`.`itemid`='$itemid' ";
             
            $queryr = $this->db->query($sqlr)->row_array();

 

            $datas[] = array(
                    'itemid' => $itemid,
                    'opening' => $rowcot['opening']+$querys['sale']-$queryr['returns'],
          
                );
 

    }

 
}

return $datas;

    }

    public function getsale($data,$item=''){

       

            $fdate=$data['from_date'];
            $tdate=$data['to_date'];

            $acode= "";
            $condj = "";
            $condj1 = "";
            $condj2 = "";
            if($item==2)
            {

                    if(!empty($data['transaction'])){
                        $condj= " AND tbl_issue_goods.sale_type='".$data['transaction']."'";
                    }
                    if(!empty($data['segment'])){
                        $condj1= " AND tblacode.segment='".$data['segment']."'";
                    }
                    if(!empty($data['items'])){
                        $condj2= " AND `tbl_issue_goods_detail`.`itemid`='".$data['items']."'";
                    }
                    
            }
            else if($item==3)
            {
                $acode= " AND `tbl_issue_goods`.`issuedto`='".$data['acode']."'";

            }
            else
            {
                $acode= " AND `tbl_issue_goods`.`issuedto`='".$data['acode']."'";

            }
        $sqlj="SELECT tbl_issue_goods.*,tblacode.* FROM `tbl_issue_goods` INNER JOIN `tblacode` ON `tbl_issue_goods`.`issuedto` = `tblacode`.`acode` WHERE `issuedate` BETWEEN '$fdate' AND '$tdate' $acode $condj $condj1 ORDER BY `issuedate` ASC";



   $queryj = $this->db->query($sqlj);
         
        if($queryj->num_rows()>0){

            foreach($queryj->result_array() as $key => $value) {


	$sqljj="SELECT itemid , wrate ,returns ,qty , sprice FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."'  
 AND wrate > 0  AND returns =0 $condj2
 UNION SELECT itemid , wrate ,returns ,qty  , sprice
 FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."'  
 AND wrate = 0  AND returns =0  $condj2
 UNION SELECT itemid , wrate ,returns ,qty , sprice FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."'  
 AND wrate > 0  AND returns > 0 $condj2
 UNION SELECT itemid , wrate ,returns ,qty-returns as qty , sprice FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."'  
 AND wrate = 0  AND returns > 0 
  $condj2";
 
 

$queryjj = $this->db->query($sqljj)->result_array();


if(!empty($queryjj))
{


//pm($queryjj);
$dataj[] = array(
                    'issuenos' => $value['issuenos'],
                    'aname' => $value['aname'],
                    'issuedate' => $value['issuedate'],
                    'sale'=>$queryjj,
                    //'return'=>$rowreturn,
                );

}
            }
        }
        //pm($dataj);
        return $dataj;
//exit;
            ////// sale end //////////


    }

public function getreturn($data){

		$fdate=$data['from_date'];
		$tdate=$data['to_date'];

        if($data['day']!='')
           {
               
                $day=$data['day']+1;

                $date_temp = $data['month'] .' '. $day.' '.$data['year'];

                $tdate = date('Y-m-d', strtotime($date_temp));
                $fdate=$tdate;
           }

           // echo $fdate;
           // echo "<br>";
           // echo $tdate;
           // exit();

		$acode= $data['acode'];


		$sqlj="SELECT * FROM `tbl_issue_return` WHERE `irdate` BETWEEN '$fdate' AND '$tdate' AND `scode`='$acode' ORDER BY `irdate` ASC";
		$queryj = $this->db->query($sqlj); 
        if($queryj->num_rows()>0){
            foreach($queryj->result_array() as $key => $value) {
			$sqljj="SELECT irnos,qty,itemid FROM `tbl_issue_return_detail` where `irnos`='".$value['irnos']."' ORDER BY `tbl_issue_return_detail`.`itemid` ASC";
			$queryjj = $this->db->query($sqljj)->result_array();
			if(!empty($queryjj))
			{ 
$dataj[] = array(
                    'issuenos' => $value['irnos'],
                    'issuedate' => $value['irdate'],
                    'return'=>$queryjj,
                    //'return'=>$rowreturn,
                );
}

            }
        }
        //pm($dataj);
        return $dataj;
//exit;
            ////// sale end //////////


    }

public function getsales($data,$item=''){ 
$fdate=$data['from_date'];
$tdate=$data['to_date'];
if($data['day']!='')
{
$day=$data['day']+1;
$date_temp = $data['month'] .' '. $day.' '.$data['year'];
$tdate = date('Y-m-d', strtotime($date_temp));
$fdate=$tdate;
}
$acode= "";
$condj = "";
$condj1 = "";
$condj2 = "";
$condj3 = "";
$sqlj="SELECT tbl_issue_goods.*,tblacode.* FROM `tbl_issue_goods` INNER JOIN `tblacode` ON `tbl_issue_goods`.`issuedto` = `tblacode`.`acode` WHERE `issuedate` BETWEEN '$fdate' AND '$tdate' $acode $condj $condj1";
$queryj = $this->db->query($sqlj);
	if($queryj->num_rows()>0){
		foreach($queryj->result_array() as $key => $value) {
			$sqljj="SELECT tbl_issue_goods_detail.* ,tblmaterial_coding.catcode,tblmaterial_coding.itemnameint
			FROM `tbl_issue_goods_detail` INNER JOIN `tblmaterial_coding` ON `tbl_issue_goods_detail`.`itemid` = `tblmaterial_coding`.`materialcode` where  `ig_detail_id`='".$value['issuenos']."'
			$condj2 $condj3 ORDER BY `itemid` ASC";

			if($condj3 || $condj2){$brand='brand';}
            
			$queryjj = $this->db->query($sqljj)->result_array();
				if(!empty($queryjj))
				{
				$total_amt= $value['security_amt']+$value['gas_amt'];
				$dataj[] = array(
				'issuenos' => $value['issuenos'],
				'aname' => $value['aname'],
				'issuedate' => $value['issuedate'],
				'total_amount' => $total_amt,
				'sale'=>$queryjj,
				'brand'=>$brand,
				);

				}
		}
	}
return $dataj;
}

 

public function getsales_return($data,$item=''){
$fdate=$data['from_date'];
$tdate=$data['to_date'];

if($data['day']!='')
{
$day=$data['day']+1;
$date_temp = $data['month'] .' '. $day.' '.$data['year'];
$tdate = date('Y-m-d', strtotime($date_temp));
$fdate=$tdate;
}
$acode= "";
$condj = "";
$condj1 = "";
$condj2 = "";
$condj3 = "";
$sqlj="SELECT tbl_issue_return.*,tblacode.* FROM `tbl_issue_return` INNER JOIN `tblacode` ON `tbl_issue_return`.`scode` = `tblacode`.`acode` 
WHERE `irdate` BETWEEN '$fdate' AND '$tdate' and type='salereturn' $acode $condj $condj1";

$queryj = $this->db->query($sqlj); echo $queryj->num_rows();
	if($queryj->num_rows()>0){
		foreach($queryj->result_array() as $key => $value) {
			$sqljj="SELECT itemid , wrate  ,qty ,tblmaterial_coding.catcode,tblmaterial_coding.itemnameint, total_amount as sprice FROM `tbl_issue_return_detail` INNER JOIN `tblmaterial_coding` 
			ON `tbl_issue_return_detail`.`itemid` = `tblmaterial_coding`.`materialcode` where  `irnos`='".$value['irnos']."'
			$condj2 $condj3 ORDER BY `itemid` ASC";
			if($condj3 || $condj2){$brand='brand';}
			$queryjj = $this->db->query($sqljj)->result_array();
				if(!empty($queryjj))
				{
				 
					
					
					$total_amt= 0;
					foreach($queryjj as $key => $value2) {
					$total_amt+= $value2['sprice'];
					}
				$dataj[] = array(
				'issuenos' => $value['irnos'],
				'aname' => $value['aname'],
				'issuedate' => $value['irdate'],
				'total_amount' => $total_amt,
				'sale_return'=>$queryjj,
				'brand'=>$brand,
				);

				}
		}
	}
return $dataj;
}

public function getpurchases_return($data,$item=''){
$fdate=$data['from_date'];
$tdate=$data['to_date'];
if($data['day']!='')
{
$day=$data['day']+1;
$date_temp = $data['month'] .' '. $day.' '.$data['year'];
$tdate = date('Y-m-d', strtotime($date_temp));
$fdate=$tdate;
}
$acode= "";
$condj = "";
$condj1 = "";
$condj2 = "";
$condj3 = "";
$sqlj="SELECT tbl_issue_return.*,tblacode.* FROM `tbl_issue_return` INNER JOIN `tblacode` ON `tbl_issue_return`.`scode` = `tblacode`.`acode` 
WHERE `irdate` BETWEEN '$fdate' AND '$tdate' and type!='salereturn' $acode $condj $condj1";
$queryj = $this->db->query($sqlj);
	if($queryj->num_rows()>0){
		foreach($queryj->result_array() as $key => $value) {
			$sqljj="SELECT itemid , wrate  ,qty , total_amount as sprice  ,tblmaterial_coding.catcode,tblmaterial_coding.itemnameint  FROM `tbl_issue_return_detail` INNER JOIN `tblmaterial_coding` 
			ON `tbl_issue_return_detail`.`itemid` = `tblmaterial_coding`.`materialcode` where  `irnos`='".$value['irnos']."'
			$condj2 $condj3 ORDER BY `itemid` ASC";
			if($condj3 || $condj2){$brand='brand';}
			$queryjj = $this->db->query($sqljj)->result_array();
				if(!empty($queryjj))
				{
				 
					
					
					$total_amt= 0;
					foreach($queryjj as $key => $value2) {
					$total_amt+= $value2['sprice'];
					}
				$dataj[] = array(
				'issuenos' => $value['irnos'],
				'aname' => $value['aname'],
				'issuedate' => $value['irdate'],
				'total_amount' => $total_amt,
				'purchases_return'=>$queryjj,
				'brand'=>$brand,
				);

				}
		}
	}
return $dataj;
}





public function getpayments($data,$item=''){
$fdate=$data['from_date'];
$tdate=$data['to_date'];
// if($data['day']!='')
// {
// $day=$data['day']+1;
// $date_temp = $data['month'] .' '. $day.' '.$data['year'];
// $tdate = date('Y-m-d', strtotime($date_temp));
// $fdate=$tdate;
// }
$acode= "";
$condj = "";
$condj1 = "";
$condj2 = "";
$condj3 = "";




					$bank_codes= '';
	$sqljj="select acode from tblacode where general='2004002000'";
			 
			$queryjj = $this->db->query($sqljj)->result_array();
				if(!empty($queryjj))
				{
				 
					
					
					foreach($queryjj as $key => $value2) {
					$bank_codes.= "'".$value2['acode']."',";
					}
				}	
					
					
  $sqlj="  select * from tbltrans_detail where acode in($bank_codes  '2003013001') and vdate  BETWEEN '$fdate' AND '$tdate' and damount='0'";
$queryj = $this->db->query($sqlj);
	if($queryj->num_rows()>0){
		foreach($queryj->result_array() as $key => $value) {
			$sqljj="SELECT * FROM `tbltrans_detail` INNER JOIN `tblacode` 
			ON `tbltrans_detail`.`acode` = `tblacode`.`acode` where  `vno`='".$value['vno']."' ";
			 
			$queryjj = $this->db->query($sqljj)->result_array();
				if(!empty($queryjj))
				{
				 foreach($queryjj as $key => $value2) {
					$aname= $value2['aname'];
					}
				 
				$dataj[] = array(
				'vno' => $value['vno'],
				'vdate' => $value['vdate'],
				'camount' => $value['camount'],
				'aname' => $aname,
				'remarks'=>$value['remarks'],
				'chequedate'=>$value['chequedate'],
				'chequeno'=>$value['chequeno'],
				);

				}
		}
	}
return $dataj;
}





public function getreceipts($data,$item=''){
$fdate=$data['from_date'];
$tdate=$data['to_date'];

$acode= "";
$condj = "";
$condj1 = "";
$condj2 = "";
$condj3 = "";




					$bank_codes= '';
	$sqljj="select acode from tblacode where general='2004002000'";
	
	//$sqljj = "select acode from tblacode where general in ('4001002000','4001001000')";
			 
			$queryjj = $this->db->query($sqljj)->result_array();
				if(!empty($queryjj))
				{
				 
					
					
					foreach($queryjj as $key => $value2) {
					$bank_codes.= "'".$value2['acode']."',";
					}
				}	
	
					
  $sqlj="  select * from tbltrans_detail where acode in($bank_codes  '2003013001') and vdate  BETWEEN '$fdate' AND '$tdate' and camount='0'";
$queryj = $this->db->query($sqlj);
	if($queryj->num_rows()>0){
		foreach($queryj->result_array() as $key => $value) {
			$sqljj="SELECT * FROM `tbltrans_detail` INNER JOIN `tblacode` 
			ON `tbltrans_detail`.`acode` = `tblacode`.`acode` where  `vno`='".$value['vno']."' ";
			 
			$queryjj = $this->db->query($sqljj)->result_array();
				if(!empty($queryjj))
				{
				 foreach($queryjj as $key => $value2) {
					$aname= $value2['aname'];
					}
				 
				$dataj[] = array(
				'vno' => $value['vno'],
				'vdate' => $value['vdate'],
				'camount' => $value['damount'],
				'aname' => $aname,
				'remarks'=>$value['remarks'],
				'chequedate'=>$value['chequedate'],
				'chequeno'=>$value['chequeno'],
				);

				}
		}
	}
return $dataj;
}




public function getpayments_new_old($data,$item=''){
 $fdate=$data['from_date']; 
 $tdate=$data['to_date'];
 $sale_point_id=$data['sale_point_id'];
 $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();

	$bank_codes= '';
	//$sqljj="select acode from tblacode where general='2004002000'";
	
	// $sqljj = "select sum(damount) as expense from tbltrans_detail where acode 
	// in( select acode from tblacode where general in ('4001002000','4001001000')) and vdate  BETWEEN '$fdate' AND '$tdate'";
  $expense_code=$fix_code['expense_code'];
 $acod=$expense_code[0].$expense_code[1].$expense_code[2].$expense_code[3].$expense_code[4].$expense_code[5];
    $sqljj = "select sum(damount) as expense from tbltrans_detail where LEFT(acode,6)= '$acod'  and LEFT(acode,7)!='4001011' and vdate  BETWEEN '$fdate' AND '$tdate'";
			 
	$queryjj = $this->db->query($sqljj)->row();
	
	

return $queryjj;
}


public function getpayments_new($data){
       $fdate=$data['from_date']; 
       $tdate=$data['to_date'];


       //  $fdate="2019-06-01"; 
       // $tdate="2019-09-03";


            $query1 = "SELECT opngbl,optype,phone_no,address,aname,acode,reg_date FROM `tblacode` WHERE LEFT(acode,6)= '400100' AND acode !='4001002001' AND acode !='4001001000'";

            $result = $this->db->query($query1);
            $line = $result->result_array();
   
    for ($i=0; $i<count($line); $i++) {


   $acode= $line[$i]['acode'];
   $query2 = $this->db->query("SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and vdate  BETWEEN '$fdate' AND '$tdate' ");

 
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


 foreach($line as $key => $value) { 
            $net_balace_exp=$net_balace_exp+$value['new_balance_pay'];
            }


  //pm($net_balace_exp);
    return $net_balace_exp;
 
    }






public function get_income($data,$item=''){
$fdate=$data['from_date'];
$tdate=$data['to_date'];

	$bank_codes= '';
	 
	$sqljj = "select sum(camount) as income from tbltrans_detail where acode 
	in( select acode from tblacode where general in ('3002001000')) and vdate  BETWEEN '$fdate' AND '$tdate'";
			 
	$queryjj = $this->db->query($sqljj)->row();
	
	

return $queryjj;
}




















     public function getpurchases($data,$item=''){
 
            $fdate=$data['from_date'];
            $tdate=$data['to_date'];
//pm($data);

        if($data['day']!='')
           {
                $day=$data['day']+1;

                $date_temp = $data['month'] .' '. $day.' '.$data['year'];

                $tdate = date('Y-m-d', strtotime($date_temp));
                $fdate=$tdate;
           }

 
            $acode= "";
            $condj = "";
            $condj1 = "";
            $condj2 = "";
            $condj3 = "";

            
               $sqlj="SELECT tbl_goodsreceiving.*,tblacode.* FROM `tbl_goodsreceiving` INNER JOIN `tblacode` 
			   ON `tbl_goodsreceiving`.`suppliercode` = `tblacode`.`acode` 
			   WHERE `receiptdate` BETWEEN '$fdate' AND '$tdate' $acode $condj $condj1";



   $queryj = $this->db->query($sqlj);
         
        if($queryj->num_rows()>0){

           

            foreach($queryj->result_array() as $key => $value) {

 
                $sqljj="SELECT itemid , (rate +vat_amount) as rate ,quantity , ex_vat_amount  ,tblmaterial_coding.catcode,tblmaterial_coding.itemnameint FROM `tbl_goodsreceiving_detail`
            	INNER JOIN `tblmaterial_coding` ON `tbl_goodsreceiving_detail`.`itemid` = `tblmaterial_coding`.`materialcode` where 
            	`receipt_detail_id`='".$value['receiptnos']."'
                $condj2 $condj3 ORDER BY `itemid` ASC";
 
                if($condj3 || $condj2){$brand='brand';}

                    $queryjj = $this->db->query($sqljj)->result_array();


                    if(!empty($queryjj))
                    {
                        $total_amt= $value['net_payable'];
                        $dataj[] = array(
                                        'issuenos' => $value['receiptnos'],
                                        'aname' => $value['aname'],
                                        'issuedate' => $value['receiptdate'],
                                        'total_amount' => $total_amt,
                                        'purchases'=>$queryjj,
                                        'brand'=>$brand,
                                    );

                    }
                }
        }
        
        return $dataj;



    }


 
   
}
 
?>