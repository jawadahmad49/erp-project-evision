<?php

class Mod_customerslist extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }
   

	 
    public function get_report($data){
       

    
	 
	 $from_date=$data['from_date'];
	 $to_date=$data['to_date'];
	 $type=$data['type'];
    
   $sql="select * from tblacode where through_app='$type' AND reg_date BETWEEN '$from_date' AND '$to_date'";
  //  $sql = "SELECT tbl_issue_goods_hosp.*,tbl_issue_goods_detail_hosp.*,tblmaterial_coding.*,tblacode.*,tbl_tank.* FROM `tbl_issue_goods_hosp` INNER JOIN `tblacode` ON `tbl_issue_goods_hosp`.`issuedto` = `tblacode`.`acode` INNER JOIN `tbl_issue_goods_detail_hosp` ON `tbl_issue_goods_hosp`.`issuenos` = `tbl_issue_goods_detail_hosp`.`ig_detail_id` INNER JOIN `tblmaterial_coding` ON `tbl_issue_goods_detail_hosp`.`itemid` = `tblmaterial_coding`.`materialcode` INNER JOIN `tbl_tank` ON `tbl_issue_goods_hosp`.`tank_id` = `tbl_tank`.`tank_id` WHERE `fill_direct`= 'fill' AND `sale_type`= 'Hosp' AND `issuedate` BETWEEN '$fromdate' AND '$todate' $condj ORDER BY STR_TO_DATE(`issuedate`, '%d-%m-%y') DESC";

          // $sql = "SELECT tbl_issue_goods.*,tbl_issue_goods_detail_hosp.* ,tblmaterial_coding.* FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail_hosp` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail_hosp`.`ig_detail_id` INNER JOIN `tblmaterial_coding` ON `tbl_issue_goods_detail_hosp`.`itemid` = `tblmaterial_coding`.`materialcode`WHERE `issuedate` BETWEEN '$fromdate' AND '$todate' $condj ORDER BY STR_TO_DATE(`issuedate`, '%d-%m-%y') DESC";
        
        $query = $this->db->query($sql);
        
        return $query->result_array();
    }
}
?>