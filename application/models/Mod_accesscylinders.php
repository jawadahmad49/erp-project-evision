<?php

class Mod_accesscylinders extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }

       public function get_details($data){


        $fdate=$data['to_date'];
        
        $thirtydays=0;
        $sixtydays=0;
        $nintydays=0;
        $morethan_nightdays=0;
        $Total=0;
        
      $fifteendays_fdate=date('Y-m-d', strtotime($fdate.'-15 days'));
     
       $fifteendays_tdate=$fdate;
    
     
     $thirtydays_fdate=date('Y-m-d', strtotime($fdate.'-30 days'));
     $thirtydays_tdate=date('Y-m-d',strtotime($fifteendays_fdate.'-1 days'));
     
     $fourtyfivedays_fdate=date('Y-m-d', strtotime($fdate.'-45 days'));
     $fourtyfivedays_tdate=date('Y-m-d',strtotime($thirtydays_fdate.'-1 days'));
     
     $sixtydays_fdate=date('Y-m-d', strtotime($fdate.'-60 days'));
     $sixtydays_tdate=date('Y-m-d',strtotime($fourtyfivedays_fdate.'-1 days'));
     
     $seventyfivedays_fdate=date('Y-m-d', strtotime($fdate.'-75 days'));
     $seventyfivedays_tdate=date('Y-m-d',strtotime($sixtydays_fdate.'-1 days'));
     
     
     $nintydays_fdate=date('Y-m-d', strtotime( $fdate.'-90 days'));
     $nintydays_tdate=date('Y-m-d',strtotime($seventyfivedays_fdate.'-1 days'));    
     $morethan_nighty_fdate='2016-01-01';
     $morethan_nighty_tdate=date('Y-m-d',strtotime($nintydays_fdate.'-1 days'));



        $total_fifteen=0;
        $total_thirty=0;
        $total_fourtyfive=0;
        $total_sixty=0;
        $total_seventyfive=0;
        $total_ninty=0;
        $total_more=0;
        $grand_total=0;



        $query = "select  opngbl,optype,  acode,aname from tblacode where LEFT(acode,4)='2004' and acode!='2004000000' order by acode ";


           
            //pm($qprice_result);

                $subtotal=0;
                $item_count=0;   
                $materialcode=0;
                $count=1;



        $res3_main_top = $this->db->query($query);


         foreach ($res3_main_top->result_array() as $key => $line3) {

                $total_received_from_party=0;
                $fifteendays=0;
                $thirtydays=0;
                $fourtyfivedays=0;
                $sixtydays=0;
                $seventyfivedays=0;
                $nintydays=0;
                $morethan_nightdays=0;
               // $sdate=$line3['aname'];
                $acode=$line3['acode'];
                $aname=$line3['aname'];
                $optype=$line3['optype'];
                $opngbl=$line3['opngbl'];

               // exit();
                //// here we get total receive amount 

            
                        $query2=$this->db->query("select coalesce(sum(camount),0) as camount from tbltrans_detail where    
                        tbltrans_detail.acode='$acode'   
                        ");

                                

                    foreach ($query2->result_array() as $key => $res2) {

                            $total_received_from_party=$res2['camount'];
                        }
                            
    if($optype=='Credit') { $total_received_from_party+=$opngbl;} else if($optype=='Debit') {$total_received_from_party-=$opngbl;}
                
              
                    $total_120_days=0;
                    $query3=$this->db->query("select coalesce(sum(damount),0) as total_120_days from tbltrans_detail where    
                    tbltrans_detail.acode='$acode' 
                     and tbltrans_detail.vdate between '$morethan_nighty_fdate' and '$morethan_nighty_tdate'");
                   
                     foreach ($query3->result_array() as $key => $res1) {


                        $balance=0;
                     
                    
                
                        $total_120_days=$res1['total_120_days'];
                     
                        $balance=$total_120_days-$total_received_from_party;
                        if($balance<0){$balance=0; $total_received_from_party=$total_received_from_party-$total_120_days; } 
                        if($balance>0){ $total_received_from_party=0;} 

         
                        $morethan_nightdays+=$balance;
                        $total_more+=$morethan_nightdays;
                    }
          
                    $total_90_days=0;
                    
                    $query3=$this->db->query("select coalesce(sum(damount),0) as total_90_days from tbltrans_detail where    
                    tbltrans_detail.acode='$acode'   
                    and tbltrans_detail.vdate between '$nintydays_fdate' and '$nintydays_tdate'");
                    
                    foreach ($query3->result_array() as $key => $res1) {
                    
                        $balance=0;
                     
                    
                
                        $total_90_days=$res1['total_90_days'];
                     
                        $balance=$total_90_days-$total_received_from_party;
                        if($balance<0){$balance=0; $total_received_from_party=$total_received_from_party-$total_90_days; } 
                        if($balance>0){ $total_received_from_party=0;} 
                        
                     
                        $nintydays+=$balance;
                        $total_ninty+=$nintydays;
                    }
                    
                    
                    
                    
                    $total_75_days=0;
                    
                    $query3=$this->db->query("select coalesce(sum(damount),0) as total_75_days from tbltrans_detail where    
                    tbltrans_detail.acode='$acode'   
                    and tbltrans_detail.vdate between '$seventyfivedays_fdate' and '$seventyfivedays_tdate'");
                    foreach ($query3->result_array() as $key => $res1)
                    {   
                        $balance=0;
                     
                    
                
                        $total_75_days=$res1['total_75_days'];
                     
                        $balance=$total_75_days-$total_received_from_party;
                        if($balance<0){$balance=0; $total_received_from_party=$total_received_from_party-$total_75_days; } 
                        if($balance>0){ $total_received_from_party=0;} 
                        
                     
                        $seventyfivedays+=$balance;
                        $total_seventyfive+=$seventyfivedays;
                    }
                    
                    
                    $total_60_days=0;
                 
                    $query3=$this->db->query("select coalesce(sum(damount),0) as total_60_days from tbltrans_detail where    
                    tbltrans_detail.acode='$acode'  
                    and tbltrans_detail.vdate between '$sixtydays_fdate' and '$sixtydays_tdate'");
                    foreach ($query3->result_array() as $key => $res1)
                    {   
                        $balance=0;
                     
                    
                
                        $total_60_days=$res1['total_60_days'];
                     
                        $balance=$total_60_days-$total_received_from_party;
                        if($balance<0){$balance=0; $total_received_from_party=$total_received_from_party-$total_60_days; } 
                        if($balance>0){ $total_received_from_party=0;} 
                        
                     
                        $sixtydays+=$balance;
                        $total_sixty+=$sixtydays;
                    }
                    
                    
                    
                    $total_45_days=0;
                 
                    $query3=$this->db->query("select coalesce(sum(damount),0) as total_45_days from tbltrans_detail where    
                    tbltrans_detail.acode='$acode'  
                    and tbltrans_detail.vdate between '$fourtyfivedays_fdate' and '$fourtyfivedays_tdate'");
                   foreach ($query3->result_array() as $key => $res1)
                    {   
                        $balance=0;
                     
                    
                
                        $total_45_days=$res1['total_45_days'];
                     
                        $balance=$total_45_days-$total_received_from_party;
                        if($balance<0){$balance=0; $total_received_from_party=$total_received_from_party-$total_45_days; } 
                        if($balance>0){ $total_received_from_party=0;} 
                        
                     
                        $fourtyfivedays+=$balance;
                        $total_fourtyfive+=$fourtyfivedays;
                    }
                    
                    
                    $total_30_days=0;
                    $query1=$this->db->query("select coalesce(sum(damount),0) as total_30_days from tbltrans_detail where    
                    tbltrans_detail.acode='$acode' 
                    and tbltrans_detail.vdate between '$thirtydays_fdate' and '$thirtydays_tdate'");
                    //   
                        foreach ($query1->result_array() as $key => $res1)
                          {


                        $balance=0;
                     
                    
                
                        $total_30_days=$res1['total_30_days'];
                     
                        $balance=$total_30_days-$total_received_from_party;
                        if($balance<0){$balance=0; $total_received_from_party=$total_received_from_party-$total_30_days; } 
                        if($balance>0){ $total_received_from_party=0;} 
                        
                        
                        $thirtydays+=$balance;
                        $total_thirty+=$thirtydays;
                    }

 
 
                    $total_15_days=0;
                    $query1=$this->db->query("select coalesce(sum(damount),0) as total_15_days from tbltrans_detail where    
                    tbltrans_detail.acode='$acode' 
                    and tbltrans_detail.vdate between '$fifteendays_fdate' and '$fifteendays_tdate'");
                     

                    foreach ($query1->result_array() as $key => $res1)
                         {

                        $balance=0;
                     
                    
                
                        $total_15_days=$res1['total_15_days'];
                     
                        $balance=$total_15_days-$total_received_from_party;
                        if($balance<0){$balance=0; $total_received_from_party=$total_received_from_party-$total_15_days; } 
                        if($balance>0){ $total_received_from_party=0;} 
                        
                        
                        $fifteendays+=$balance;
                        $total_fifteen+=$fifteendays;
                    }

                    
                $Total=$thirtydays+$fifteendays+$fourtyfivedays+$sixtydays+$seventyfivedays+$nintydays+$morethan_nightdays;
                
                $grand_total+=$Total;

       

                $datas[] = array(
                    'acode' => $acode,
                    'aname' => $aname,
                    'fifteendays' => $fifteendays,
                    'thirtydays'=>$thirtydays,
                    'fourtyfivedays'=>$fourtyfivedays,
                    'sixtydays'=>$sixtydays,
                    'seventyfivedays'=>$seventyfivedays,
                    'nintydays'=>$nintydays,
                    'morethan_nightdays'=>$morethan_nightdays,
                    'Total'=>$Total,
                    //'filledstock'=>$filledstock,
                );
            
  }

        //pm($datas);

        return $datas;
    }
	
	
	
	
	
	
	
	
	
	
	
	
	
	
    public function get_total_customer_stock($to_date){

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
           
                // $sqlcot = "SELECT qty as opening FROM `tbl_customer_opening` WHERE acode ='$acode' AND materialcode ='$itemid'";// AND materialcode ='$itemid'COALESCE(SUM(`qty`),0) as opening
                // $querycot = $this->db->query($sqlcot);
                // $rowcot = $querycot->row_array();
 

				   $sqls = " SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`-`tbl_issue_goods_detail`.`returns`),0) as sale  
				  FROM `tbl_issue_goods` 
				INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` 
				WHERE  `issuedto` ='$acode' AND `tbl_issue_goods_detail`.`wrate`=0 
				AND `tbl_issue_goods_detail`.`returns`>0 AND `tbl_issue_goods_detail`.`itemid`='$itemid'
				and  tbl_issue_goods.issuedate<='$to_date'" ;

				$querys = $this->db->query($sqls)->row_array();

            $sqlr = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returns
			FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` 
			ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `scode` ='$acode' 
			AND `tbl_issue_return_detail`.`itemid`='$itemid' 	and  tbl_issue_return.irdate<='$to_date' ";
             
            $queryr = $this->db->query($sqlr)->row_array();

 
		// print '---opening:'.$rowcot['opening'];
		// print '---returns:'.$queryr['returns'];
		// print '---sale:'.$querys['sale'];
		$rowcot['opening']=0;
                $opening_balance= $rowcot['opening']-$querys['sale']-$queryr['returns'];

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
	
	
    public function get_total_business_stock($to_date){

            $query1 = "SELECT opngbl,optype,phone_no,address,aname,acode FROM `tblacode`
			WHERE LEFT(acode,7)= '1001001' AND acode !='1001001000'"; 

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
           
                // $sqlcot = "SELECT qty as opening FROM `tbl_customer_opening` WHERE acode ='$acode' AND materialcode ='$itemid'";// AND materialcode ='$itemid'COALESCE(SUM(`qty`),0) as opening
                // $querycot = $this->db->query($sqlcot);
                // $rowcot = $querycot->row_array();
 

				    $sqls = " SELECT COALESCE(SUM(`tbl_goodsreceiving_detail`.`quantity`-`tbl_goodsreceiving_detail`.`ereturn`),0) as sale  
				  FROM `tbl_goodsreceiving` 
				INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` 
				WHERE  `suppliercode` ='$acode'  
				AND `tbl_goodsreceiving_detail`.`ereturn`>0 AND `tbl_goodsreceiving_detail`.`itemid`='$itemid'
				and  tbl_goodsreceiving.receiptdate<='$to_date'" ;

				$querys = $this->db->query($sqls)->row_array();

            // $sqlr = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returns
			// FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` 
			// ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `scode` ='$acode' 
			// AND `tbl_issue_return_detail`.`itemid`='$itemid' 	and  tbl_issue_return.irdate<='$to_date' ";
             
            // $queryr = $this->db->query($sqlr)->row_array();

 
		// print '---opening:'.$rowcot['opening'];
		// print '---returns:'.$queryr['returns'];
	 //print '---sale:'.$querys['sale'];
		$rowcot['opening']=0;
		$queryr['returns']=0;
                $opening_balance= $rowcot['opening']-$querys['sale']-$queryr['returns'];

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
}

?>