<?php
class Mod_admin extends CI_Model {
    function __construct() {
        parent::__construct();
    }
   public function manage_salelpg(){
        $this->db->select('tbl_issue_goods.*,tblacode.*,SUM(tbl_issue_goods_detail.total_amount) as amounttotal');
        $this->db->from('tbl_issue_goods');
        $this->db->join('tblacode', 'tbl_issue_goods.issuedto = tblacode.acode');
        $this->db->join('tbl_issue_goods_detail', ' tbl_issue_goods_detail.ig_detail_id= tbl_issue_goods.issuenos');
        $this->db->where('issuedate=',date("Y-m-d"));
        $this->db->group_by('ig_detail_id');
        $this->db->order_by("issuenos", "desc");
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getmonthly_stock($data){
        $start_date=date('Y-m-d',strtotime('-1 month'));
        $start_date=date('Y-m-01');
        $end_date=date('Y-m-d');
        if($data!='')
        {
            $month=$data['chart_month'];
            $year=$data['chart_year'];
            $timestamp    = strtotime("$month" . "$year");
            $start_date = date('Y-m-01', $timestamp);
             $end_date  = date('Y-m-t', $timestamp);
        }
             $sqlcot = "SELECT COALESCE(sum((select itemnameint from tblmaterial_coding where catcode='1' AND materialcode=d.itemid) *d.qty),0)/1000 as totala,m.issuedate from tbl_issue_goods_detail d , tbl_issue_goods m where d.ig_detail_id= m.issuenos AND issuedate BETWEEN '$start_date' AND '$end_date' group by m.issuedate";
            $querycot = $this->db->query($sqlcot);
            return $querycot->result_array();
    }
    public function getmonthly_stock_customer_wise($data){
        // $start_date=date('Y-m-d',strtotime('-1 month'));
        // $start_date=date('Y-m-01');
        // $end_date=date('Y-m-d');
        // if($data!='')
        // {
            $month=$data['chart_month'];
            $year=$data['chart_year'];
            $sale_point_id=$data['sale_point_id'];
            $customer=$data['customer'];
            $timestamp    = strtotime("$month" . "$year");
            // $start_date = date('Y-m-01', $timestamp);
              //$end_date  = date('Y-m-t', $timestamp);
             $start_date = $year."-".$month."-"."01";
              $end_date  = $year."-".$month."-"."31";
//echo $year."-".$month;exit;
        //}
             $sqlcot = "SELECT COALESCE(sum((select itemnameint from tblmaterial_coding where catcode='1' AND materialcode=d.itemid) *d.qty),0)/1000 as totala,m.issuedate from tbl_issue_goods_detail d , tbl_issue_goods m where d.ig_detail_id= m.issuenos AND issuedate BETWEEN '$start_date' AND '$end_date' AND m.sale_point_id='$sale_point_id' AND m.issuedto='$customer' group by m.issuedate";
             // echo "SELECT COALESCE(sum((select itemnameint from tblmaterial_coding where catcode='1' AND materialcode=d.itemid) *d.qty),0)/1000 as totala,m.issuedate from tbl_issue_goods_detail d , tbl_issue_goods m where d.ig_detail_id= m.issuenos AND issuedate BETWEEN '$start_date' AND '$end_date' AND m.sale_point_id='$sale_point_id' AND m.issuedto='$customer' group by m.issuedate";exit();
            $querycot = $this->db->query($sqlcot);
            return $querycot->result_array();
    }
    public function getmonthly_stock_customer_wise_sec($data){
            $month_sec=$data['chart_month_sec'];
            $year_sec=$data['chart_year_sec'];
            $sale_point_id=$data['sale_point_id'];
            $customer=$data['customer'];
            $timestamp    = strtotime("$month" . "$year");
            // $start_date = date('Y-m-01', $timestamp);
              //$end_date  = date('Y-m-t', $timestamp);
              $start_date_sec = $year_sec."-".$month_sec."-"."01";
              $end_date_sec = $year_sec."-".$month_sec."-"."31";
//echo $year."-".$month;exit;
        //}
             $sqlcot_sec= "SELECT COALESCE(sum((select itemnameint from tblmaterial_coding where catcode='1' AND materialcode=d.itemid) *d.qty),0)/1000 as totala_sec,m.issuedate from tbl_issue_goods_detail d , tbl_issue_goods m where d.ig_detail_id= m.issuenos AND issuedate BETWEEN '$start_date_sec' AND '$end_date_sec' AND m.sale_point_id='$sale_point_id' AND m.issuedto='$customer' group by m.issuedate";
            $querycot_sec = $this->db->query($sqlcot_sec);
            return $querycot_sec->result_array();
    }
    public function bank_position(){
        $rest_creditors_code='2004002';
        $this->db->select('SUM(trcode.damount) as dd ,SUM(trcode.camount) as cc,tcode.aname,tcode.acode');
        $this->db->join('tblacode as tcode', 'trcode.acode = tcode.acode');
       $this->db->where('LEFT(trcode.acode,7)',$rest_creditors_code);
        $this->db->from('tbltrans_detail as trcode');
       $this->db->group_by('trcode.acode');
        $query = $this->db->get();
        return $query->result_array();
    }
    public function bank_position_ledger($data = ''){
      $login_user=$this->session->userdata('id');
      $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
      $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
      $bank_code=$fix_code['bank_code'];
      if ($sale_point_id=='0') {
          $sale_point_id=$data['sale_point_id'];
          $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
          $bank_code=$fix_code['bank_code'];
      }
      $acod=$bank_code[0].$bank_code[1].$bank_code[2].$bank_code[3].$bank_code[4].$bank_code[5].$bank_code[6];
			$query1 = "SELECT opngbl,optype,aname ,acode FROM `tblacode` WHERE LEFT(`acode`,7) = '$acod' AND `atype` = 'Child' ORDER BY `aname` ASC";
            $result_main = $this->db->query($query1);
            $result_main->result_array();
            foreach($result_main->result_array() as $key=>$line){
            $opngbl = ''; $optype = '';
            $opngbl = $line['opngbl'];
            $optype = $line['optype'];
            $aname = $line['aname'];
            $acode = $line['acode'];
            $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and sale_point_id='$sale_point_id'";
            $result = $this->db->query($query2);
            foreach($result->result_array() as $key=>$line){
                $op_damount = $line['op_damount'];
                $op_camount = $line['op_camount'];
            }
            $total_opngbl = '';
            if($optype=='Credit'){
                $opngbl = -1*($opngbl) ;
            }
            $total_opngbl =    ($op_damount    -$op_camount )+($opngbl);
			number_format($total_opngbl);
			$datas[] = array(
                        "accountcode" =>$acode,
                        "accountname" =>$aname,
                        "tbalance" => $total_opngbl,
                    );
        }
			return $datas;
    }
        public function one_bank_position_ledger($acode=''){
              $query1 = "SELECT opngbl,optype,aname ,acode FROM `tblacode` WHERE LEFT(`acode`,7) = '2004002' AND `atype` = 'Child' and acode='$acode' ORDER BY `aname` ASC";
            $result_main = $this->db->query($query1);
            $line=$result_main->result_array();
               //  echo "ssssss";
               // echo "<br>";
            $opngbl = ''; $optype = '';
            $opngbl = $line['opngbl'];
            $optype = $line['optype'];
            $aname = $line['aname'];
            //$acode = $line['acode'];
            $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' ";
            $result = $this->db->query($query2);
            foreach($result->result_array() as $key=>$line){
                $op_damount = $line['op_damount'];
                $op_camount = $line['op_camount'];
            }
            $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' order by vdate,vno  ";
                        $total_opngbl = '';
            if($optype=='Credit'){
                $opngbl = -1*($opngbl) ;
            }
            $total_opngbl =    ($op_damount    -$op_camount )+($opngbl);
            if(($_POST['filter1']=="amount") or ($_POST['filter2']=="nar"))
            {
                /// not display opening set 0
                 $total_opngbl_new=$total_opngbl=0;
            }else{
                  $total_opngbl_new=$total_opngbl;
            }
            $total_bal= $total_damount=$total_camount=$i=0;
            $result = $this->db->query($query3);
            //pm($this->db->last_query());
            foreach($result->result_array() as $key=>$line){
                $vno = $line['vno'];
                $vdate = $line['vdate'];
                $vtype = $line['vtype'];
                $remarks = $line['remarks'];
                $damount = $line['damount'];
                $total_damount =  $damount + $total_damount;
                // if($filter == "party"){
                if($damount!=0){
                    $total_opngbl = $damount + $total_opngbl;
                }
                //}
                    $camount = $line['camount'];
                    $total_camount =  $camount + $total_camount;
                // if($filter == "party"){
                if($camount!=0){
                    $total_opngbl = $camount -( $total_opngbl);
                    $total_opngbl = -1*($total_opngbl) ;
                }
                //}
                $i++;
                // $string = htmlentities($remarks, null, 'utf-8');
                // $remarks = str_replace(" ", "&nbsp;", $string);
                // $remarks = html_entity_decode($remarks);
                    $car_code=$line['car_code'];
                      if($line['vtype']=='SV' ||  $line['svtype']=='FS')  {
                         $vno;
                  } else if($line['vtype']=='CP' ||  $line['vtype']=='JV'||  $line['vtype']=='CR' ||  $line['vtype']=='BP' ||  $line['vtype']=='BR'  ||  $line['vtype']=='RC') {
                         $vno;
                    } else if($line['vtype']=='SA') {
                         $vno;
                    }else if($line['vtype']=='PV') {
                         $vno;
                    }else {
                         $vno;
                      }
                         $vdate;
                         $remarks;
                         number_format($damount);
                         number_format($camount);
                         //if($filter == "party"){
                         number_format($total_opngbl);
                            //}
                    //Total
                    $g_total=0;
                    if($total_opngbl_new>0){ $g_total=($total_damount-$total_camount)+$total_opngbl_new;
                    }else{  $g_total=($total_damount-$total_camount)-$total_opngbl_new; }
                    number_format($total_opngbl);
            }
                    $datas[] = array(
                        "accountcode" =>$acode,
                        "accountname" =>$aname,
                        "fromdate" =>$fdate,
                        "todate" =>$tdate,
                        'openingbalbal'=>($total_opngbl_new>0? $total_opngbl_new.' Dr': '0 Cr'),
                        "voucherno" =>$vno,
                        "voucherdate" =>$vdate,
                        "description" =>$remarks,
                        "debit" =>$damount,
                        "credit" =>$camount,
                        "balance" =>number_format($total_opngbl),
                        "tdebit" =>number_format($total_damount),
                        "tcredit" =>number_format($total_camount),
                        "tbalance" =>number_format($total_opngbl),
                    );
        //pm($datas);
       return $datas;
    }
    public function cash_position(){
        $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $general = $this->db->query("select cash_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['cash_code'];
        $this->db->select('SUM(damount) as damount,SUM(camount) as camount');
        $this->db->from('tbltrans_detail');
        $this->db->where('acode',$general);
        $this->db->where('sale_point_id',$sale_point_id);
        $query = $this->db->get();
        foreach($query->result_array() as $key => $value) {
                $damount = $value['damount'];
                $camount = $value['camount'];
        }
        $this->db->select('opngbl,optype');
        $this->db->from('tblacode');
        $this->db->where('acode','$general');
        $query = $this->db->get();
        foreach($query->result_array() as $key => $value) {
                $opngbl = $value['opngbl'];
                $optype = $value['optype'];
        }
 $datas='';
                $datas[] = array(
                    'damount' => $damount,
                    'camount' => $camount,
                    'opngbl' => $opngbl,
                    'optype' => $optype
                );
        return $datas;
    }
    public function cash_position_today(){
		$today=date('Y-m-d');
        $this->db->select('SUM(damount) as damount,SUM(camount) as camount');
        $this->db->from('tbltrans_detail');
        $this->db->where('acode','2003013001');
        $this->db->where('vdate',$today);
        $query = $this->db->get();
        foreach($query->result_array() as $key => $value) {
                $damount = $value['damount'];
                $camount = $value['camount'];
        }
 $datas='';
                $datas[] = array(
                    'damount' => $damount,
                    'camount' => $camount,
                );
        return $datas;
    }
    public function get_opening($data){
       //error_reporting(E_ALL);
        //echo "string"; exit();
        // $daterange= $data['daterange'];
        // $sr=explode("/",($daterange));
        // $fdate=trim($sr[0]);
        // $tdate=trim($sr[1]);
        //     $fdate=$data['from_date'];
        //     $tdate=$data['to_date'];
        // $fdate=$data['from_date'];
        // $tdate=$data['to_date'];
 // pm($data);
         $acode= $data['acode'];
         $from_date= $data['from_date'];
        $sql="SELECT * from `tblmaterial_coding`";
        $query = $this->db->query($sql);
         //pm($query->result_array());
        if($query->num_rows()>0){
            foreach($query->result_array() as $key => $value) {
                $itemid=$value['materialcode'];
            ///////// opening start    ////
             $sqlcot = "SELECT qty as opening FROM `tbl_customer_opening` WHERE materialcode ='$itemid' and acode='$acode'";// AND materialcode ='$itemid'COALESCE(SUM(`qty`),0) as opening
            $querycot = $this->db->query($sqlcot);
            $rowcot = $querycot->row_array();
                $sqls = " SELECT   (SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as sale
                FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail`
                ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
                WHERE `issuedto` ='$acode' AND `tbl_issue_goods`.`issuedate`<'$from_date' AND `tbl_issue_goods_detail`.`wrate`>0
                AND `tbl_issue_goods_detail`.`returns`=0 AND `tbl_issue_goods_detail`.`itemid`='$itemid')
                +
                (SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as sale
                FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
                WHERE `issuedto` ='$acode' AND `tbl_issue_goods`.`issuedate`<'$from_date' AND `tbl_issue_goods_detail`.`wrate`=0 AND `tbl_issue_goods_detail`.`returns`=0
                AND `tbl_issue_goods_detail`.`itemid`='$itemid')
                +
                (SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as sale   FROM `tbl_issue_goods`
                INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
                WHERE `issuedto` ='$acode' AND `tbl_issue_goods`.`issuedate`<'$from_date' AND `tbl_issue_goods_detail`.`wrate`>0 AND `tbl_issue_goods_detail`.`returns`>0
                AND `tbl_issue_goods_detail`.`itemid`='$itemid')
                +
                (SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`-`tbl_issue_goods_detail`.`returns`),0) as sale   FROM `tbl_issue_goods`
                INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
                WHERE  `issuedto` ='$acode' AND `tbl_issue_goods`.`issuedate`<'$from_date'  AND `tbl_issue_goods_detail`.`wrate`=0
                AND `tbl_issue_goods_detail`.`returns`>0 AND `tbl_issue_goods_detail`.`itemid`='$itemid'
                ) as sale" ;
                // $sqls = " SELECT  (SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as sale
                // FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON
                // `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
                // WHERE `tbl_issue_goods_detail`.`wrate`>0 AND `tbl_issue_goods_detail`.`returns`=0
                // AND `tbl_issue_goods_detail`.`itemid`='$itemid'  and issuedto='$acode')
               // +
              // (SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as sale
               // FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail`
               // ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
               // WHERE `tbl_issue_goods_detail`.`wrate`=0 AND `tbl_issue_goods_detail`.`returns`=0
               // AND `tbl_issue_goods_detail`.`itemid`='$itemid'  and issuedto='$acode')
               // +
               // (SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as sale
               // FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON
               // `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
               // WHERE `tbl_issue_goods_detail`.`wrate`>0 AND `tbl_issue_goods_detail`.`returns`>0
               // AND `tbl_issue_goods_detail`.`itemid`='$itemid'  and issuedto='$acode'  )
               // +
               // (SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`-`tbl_issue_goods_detail`.`returns`),0) as sale   FROM `tbl_issue_goods`
               // INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
               // WHERE  `issuedto` ='$acode' AND `tbl_issue_goods_detail`.`wrate`=0
               // AND `tbl_issue_goods_detail`.`returns`>0 AND `tbl_issue_goods_detail`.`itemid`='$itemid'
               // ) as  sale" ;
           //echo '<br>';
            $querys = $this->db->query($sqls)->row_array();
            $sqlr = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returns  FROM `tbl_issue_return` INNER
            JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos`
            WHERE `tbl_issue_return_detail`.`itemid`='$itemid'   and scode='$acode' ";
            $queryr = $this->db->query($sqlr)->row_array();
            ////// return  end //////////
            $datas[] = array(
                    'itemid' => $itemid,
                    //'onerow' => $rowcot,
                    'opening' => $rowcot['opening']+$querys['sale']-$queryr['returns'],
                    //'sale'=>$rowsale,
                    //'return'=>$rowreturn,
                );
    }
    //pm($datas);
}
return $datas;
    }
    public function getsale($data){
   $acode= $data['acode'];
            $sqlj="SELECT * FROM `tbl_issue_goods` where  issuedto='$acode'";
   $queryj = $this->db->query($sqlj);
        if($queryj->num_rows()>0){
    foreach($queryj->result_array() as $key => $value) {
  $sqljj="SELECT itemid , wrate ,returns ,qty FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."'
 AND wrate > 0  AND returns =0
 UNION SELECT itemid , wrate ,returns ,qty
 FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."'
 AND wrate = 0  AND returns =0
 UNION SELECT itemid , wrate ,returns ,qty FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."'
 AND wrate > 0  AND returns > 0
 UNION SELECT itemid , wrate ,returns ,qty-returns as qty FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."'
 AND wrate = 0  AND qty-returns > 0
 ORDER BY `itemid` ASC";
                $queryjj = $this->db->query($sqljj)->result_array();
                if(!empty($queryjj))
                {
                $dataj[] = array(
                'issuenos' => $value['issuenos'],
                'issuedate' => $value['issuedate'],
                'sale'=>$queryjj,
                //'return'=>$rowreturn,
                );
                }
            }
        }
        return $dataj;
    }
public function getreturn($data){
  $acode= $data['acode'];
    $sqlj="SELECT * FROM `tbl_issue_return` where scode='$acode'";
   $queryj = $this->db->query($sqlj);
        if($queryj->num_rows()>0){
            foreach($queryj->result_array() as $key => $value) {
$sqljj="SELECT irnos,qty,itemid FROM `tbl_issue_return_detail`
where irnos='".$value['irnos']."'
ORDER BY `tbl_issue_return_detail`.`itemid` ASC";
$queryjj = $this->db->query($sqljj)->result_array();
if(!empty($queryjj))
{//pm($queryjj);
$dataj[] = array(
                    'issuenos' => $value['irnos'],
                    'issuedate' => $value['irdate'],
                    'return'=>$queryjj,
                    //'return'=>$rowreturn,
                );
}
            }
        }
        return $dataj;
    }
    public function manage_bookorder($fdate,$tdate,$status){
		//print 'aaaaa'.$fdate;
        $this->db->select('tbl_orderbooking.*,tbl_orderbooking_detail.orderid,tbl_orderbooking_detail.quantity,tbl_orderbooking_detail.refillnew,tbl_orderbooking_detail.itemid,tblacode.aname,tblmaterial_coding.itemname');
        $this->db->from('tbl_orderbooking');
        $this->db->join('tbl_orderbooking_detail', 'tbl_orderbooking.id = tbl_orderbooking_detail.orderid');
        $this->db->join('tblacode', ' tbl_orderbooking.acode= tblacode.acode');
		$this->db->join('tblmaterial_coding', ' tbl_orderbooking_detail.itemid= tblmaterial_coding.materialcode');
        $this->db->where('tbl_orderbooking.status',$status);
		$this->db->where('tbl_orderbooking.date >=',$fdate);
        $this->db->where('tbl_orderbooking.date <=',$tdate);
        $this->db->group_by('tbl_orderbooking.id');
        $this->db->order_by("tbl_orderbooking.id", "desc");
        $query = $this->db->get();
        return $query->result_array();
    }
public function get_all_brand()
{
    $sql_brand="SELECT b_code.brand_name, b_code.brand_id,b_code.brand_name from `tblmaterial_coding` mt_code INNER JOIN tbl_brand b_code
    on b_code.brand_id = mt_code.brandname  WHERE catcode=1 AND b_code.status ='Active' group by brandname ";
        $query_brand = $this->db->query($sql_brand);
        return $query_brand->result_array();
}
    public function getcurrent_stock($fdate='') {
        $fromdate=$fdate;
        if($fdate=='')
        $fromdate=date("Y-m-d");
        $sql_brand="SELECT b_code.brand_name, b_code.brand_id,b_code.brand_name from `tblmaterial_coding` mt_code INNER JOIN tbl_brand b_code
    on b_code.brand_id = mt_code.brandname  WHERE catcode=1 group by brandname ";
        $query_brand = $this->db->query($sql_brand);
        $result_brand= $query_brand->result_array();
        for($i=0; $i<count($result_brand); $i++)
        {
            $brand_id=$result_brand[$i]['brand_id'];
            $sql="SELECT * from `tblmaterial_coding` WHERE catcode=1 AND brandname ='$brand_id'";
        $query = $this->db->query($sql);
        if($query->num_rows()>0){
            foreach($query->result_array() as $key => $value) {
                $itemname = $value['itemname'];
                $itemid = $value['materialcode'];
                $materialcode_new = $value['materialcode'];
                /* here is code for filled */
                /*   opening balnace start     */
                 $sqls ="SELECT  * from `tbl_shop_opening` WHERE `date`<='$fromdate' AND `type`='Filled' AND `materialcode`=$itemid";
                $querys = $this->db->query($sqls)->row_array();
                $sql_con = "SELECT  COALESCE(SUM(`tbl_cylinderconversion_detail`.`qty`),0) as from_qty FROM `tbl_cylinderconversion_master` INNER JOIN `tbl_cylinderconversion_detail` ON `tbl_cylinderconversion_master`.`trans_id` = `tbl_cylinderconversion_detail`.`trans_id` WHERE `trans_date`<='$fromdate' AND `tbl_cylinderconversion_detail`.`type`='from' AND `tbl_cylinderconversion_detail`.`itemcode`=$itemid";
                $query_con = $this->db->query($sql_con);
                $recfrmvenf_con = $query_con->row_array();
                $sql_con_to = "SELECT  COALESCE(SUM(`tbl_cylinderconversion_detail`.`qty`),0) as to_qty FROM `tbl_cylinderconversion_master` INNER JOIN `tbl_cylinderconversion_detail` ON `tbl_cylinderconversion_master`.`trans_id` = `tbl_cylinderconversion_detail`.`trans_id` WHERE `trans_date`<='$fromdate' AND `tbl_cylinderconversion_detail`.`type`='to' AND `tbl_cylinderconversion_detail`.`itemcode`=$itemid";
                $query_con_to = $this->db->query($sql_con_to);
                $recfrmvenf_con_to = $query_con_to->row_array();
                //    if($itemid==22)
                // pm();
                $sqlv = "SELECT  COALESCE(SUM(`tbl_goodsreceiving_detail`.`quantity`),0) as Dgsumq, COALESCE(SUM(`tbl_goodsreceiving_detail`.`ereturn`),0) as otvendor FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<='$fromdate' AND `tbl_goodsreceiving_detail`.`type`='Filled' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryv = $this->db->query($sqlv);
                $recfrmvenf = $queryv->row_array();
                //$sqlv ="SELECT SUM(`quantity`) as Dgsumq,SUM(`ereturn`) as otvendor from `tbl_goodsreceiving_detail` WHERE `type`='Filled' AND `itemid`=$itemid";
                //$queryv = $this->db->query($sqlv);
                //$recfrmvenf = $queryv->row_array();
                //$sqlsc ="SELECT SUM(`qty`) as igsumq,SUM(`returns`) as rfcustomer from `tbl_issue_goods_detail` WHERE `returns`!='' AND `itemid`=$itemid";
                /*$sqlsc = "SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as igsumq,COALESCE(SUM(`tbl_issue_goods_detail`.`returns`),0) as rfcustomer  FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE `issuedate`<='$fromdate' AND  `tbl_issue_goods_detail`.`returns`!='' AND `tbl_issue_goods_detail`.`itemid`=$itemid";
                $querysc = $this->db->query($sqlsc);
                $saltcusf = $querysc->row_array();*/
                 $sqlsc = "SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as igsumq   FROM `tbl_issue_goods`
                 INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
                 WHERE `issuedate`<='$fromdate'   AND `tbl_issue_goods_detail`.`itemid`=$itemid";
                $querysc = $this->db->query($sqlsc);
                $saltcusf = $querysc->row_array();
                  $sqlreturnf = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returnqtyf  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `irdate`<='$fromdate' AND `tbl_issue_return_detail`.`type`='Filled' AND `tbl_issue_return`.`type`='purchasereturn' AND `tbl_issue_return_detail`.`itemid`=$itemid";
                $queryreturnf = $this->db->query($sqlreturnf);
                $return_qtyf = $queryreturnf->row_array();
                $sqlreturnf_sale = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returnqtyf  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `irdate`<='$fromdate' AND `tbl_issue_return_detail`.`type`='Filled' AND `tbl_issue_return`.`type`='salereturn' AND `tbl_issue_return_detail`.`itemid`=$itemid";
                $queryreturnf_sale = $this->db->query($sqlreturnf_sale);
                $return_qtyf_sale = $queryreturnf_sale->row_array();
//echo $querys['qty']."<br>";
/*echo $recfrmvenf['Dgsumq']."<br>";
echo $saltcusf['igsumq'];
exit;*/
//echo $querys['qty'];
//echo $recfrmvenf['Dgsumq'];
//echo $saltcusf['igsumq'];
                $opgbalfilled = $querys['qty']-$return_qtyf['returnqtyf']+$return_qtyf_sale['returnqtyf']+$recfrmvenf['Dgsumq']-$saltcusf['igsumq']-$recfrmvenf_con['from_qty']+$recfrmvenf_con_to['to_qty'];
//pm( $opgbalfilled );
                //echo $opgbalfilled;
                //exit;
                //$opgbalfilled = $querys['qty'];
                /*   opening balnace end     */
                /*   rest four columns b/w date for filled     */
                $sqlbdf ="SELECT * from `tbl_shop_opening` WHERE  `type`='Filled' AND `materialcode`=$itemid";
                $querybdf= $this->db->query($sqlbdf)->row_array();
                $sqlvv = "SELECT COALESCE(SUM(`tbl_goodsreceiving_detail`.`quantity`),0) as Dgsumq,COALESCE(SUM(`tbl_goodsreceiving_detail`.`ereturn`),0) as otvendor  FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `tbl_goodsreceiving_detail`.`type`='Filled' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryvv = $this->db->query($sqlvv);
                $recfrmvenff = $queryvv->row_array();
                 $sqlscc = "SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as igsumq,COALESCE(SUM(`tbl_issue_goods_detail`.`returns`),0) as rfcustomer  FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE  `tbl_issue_goods_detail`.`returns`!='' AND `tbl_issue_goods_detail`.`itemid`=$itemid";
                $queryscc = $this->db->query($sqlscc);
                $saltcusff = $queryscc->row_array();
                /*   end rest four columns b/w date for filled   */
                /* end here is code for filled */
                /* here is code for empty */
                $sqls ="SELECT  * from `tbl_shop_opening` WHERE `date`<='$fromdate' AND  `type`='Empty' AND `materialcode`=$itemid";
                $querys = $this->db->query($sqls)->row_array();
                //$sqlsc ="SELECT SUM(`qty`) as igsumq,SUM(`returns`) as rfcustomer from `tbl_issue_goods_detail` WHERE `returns`='' AND `itemid`=$itemid";
                 $sqlsc = "SELECT  COALESCE(SUM(`tbl_issue_goods_detail`.`returns`),0) as rfcustomer  FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE `issuedate`<='$fromdate'   AND `tbl_issue_goods_detail`.`itemid`=$itemid";
                $querysc = $this->db->query($sqlsc);
                $saltcuse = $querysc->row_array();
                //$sqlv ="SELECT SUM(`quantity`) as Dgsumq,SUM(`ereturn`) as otvendor from `tbl_goodsreceiving_detail` WHERE `type`='Empty' AND `itemid`=$itemid";
                 $sqlv = "SELECT COALESCE(SUM(`tbl_goodsreceiving_detail`.`quantity`),0) as Dgsumq   FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<='$fromdate' AND  `tbl_goodsreceiving_detail`.`type`='Empty' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryv = $this->db->query($sqlv);
                $recfrmvene=$queryv->row_array();
                 $sqlv_e = "SELECT  COALESCE(SUM(`tbl_goodsreceiving_detail`.`ereturn`),0) as otvendor  FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<='$fromdate'   AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryv_e = $this->db->query($sqlv_e);
                $recfrmvene_e=$queryv_e->row_array();
                $sqlreturn = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returnqty  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `irdate`<='$fromdate' AND `tbl_issue_return_detail`.`type`='Empty'AND `tbl_issue_return`.`type`='purchasereturn' AND `tbl_issue_return_detail`.`itemid`=$itemid";
                $queryreturn = $this->db->query($sqlreturn);
                $return_qty = $queryreturn->row_array();
                  $sqlreturn_sale = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returnqty  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `irdate`<='$fromdate' AND `tbl_issue_return_detail`.`type`='Empty'AND `tbl_issue_return`.`type`='salereturn' AND `tbl_issue_return_detail`.`itemid`=$itemid";
                $queryreturn_sale = $this->db->query($sqlreturn_sale);
                $return_qty_sale = $queryreturn_sale->row_array();
//echo $querys['qty'];
//echo $saltcuse['igsumq'];
//echo $recfrmvene['otvendor'];
//echo $recfrmvene['Dgsumq'];
//exit;
                //pm($return_qty['returnqty']);
                //pm($recfrmvene_e['otvendor']);
                //pm($return_qty['returnqty']);
                $opgbalempty = $querys['qty']+$saltcuse['rfcustomer']-$return_qty['returnqty']+$return_qty_sale['returnqty']+$recfrmvene['Dgsumq']-$recfrmvene_e['otvendor']+$recfrmvenf_con['from_qty']-$recfrmvenf_con_to['to_qty'];
                //$opgbalempty = $querys['qty'];
                //pm($opgbalempty);
                /*   rest four columns b/w date for empty    */
                $sqlbdf ="SELECT * from `tbl_shop_opening` WHERE  `type`='Empty' AND `materialcode`=$itemid";
                $querybdf= $this->db->query($sqlbdf)->row_array();
                $sqlsccc = "SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as igsumq,SUM(`tbl_issue_goods_detail`.`returns`) as rfcustomer  FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE  `tbl_issue_goods_detail`.`returns`='' AND `tbl_issue_goods_detail`.`itemid`=$itemid";
                $querysccc = $this->db->query($sqlsccc);
                $saltcusee = $querysccc->row_array();
                 $sqlvvv = "SELECT tbl_goodsreceiving.*,SUM(`tbl_goodsreceiving_detail`.`quantity`) as Dgsumq,SUM(`tbl_goodsreceiving_detail`.`ereturn`) as otvendor  FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `tbl_goodsreceiving_detail`.`type`='Empty' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryvvv = $this->db->query($sqlvvv);
                $recfrmvenee=$queryvvv->row_array();
                /*   end rest four columns b/w date for empty    */
                /* end here is code for empty */
                //$datas = array();
                $datas[] = array(
                    'itemid' => $itemname,
                    'materialcode_new' => $materialcode_new,
                    'filled' => $opgbalfilled,
                    'empty' => $opgbalempty,
                    'RFVF'=>$recfrmvenff['Dgsumq'],
                    'otvendorf'=>$recfrmvenff['otvendor'],
                    'saleoutf'=>$saltcusff['igsumq'],
                    'rfcustomerf'=>$saltcusff['rfcustomer'],
                    'RFVE'=>$recfrmvenee['Dgsumq'],
                    'otvendore'=>$recfrmvenee['otvendor'],
                    'saleoute'=>$saltcusee['igsumq'],
                    'rfcustomere'=>$saltcusee['rfcustomer'],
                );
                // if num->0
            }
        }
        $result_brand[$i]['report_stock_sub']=$datas;
        $datas = array();
    }
       return $result_brand;
    }
    public function getcurrent_stock_new($id) {
        $fromdate=date("Y-m-d");
			$sqls ="SELECT  COALESCE(SUM(`tbl_customer_opening`.`qty`),0) as open_qty
			from `tbl_customer_opening` WHERE    `materialcode`='$id'";
			$querys = $this->db->query($sqls)->row_array();
			  $sqlsc = "
			SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty` - `tbl_issue_goods_detail`.`returns`),0) as igsumq   FROM `tbl_issue_goods`
			INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
			WHERE   tbl_issue_goods.decanting!='Yes'
			AND `tbl_issue_goods_detail`.`itemid`='$id'  		AND `tbl_issue_goods_detail`.`itemid`=`tbl_issue_goods_detail`.`item_return` AND `tbl_issue_goods_detail`.`wrate`=0
			";
			$querysc = $this->db->query($sqlsc);
			$saltcusf = $querysc->row_array();
			  $sqlsc = "SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty` ),0) as igsumq
			  FROM `tbl_issue_goods`
			INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
			WHERE   tbl_issue_goods.decanting!='Yes'
			AND `tbl_issue_goods_detail`.`itemid`='$id'
			AND `tbl_issue_goods_detail`.`itemid`!=`tbl_issue_goods_detail`.`item_return`
			AND `tbl_issue_goods_detail`.`wrate`=0";
			$querysc = $this->db->query($sqlsc);
			$saltcusf_d = $querysc->row_array();
			  $sqlsc = "SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`returns`),0) as igsumq
			  FROM `tbl_issue_goods`
			INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
			WHERE   tbl_issue_goods.decanting!='Yes'
			AND `tbl_issue_goods_detail`.`item_return`='$id'
			AND `tbl_issue_goods_detail`.`itemid`!=`tbl_issue_goods_detail`.`item_return`
			AND `tbl_issue_goods_detail`.`wrate`=0";
			$querysc = $this->db->query($sqlsc);
			$saltcusf_s = $querysc->row_array();
			$sqlreturnf = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returnqtyf
			FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON
			`tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos`
			WHERE   `tbl_issue_return`.`type`='salereturn' AND `tbl_issue_return_detail`.`itemid`='$id'";
			$queryreturnf = $this->db->query($sqlreturnf);
			$return_qtyf = $queryreturnf->row_array();
                $opgbalfilled = $querys['open_qty']+$saltcusf['igsumq']+$saltcusf_d['igsumq']-$saltcusf_s['igsumq']-$return_qtyf['returnqtyf'];
       return $opgbalfilled;
    }
    public function getcurrent_stock_new_access_old($itemcode,$acode,$fdate,$market_access) {
		if($acode=='All'){
			$sqls_master="SELECT * from `tblacode` WHERE general='2004001000'";
            // $sqls_master="SELECT opngbl,optype,phone_no,address,aname,acode FROM `tblacode`
            // WHERE LEFT(acode,7)= '2004001' AND acode !='2004001000'";
		}else{
			$sqls_master="SELECT * from `tblacode` WHERE acode='$acode'";
		}
		//print $sqls;
			$querys_master = $this->db->query($sqls_master);
			if($querys_master->num_rows()>0){
    			foreach($querys_master->result_array() as $key => $values_master) {
        			$acode = $values_master['acode'];
        			$aname = $values_master['aname'];
        			$address = $values_master['address'];
        			$phone_no = $values_master['phone_no'];
        			$cell = $values_master['cell'];
            		if($itemcode=='All'){
            			$sql="SELECT * from `tblmaterial_coding` where catcode=1   ";
            		}else{
            		    $sql="SELECT * from `tblmaterial_coding` where materialcode='$itemcode'   ";
            		}
		//	print $sql;
			        $query = $this->db->query($sql);
			        if($query->num_rows()>0){
                        foreach($query->result_array() as $key => $value) {
            			     $itemid=$value['materialcode'];
			                 $sqlcot = "SELECT qty as opening FROM `tbl_customer_opening` WHERE  acode ='$acode' AND materialcode ='$itemid'";
                			$querycot = $this->db->query($sqlcot);
                            $rowcot = $querycot->row_array();
            			   $sqls = "
            			   SELECT (
                           (SELECT COALESCE(SUM(d.qty),0) as sale
            					FROM  tbl_issue_goods m ,tbl_issue_goods_detail d
            					WHERE
            					m.issuenos = d.ig_detail_id and
            					m.issuedate<='$fdate'
            					AND m.issuedto ='$acode' AND d.wrate=0
            				   AND d.returns=0 AND d.itemid='$itemid'
                           )
            					+
                         	(
                           SELECT COALESCE(SUM(d.qty),0) as sale
            					FROM tbl_issue_goods m, tbl_issue_goods_detail d
            					WHERE
            					m.issuenos = d.ig_detail_id and
            					m.issuedate<='$fdate' AND m.issuedto ='$acode'
            					AND d.wrate=0  AND  d.returns > 0 AND d.itemid='$itemid'
            				   and d.itemid!=d.item_return
            				   )
            					+
            					(
                           SELECT COALESCE(SUM(d.qty-d.returns),0) as sale
            					FROM tbl_issue_goods m, tbl_issue_goods_detail d
            			  		WHERE
            					m.issuenos = d.ig_detail_id and
            					m.issuedate<='$fdate' AND m.issuedto ='$acode'
            					AND d.wrate=0  AND  d.returns > 0 AND d.itemid='$itemid'
            			 	   and d.itemid=d.item_return
            			   ))as  sale" ;
 // print                        $sqls; exit;
         // print '<br><br>';
            $querys = $this->db->query($sqls)->row_array();
             $sqlr = "
				select(SELECT  COALESCE(SUM(d.qty),0) as returns  FROM tbl_issue_return m,  tbl_issue_return_detail d
				WHERE   m.irnos = d.irnos and total_amount=0 and
				m.irdate<='$fdate' AND m.scode ='$acode' AND d.itemid='$itemid'
				)
				+
				(SELECT  COALESCE(SUM(d.returns),0) as returns  FROM tbl_issue_goods m, tbl_issue_goods_detail d
				WHERE m.issuenos = d.ig_detail_id and m.issuedate<='$fdate' AND m.issuedto ='$acode'
				AND d.item_return='$itemid' and d.itemid!=d.item_return
				) as returns
			";
      //print                        $sqlr; exit;
            $queryr = $this->db->query($sqlr)->row_array();
			//////for security  received
			$sec = "SELECT  COALESCE(SUM(`tbl_security_receipt`.`qty`),0) as securytrecv
			FROM `tbl_security_receipt`
			WHERE `customercode` ='$acode'  and `dt`<='$fdate'
			AND itemid ='$itemid'";
			$queryreturnfsec = $this->db->query($sec);
			$return_qtyfsec = $queryreturnfsec->row_array();
			$total=0;
			$total=$rowcot['opening']+$querys['sale']-$queryr['returns']-$return_qtyfsec['securytrecv'];
			//$total=$querys['security_amt']-$querys['return_amount']+$return_qtyfsec['securytrecv'];
			// print '<br>';
			// print $acode;
			// print '<br>';
if($market_access=='Access'){
if($total<0){
            $datas[] = array(
                    'acode' => $acode,
                    'aname' => $aname,
                    'address' => $address,
                    'phone_no' => $phone_no,
                    'cell' => $cell,
					'itemid' => $itemid,
                    'opening' => $total,
                );
}
}
if($market_access=='Market'){
if($total>0){
            $datas[] = array(
					'acode' => $acode,
                    'aname' => $aname,
					'address' => $address,
                    'phone_no' => $phone_no,
                    'cell' => $cell,
                    'itemid' => $itemid,
                    'opening' => $total,
                );
}
}
    }
}
		}
		}
     //pm($datas);
return $datas;
    }
    public function getcurrent_stock_new_access($itemcode,$acode,$fdate,$market_access,$location) {
      $login_user=$this->session->userdata('id');
      $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
      $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
      $customer_code=$fix_code['customer_code'];
      if ($sale_point_id=='0') {
          $sale_point_id=$location;
          $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
         $customer_code=$fix_code['customer_code'];
         }
        if($acode=='All'){
            $sqls_master="SELECT * from `tblacode` WHERE general='$customer_code'";
        }else{
            $sqls_master="SELECT * from `tblacode` WHERE acode='$acode'";
        }
        //print $sqls;
            $querys_master = $this->db->query($sqls_master);
            if($querys_master->num_rows()>0){
            foreach($querys_master->result_array() as $key => $values_master) {
            $acode = $values_master['acode'];
            $aname = $values_master['aname'];
            $address = $values_master['address'];
            $phone_no = $values_master['phone_no'];
            $cell = $values_master['cell'];
        if($itemcode=='All'){
            $sql="SELECT * from `tblmaterial_coding` where catcode=1   ";
        }else{
        $sql="SELECT * from `tblmaterial_coding` where materialcode='$itemcode'   ";
        }
        //  print $sql;
            $query = $this->db->query($sql);
            if($query->num_rows()>0){
            foreach($query->result_array() as $key => $value) {
            $itemid=$value['materialcode'];
              $sqlcot = "SELECT qty as opening FROM `tbl_customer_opening` WHERE  acode ='$acode' AND materialcode ='$itemid' AND sale_point_id='$sale_point_id'";
            $querycot = $this->db->query($sqlcot);
            $rowcot = $querycot->row_array();
               $sqls = "
               SELECT (
               (SELECT COALESCE(SUM(d.qty),0) as sale
                    FROM  tbl_issue_goods m ,tbl_issue_goods_detail d
                    WHERE
                    m.issuenos = d.ig_detail_id and
                    m.issuedate<='$fdate'
                    AND m.issuedto ='$acode' AND d.wrate=0
                   AND d.returns=0 AND d.itemid='$itemid'
                   AND m.sale_point_id='$sale_point_id'
               )
                    +
                (
               SELECT COALESCE(SUM(d.qty),0) as sale
                    FROM tbl_issue_goods m, tbl_issue_goods_detail d
                    WHERE
                    m.issuenos = d.ig_detail_id and
                    m.issuedate<='$fdate' AND m.issuedto ='$acode'
                    AND d.wrate=0  AND  d.returns > 0 AND d.itemid='$itemid'
                   and d.itemid!=d.item_return and m.sale_point_id='$sale_point_id'
                   )
                    +
                    (
               SELECT COALESCE(SUM(d.qty-d.returns),0) as sale
                    FROM tbl_issue_goods m, tbl_issue_goods_detail d
                    WHERE
                    m.issuenos = d.ig_detail_id and
                    m.issuedate<='$fdate' AND m.issuedto ='$acode'
                    AND d.wrate=0  AND  d.returns > 0 AND d.itemid='$itemid'
                   and d.itemid=d.item_return and m.sale_point_id='$sale_point_id'
               ))as  sale" ;
 // print                        $sqls; exit;
         // print '<br><br>';
            $querys = $this->db->query($sqls)->row_array();
             $sqlr = "
                select(SELECT  COALESCE(SUM(d.qty),0) as returns  FROM tbl_issue_return m,  tbl_issue_return_detail d
                WHERE   m.irnos = d.irnos and
                m.irdate<='$fdate' AND m.scode ='$acode' AND d.wrate=0 AND d.itemid='$itemid' AND d.sale_point_id='$sale_point_id'
                )
                +
                (SELECT  COALESCE(SUM(d.returns),0) as returns  FROM tbl_issue_goods m, tbl_issue_goods_detail d
                WHERE m.issuenos = d.ig_detail_id and m.issuedate<='$fdate' AND m.issuedto ='$acode'
                AND d.item_return='$itemid' AND d.wrate=0 and d.itemid!=d.item_return AND d.sale_point_id='$sale_point_id'
                ) as returns
            ";
            $queryr = $this->db->query($sqlr)->row_array();
            //////for security  received
            $sec = "SELECT  COALESCE(SUM(`tbl_security_receipt`.`qty`),0) as securytrecv
            FROM `tbl_security_receipt`
            WHERE `customercode` ='$acode'  and `dt`<='$fdate'
            AND itemid ='$itemid'";
            $queryreturnfsec = $this->db->query($sec);
            $return_qtyfsec = $queryreturnfsec->row_array();
            $total=0;
            $total=$rowcot['opening']+$querys['sale']-$queryr['returns']-$return_qtyfsec['securytrecv'];
            //$total=$querys['security_amt']-$querys['return_amount']+$return_qtyfsec['securytrecv'];
            // print '<br>';
            // print $acode;
            // print '<br>';
if($market_access=='Access'){
if($total<0){
            $datas[] = array(
                    'acode' => $acode,
                    'aname' => $aname,
                    'address' => $address,
                    'phone_no' => $phone_no,
                    'cell' => $cell,
                    'itemid' => $itemid,
                    'opening' => $total,
                );
}
}
if($market_access=='Market'){
if($total>0){
            $datas[] = array(
                    'acode' => $acode,
                    'aname' => $aname,
                    'address' => $address,
                    'phone_no' => $phone_no,
                    'cell' => $cell,
                    'itemid' => $itemid,
                    'opening' => $total,
                );
}
}
    }
}
        }
        }
      //pm($datas);
return $datas;
    }
    public function getcurrent_security_cylinder_old($itemcode,$acode,$fdate,$market_access) {
        if($acode=='All'){
            $sqls_master="SELECT * from `tblacode` WHERE general='2004001000'";
        }else{
            $sqls_master="SELECT * from `tblacode` WHERE acode='$acode'";
        }
    //      $sqls_master="SELECT * from `tblacode` WHERE acode='2004001103'";
        $querys_master = $this->db->query($sqls_master);
            if($querys_master->num_rows()>0){
            foreach($querys_master->result_array() as $key => $values_master) {
            $acode = $values_master['acode'];
            $aname = $values_master['aname'];
            $address = $values_master['address'];
            $phone_no = $values_master['phone_no'];
            $cell = $values_master['cell'];
            if($itemcode=='All'){
                $sql="SELECT * from `tblmaterial_coding` where catcode=1   ";
            }else{
            $sql="SELECT * from `tblmaterial_coding` where materialcode='$itemcode'   ";
            }
            $query = $this->db->query($sql);
            if($query->num_rows()>0){
            foreach($query->result_array() as $key => $value) {
             $itemid=$value['materialcode'];
        $sqls = " SELECT ((SELECT COALESCE(SUM(d.qty),0) as sale FROM  tbl_issue_goods m ,tbl_issue_goods_detail d
                  WHERE m.issuenos = d.ig_detail_id and m.issuedate<='$fdate'  AND m.issuedto ='$acode' AND d.wrate>0
                      AND d.returns=0 AND d.itemid='$itemid' ) )as  sale" ;
           $querys = $this->db->query($sqls)->row_array();
            $sqlr = " select(SELECT  COALESCE(SUM(d.qty),0) as returns  FROM tbl_issue_return m,  tbl_issue_return_detail d
                      WHERE  m.irnos = d.irnos and m.irdate<='$fdate' AND m.scode ='$acode' AND d.itemid='$itemid' AND d.wrate>0  ) as returns ";
            $queryr = $this->db->query($sqlr)->row_array();
            //////for security  received
            $sec = "SELECT  COALESCE(SUM(`tbl_security_receipt`.`qty`),0) as securytrecv
            FROM `tbl_security_receipt`
            WHERE `customercode` ='$acode'  and `dt`<='$fdate'
            AND itemid ='$itemid'";
            // print '<br>';
            $queryreturnfsec = $this->db->query($sec);
            $return_qtyfsec = $queryreturnfsec->row_array();
            // print '<br>';print_r ($return_qtyfsec['returns']);
            $total=0;
            $total=$querys['sale']-$queryr['returns']+$return_qtyfsec['securytrecv'];
              // print '<br>';print_r ($total);
            if($market_access=='Access'){
            if($total<0){
                        $datas[] = array(
                                'acode' => $acode,
                                'aname' => $aname,
                                'address' => $address,
                                'phone_no' => $phone_no,
                                'cell' => $cell,
                                'itemid' => $itemid,
                                'opening' => $total,
                            );
            }
            }
            if($market_access=='Market'){
            if($total>0){
                        $datas[] = array(
                                'acode' => $acode,
                                'aname' => $aname,
                                'address' => $address,
                                'phone_no' => $phone_no,
                                'cell' => $cell,
                                'itemid' => $itemid,
                                'opening' => $total,
                            );
            }
            }
        }
        }
    }
    }
     // pm($datas);
return $datas;
    }
    public function getcurrent_security_cylinder($itemcode,$acode,$fdate,$market_access,$location) {
      $login_user=$this->session->userdata('id');
      $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
      $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
      $customer_code=$fix_code['customer_code'];
      if ($sale_point_id=='0') {
          $sale_point_id=$location;
          $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
          $customer_code=$fix_code['customer_code'];
         }
        if($acode=='All'){
            $sqls_master="SELECT * from `tblacode` WHERE general='$customer_code'";
        }else{
            $sqls_master="SELECT * from `tblacode` WHERE acode='$acode'";
        }
    //      $sqls_master="SELECT * from `tblacode` WHERE acode='2004001103'";
        $querys_master = $this->db->query($sqls_master);
            if($querys_master->num_rows()>0){
            foreach($querys_master->result_array() as $key => $values_master) {
            $acode = $values_master['acode'];
            $aname = $values_master['aname'];
            $address = $values_master['address'];
            $phone_no = $values_master['phone_no'];
            $cell = $values_master['cell'];
            if($itemcode=='All'){
                $sql="SELECT * from `tblmaterial_coding` where catcode=1   ";
            }else{
            $sql="SELECT * from `tblmaterial_coding` where materialcode='$itemcode'   ";
            }
            $query = $this->db->query($sql);
            if($query->num_rows()>0){
            foreach($query->result_array() as $key => $value) {
             $itemid=$value['materialcode'];
        $sqls = " SELECT ((SELECT COALESCE(SUM(d.qty),0) as sale FROM  tbl_issue_goods m ,tbl_issue_goods_detail d
                  WHERE m.issuenos = d.ig_detail_id and m.issuedate<='$fdate'  AND m.issuedto ='$acode' AND d.wrate>0
                      AND d.returns=0 AND d.itemid='$itemid' AND d.sale_point_id='$sale_point_id' ) )as  sale" ;
           $querys = $this->db->query($sqls)->row_array();
            $sqlr = " select(SELECT  COALESCE(SUM(d.qty),0) as returns  FROM tbl_issue_return m,  tbl_issue_return_detail d
                      WHERE  m.irnos = d.irnos and m.irdate<='$fdate' AND m.scode ='$acode' AND d.itemid='$itemid' AND d.wrate>0 AND d.sale_point_id='$sale_point_id'  ) as returns ";
            $queryr = $this->db->query($sqlr)->row_array();
            //////for security  received
            $sec = "SELECT  COALESCE(SUM(`tbl_security_receipt`.`qty`),0) as securytrecv
            FROM `tbl_security_receipt`
            WHERE `customercode` ='$acode'  and `dt`<='$fdate'
            AND itemid ='$itemid'";
            // print '<br>';
            $queryreturnfsec = $this->db->query($sec);
            $return_qtyfsec = $queryreturnfsec->row_array();
            // print '<br>';print_r ($queryreturnfsec['returns']);
            $total=0;
            $total=$querys['sale']-$queryr['returns']+$return_qtyfsec['securytrecv'];
              // print '<br>';print_r ($total);
            if($market_access=='Access'){
            if($total<0){
                        $datas[] = array(
                                'acode' => $acode,
                                'aname' => $aname,
                                'address' => $address,
                                'phone_no' => $phone_no,
                                'cell' => $cell,
                                'itemid' => $itemid,
                                'opening' => $total,
                            );
            }
            }
            if($market_access=='Market'){
            if($total>0){
                        $datas[] = array(
                                'acode' => $acode,
                                'aname' => $aname,
                                'address' => $address,
                                'phone_no' => $phone_no,
                                'cell' => $cell,
                                'itemid' => $itemid,
                                'opening' => $total,
                            );
            }
            }
        }
        }
    }
    }
     // pm($datas);
return $datas;
    }
   public function getcurrent_security_cylinder1($itemcode,$acode,$fdate,$market_access) {
		if($acode=='All'){
			$sqls_master="SELECT * from `tblacode` WHERE general='2004001000'";
		}else{
			$sqls_master="SELECT * from `tblacode` WHERE acode='$acode'";
		}
	//		$sqls_master="SELECT * from `tblacode` WHERE acode='2004001103'";
		$querys_master = $this->db->query($sqls_master);
			if($querys_master->num_rows()>0){
			foreach($querys_master->result_array() as $key => $values_master) {
			$acode = $values_master['acode'];
			$aname = $values_master['aname'];
			$address = $values_master['address'];
			$phone_no = $values_master['phone_no'];
			$cell = $values_master['cell'];
			if($itemcode=='All'){
				$sql="SELECT * from `tblmaterial_coding` where catcode=1   ";
			}else{
			$sql="SELECT * from `tblmaterial_coding` where materialcode='$itemcode'   ";
			}
			$query = $this->db->query($sql);
			if($query->num_rows()>0){
            foreach($query->result_array() as $key => $value) {
			$itemid=$value['materialcode'];
			$sqls = " SELECT ((SELECT COALESCE(SUM(d.qty),0) as sale FROM  tbl_issue_goods m ,tbl_issue_goods_detail d
					  WHERE m.issuenos = d.ig_detail_id and m.issuedate<='$fdate'  AND m.issuedto ='$acode' AND d.wrate>0
					  AND d.returns=0 AND d.itemid='$itemid' ) )as  sale" ;
            $querys = $this->db->query($sqls)->row_array();
            $sqlr = " select(SELECT  COALESCE(SUM(d.qty),0) as returns  FROM tbl_issue_return m,  tbl_issue_return_detail d
					  WHERE  m.irnos = d.irnos and m.irdate<='$fdate' AND m.scode ='$acode' AND d.itemid='$itemid' AND d.wrate > 0 ) as returns ";
            $queryr = $this->db->query($sqlr)->row_array();
			//////for security  received
			$sec = "SELECT  COALESCE(SUM(`tbl_security_receipt`.`qty`),0) as securytrecv
			FROM `tbl_security_receipt`
			WHERE `customercode` ='$acode'  and `dt`<='$fdate'
			AND itemid ='$itemid'";
			// print 	$sec.';';
			// print '<br>';
			$queryreturnfsec = $this->db->query($sec);
			$return_qtyfsec = $queryreturnfsec->row_array();
			$total=0;
			$total=$querys['sale']-$queryr['returns']+$return_qtyfsec['securytrecv'];
			if($market_access=='Access'){
			if($total<0){
						$datas[] = array(
								'acode' => $acode,
								'aname' => $aname,
								'address' => $address,
								'phone_no' => $phone_no,
								'cell' => $cell,
								'itemid' => $itemid,
								'opening' => $total,
							);
			}
			}
			if($market_access=='Market'){
			if($total>0){
						$datas[] = array(
								'acode' => $acode,
								'aname' => $aname,
								'address' => $address,
								'phone_no' => $phone_no,
								'cell' => $cell,
								'itemid' => $itemid,
								'opening' => $total,
							);
			}
			}
		}
		}
	}
	}
     // pm($datas);
return $datas;
    }
		public function getcurrent_stock_old($fdate='') {
        $fromdate=$fdate;
        if($fdate=='')
        $fromdate=date("Y-m-d");
        //$sql="SELECT DISTINCT(brandname),itemname,materialcode from `tblmaterial_coding` WHERE catcode=1 ";
        $sql="SELECT * from `tblmaterial_coding` WHERE catcode=1 ";
        $query = $this->db->query($sql);
        if($query->num_rows()>0){
            foreach($query->result_array() as $key => $value) {
                $itemname = $value['itemname'];
                $itemid = $value['materialcode'];
                /* here is code for filled */
                /*   opening balnace start     */
                 $sqls ="SELECT  * from `tbl_shop_opening` WHERE `date`<='$fromdate' AND `type`='Filled' AND `materialcode`=$itemid";
                $querys = $this->db->query($sqls)->row_array();
                $sqlv = "SELECT  COALESCE(SUM(`tbl_goodsreceiving_detail`.`quantity`),0) as Dgsumq, COALESCE(SUM(`tbl_goodsreceiving_detail`.`ereturn`),0) as otvendor FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<='$fromdate' AND `tbl_goodsreceiving_detail`.`type`='Filled' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryv = $this->db->query($sqlv);
                $recfrmvenf = $queryv->row_array();
                //$sqlv ="SELECT SUM(`quantity`) as Dgsumq,SUM(`ereturn`) as otvendor from `tbl_goodsreceiving_detail` WHERE `type`='Filled' AND `itemid`=$itemid";
                //$queryv = $this->db->query($sqlv);
                //$recfrmvenf = $queryv->row_array();
                //$sqlsc ="SELECT SUM(`qty`) as igsumq,SUM(`returns`) as rfcustomer from `tbl_issue_goods_detail` WHERE `returns`!='' AND `itemid`=$itemid";
                /*$sqlsc = "SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as igsumq,COALESCE(SUM(`tbl_issue_goods_detail`.`returns`),0) as rfcustomer  FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE `issuedate`<='$fromdate' AND  `tbl_issue_goods_detail`.`returns`!='' AND `tbl_issue_goods_detail`.`itemid`=$itemid";
                $querysc = $this->db->query($sqlsc);
                $saltcusf = $querysc->row_array();*/
                 $sqlsc = "SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as igsumq   FROM `tbl_issue_goods`
                 INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
                 WHERE `issuedate`<='$fromdate'   AND `tbl_issue_goods_detail`.`itemid`=$itemid";
                $querysc = $this->db->query($sqlsc);
                $saltcusf = $querysc->row_array();
                  $sqlreturnf = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returnqtyf  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `irdate`<='$fromdate' AND `tbl_issue_return_detail`.`type`='Filled' AND `tbl_issue_return`.`type`='purchasereturn' AND `tbl_issue_return_detail`.`itemid`=$itemid";
                $queryreturnf = $this->db->query($sqlreturnf);
                $return_qtyf = $queryreturnf->row_array();
                $sqlreturnf_sale = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returnqtyf  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `irdate`<='$fromdate' AND `tbl_issue_return_detail`.`type`='Filled' AND `tbl_issue_return`.`type`='salereturn' AND `tbl_issue_return_detail`.`itemid`=$itemid";
                $queryreturnf_sale = $this->db->query($sqlreturnf_sale);
                $return_qtyf_sale = $queryreturnf_sale->row_array();
				//echo $querys['qty']."<br>";
				/*echo $recfrmvenf['Dgsumq']."<br>";
				echo $saltcusf['igsumq'];
				exit;*/
				//echo $querys['qty'];
				//echo $recfrmvenf['Dgsumq'];
				//echo $saltcusf['igsumq'];
                $opgbalfilled = $querys['qty']-$return_qtyf['returnqtyf']+$return_qtyf_sale['returnqtyf']+$recfrmvenf['Dgsumq']-$saltcusf['igsumq'];
				//pm( $opgbalfilled );
                //echo $opgbalfilled;
                //exit;
                //$opgbalfilled = $querys['qty'];
                /*   opening balnace end     */
                /*   rest four columns b/w date for filled     */
                $sqlbdf ="SELECT * from `tbl_shop_opening` WHERE  `type`='Filled' AND `materialcode`=$itemid";
                $querybdf= $this->db->query($sqlbdf)->row_array();
                $sqlvv = "SELECT COALESCE(SUM(`tbl_goodsreceiving_detail`.`quantity`),0) as Dgsumq,COALESCE(SUM(`tbl_goodsreceiving_detail`.`ereturn`),0) as otvendor  FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `tbl_goodsreceiving_detail`.`type`='Filled' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryvv = $this->db->query($sqlvv);
                $recfrmvenff = $queryvv->row_array();
                 $sqlscc = "SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as igsumq,COALESCE(SUM(`tbl_issue_goods_detail`.`returns`),0) as rfcustomer  FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE  `tbl_issue_goods_detail`.`returns`!='' AND `tbl_issue_goods_detail`.`itemid`=$itemid";
                $queryscc = $this->db->query($sqlscc);
                $saltcusff = $queryscc->row_array();
                /*   end rest four columns b/w date for filled   */
                /* end here is code for filled */
                /* here is code for empty */
                $sqls ="SELECT  * from `tbl_shop_opening` WHERE `date`<='$fromdate' AND  `type`='Empty' AND `materialcode`=$itemid";
                $querys = $this->db->query($sqls)->row_array();
                //$sqlsc ="SELECT SUM(`qty`) as igsumq,SUM(`returns`) as rfcustomer from `tbl_issue_goods_detail` WHERE `returns`='' AND `itemid`=$itemid";
                 $sqlsc = "SELECT  COALESCE(SUM(`tbl_issue_goods_detail`.`returns`),0) as rfcustomer  FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE `issuedate`<='$fromdate'   AND `tbl_issue_goods_detail`.`itemid`=$itemid";
                $querysc = $this->db->query($sqlsc);
                $saltcuse = $querysc->row_array();
                //$sqlv ="SELECT SUM(`quantity`) as Dgsumq,SUM(`ereturn`) as otvendor from `tbl_goodsreceiving_detail` WHERE `type`='Empty' AND `itemid`=$itemid";
                 $sqlv = "SELECT COALESCE(SUM(`tbl_goodsreceiving_detail`.`quantity`),0) as Dgsumq   FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<='$fromdate' AND  `tbl_goodsreceiving_detail`.`type`='Empty' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryv = $this->db->query($sqlv);
                $recfrmvene=$queryv->row_array();
                 $sqlv_e = "SELECT  COALESCE(SUM(`tbl_goodsreceiving_detail`.`ereturn`),0) as otvendor  FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<='$fromdate'   AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryv_e = $this->db->query($sqlv_e);
                $recfrmvene_e=$queryv_e->row_array();
                $sqlreturn = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returnqty  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `irdate`<='$fromdate' AND `tbl_issue_return_detail`.`type`='Empty'AND `tbl_issue_return`.`type`='purchasereturn' AND `tbl_issue_return_detail`.`itemid`=$itemid";
                $queryreturn = $this->db->query($sqlreturn);
                $return_qty = $queryreturn->row_array();
                  $sqlreturn_sale = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returnqty  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `irdate`<='$fromdate' AND `tbl_issue_return_detail`.`type`='Empty'AND `tbl_issue_return`.`type`='salereturn' AND `tbl_issue_return_detail`.`itemid`=$itemid";
                $queryreturn_sale = $this->db->query($sqlreturn_sale);
                $return_qty_sale = $queryreturn_sale->row_array();
				//echo $querys['qty'];
				//echo $saltcuse['igsumq'];
				//echo $recfrmvene['otvendor'];
				//echo $recfrmvene['Dgsumq'];
				//exit;
                //pm($return_qty['returnqty']);
                //pm($recfrmvene_e['otvendor']);
                //pm($return_qty['returnqty']);
                $opgbalempty = $querys['qty']+$saltcuse['rfcustomer']-$return_qty['returnqty']+$return_qty_sale['returnqty']+$recfrmvene['Dgsumq']-$recfrmvene_e['otvendor'];
                //$opgbalempty = $querys['qty'];
                //pm($opgbalempty);
                /*   rest four columns b/w date for empty    */
                $sqlbdf ="SELECT * from `tbl_shop_opening` WHERE  `type`='Empty' AND `materialcode`=$itemid";
                $querybdf= $this->db->query($sqlbdf)->row_array();
                $sqlsccc = "SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as igsumq,SUM(`tbl_issue_goods_detail`.`returns`) as rfcustomer  FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE  `tbl_issue_goods_detail`.`returns`='' AND `tbl_issue_goods_detail`.`itemid`=$itemid";
                $querysccc = $this->db->query($sqlsccc);
                $saltcusee = $querysccc->row_array();
                 $sqlvvv = "SELECT tbl_goodsreceiving.*,SUM(`tbl_goodsreceiving_detail`.`quantity`) as Dgsumq,SUM(`tbl_goodsreceiving_detail`.`ereturn`) as otvendor  FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `tbl_goodsreceiving_detail`.`type`='Empty' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryvvv = $this->db->query($sqlvvv);
                $recfrmvenee=$queryvvv->row_array();
                /*   end rest four columns b/w date for empty    */
                /* end here is code for empty */
                $datas[] = array(
                    'itemid' => $itemname,
                    'filled' => $opgbalfilled,
                    'empty' => $opgbalempty,
                    'RFVF'=>$recfrmvenff['Dgsumq'],
                    'otvendorf'=>$recfrmvenff['otvendor'],
                    'saleoutf'=>$saltcusff['igsumq'],
                    'rfcustomerf'=>$saltcusff['rfcustomer'],
                    'RFVE'=>$recfrmvenee['Dgsumq'],
                    'otvendore'=>$recfrmvenee['otvendor'],
                    'saleoute'=>$saltcusee['igsumq'],
                    'rfcustomere'=>$saltcusee['rfcustomer'],
                );
            }
        }
        //pm($datas);
        return $datas;
    }
    public function get_account_balance($acode){
        $this->db->select('SUM(damount) as damount,SUM(camount) as camount');
        $this->db->from('tbltrans_detail');
        $this->db->where('acode',$acode);
        $query = $this->db->get();
        foreach($query->result_array() as $key => $value) {
                $damount = $value['damount'];
                $camount = $value['camount'];
        }
        $this->db->select('opngbl,optype');
        $this->db->from('tblacode');
        $this->db->where('acode',$acode);
        $query = $this->db->get();
        foreach($query->result_array() as $key => $value) {
                $opngbl = $value['opngbl'];
                $optype = $value['optype'];
        }
 $datas='';
                $datas[] = array(
                    'damount' => $damount,
                    'camount' => $camount,
                    'opngbl' => $opngbl,
                    'optype' => $optype
                );
		//		pm($datas);
        return $datas;
    }
}
