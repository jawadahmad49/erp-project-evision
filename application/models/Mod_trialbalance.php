<?php

class Mod_trialbalance extends CI_Model {

    function __construct() {

        parent::__construct();
         $this->load->model(array(
           "mod_common","mod_profitreport","mod_admin"
        ));

        //error_reporting(E_ALL);
    
    }

    public function netprofit($data1){
        //pm($data1);
          $data['from_date'] = $data1['fdate'];
        $data['to_date'] = $data1['tdate'];

        $salquery =  $this->db->query("SELECT SUM(sprice*qty) as totalamount,sum(qty*itemnameint/1000) as saleqty FROM `tbl_issue_goods` INNER join tbl_issue_goods_detail on tbl_issue_goods.issuenos=ig_detail_id inner join `tblmaterial_coding` ON `tbl_issue_goods_detail`.`itemid` = `tblmaterial_coding`.`materialcode` WHERE tbl_issue_goods.type='Fill' and issuedate 
            BETWEEN '".$data['from_date']."' and '".$data['to_date']."'")->result_array()[0];

        $returnquery = $this->db->query("select sum(tbl_issue_return_detail.qty*itemnameint/1000) as returnqty,sum(tbl_issue_return_detail.total_amount) as returnamount from tblmaterial_coding inner join tbl_issue_return_detail on materialcode=tbl_issue_return_detail.itemid inner join tbl_issue_return on tbl_issue_return.irnos=tbl_issue_return_detail.irnos where tblmaterial_coding.catcode=1 and tbl_issue_return_detail.type='Filled' and tbl_issue_return.type='salereturn' and irdate BETWEEN '".$data['from_date']."' and '".$data['to_date']."'")->result_array()[0];

         $cost_of_sales = $salquery['saleqty'] - $returnquery['returnqty'];


        $totalsaleamt = $salquery['totalamount'] - $returnquery['returnamount'];


         $netpurchaserate = $this->purchaserate($data['from_date'],$data['to_date']);

         $costofsaleamt = $netpurchaserate * $cost_of_sales;

        $grosprofit = $totalsaleamt - $costofsaleamt;


        $data_posted['from_date']=$data['from_date'];
        $data_posted['to_date']=$data['to_date'];

        $expenses=  $this->mod_profitreport->getpayments_new_old($data_posted,2)->expense;
        $otherincome =  $this->mod_profitreport->get_income($data_posted,2)->income;

        return $net_profit= $grosprofit - $expenses + $otherincome;
    }


    public function purchaserate($from,$to){

        $tankvaluee=0;
        $totaltnkqtyy=0;
        
        $tbltankquery = $this->db->query("select tbl_shop_opening.*,tblmaterial_coding.itemnameint from tbl_shop_opening  inner join tblmaterial_coding on tblmaterial_coding.materialcode = tbl_shop_opening.materialcode where tbl_shop_opening.type ='Filled' and date BETWEEN '$from' and '$to'");
        //pm($tbltankquery->result_array());
        foreach ($tbltankquery->result_array() as $key2 => $value2) {
            $totaltnkqtyy +=  (($value2['itemnameint'] * $value2['qty']))/1000;
            if($value2['cost_price'] != ""){
                $tankvaluee =  $value2['cost_price'];
            }
            
        }


        $query = $this->db->query("SELECT sum(tbl_goodsreceiving_detail.quantity *itemnameint/1000) as qty,SUM(inc_vat_amount) as totalamount FROM `tbl_goodsreceiving` inner join tbl_goodsreceiving_detail on tbl_goodsreceiving.receiptnos=tbl_goodsreceiving_detail.receipt_detail_id INNER join tblmaterial_coding on materialcode=tbl_goodsreceiving_detail.itemid WHERE catcode='1' and trans_typ='purchasefilled' and receiptdate BETWEEN '$from' and '$to'")->result_array()[0];
        
        $purchaseamt = $tankvaluee+$query['totalamount'];
        $purchaseqty = $totaltnkqtyy+$query['qty'];
        return $purchaseamt/$purchaseqty;
    }

    public function opening_stock($last_date) {     
            


        $all_brand=$this->mod_admin->get_all_brand();
        //pm($all_brand);

        $brand_count=0;
        foreach ($all_brand as $key => $value) {
                $brand_id=$value['brand_id']; 
                $date=date('Y-m-d');
                
                $where_item = array('catcode' =>1,'brandname' =>$brand_id);
                $all_brand[$brand_count]['item'] = $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_item);

                $new_i=0;
                $item_count=0;
            
                foreach ($all_brand[$brand_count]['item'] as $key => $value) {
                         $id=$value['materialcode'];
                         $itemnameint=$value['itemnameint'];

                         $today_stock=$this->mod_common->stock($id,'empty',$last_date,1);
                        //print_r(($today_stock)); echo "<br>";
                        
                         $empty_filled= explode('_', $today_stock);
                         $filled=$empty_filled[0] ;
                        $total_tonnage+=($itemnameint*$filled)/1000;    
                 }
        
        
        }   
        return ($total_tonnage); 
        //pm($total_tonnage);
     
    
    }

    public function filled_purchases($data){

        $data['from_date'] = $data['fdate'];
        $data['to_date'] = $data['tdate'];

        $last_date = strtotime($data['to_date'])+86400;
        $last_date = date("Y-m-d",$last_date);
        $closingqty = $this->opening_stock($last_date);
        $closingpurchaserate = $this->purchaserate($data['from_date'],$data['to_date']);
        return $closingpurchaserate*$closingqty;

    }

    function get_report_data($data){ 
        $count=0;
		
	 
        $myprofit = $this->netprofit($data);
        $filledpurchase = $this->filled_purchases($data); 


        $userid = $this->session->userdata('id');
        
        $fdate='';
        $tdate=''; 
        $rad_date='';
        $chk_rad=0;
        $chk_rad1=0;
        $scode='';
        $ecode='';
        $fdate = $data['fdate'];
        if(isset($data['inzero']))
        {
            $inzero=1;
        }
        else
        {
            $inzero=0;
        }

        $tdate = $data['tdate'];
        $rad_date=$data['cp'];
        $account_range=$data['acount_range'];
        $totalOpeningDebit=0;
        $totalOpeningCredit=0;
        $totalFPeriodDebit=0;
        $totalFPeriodCredit=0;
        $totalCurrentDebit=0;
        $totalCurrentCredit=0;

        $firstacount='';
        $lastaccount='';

        $this->db->select_max('acode', 'max');
        $this->db->select_min('acode', 'min');

        $line = $this->db->get('tblacode')->row_array();

        $Code = 1;

        $lastaccount=$line["max"];
        $firstacount=$line["min"];


        if($rad_date=='current')
        {
            $chk_rad=0;
            $fdate='2018-01-01';
            $tdate=date('Y-m-d');
            
        }
        if ($rad_date=='period')
        {
            $chk_rad=1;
            
        }
        if($account_range=='allac')
        {
            $chk_rad1=0;
        }
        if ($account_range=='range') 
        {
            
            $firstacount=$_POST['f_ac'];
            $lastaccount=$_POST['t1_ac'];
            $chk_rad1=1;

        }
        $userid=$this->session->userdata('id');
		
		// print $fdate;
		// print $tdate;
		// exit;
        ?>

<!DOCTYPE HTML>
<html>
<head><title>Financials </title>
    <link href="<?php echo SURL; ?>application/views/en/include/shv.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?php echo SURL; ?>assets/js/jquery-2.1.4.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo SURL; ?>application/views/en/include/style.css" />
</head>
<body>





    <?php
    $scode="";
    $ecode="";
    $z=1;
    $csv_hdr="";
    $csv_output="";
    $csv_hdr .= ",, ,Trial Balance \n"; 

    ?>

    <?php 
    $logo1='/assets/images/company/logo.png';
    ?>
    <table width="954px" align="center" border="0" class="imagetable">

        <tr align="center">
            <td colspan="3" ><img src="<?php print  SURL.$logo1 ;?>"> </td>
            <td colspan="18" width="100%"  align="center">
                <span align="center" style="color :#153860; font-family: times New Roman;   font-size: 22px;  height: 29px;">Trial Balance Report</span>
                <br>
                <span align="center" style="color :#153860; font-family: times New Roman;   font-size: 12px;  height: 29px;">
                    <?php if(($scode)&&($ecode)) {?>    A/C: <?php echo $scode; ?>-<?php echo $ecode;  "\n" ;     $csv_hdr .= " ,, A/C:,".$scode."-".$ecode." \n";  ?> <?php } ?>
                        <br> Date Range: <?php echo $fdate; ?>-<?php echo $tdate; 
						    $csv_hdr .= " ,, Date Range:,".$fdate."-".$tdate." \n"; 
    $csv_hdr .= " Account Code.,Account Title , Opening-Debit, Opening-Credit, For Period-Debit  ,For Period-Credit,Current-Debit,Current-Credit \n ";    
						?>
                        <br>  
                    </span> 
                </td>
            </tr>
        </table>
        <table width="954px" height="30"  align="center" class="imagetable"  >
            <thead>
                <tr>
                    <td ></td>
                    <td width="100"><strong>  </strong></td>
                    <tr>
                        <th style="width:40px">Account Code.</th>
                        <th style="width:90px">Account Title</th>
                        <th style="width:90px"> Opening-Debit</th>
                        <th style="width:90px"> Opening-Credit</th>
                        <th style="width:90px"> For Period-Debit</th>
                        <th style="width:90px"> For Period-Credit</th>
                        <th style="width:70px"> Current-Debit</th>
                        <th style="width:70px"> Current-Credit</th>
                    </tr>
                </thead>
                <tbody>
        <?php
 
		 
			  $query = "Delete from tbl_trialbalance_rpt where userid='$userid'";
			  $result = $this->db->query($query);


		
        $query_ins = $this->db->get_where('tblacode',array('acode >='=>$firstacount,'acode <='=>$lastaccount,'atype'=>"Child"))->result();
 
        //pm($query_ins);

        foreach ($query_ins as $key => $value) {
            $acode=$value->acode;
            
            $aname='';
            $general='';
            $opngbl='';
            $optype='';
            $cbalance='';
            $tcamount='';
            $tdamount='';
            $parent1='';

               $query_d = ("select distinct tba.aname,
                            tba.acode , tba.atype ,tba.general ,
                            tba.opngbl,tba.optype,
                            COALESCE(((SELECT sum(damount) -sum(camount)
                            from tbltrans_detail where acode=tba.acode 
                            and vdate>='$fdate' and vdate<='$tdate' )),0) as cbalance,
                            COALESCE((SELECT sum(camount) from tbltrans_detail
                            where acode=tba.acode and vdate>='$fdate' and vdate<='$tdate' ),0) as 'tcamount' ,COALESCE((SELECT 
                                sum(damount) from tbltrans_detail where acode=tba.acode and vdate>='$fdate' and 
                                vdate<='$tdate' ),0) as 'tdamount',(select general from tblacode where acode=tba.general)  as 'parent1'
                                from tblacode tba WHERE tba.acode='$acode'");
 
            $qres = $this->db->query($query_d)->row_array();
            $aname=$qres['aname'];
              $general=$qres['general'];
            $opngbl=$qres['opngbl'];
            $optype=$qres['optype'];
            $cbalance=$qres['cbalance'];
            $tcamount=$qres['tcamount'];
            $tdamount=$qres['tdamount'];
            $parent1=$qres['parent1'];

            if($chk_rad==1){
                $opening_debit=0;
                $opening_credit=0;

                $query21 = "SELECT COALESCE(sum(damount),0) as op_damount ,COALESCE(sum(camount),0) as op_camount
                FROM `tbltrans_detail`
                WHERE acode ='$acode' and vdate < '$fdate'";
             
                $result1= $this->db->query($query21)->row_array();
                
                $op_damount = $result1['op_damount'];
                $op_camount = $result1['op_camount'];

                $opbalance=$op_damount-$op_camount;
                $opening_balance=$opbalance;
                $total_opngbl = 0;
                if($optype=='Credit')
                {
                    $opngbl = -1*($opngbl) ;
                }
                $total_opngbl = ($op_damount  -$op_camount )+($opngbl);
         
            if($total_opngbl<0){$optype='Credit'; }else{$optype='Debit';
                }
            } 
            else
            {            
                if( $optype=='Credit'){ $total_opngbl =-($opngbl);}else{$total_opngbl =($opngbl);}
            }

            $query_p2=("select general from tblacode where acode=(select general from tblacode where acode='$general')");

            $qres_=$this->db->query($query_p2)->row_array();

            $parent2=$qres_['general'];
            if($parent2=="" && $parent1!='')
                $parent2=$parent1;

			
			
			// print "INSERT INTO `tbl_trialbalance_rpt`(`aname`, `acode`, `atype`, `general`, `opbalance`, `optype`, `cbalance`, `tcamount`, `tdamount`, `parent1`, `parent2`,userid)
             // VALUES 
             // ('$aname','$acode','$atype','$general','$total_opngbl','$optype','$cbalance','$tcamount','$tdamount','$parent1','$parent2','$userid')";
			
			// print '<br>';
			// print '<br>';
			
            $ins = $this->db->query("INSERT INTO `tbl_trialbalance_rpt`(`aname`, `acode`, `atype`, `general`, `opbalance`, `optype`, `cbalance`, `tcamount`, `tdamount`, `parent1`, `parent2`,userid)
            VALUES 
            ('$aname','$acode','$atype','$general','$total_opngbl','$optype','$cbalance','$tcamount','$tdamount','$parent1','$parent2','$userid')");
        }


        $query_parent="select distinct parent2 from tbl_trialbalance_rpt where parent2!='' and userid='$userid' order by parent2 ";
        $result_parent=$this->db->query($query_parent)->result_array();
       foreach ($result_parent as $key => $line)
        {
            $cbalance_credit=0;
            $cbalance_debit=0;
            $opbalance_debit=0;
            $opbalance=0;
            $opbalance_credit=0;
            $tdamount=0;
            $tcamount=0;
            $cbalance=0;
            $parent2=$line['parent2'];

            $q_c_opc=("select sum(opbalance) as 'opbalance_credit' from tbl_trialbalance_rpt where parent2='$parent2' and optype='Credit' and userid='$userid'");
            $res_opc=$this->db->query($q_c_opc)->row_array();

            $opbalance_credit=$res_opc['opbalance_credit'];

            $q_c_opd=("select sum(opbalance) as 'opbalance_debit' from tbl_trialbalance_rpt where parent2='$parent2' and optype='Debit' and userid='$userid'");
            $res_opd=$this->db->query($q_c_opd)->row_array();

            $opbalance_debit=$res_opd['opbalance_debit'];
            
            $opbalance=$opbalance_debit+$opbalance_credit;

            if($opbalance>0)
            {
                $opbalance_debit=$opbalance;
                $opbalance_credit=0;
            }
            else
            {
                $opbalance_credit=(-1)*$opbalance;
                $opbalance_debit=0;
            }

       

            $q_c=("select (select aname from tblacode where acode='$parent2') as 'aname',sum(cbalance) as 'cbalance',sum(tcamount) as 'tcamount',sum(tdamount) as 'tdamount' from tbl_trialbalance_rpt where  parent2='$parent2' and userid='$userid'");
            
            $res_c=$this->db->query($q_c)->row_array();
            //$cbalance=$res_c['cbalance'];
            $tcamount=$res_c['tcamount'];
            $tdamount=$res_c['tdamount'];
            $aname=$res_c['aname'];
            $parent1=$res_c['parent1'];
            $fp_bal=$tdamount-$tcamount;
            
            $cbalance=$opbalance_debit-$opbalance_credit+$tdamount-$tcamount;

            if($cbalance>0)
            {
                $cbalance_debit=$cbalance;
                $cbalance_credit=0;
            }
            else
            {
                $cbalance_credit=(-1)*$cbalance;
                $cbalance_debit=0;
            }

            

?>
<!-- top parent code is here below -->
<tr>
    <td style="cursor: pointer; text-decoration: underline;" onclick="voucher_click('<?php echo $parent2."_".$aname; ?>')"><strong><?php echo $parent2; $csv_output .=trim($parent2).","; ?></strong></td>
    <td><strong><?php echo strtoupper($aname); $csv_output .=trim($aname).","; ?></strong></td>
    <td align="right"><strong><?php echo number_format($opbalance_debit);   $csv_output .=trim($opbalance_debit).","; ?></strong></td>
    <td align="right"><strong><?php echo number_format($opbalance_credit);  $csv_output .=trim($opbalance_credit).","; ?></strong></td>
    <td align="right"><strong><?php echo number_format($tdamount);      $csv_output .=trim($tdamount).","; ?></strong></td>
    <td align="right"><strong><?php echo number_format($tcamount);      $csv_output .=trim($tcamount).",";?></strong></td>
    <td align="right"><strong><?php echo number_format($cbalance_debit);    $csv_output .=trim($cbalance_debit).",";?></strong></td>
    <td align="right"><strong><?php echo number_format($cbalance_credit);   $csv_output .=trim($cbalance_credit)."\n";?></strong></td>


    </tr>
<?php

        $query_parent1="select distinct parent1 from tbl_trialbalance_rpt where parent2='$parent2' and userid='$userid'";
        $result_parent1=$this->db->query($query_parent1)->result_array();

        foreach ($result_parent1 as $key=>$line1)
        {
            $cbalance_credit1=0;
            $cbalance_debit1=0;
            $opbalance_debit1=0;
            $opbalance1=0;
            $opbalance_credit1=0;
            $tdamount1=0;
            $tcamount1=0;
            $cbalance1=0;
            $parent1=$line1['parent1'];

            $q_c_opc1=("select sum(opbalance) as 'opbalance_credit' from tbl_trialbalance_rpt where parent1='$parent1' and optype='Credit' and userid='$userid'");
            $res_opc1=$this->db->query($q_c_opc1)->row_array();
              $opbalance_credit1=$res_opc1['opbalance_credit'];
            $q_c_opd1=("select sum(opbalance) as 'opbalance_debit' from tbl_trialbalance_rpt where parent1='$parent1' and optype='Debit' and userid='$userid'");
            $res_opd1=$this->db->query($q_c_opd1)->row_array();
            $opbalance_debit1=$res_opd1['opbalance_debit'];

            $opbalance1=$opbalance_debit1+$opbalance_credit1;
            if($opbalance1>0)
            {
                $opbalance_debit1=$opbalance1;
                $opbalance_credit1=0;
            }
            else
            {
                $opbalance_credit1=(-1)*$opbalance1;
                $opbalance_debit1=0;
            }

              $q_c1=("select (select aname from tblacode where acode='$parent1') as 'aname',sum(cbalance) as 'cbalance',
            sum(tcamount) as 'tcamount',sum(tdamount) as 'tdamount' from tbl_trialbalance_rpt where  
            parent1='$parent1' and userid='$userid' ");

            $res_c1=$this->db->query($q_c1)->row_array();
			
		  
            //$cbalance1=$res_c1['cbalance'];
            $tcamount1=$res_c1['tcamount'];
             $tdamount1=$res_c1['tdamount'];
            $aname1=$res_c1['aname'];
        //$parent1=$res_c1['parent1'];
            $fp_bal1=$tdamount1-$tcamount1;

            $cbalance1=$opbalance_debit1-$opbalance_credit1+$tdamount1-$tcamount1;
            if($cbalance1>0)
            {
                $cbalance_debit1=$cbalance1;
                $cbalance_credit1=0;
            }
            else
            {
                $cbalance_credit1=(-1)*$cbalance1;
                $cbalance_debit1=0;
            }
            ?>
<tr>
    <td style="cursor: pointer; text-decoration: underline;" onclick="voucher_click('<?php echo $parent1."_".$aname1; ?>')">
        <strong><?php echo $parent1; $csv_output .=trim($parent1).","; ?></strong></td>
        <td> <strong><?php echo strtoupper($aname1); $csv_output .=trim($aname1).","; ?></strong></td>
        <td align="right"><strong><?php echo number_format($opbalance_debit1);  $csv_output .=trim($opbalance_debit1).","; ?></strong></td>
        <td align="right"><strong><?php echo number_format($opbalance_credit1);   $csv_output .=trim($opbalance_credit1).","; ?></strong></td>
        <td align="right"><strong><?php echo number_format($tdamount1);  $csv_output .=trim($tdamount1).","; ?></strong></td>
        <td align="right"><strong><?php echo number_format($tcamount1); $csv_output .=trim($tcamount1).",";?></strong></td>
        <td align="right"><strong><?php echo number_format($cbalance_debit1);   $csv_output .=trim($cbalance_debit1).",";?></strong></td>
        <td align="right"><strong><?php echo number_format($cbalance_credit1);  $csv_output .=trim($cbalance_credit1)."\n";?></strong></td>
    </tr>

<?php
                $query_parent12="select distinct general from tbl_trialbalance_rpt where parent1='$parent1' and userid='$userid'";
                $result_parent12=$this->db->query($query_parent12)->result_array();
                foreach ($result_parent12 as $key => $line12)
                {
                    $cbalance_credit12=0;
                    $cbalance_debit12=0;
                    $opbalance_debit12=0;
                    $opbalance12=0;
                    $opbalance_credit12=0;
                    $tdamount12=0;
                    $tcamount12=0;
                    $cbalance12=0;
                    $general=$line12['general'];

                    $q_c_opc12=("select sum(opbalance) as 'opbalance_credit' from tbl_trialbalance_rpt where general='$general' and optype='Credit' and userid='$userid'");
                    $res_opc12=$this->db->query($q_c_opc12)->row_array();

                    $opbalance_credit12=$res_opc12['opbalance_credit'];
                    $q_c_opd12=("select sum(opbalance) as 'opbalance_debit' from tbl_trialbalance_rpt where general='$general' and optype='Debit' and userid='$userid'");
                    $res_opd12=$this->db->query($q_c_opd12)->row_array();

                    $opbalance_debit12=$res_opd12['opbalance_debit'];
                    
                    $opbalance12=$opbalance_debit12+$opbalance_credit12;

                    if($opbalance12>0)
                    {
                        $opbalance_debit12=$opbalance12;
                        $opbalance_credit12=0;
                    }
                    else
                    {
                        $opbalance_credit12=(-1)*$opbalance12;
                        $opbalance_debit12=0;
                    }

                    $q_c12=("select (select aname from tblacode where acode='$general') as 'aname',sum(cbalance) as 'cbalance',sum(tcamount) as 'tcamount',sum(tdamount) as 'tdamount' from tbl_trialbalance_rpt where  general='$general'  and userid='$userid' ");

                    $res_c12=$this->db->query($q_c12)->row_array();

                    $tcamount12=$res_c12['tcamount'];
                    $tdamount12=$res_c12['tdamount'];
                    $aname12=$res_c12['aname'];

                    $fp_bal12=$tdamount12-$tcamount12;
                  
                    $cbalance12=$opbalance_debit12-$opbalance_credit12+$tdamount12-$tcamount12;
                    if($cbalance12>0)
                    {
                        $cbalance_debit12=$cbalance12;
                        $cbalance_credit12=0;
                    }
                    else
                    {
                        $cbalance_credit12=(-1)*$cbalance12;
                        $cbalance_debit12=0;
                    }

                    if($general == "1001005000"){
                        $tdamount12 = $myprofit;
                        $cbalance_debit12 = $myprofit;
                    }

                    ?>

<!-- ===========================+++++waqas task is here belwo ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++-->
                
                <tr>
                    <td style="cursor: pointer; text-decoration: underline;" onclick="voucher_click('<?php echo $general."_".$aname1; ?>')">
                    <strong>   <?php echo $general; $csv_output .=trim($general).","; ?></strong></td>
                    <td> <strong><?php echo strtoupper($aname12);  $csv_output .=trim($aname12).","; ?></strong></td>
                    <td align="right"><strong><?php echo number_format($opbalance_debit12);     $csv_output .=trim($opbalance_debit12).","; ?></strong></td>
                    <td align="right"><strong><?php echo number_format($opbalance_credit12);    $csv_output .=trim($opbalance_credit12).","; ?></strong></td>
                    <td align="right"><strong><?php echo number_format($tdamount12);        $csv_output .=trim($tdamount12).","; ?></strong></td>
                    <td align="right"><strong><?php echo number_format($tcamount12);        $csv_output .=trim($tcamount12).",";?></strong></td>
                    <td align="right"><strong><?php echo number_format($cbalance_debit12);      $csv_output .=trim($cbalance_debit12).",";?></strong></td>
                    <td align="right"><strong><?php echo number_format($cbalance_credit12);     $csv_output .=trim($cbalance_credit12)."\n";?></strong></td>
                </tr>

<?php
                    $query_parent123="select  acode from tbl_trialbalance_rpt where general='$general' and userid='$userid' ";
                    $result_parent123=$this->db->query($query_parent123)->result_array();
                    foreach ($result_parent123 as $key => $value)
                    {
                        $cbalance_credit123=0;
                        $cbalance_debit123=0;
                        $opbalance_debit123=0;
                        $opbalance123=0;
                        $opbalance_credit123=0;
                        $tdamount123=0;
                        $tcamount123=0;
                        $cbalance123=0;
                        $acode=$value['acode'];

                        $q_c_opc123=("select sum(opbalance) as 'opbalance_credit', optype from tbl_trialbalance_rpt where acode='$acode'   and userid='$userid'");
                        $res_opc123=$this->db->query($q_c_opc123)->row_array();
                        $opbalance123=$res_opc123['opbalance_credit'];
                        $optype=$res_opc123['optype'];
                        
                        
                        if($optype=='Credit'){
                             $opbalance_credit123=(-1)*$opbalance123;
                            
                        }else{
                             $opbalance_debit123=$opbalance123;
                            
                        }


                        


                        $q_c_opd123=("select sum(opbalance) as 'opbalance_debit' from tbl_trialbalance_rpt where acode='$acode' and optype='Debit' and userid='$userid'");
                        $res_opd123=$this->db->query($q_c_opd123)->row_array();
                        $opbalance_debit123=$res_opd123['opbalance_debit'];

                           $q_c123=("select (select aname from tblacode where acode='$acode') as 'aname',
						  sum(cbalance) as 'cbalance',sum(tcamount) as 'tcamount',sum(tdamount) as 'tdamount' 
						  from tbl_trialbalance_rpt where  acode='$acode' and userid='$userid' ");

						//  print '<br>';
                        $res_c123=$this->db->query($q_c123)->row_array();
                        $cbalance123=$res_c123['cbalance'];
                        $tcamount123=$res_c123['tcamount'];
                        $tdamount123=$res_c123['tdamount'];
                        $aname123=$res_c123['aname'];

                        $fp_bal123=$tdamount123-$tcamount123;
                       
                            $cbalance123=$opbalance_debit123-$opbalance_credit123+$tdamount123-$tcamount123;
                            if($cbalance123>0)
                            {
                                $cbalance_debit123=$cbalance123;
                                $cbalance_credit123=0;
                            }
                            else
                            {
                                $cbalance_credit123=(-1)*$cbalance123;
                                $cbalance_debit123=0;
                            }

                            $totalOpeningDebit=$totalOpeningDebit+$opbalance_debit123;
                            $totalOpeningCredit=$totalOpeningCredit+$opbalance_credit123;
                            $totalFPeriodCredit=$totalFPeriodCredit+$tcamount123;
                            $totalFPeriodDebit=$totalFPeriodDebit+$tdamount123;
                            $totalCurrentCredit=$totalCurrentCredit+$cbalance_credit123;
                            $totalCurrentDebit=$totalCurrentDebit+$cbalance_debit123;

                            if($z==1){
                                $totalCurrentDebit += $myprofit;
                                $totalFPeriodDebit += $myprofit;
                            }
                              $z++;
                            

                            ?>
 
                            <tr>
                            <td style="cursor: pointer; text-decoration: underline;" onclick="voucher_click('<?php echo $acode."_".$aname1; ?>')">
                                <?php echo $acode;   $csv_output .=trim($acode).","; ?></td>
                                <td> <?php echo strtoupper($aname123);   $csv_output .=trim($aname123).","; ?></td>
                                <td align="right"><?php echo number_format($opbalance_debit123);    $csv_output .=trim($opbalance_debit123).","; ?></td>
                                <td align="right"><?php //echo number_format($opbalance_credit123);   $csv_output .=trim($opbalance_credit123).","; ?></td>
                                <td align="right"><?php echo number_format($tdamount123);       $csv_output .=trim($tdamount123).","; ?></td>
                                <td align="right"><?php echo number_format($tcamount123);       $csv_output .=trim($tcamount123).",";?></td>
                                <td align="right"><?php echo number_format($cbalance_debit123);     $csv_output .=trim($cbalance_debit123).",";?></td>
                                <td align="right"><?php echo number_format($cbalance_credit123);    $csv_output .=trim($cbalance_credit123)."\n";?></td>
                            </tr>

            <?php
                    }
                 }      
            }
        }
        ?>

        <tr class="exist_rec_sb">
                <td colspan="2" align="right" style="padding-right:15px"><strong>Total <?php    $csv_output .=trim(',Total').","; ?></strong></td>
                <td align="right"> <strong><?php echo number_format($totalOpeningDebit);        $csv_output .=trim($totalOpeningDebit).","; ?></strong></td>
                <td align="right"><strong><?php  echo number_format($totalOpeningCredit);   $csv_output .=trim($totalOpeningCredit).","; ?></strong></td>
                <td align="right"> <strong><?php echo number_format($totalFPeriodDebit);    $csv_output .=trim($totalFPeriodDebit).","; ?></strong></td>
                <td align="right"><strong><?php  echo number_format($totalFPeriodCredit);   $csv_output .=trim($totalFPeriodCredit).","; ?></strong></td>
                <td align="right"> <strong><?php echo number_format($totalCurrentDebit);    $csv_output .=trim($totalCurrentDebit).","; ?></strong></td>
                <td align="right"><strong><?php  echo number_format($totalCurrentCredit);   $csv_output .=trim($totalCurrentCredit)."\n"; ?></strong></td>
            </tr>

            <td colspan="10" class="odd_frm_top" align="center">		   <form name="export1" action="<?php echo SURL."Common/export"?>" method="post">
                <input type="button" value="" class="export_excel_btn" onClick="exportfile()">
                <input type="hidden" value="<?php echo $csv_hdr; ?>" name="csv_hdr" id="csv_hdr">
                <input type="hidden" value="<?php echo $csv_output; ?>" name="csv_output" id="csv_output">
            </form></td>
        </tr>
    </tbody>

</table>


                            

                <script type="text/javascript">
                    function printVoucher()
                    {
                        var printContents = document.getElementById('printable').innerHTML;
                        var originalContents = document.body.innerHTML;
                        document.body.innerHTML = printContents;
                        window.print();
                        document.body.innerHTML = originalContents;
                    }
                    function exportfile()
                    {
    //alert(document.getElementById("csv_output").value);
    document.export1.submit();
}
function voucher_click(acode_aname)
{
    res=acode_aname.split("_");
    acode=res[0];
    aname=res[1];
    var fdate='<?php echo $fdate?>';
    var tdate= '<?php echo $tdate; ?>';
    if(tdate=="")
    {
        tdate='<?php echo date('Y-m-d'); ?>'
    }

    var url = 'ledger_report';
    var form = $('<form action="' + url + '" method="post">' +
        '<input type="hidden" name="from_date" value="' + fdate + '" />' + 
        '<input type="hidden" name="to_date" value="' + tdate + '" />'+
        '<input type="hidden" name="acode" value="' + acode + '" />'+
        '<input type="hidden" name="aname" value="' + aname + '" />'+
        '<input type="hidden" name="filter" value="' + 'party' + '" />'+
        '<input type="hidden" name="sort" value="vno" />'+
        '</form>');
    $('body').append(form);
    form.submit();

}
</script>
</body>
</html>

<?php
    }
}

?>