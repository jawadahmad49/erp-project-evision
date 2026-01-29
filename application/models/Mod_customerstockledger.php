<?php
class Mod_customerstockledger extends CI_Model {
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
                      $sale_point_id=$data['location'];
                      $typee=$data['typee'];
                      $acode=$data['acode'];
                      $sale_type=$data['sale_type'];
 if($typee=='Empty'){ $where_type= "and d.type='sale'  "; }else{ $where_type =""; }
 if($acode!='All'){ $where_acode= "and m.issuedto='$acode'  "; }else{ $where_acode =""; }
 if($sale_type!=''){ $where_sale_type= " AND `m`.`type`='$sale_type'"; }else{ $where_sale_type =""; }
                      //pm($data);exit();
                    $sqlcot = "SELECT COALESCE(sum((select itemnameint from tblmaterial_coding where catcode='1' AND materialcode=d.itemid) *d.qty),0)/1000 as totala , COALESCE(sum(d.qty)) as qtyyyyy from tbl_issue_goods_detail d , tbl_issue_goods m where d.ig_detail_id= m.issuenos AND issuedate  >= '$fdate' AND issuedate  <= '$tdate' AND d.itemid = '$itemid' AND d.sale_point_id='$sale_point_id' and m.type='$typee' $where_type $where_acode $where_sale_type";
                }
                else
                {
                    $sqlcot = "SELECT COALESCE(sum((select itemnameint from tblmaterial_coding where catcode='1' AND materialcode=d.itemid) *d.qty),0)/1000 as totala, COALESCE(sum(d.qty)) as qtyyyyy  from tbl_issue_goods_detail d , tbl_issue_goods m where d.ig_detail_id= m.issuenos AND issuedate  = '$data' AND d.itemid = '$itemid' AND d.sale_point_id='$sale_point_id' and m.type='$typee' $where_acode $where_sale_type";
                }
            $querycot = $this->db->query($sqlcot);
            $result=$querycot->row_array();
            $new_data[]=$result;
             $new_data[$count]['materialcode']=$value['materialcode'];
             $new_data[$count++]['itemname']=$value['itemname'];
         }
         return $new_data;
    }
    public function get_total_balance_expenses($data){
		  $date=$data['to_date'];
            $query1 = "SELECT opngbl,optype,phone_no,address,aname,acode,reg_date FROM `tblacode` WHERE LEFT(acode,6)= '400100' AND acode !='4001002001' AND acode !='4001001000'  AND reg_date<'$date'";
            $result = $this->db->query($query1);
            $line = $result->result_array();
    for ($i=0; $i<count($line); $i++) {
   $acode= $line[$i]['acode'];
   $query2 = $this->db->query("SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' AND `vdate`<='$date'");
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
	 public function get_total_balance_expenses1($data){
		 // $date=$data['to_date'];
$net_balace_exp=0;
            $query1 = "SELECT opngbl,optype,phone_no,address,aname,acode,reg_date FROM `tblacode` WHERE LEFT(acode,6)= '400100' AND acode !='4001002001' AND acode !='4001001000'";
            $result = $this->db->query($query1);
            $line = $result->result_array();
    for ($i=0; $i<count($line); $i++) {
   $acode= $line[$i]['acode'];
   $query2 = $this->db->query("SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' ");
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
            // foreach($line as $key => $value) {
            // $net_balace_exp=$net_balace_exp+$value['new_balance_pay'];
            // }
    return $line;
    }
    public function get_total_balance_expenses_current($data){
		$month = date('Y-m');
		$fdate=$month.'-01';
		$tdate=$month.'-31';
    $sale_point_id=$data['sale_point_id'];
    $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
      $expense_code=$fix_code['expense_code'];
      $acod=$expense_code[0].$expense_code[1].$expense_code[2].$expense_code[3].$expense_code[4].$expense_code[5].$expense_code[6];
        $net_balace_exp=0;
        $query1 = "SELECT opngbl,optype,phone_no,address,aname,acode,reg_date FROM `tblacode` WHERE LEFT(acode,7)= '$acod' AND acode !='4001011'";
        $result = $this->db->query($query1);
        $line = $result->result_array();
        for ($i=0; $i<count($line); $i++) {
           $acode= $line[$i]['acode'];
           $query2 = $this->db->query("SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' AND `vdate` BETWEEN '$fdate' AND '$tdate' AND sale_point_id='$sale_point_id' ");
           $credit_debit = $query2->result_array();
           $change_difference=$credit_debit[0]['op_camount']-$credit_debit[0]['op_damount'];
           $opngbl_new=$line[$i]['opngbl'];
            if($line[$i]['optype']=='Credit')
            {
                $opngbl_new=-$line[$i]['opngbl'];
               $line[$i]['new_balance_pay']= $change_difference;
            }else{
            	   $line[$i]['new_balance_pay']=- $change_difference;
            }
            if($line[$i]['new_balance_pay']<=0){
                $line[$i]['optype']='Debit';
            }else{
                $line[$i]['optype']='Credit';}
            }
    return $line;
    }
	public function getdate_stock_report_customer($data,$type='',$acode){
        $sql="SELECT * from `tblmaterial_coding` where catcode='1'";
        $query = $this->db->query($sql);
        $count=0;
            foreach($query->result_array() as $key => $value) {
                $itemid=$value['materialcode'];
                if($type==2)
                {
                      $fdate=$data['from_date'];
                      $tdate=$data['to_date'];
                      $sale_point_id=$data['location'];
                     // pm($data);
                    $sqlcot = "SELECT COALESCE(sum((select itemnameint from tblmaterial_coding where catcode='1' AND materialcode=d.itemid) *d.qty),0)/1000 as totala from tbl_issue_goods_detail d , tbl_issue_goods m where m.issuedto='$acode' AND d.ig_detail_id= m.issuenos AND issuedate  >= '$fdate' AND issuedate  <= '$tdate' AND d.itemid = '$itemid' AND d.sale_point_id='$sale_point_id'";
                }
            $querycot = $this->db->query($sqlcot);
            $result=$querycot->row_array();
			//$issueno_q = "SELECT issuenos from tbl_issue_goods m where m.issuedto='$acode' AND issuedate  >= '$fdate' AND issuedate  <= '$tdate' ";
			//$issueno_query = $this->db->query($issueno_q);
            //$issueno_result=$issueno_query->row_array();
            $new_data[]=$result;
			//$new_data[$count]['issueno']=$issueno_result;
             $new_data[$count]['materialcode']=$value['materialcode'];
             $new_data[$count++]['itemname']=$value['itemname'];
         }
         return $new_data;
    }
	 public function get_total_balance_pay($data){
      $date=$data['to_date'];
      $date=$data['to_date'];
      $login_user=$this->session->userdata('id');
      $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
      $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
      $customer_code=$fix_code['customer_code'];
      $vendor_code=$fix_code['vendor_code'];
            $query1 = "SELECT opngbl,optype,cell,phone_no,address,aname,acode,general FROM `tblacode` WHERE general in ('$vendor_code','$customer_code') AND reg_date<'$date'";
            $result = $this->db->query($query1);
            $line = $result->result_array();
    for ($i=0; $i<count($line); $i++) {
   $acode= $line[$i]['acode'];
   $query2 = $this->db->query("SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode'");
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
// //entry in next db
//   $hostname = "localhost";
// $username = "root";
// $password = "";
// $database = "hasnantraders_2021";
// $Conn_db = mysql_connect($hostname, $username, $password) or die(mssql_error());
// mysql_select_db($database, $Conn_db);
// $opngl_enter=$line[$i]['new_balance_pay'];
// $optype=$line[$i]['optype'];
// if($optype=='Credit'){
// mysql_query("update tblacode set opngbl='$opngl_enter',optype='$optype' where  acode='$acode' ");
// }
// //ends here
  $acode= $line[$i]['general'];
}
 // pm($line);
    return $line;
    }
	 public function get_total_balance_pay1($data=''){
      $date=$data->to_date;
      $login_user=$this->session->userdata('id');
      $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
      $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
      $customer_code=$fix_code['customer_code'];
      $vendor_code=$fix_code['vendor_code'];
      if ($sale_point_id=='0') {
        $sale_point_id=$data['sale_point_id'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $customer_code=$fix_code['customer_code'];
        $vendor_code=$fix_code['vendor_code'];
      }
      $query1 = "SELECT opngbl,optype,cell,phone_no,address,aname,acode,general FROM `tblacode` WHERE tblacode.general in ('$vendor_code','$customer_code')   ";
            $result = $this->db->query($query1);
            $line = $result->result_array();
    for ($i=0; $i<count($line); $i++) {
   $acode= $line[$i]['acode'];
   $query2 = $this->db->query("SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and sale_point_id='$sale_point_id'");
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
  $acode= $line[$i]['general'];
}
 // pm($line);
    return $line;
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
    public function get_total_customer_stock($data){
       $sale_point_id=$data['location'];
        $fdate=$data['to_date'];
        $scode=$data['scode'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $customer_code=$fix_code['customer_code'];
          if($customer_code !=''){ $where_customer= " and tblacode.general='$customer_code'  "; }else{ $where_customer =""; }
            $query1 = "SELECT opngbl,optype,phone_no,address,aname,acode FROM `tblacode`
			WHERE atype='Child' $where_customer";
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
           if($scode !='0'){ $where_scode= "and scode='$scode'  "; }else{ $where_scode =""; }
          $sqlcot = "SELECT qty as opening FROM `tbl_customer_opening` WHERE  acode ='$acode' AND materialcode ='$itemid' AND sale_point_id='$sale_point_id' $where_scode";// AND materialcode ='$itemid'COALESCE(SUM(`qty`),0) as opening
      // print '<br><br>';
      $querycot = $this->db->query($sqlcot);
            $rowcot = $querycot->row_array();
        if($scode !='0'){ $where_scode= "and `tbl_issue_goods_detail`.`scode`='$scode'  "; }else{ $where_scode =""; }
        $sqls = " SELECT (
               (SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as sale   FROM `tbl_issue_goods`
         INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
         WHERE `issuedate`<='$fdate'  AND `issuedto` ='$acode' AND `tbl_issue_goods_detail`.`wrate`=0
         AND `tbl_issue_goods_detail`.`returns`=0 AND `tbl_issue_goods_detail`.`itemid`='$itemid' AND `tbl_issue_goods_detail`.`sale_point_id`='$sale_point_id' $where_scode
               )+
                (
               SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as sale   FROM `tbl_issue_goods`
         INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
         WHERE `issuedate`<='$fdate'  AND `issuedto` ='$acode' AND `tbl_issue_goods_detail`.`wrate`=0
         AND `tbl_issue_goods_detail`.`returns`>0 AND `tbl_issue_goods_detail`.`itemid`='$itemid' AND `tbl_issue_goods_detail`.`sale_point_id`='$sale_point_id'
         and tbl_issue_goods_detail.itemid!=tbl_issue_goods_detail.item_return $where_scode
         )+(
               SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`-`tbl_issue_goods_detail`.`returns`),0) as sale   FROM `tbl_issue_goods`
         INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
         WHERE `issuedate`<='$fdate'  AND `issuedto` ='$acode' AND `tbl_issue_goods_detail`.`wrate`=0
         AND `tbl_issue_goods_detail`.`returns`>0 AND `tbl_issue_goods_detail`.`itemid`='$itemid' AND `tbl_issue_goods_detail`.`sale_point_id`='$sale_point_id' and tbl_issue_goods_detail.itemid=tbl_issue_goods_detail.item_return $where_scode
         ))as  sale" ;
 // print                        $sqls;
         // print '<br><br>';
            $querys = $this->db->query($sqls)->row_array();
 if($scode !='0'){ $where_scode_is= "and `tbl_issue_return_detail`.`branch_code`='$scode'  "; }else{ $where_scode_is =""; }
 if($scode !='0'){ $where_sscode_is= "and `tbl_issue_goods_detail`.`scode`='$scode'  "; }else{ $where_scode_is =""; }
 if($scode !='0'){ $where_wo_sec_scode_is= "and `tbl_goodsreceiving_detail`.`scode`='$scode'  "; }else{ $where_wo_sec_scode_is =""; }
             $sqlr = "select(SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returns  FROM `tbl_issue_return`
      INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos`
      WHERE `irdate`<='$fdate' AND `scode` ='$acode' AND `tbl_issue_return_detail`.`itemid`='$itemid' and wrate=0 AND `tbl_issue_return_detail`.`sale_point_id`='$sale_point_id' $where_scode_is
        )
      +
      (SELECT  COALESCE(SUM(`tbl_issue_goods_detail`.`returns`),0) as returns  FROM `tbl_issue_goods`
      INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
      WHERE `issuedate`<='$fdate' AND `issuedto` ='$acode' AND `tbl_issue_goods_detail`.`item_return`='$itemid' AND `tbl_issue_goods_detail`.`sale_point_id`='$sale_point_id'
               and tbl_issue_goods_detail.itemid!=tbl_issue_goods_detail.item_return $where_sscode_is
      ) +
      (SELECT  COALESCE(SUM(`tbl_goodsreceiving_detail`.`quantity`),0) as returns  FROM `tbl_goodsreceiving`
      INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id`
      WHERE `receiptdate`<='$fdate' AND `suppliercode` ='$acode' AND `tbl_goodsreceiving_detail`.`itemid`='$itemid' AND `tbl_goodsreceiving_detail`.`sale_point_id`='$sale_point_id' $where_wo_sec_scode_is
      ) as returns
      ";
        // print $sqlr;
      // print '<br>';
            $queryr = $this->db->query($sqlr)->row_array();
      //////for security  received
      $sec = "SELECT  COALESCE(SUM(`tbl_security_receipt`.`qty`),0) as securytrecv
      FROM `tbl_security_receipt`
      WHERE `customercode` ='$acode'  and `dt`<'$fdate'
      AND itemid ='$itemid'";
      $queryreturnfsec = $this->db->query($sec);
      $return_qtyfsec = $queryreturnfsec->row_array();
	   $opening_balance= $rowcot['opening']+$querys['sale']-$queryr['returns']-$return_qtyfsec['securytrecv'];
                $opening_balance_sum=$opening_balance;
                $line[$i]['stock'][$itemid]=$opening_balance;
                    if($opening_balance<0)
                    {
                      //  $opening_balance_sum=$opening_balance_sum*-1;
                       // $total_opening_balance=$total_opening_balance+$opening_balance_sum;
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
    public function get_total_customer_stock_one($id){
            $query1 = "SELECT opngbl,optype,phone_no,address,aname,acode FROM `tblacode`
			WHERE acode ='$id'";
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
            $itemname=$coding_line[$j]['itemname'];
                 $sqlcot = "SELECT   COALESCE(SUM(`tbl_customer_opening`.`qty`),0) as open_qty   FROM `tbl_customer_opening`
				WHERE acode ='$acode' AND materialcode ='$itemid'";
                $querycot = $this->db->query($sqlcot);
                $rowcot = $querycot->row_array();
			  $sqlsc = "SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty` - `tbl_issue_goods_detail`.`returns`),0) as igsumq   FROM `tbl_issue_goods`
			INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
			WHERE   tbl_issue_goods.decanting!='Yes' and tbl_issue_goods.`issuedto` ='$acode'
			AND `tbl_issue_goods_detail`.`itemid`='$itemid'  AND `tbl_issue_goods_detail`.`wrate`=0";
			$querysc = $this->db->query($sqlsc);
			$saltcusf = $querysc->row_array();
			$sqlreturnf = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returnqtyf
			FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON
			`tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos`
			WHERE `scode` ='$acode'
			AND `tbl_issue_return`.`type`='salereturn' AND `tbl_issue_return_detail`.`itemid`='$itemid'";
			$queryreturnf = $this->db->query($sqlreturnf);
			$return_qtyf = $queryreturnf->row_array();
                $opening_balance= $rowcot['open_qty']+$saltcusf['igsumq']-$return_qtyf['returnqtyf'];
                $opening_balance_sum=$opening_balance;
				if($opening_balance>0){
                $line[$i]['stock'][$itemid]=$itemname.'  : '.$opening_balance;
                }
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
		 $date=$data['to_date'];
            $query1 = "SELECT opngbl,optype,cell,phone_no,address,aname,reg_date,acode,general FROM `tblacode`
			WHERE  LEFT(acode,7) in ('1001001','2004001')
 AND acode not in ('1001001000' ,'2004001000','4001002000') AND reg_date<'$date' ";
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
  //entry in next db
//   $hostname = "localhost";
// $username = "root";
// $password = "";
// $database = "hasnantraders_2021";
// $Conn_db = mysql_connect($hostname, $username, $password) or die(mssql_error());
// mysql_select_db($database, $Conn_db);
// $opngl_enter=$line[$i]['new_balance'];
// $optype=$line[$i]['optype'];
// if($optype=='Debit'){
// mysql_query("update tblacode set opngbl='$opngl_enter',optype='$optype' where  acode='$acode' ");
// }
//ends here
        $acode= $line[$i]['general'];
}
  //  pm($line);
    return $line;
    }
    public function get_total_balancenew($data){
         $date=$data['to_date'];
            $query1 = "SELECT opngbl,optype,cell,phone_no,address,aname,reg_date,acode,general FROM `tblacode`
            WHERE  LEFT(acode,7) in ('2004001','4001002')
 AND acode not in ('1001001000' ,'2004001000','4001002000') AND reg_date<'$date' ";
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
        $acode= $line[$i]['general'];
}
  //  pm($line);
    return $line;
    }
	   public function get_total_balance1($data=''){

      $login_user=$this->session->userdata('id');
	  $date = $data->to_date;
      $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
      $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
      $customer_code=$fix_code['customer_code'];
      $vendor_code=$fix_code['vendor_code'];
       if ($sale_point_id=='0') {
        $sale_point_id=$data['sale_point_id'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $customer_code=$fix_code['customer_code'];
        $vendor_code=$fix_code['vendor_code'];
      }
      $query1 = "SELECT opngbl,optype,cell,phone_no,address,aname,reg_date,acode,general FROM `tblacode`
      WHERE  general in ('$customer_code') ";
       $result = $this->db->query($query1);
            $line = $result->result_array();
            //pm($line);
    for ($i=0; $i<count($line); $i++) {
     $acode= $line[$i]['acode'];
    $query2 = $this->db->query("SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode'");
   $credit_debit = $query2->result_array();
     $change_difference=$credit_debit[0]['op_damount']-$credit_debit[0]['op_camount'];
$opngbl_new=$line[$i]['opngbl'];
if($line[$i]['optype']=='Credit')
{
$opngbl_new=-$line[$i]['opngbl'];
   $line[$i]['new_balance']=-$line[$i]['opngbl']+ $change_difference;
}else{
       $line[$i]['new_balance']=$line[$i]['opngbl']+ $change_difference;
}
if($line[$i]['new_balance']<=0){$line[$i]['optype']='Credit'; }else{$line[$i]['optype']='Debit';}
        $acode= $line[$i]['general'];
}
  //  pm($line);
    return $line;
    }
         public function get_total_balance_date($data){
      $to_date=$data['to_date'];
      $login_user=$this->session->userdata('id');
      $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
      $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
      $customer_code=$fix_code['customer_code'];
      $vendor_code=$fix_code['vendor_code'];
       if ($sale_point_id=='0') {
        $sale_point_id=$data['sale_point_id'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $customer_code=$fix_code['customer_code'];
        $vendor_code=$fix_code['vendor_code'];
      }
      $query1 = "SELECT opngbl,optype,cell,phone_no,address,aname,reg_date,acode,general FROM `tblacode`
      WHERE  general in ('$customer_code') ";
       $result = $this->db->query($query1);
            $line = $result->result_array();
            //pm($line);
    for ($i=0; $i<count($line); $i++) {
     $acode= $line[$i]['acode'];
     $from_date= "2020-12-31";
    $query2 = $this->db->query("SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and vdate BETWEEN '$from_date' and '$to_date'");
   $credit_debit = $query2->result_array();
     $change_difference=$credit_debit[0]['op_damount']-$credit_debit[0]['op_camount'];
$opngbl_new=$line[$i]['opngbl'];
if($line[$i]['optype']=='Credit')
{
$opngbl_new=-$line[$i]['opngbl'];
   $line[$i]['new_balance']=-$line[$i]['opngbl']+ $change_difference;
}else{
       $line[$i]['new_balance']=$line[$i]['opngbl']+ $change_difference;
}
if($line[$i]['new_balance']<=0){$line[$i]['optype']='Credit'; }else{$line[$i]['optype']='Debit';}
        $acode= $line[$i]['general'];
}
  //  pm($line);
    return $line;
    }
    public function getsaleledger($data,$item=''){
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
                $acode= " `tbl_issue_goods`.`issuedto`='".$data['acode']."'";
            }
            else
            {
                $acode= " `tbl_issue_goods`.`issuedto`='".$data['acode']."'";
            }
  //       $sqlj="SELECT tbl_issue_goods.*,tblacode.* FROM `tbl_issue_goods`
		// INNER JOIN `tblacode` ON `tbl_issue_goods`.`issuedto` = `tblacode`.`acode` WHERE `issuedate` BETWEEN '$fdate' AND '$tdate' $acode $condj $condj1 ORDER BY `issuedate` ASC";
         $sqlj="SELECT tbl_issue_goods.*,tblacode.* FROM `tbl_issue_goods`
        INNER JOIN `tblacode` ON `tbl_issue_goods`.`issuedto` = `tblacode`.`acode` WHERE `issuedate` BETWEEN '$fdate' AND '$tdate'
		and $acode $condj $condj1 ORDER BY `issuedate` ASC";
   $queryj = $this->db->query($sqlj);
        if($queryj->num_rows()>0){
            foreach($queryj->result_array() as $key => $value) {
	//  $sqljj="SELECT itemid , wrate ,returns ,qty , sprice FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."'
 // AND wrate > 0  AND returns =0 $condj2
 // UNION SELECT itemid , wrate ,returns ,qty  , sprice
 // FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."'
 // AND wrate = 0  AND returns =0  $condj2
 // UNION SELECT itemid , wrate ,returns ,qty , sprice FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."'
 // AND wrate > 0  AND returns > 0 $condj2
 // UNION SELECT itemid , wrate ,returns ,qty-returns as qty , sprice FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."'
 // AND wrate = 0  AND returns > 0
 //  $condj2";
    //echo "<br>";
 //exit;
	  $sqljj="  SELECT itemid , wrate ,returns ,qty  , sprice
 FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."'
 AND wrate = 0  AND returns =0    $condj2
 UNION SELECT itemid , wrate ,returns ,qty as qty , sprice FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."'
 AND wrate = 0  AND returns > 0  and itemid!=item_return
 UNION SELECT itemid , wrate ,returns ,qty as qty , sprice FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."'
 AND wrate = 0  AND returns > 0  and itemid=item_return
 $condj2";
$queryjj = $this->db->query($sqljj)->result_array();
if(!empty($queryjj))
{
$dataj[] = array(
                    'issuenos' => $value['issuenos'],
                    'aname' => $value['aname'],
                    'issuedate' => $value['issuedate'],
                    'sale'=>$queryjj,
                );
}
            }
        }
        return $dataj;
    }
    public function get_opening($data,$category_id=''){
      // $login_user=$this->session->userdata('id');
      // $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        if($category_id==1)
        {
            $category_id='WHERE catcode=1';
        }
         $fdate=$data['from_date'];
         $tdate=$data['to_date'];
        $acode= $data['acode'];
        $sale_point_id=$data['location'];
        $scode=$data['scode'];
         if($scode !='0'){ $where_scode= "and scode='$scode'  "; }else{ $where_scode =""; }
        //echo $sale_point_id;exit();
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
       // $sql="SELECT * from `tblmaterial_coding` where materialcode='11'";
        $query = $this->db->query($sql);
        if($query->num_rows()>0){
            foreach($query->result_array() as $key => $value) {
                $itemid=$value['materialcode'];
            ///////// opening start    ////
if($scode !='0'){ $where_scode= "and scode='$scode'  "; }else{ $where_scode =""; }
          $sqlcot = "SELECT qty as opening FROM `tbl_customer_opening` WHERE  acode ='$acode' AND materialcode ='$itemid' AND sale_point_id='$sale_point_id' $where_scode";// AND materialcode ='$itemid'COALESCE(SUM(`qty`),0) as opening
			// print '<br><br>';
			$querycot = $this->db->query($sqlcot);
            $rowcot = $querycot->row_array();
        if($scode !='0'){ $where_scode= "and `tbl_issue_goods_detail`.`scode`='$scode'  "; }else{ $where_scode =""; }
        $sqls = " SELECT (
               (SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as sale   FROM `tbl_issue_goods`
			   INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
			   WHERE `issuedate`<'$fdate'  AND `issuedto` ='$acode' AND `tbl_issue_goods_detail`.`wrate`=0
			   AND `tbl_issue_goods_detail`.`returns`=0 AND `tbl_issue_goods_detail`.`itemid`='$itemid' AND `tbl_issue_goods_detail`.`sale_point_id`='$sale_point_id' $where_scode
               )+
                (
               SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as sale   FROM `tbl_issue_goods`
			   INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
			   WHERE `issuedate`<'$fdate'  AND `issuedto` ='$acode' AND `tbl_issue_goods_detail`.`wrate`=0
			   AND `tbl_issue_goods_detail`.`returns`>0 AND `tbl_issue_goods_detail`.`itemid`='$itemid' AND `tbl_issue_goods_detail`.`sale_point_id`='$sale_point_id'
			   and tbl_issue_goods_detail.itemid!=tbl_issue_goods_detail.item_return $where_scode
			   )+(
               SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`-`tbl_issue_goods_detail`.`returns`),0) as sale   FROM `tbl_issue_goods`
			   INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
			   WHERE `issuedate`<'$fdate'  AND `issuedto` ='$acode' AND `tbl_issue_goods_detail`.`wrate`=0
			   AND `tbl_issue_goods_detail`.`returns`>0 AND `tbl_issue_goods_detail`.`itemid`='$itemid' AND `tbl_issue_goods_detail`.`sale_point_id`='$sale_point_id' and tbl_issue_goods_detail.itemid=tbl_issue_goods_detail.item_return $where_scode
			   ))as  sale" ;
 // print                        $sqls;
         // print '<br><br>';
            $querys = $this->db->query($sqls)->row_array();
 if($scode !='0'){ $where_scode_is= "and `tbl_issue_return_detail`.`branch_code`='$scode'  "; }else{ $where_scode_is =""; }
 if($scode !='0'){ $where_sscode_is= "and `tbl_issue_goods_detail`.`scode`='$scode'  "; }else{ $where_scode_is =""; }
 if($scode !='0'){ $where_wo_sec_scode_is= "and `tbl_goodsreceiving_detail`.`scode`='$scode'  "; }else{ $where_wo_sec_scode_is =""; }
             $sqlr = "select(SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returns  FROM `tbl_issue_return`
			INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos`
			WHERE `irdate`<'$fdate' AND `scode` ='$acode' AND `tbl_issue_return_detail`.`itemid`='$itemid' and wrate=0 AND `tbl_issue_return_detail`.`sale_point_id`='$sale_point_id' $where_scode_is
				)
			+
			(SELECT  COALESCE(SUM(`tbl_issue_goods_detail`.`returns`),0) as returns  FROM `tbl_issue_goods`
			INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
			WHERE `issuedate`<'$fdate' AND `issuedto` ='$acode' AND `tbl_issue_goods_detail`.`item_return`='$itemid' AND `tbl_issue_goods_detail`.`sale_point_id`='$sale_point_id'
						   and tbl_issue_goods_detail.itemid!=tbl_issue_goods_detail.item_return $where_sscode_is
			) +
      (SELECT  COALESCE(SUM(`tbl_goodsreceiving_detail`.`quantity`),0) as returns  FROM `tbl_goodsreceiving`
      INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id`
      WHERE `receiptdate`<'$fdate' AND `suppliercode` ='$acode' AND `tbl_goodsreceiving_detail`.`itemid`='$itemid' AND `tbl_goodsreceiving_detail`.`sale_point_id`='$sale_point_id' $where_wo_sec_scode_is
      ) as returns
			";
        // print $sqlr;
		  // print '<br>';
            $queryr = $this->db->query($sqlr)->row_array();
 			//////for security  received
			$sec = "SELECT  COALESCE(SUM(`tbl_security_receipt`.`qty`),0) as securytrecv
			FROM `tbl_security_receipt`
			WHERE `customercode` ='$acode'  and `dt`<'$fdate'
			AND itemid ='$itemid'";
			$queryreturnfsec = $this->db->query($sec);
			$return_qtyfsec = $queryreturnfsec->row_array();
            $datas[] = array(
                    'itemid' => $itemid,
                    'opening' => $rowcot['opening']+$querys['sale']-$queryr['returns']-$return_qtyfsec['securytrecv'],
                );
    }
}
return $datas;
    }
    public function getsale($data,$item=''){
            $fdate=$data['from_date'];
            $tdate=$data['to_date'];
            $sale_point_id=$data['location'];
            $scode=$data['scode'];
         //   echo $sale_point_id;exit;
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
             if($scode !='0'){ $where_scode= "and tbl_issue_goods.scode='$scode'  "; }else{ $where_scode =""; }
        $sqlj="SELECT tbl_issue_goods.*,tblacode.* FROM `tbl_issue_goods`
		INNER JOIN `tblacode` ON `tbl_issue_goods`.`issuedto` = `tblacode`.`acode` WHERE `issuedate`
		BETWEEN '$fdate' AND '$tdate' AND `tbl_issue_goods`.`sale_point_id`='$sale_point_id' $acode $condj $condj1 $where_scode ORDER BY `issuedate` ASC";
   $queryj = $this->db->query($sqlj);
        if($queryj->num_rows()>0){
            foreach($queryj->result_array() as $key => $value) {
    if($scode !='0'){ $where_scode_is= "and tbl_issue_goods_detail.scode='$scode'  "; }else{ $where_scode_is =""; }
	//   $sqljj="  SELECT itemid , wrate ,returns ,qty  , sprice
 // FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."'
 // AND type in ('wo_sec','refill') $condj2 $where_scode_is
 // UNION SELECT itemid , wrate ,returns ,qty as qty , sprice FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."'
 // AND wrate = 0  AND returns > 0  and itemid!=item_return $where_scode_is
 // UNION SELECT itemid , wrate ,returns ,qty-returns as qty , sprice FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."'
 // AND wrate = 0  AND returns > 0  and itemid=item_return
 // $condj2 $where_scode_is group by type";
    $sqljj="SELECT itemid , wrate ,returns ,sum(qty)-sum(returns) as qty , sprice FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."'
   and itemid=item_return  AND type in ('wo_sec','refill')
 $condj2 $where_scode_is";
$queryjj = $this->db->query($sqljj)->result_array();
if(!empty($queryjj))
{
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
            ////// sale end //////////
    }
    public function getsaler($data,$item=''){
            $fdate=$data['from_date'];
            $tdate=$data['to_date'];
            $sale_point_id=$data['location'];
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
        $sqlj="SELECT tbl_issue_goods.*,tblacode.* FROM `tbl_issue_goods`
		INNER JOIN `tblacode` ON `tbl_issue_goods`.`issuedto` = `tblacode`.`acode` WHERE `issuedate`
		BETWEEN '$fdate' AND '$tdate' AND `tbl_issue_goods`.`sale_point_id`='$sale_point_id' $acode $condj $condj1 ORDER BY `issuedate` ASC";
   $queryj = $this->db->query($sqlj);
        if($queryj->num_rows()>0){
            foreach($queryj->result_array() as $key => $value) {
	  $sqljj="  SELECT itemid , wrate ,returns ,qty  , sprice
 FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."'
 AND wrate = 0  AND returns =0  AND sale_point_id='$sale_point_id'  $condj2
 UNION SELECT itemid , wrate ,returns ,qty as qty , sprice FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."'
 AND wrate = 0  AND returns > 0  and itemid!=item_return and sale_point_id='$sale_point_id'
 UNION SELECT itemid , wrate ,returns ,qty  as qty , sprice FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."'
 AND wrate = 0  AND returns > 0  and itemid=item_return AND sale_point_id='$sale_point_id'
 $condj2";
$queryjj = $this->db->query($sqljj)->result_array();
if(!empty($queryjj))
{
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
            ////// sale end //////////
    }
    public function getsale_ledger($data,$item=''){
            $fdate=$data['from_date'];
            $tdate=$data['to_date'];
            $acode=$data['acode'];
            $condj = "";
            $condj1 = "";
            $condj2 = "";
		           $sql="SELECT * from `tblmaterial_coding` where catcode='1'";
        $query = $this->db->query($sql);
        $count=0;
            foreach($query->result_array() as $key => $value) {
                $itemid=$value['materialcode'];
		   $qty=0;
        $sqlj="SELECT tbl_issue_goods.*,tblacode.* FROM `tbl_issue_goods`
		INNER JOIN `tblacode` ON `tbl_issue_goods`.`issuedto` = `tblacode`.`acode`
		WHERE `tbl_issue_goods`.`issuedto` ='$acode' and `issuedate` BETWEEN '$fdate' AND '$tdate'  ORDER BY `issuedate` ASC";
   $queryj = $this->db->query($sqlj);
        if($queryj->num_rows()>0){
            foreach($queryj->result_array() as $key => $value) {
				$sqljj="SELECT itemid , wrate ,returns ,qty , sprice FROM `tbl_issue_goods_detail`
				where  `ig_detail_id`='".$value['issuenos']."'  and wrate=0   and itemid=item_return and itemid='$itemid' and type='refill'";
				$queryjj = $this->db->query($sqljj);
					foreach($queryjj->result_array() as $key => $valuejj) {
						$qty+=$valuejj['qty'];
					}
        }
        }
		$dataj[] = array(
                    'itemid' => $itemid,
                    'qty'=>$qty,
                    //'return'=>$rowreturn,
                );
			}
        //pm($dataj);
        return $dataj;
            ////// sale end //////////
    }
    public function getsale_ledger_security($data,$item=''){
            $fdate=$data['from_date'];
            $tdate=$data['to_date'];
            $acode=$data['acode'];
            $condj = "";
            $condj1 = "";
            $condj2 = "";
		        $sql="SELECT * from `tblmaterial_coding` where catcode='1'";
            $query = $this->db->query($sql);
            $count=0;
            foreach($query->result_array() as $key => $value) {
                $itemid=$value['materialcode'];
		            $qty=0;
                $sqlj="SELECT tbl_issue_goods.*,tblacode.* FROM `tbl_issue_goods` INNER JOIN `tblacode`
                ON `tbl_issue_goods`.`issuedto` = `tblacode`.`acode` WHERE `tbl_issue_goods`.`issuedto` ='$acode'
                and `issuedate` BETWEEN '$fdate' AND '$tdate'  ORDER BY `issuedate` ASC";
                $queryj = $this->db->query($sqlj);
                if($queryj->num_rows()>0){
                  foreach($queryj->result_array() as $key => $value) {
				            $sqljj="SELECT itemid , wrate ,returns ,qty , sprice FROM `tbl_issue_goods_detail`
				                    where `ig_detail_id`='".$value['issuenos']."'  and wrate>0 and itemid='$itemid' and type='security'";
                    $queryjj = $this->db->query($sqljj);
          					foreach($queryjj->result_array() as $key => $valuejj) {
          						$qty+=$valuejj['qty'];
          					}
			            }
					      }
    					  $sqlr = " select(SELECT  COALESCE(SUM(d.qty),0)   FROM tbl_issue_return m,  tbl_issue_return_detail d
    					  WHERE  m.irnos = d.irnos and m.irdate BETWEEN '$fdate' AND '$tdate'  AND m.scode ='$acode' AND d.itemid='$itemid' AND d.wrate>0  ) as returns ";
                $queryr = $this->db->query($sqlr)->row_array();
    						$returns=$queryr['returns'];
							  $sec = "SELECT  COALESCE(SUM(`tbl_security_receipt`.`qty`),0) as securytrecv FROM `tbl_security_receipt`
      			             WHERE `customercode` ='$acode'  and `dt` BETWEEN  '$fdate' AND '$tdate' AND itemid ='$itemid'";
      			     $queryreturnfsec = $this->db->query($sec);
      			     $return_qtyfsec = $queryreturnfsec->row_array();
      			     $qtyr=$return_qtyfsec['securytrecv'];
    		        $tot=$qty-$returns+$qtyr;
    		        $dataj[] = array(
                            'itemid' => $itemid,
                            'qty'=>$tot,
                        //'return'=>$rowreturn,
                           );
			      }       //pm($dataj);
        return $dataj;
    }
  public function getsale_ledger_wo_security($data,$item=''){
            $fdate=$data['from_date'];
            $tdate=$data['to_date'];
            $acode=$data['acode'];
            $condj = "";
            $condj1 = "";
            $condj2 = "";
               $sql="SELECT * from `tblmaterial_coding` where catcode='1'";
        $query = $this->db->query($sql);
        $count=0;
            foreach($query->result_array() as $key => $value) {
                $itemid=$value['materialcode'];
       $qty=0;
        $sqlj="SELECT tbl_issue_goods.*,tblacode.* FROM `tbl_issue_goods`
    INNER JOIN `tblacode` ON `tbl_issue_goods`.`issuedto` = `tblacode`.`acode`
    WHERE `tbl_issue_goods`.`issuedto` ='$acode' and `issuedate` BETWEEN '$fdate' AND '$tdate'  ORDER BY `issuedate` ASC";
   $queryj = $this->db->query($sqlj);
        if($queryj->num_rows()>0){
            foreach($queryj->result_array() as $key => $value) {
        $sqljj="SELECT itemid , wrate ,returns ,qty , sprice FROM `tbl_issue_goods_detail`
        where  `ig_detail_id`='".$value['issuenos']."'  and wrate=0   and itemid=item_return and itemid='$itemid' and type='wo_sec'";
        $queryjj = $this->db->query($sqljj);
          foreach($queryjj->result_array() as $key => $valuejj) {
            $qty+=$valuejj['qty'];
          }
        }
        }
    $dataj[] = array(
                    'itemid' => $itemid,
                    'qty'=>$qty,
                );
      }
        //pm($dataj);
        return $dataj;
            ////// sale end //////////
    }
    public function getreturn_ledger($data,$item=''){
      //pm($this->input->post());
            $fdate=$data['from_date'];
            $tdate=$data['to_date'];
            $acode=$data['acode'];
            $condj = "";
            $condj1 = "";
            $condj2 = "";
		        $sql="SELECT * from `tblmaterial_coding` where catcode='1'";
            $query = $this->db->query($sql);
            $count=0;
            foreach($query->result_array() as $key => $value) {
                $itemid=$value['materialcode'];
		            $qty=0;
          	  	$sqlj="SELECT * FROM `tbl_issue_return` WHERE scode='$acode' and  `irdate` BETWEEN '$fdate' AND '$tdate' ORDER BY `irdate` ASC";
		            $queryj = $this->db->query($sqlj);
                if($queryj->num_rows()>0){
                  foreach($queryj->result_array() as $key => $value) {
					           $sqljj="SELECT  qty   FROM `tbl_issue_return_detail` where  `irnos`='".$value['irnos']."'  and itemid='$itemid'
                     and wrate=0";
				             $queryjj = $this->db->query($sqljj);
          					foreach($queryjj->result_array() as $key => $valuejj) {
          						$qty+=$valuejj['qty'];
          					}
                  }
                }
		            $sqlj="SELECT tbl_issue_goods.*,tblacode.* FROM `tbl_issue_goods` INNER JOIN `tblacode` ON `tbl_issue_goods`.`issuedto` = `tblacode`.`acode` WHERE  `tbl_issue_goods`.`issuedto` ='$acode' and `issuedate` BETWEEN  '$fdate'
                  AND '$tdate'  ORDER BY `issuedate` ASC";
                $queryj = $this->db->query($sqlj);
                if($queryj->num_rows()>0){
                  foreach($queryj->result_array() as $key => $value) {
                      $sqljj="SELECT  returns   FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."' and item_return='$itemid' and type='refill'";
                      $queryjj = $this->db->query($sqljj);
                    	foreach($queryjj->result_array() as $key => $valuejj) {
                    		$qty+=$valuejj['returns'];
                    	}
                  }
                }
					//////for security  received
      			     $sec = "SELECT  COALESCE(SUM(`tbl_security_receipt`.`qty`),0) as securytrecv FROM `tbl_security_receipt`
      			             WHERE `customercode` ='$acode'  and `dt` BETWEEN  '$fdate' AND '$tdate' AND itemid ='$itemid'";
      			     $queryreturnfsec = $this->db->query($sec);
      			     $return_qtyfsec = $queryreturnfsec->row_array();
      			     $qty+=$return_qtyfsec['securytrecv'];
		             $dataj[] = array(
                              'itemid' => $itemid,
                              'qty'=>$qty,
                            );
			      }
        //pm($dataj);
        return $dataj;
            ////// sale end //////////
    }
        public function getreturn_wo_sec_ledger($data,$item=''){
      //pm($this->input->post());
            $fdate=$data['from_date'];
            $tdate=$data['to_date'];
            $acode=$data['acode'];
            $condj = "";
            $condj1 = "";
            $condj2 = "";
            $sql="SELECT * from `tblmaterial_coding` where catcode='1'";
            $query = $this->db->query($sql);
            $count=0;
            foreach($query->result_array() as $key => $value) {
                $itemid=$value['materialcode'];
                $qty=0;
                $sqlj="SELECT * FROM `tbl_goodsreceiving` WHERE suppliercode='$acode' and  `receiptdate` BETWEEN '$fdate' AND '$tdate' ORDER BY `receiptdate` ASC";
                $queryj = $this->db->query($sqlj);
                if($queryj->num_rows()>0){
                  foreach($queryj->result_array() as $key => $value) {
                     $sqljj="SELECT  quantity   FROM `tbl_goodsreceiving_detail` where  `receipt_detail_id`='".$value['receiptnos']."'  and itemid='$itemid'
                     and wrate=0  and sub_type='wo_sec_return'";
                     $queryjj = $this->db->query($sqljj);
                    foreach($queryjj->result_array() as $key => $valuejj) {
                      $qty+=$valuejj['quantity'];
                    }
                  }
                }
                $sqlj="SELECT tbl_issue_goods.*,tblacode.* FROM `tbl_issue_goods` INNER JOIN `tblacode` ON `tbl_issue_goods`.`issuedto` = `tblacode`.`acode` WHERE  `tbl_issue_goods`.`issuedto` ='$acode' and `issuedate` BETWEEN  '$fdate'
                  AND '$tdate'  ORDER BY `issuedate` ASC";
                $queryj = $this->db->query($sqlj);
                if($queryj->num_rows()>0){
                  foreach($queryj->result_array() as $key => $value) {
                      $sqljj="SELECT  returns   FROM `tbl_issue_goods_detail` where  `ig_detail_id`='".$value['issuenos']."' and item_return='$itemid' and type='wo_sec'";
                      $queryjj = $this->db->query($sqljj);
                      foreach($queryjj->result_array() as $key => $valuejj) {
                        $qty+=$valuejj['returns'];
                      }
                  }
                }
          //////for security  received
                 $sec = "SELECT  COALESCE(SUM(`tbl_security_receipt`.`qty`),0) as securytrecv FROM `tbl_security_receipt`
                         WHERE `customercode` ='$acode'  and `dt` BETWEEN  '$fdate' AND '$tdate' AND itemid ='$itemid'";
                 $queryreturnfsec = $this->db->query($sec);
                 $return_qtyfsec = $queryreturnfsec->row_array();
                 $qty+=$return_qtyfsec['securytrecv'];
                 $dataj[] = array(
                              'itemid' => $itemid,
                              'qty'=>$qty,
                            );
            }
        //pm($dataj);
        return $dataj;
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
		$acode= $data['acode'];
    $sale_point_id= $data['location'];
    $scode=$data['scode'];
         if($scode !='0'){ $where_scode= "and branch_code='$scode'  "; }else{ $where_scode =""; }
	  	$sqlj="SELECT * FROM `tbl_issue_return` WHERE `irdate` BETWEEN '$fdate' AND '$tdate' AND `scode`='$acode' AND `sale_point_id`='$sale_point_id' $where_scode ORDER BY `irdate` ASC";
		$queryj = $this->db->query($sqlj);
        if($queryj->num_rows()>0){
            foreach($queryj->result_array() as $key => $value) {
			$sqljj="SELECT irnos,qty,itemid FROM `tbl_issue_return_detail` where `irnos`='".$value['irnos']."'  and wrate='0' AND `sale_point_id`='$sale_point_id' $where_scode ORDER BY `tbl_issue_return_detail`.`itemid` ASC";
			$queryjj = $this->db->query($sqljj)->result_array();
			if(!empty($queryjj))
			{
$dataj[] = array(
                    'issuenos' => $value['irnos'],
					          'issuetype' => 'Return',
                    'issuedate' => $value['irdate'],
                    'return'=>$queryjj,
                    //'return'=>$rowreturn,
                );
}
            }
        }
    if($scode !='0'){ $where_scode_is= "and scode='$scode'  "; }else{ $where_scode =""; }
		$sqlj="SELECT * FROM `tbl_issue_goods` WHERE issuedate BETWEEN '$fdate' AND '$tdate' AND `issuedto`='$acode' AND `sale_point_id`='$sale_point_id' $where_scode_is ORDER BY `issuedate` ASC";
		$queryj = $this->db->query($sqlj);
        if($queryj->num_rows()>0){
            foreach($queryj->result_array() as $key => $value) {
			$sqljj="SELECT ig_detail_id as irnos ,returns as qty ,item_return as itemid FROM `tbl_issue_goods_detail` where `ig_detail_id`='".$value['issuenos']."'
			and itemid!=item_return and returns>0 $where_scode_is
			ORDER BY `tbl_issue_goods_detail`.`item_return` ASC";
			$queryjj = $this->db->query($sqljj)->result_array();
			if(!empty($queryjj))
			{
			$dataj[] = array(
                    'issuenos' => $value['issuenos'],
                    'issuetype' => 'Sale-Return',
                    'issuedate' => $value['issuedate'],
                    'return'=>$queryjj,
                    //'return'=>$rowreturn,
                );
}
            }
        }
		$sqlj="
		SELECT  *
			FROM `tbl_security_receipt`
			WHERE `customercode` ='$acode'
			 and dt BETWEEN '$fdate' AND '$tdate'   ORDER BY `dt` ASC";
		$queryj = $this->db->query($sqlj);
        if($queryj->num_rows()>0){
            foreach($queryj->result_array() as $key => $value) {
			  $trans_id= $value['trans_id'];
			  $sqljj="SELECT trans_id ,  qty , itemid FROM `tbl_security_receipt`
			  where `trans_id`='".$value['trans_id']."'  ";
			$queryjj = $this->db->query($sqljj)->result_array();
			if(!empty($queryjj)){
			$dataj[] = array(
                    'issuenos' => $value['trans_id'],
                    'issuetype' => 'Security Receipt',
                    'issuedate' => $value['dt'],
                    'return'=>$queryjj,
                    //'return'=>$rowreturn,
                );
			}
            }
        }
     // pm($dataj);
        return $dataj;
//exit;
            ////// sale end //////////
    }
    public function getreturn_wo_sec($data){
      $fdate=$data['from_date'];
    $tdate=$data['to_date'];
        if($data['day']!='')
           {
                $day=$data['day']+1;
                $date_temp = $data['month'] .' '. $day.' '.$data['year'];
                $tdate = date('Y-m-d', strtotime($date_temp));
                $fdate=$tdate;
           }
    $acode= $data['acode'];
    $sale_point_id= $data['location'];
    $scode=$data['scode'];
         if($scode !='0'){ $where_scode= "and scode='$scode'  "; }else{ $where_scode =""; }
      $sqlj="SELECT * FROM `tbl_goodsreceiving` WHERE `receiptdate` BETWEEN '$fdate' AND '$tdate' AND `suppliercode`='$acode' AND `sale_point_id`='$sale_point_id' $where_scode ORDER BY `receiptdate` ASC";
    $queryj = $this->db->query($sqlj);
        if($queryj->num_rows()>0){
            foreach($queryj->result_array() as $key => $value) {
      $sqljj="SELECT receipt_detail_id,quantity,itemid FROM `tbl_goodsreceiving_detail` where `receipt_detail_id`='".$value['receiptnos']."'  and wrate='0' AND `sale_point_id`='$sale_point_id' and sub_type='wo_sec_return' $where_scode ORDER BY `tbl_goodsreceiving_detail`.`itemid` ASC";
      $queryjj = $this->db->query($sqljj)->result_array();
      if(!empty($queryjj))
      {
$dataj[] = array(
                    'receiptnos' => $value['receiptnos'],
                    'issuetype' => 'Without Security Return',
                    'receiptdate' => $value['receiptdate'],
                    'return'=>$queryjj,
                    //'return'=>$rowreturn,
                );
}
            }
        }
return $dataj;
//exit;
    }
     public function getsales($data,$item=''){
            $fdate=$data['from_date'];
            $tdate=$data['to_date'];
            $sale_point_id=$data['location'];
            //$acode=$data['acode'];
           // pm($sale_point_id);exit();
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
            $acode= "";
            $condj = "";
            $condj1 = "";
            $condj2 = "";
            $condj3 = "";
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
                    if(!empty($data['brandname'])){
                        $condj3= " AND `tblmaterial_coding`.`brandname`='".$data['brandname']."'";
                    }
                    if($data['type']=='Empty'){ $where_type= " AND `tbl_issue_goods_detail`.`type`='sale'"; }else{ $where_type =""; }
                    if($data['acode']!='All'){ $where_acode= " AND `tbl_issue_goods`.`issuedto`='".$data['acode']."'"; }else{ $where_acode =""; }
            }
            else if($item==3)
            {
                $acode= " AND `tbl_issue_goods`.`issuedto`='".$data['acode']."'";
            }
            else
            {
                $acode= " AND `tbl_issue_goods`.`issuedto`='".$data['acode']."'";
            }
               $sqlj="SELECT tbl_issue_goods.*,tblacode.* FROM `tbl_issue_goods` INNER JOIN `tblacode` ON `tbl_issue_goods`.`issuedto` = `tblacode`.`acode` WHERE `issuedate` BETWEEN '$fdate' AND '$tdate' and `tbl_issue_goods`.`sale_point_id`='$sale_point_id' $acode $condj $condj1 $where_acode";
   $queryj = $this->db->query($sqlj);
        if($queryj->num_rows()>0){
            foreach($queryj->result_array() as $key => $value) {
    $sqljj="SELECT itemid , wrate ,returns ,sum(qty) as qty , sprice FROM `tbl_issue_goods_detail` INNER JOIN `tblmaterial_coding` ON `tbl_issue_goods_detail`.`itemid` = `tblmaterial_coding`.`materialcode` where  `ig_detail_id`='".$value['issuenos']."' and `sale_point_id`='$sale_point_id' and tblmaterial_coding.catcode='1'
  $condj2 $condj3 $where_type group by itemid ORDER BY `itemid` ASC";
if($condj3 || $condj2){$brand='brand';}
$queryjj = $this->db->query($sqljj)->result_array();
if(!empty($queryjj))
{
$dataj[] = array(
                    'issuenos' => $value['issuenos'],
                    'aname' => $value['aname'],
                    'issuedate' => $value['issuedate'],
                    'sale'=>$queryjj,
                    'brand'=>$brand,
                );
}
            }
        }
        return $dataj;
    }
 /*   public function get_sale($data){
        $daterange= $data['daterange'];
        $sr=explode("/",($daterange));
        $fdate=trim($sr[0]);
        $tdate=trim($sr[1]);
        $acode= $data['acode'];
        //sale
        $sqlsale = "SELECT itemid,issuenos,issuedate,COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as sales FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE `issuedate` BETWEEN '$fdate' AND '$tdate' AND `tbl_issue_goods_detail`.`returns`=0 AND `issuedto`=$acode";
        $querysale= $this->db->query($sqlsale);
        $rowsale = $querysale->result_array();
        return $rowsale;
    }
    public function get_return($data){
        $daterange= $data['daterange'];
        $sr=explode("/",($daterange));
        $fdate=trim($sr[0]);
        $tdate=trim($sr[1]);
        $acode= $data['acode'];
        //sale return
        $sqlreturn = "SELECT itemid,`tbl_issue_return`.`irnos`,irdate,COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returns FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `irdate` BETWEEN '$fdate' AND '$tdate' AND `scode`=$acode";
        $queryreturn= $this->db->query($sqlreturn);
        $rowreturn = $queryreturn->result_array();
        return $rowreturn;
    }*/
}
?>