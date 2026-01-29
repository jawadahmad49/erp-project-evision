<?php

class Mod_userlog extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }

    public function get_details_item_report($data){
        
        $fromdate=$data['from_date'];
        $todate=$data['to_date'];

        if(!$data){
            $fromdate=date("Y-m-d");
            $todate=date("Y-m-d");
        }

        $condj = "";
        if(!empty($data['transaction'])){
            $condj .= " AND tbl_issue_goods.sale_type='".$data['transaction']."'";
        }
        if(!empty($data['segment'])){
            $condj .= " AND tblacode.segment='".$data['segment']."'";
        }
        if(!empty($data['items'])){
            $condj .= " AND `tbl_issue_goods_detail`.`itemid`='".$data['items']."'";
        }
        if(!empty($data['brandname'])){
            $condj .= " AND `tblmaterial_coding`.`brandname`='".$data['brandname']."'";
        }


           $sql = "SELECT tbl_issue_goods.*,tbl_issue_goods_detail.*,tblmaterial_coding.*,tblacode.* FROM `tbl_issue_goods` INNER JOIN `tblacode` ON `tbl_issue_goods`.`issuedto` = `tblacode`.`acode` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` INNER JOIN `tblmaterial_coding` ON `tbl_issue_goods_detail`.`itemid` = `tblmaterial_coding`.`materialcode` WHERE `issuedate` BETWEEN '$fromdate' AND '$todate' $condj ORDER BY STR_TO_DATE(`issuedate`, '%d-%m-%y') DESC";

          // $sql = "SELECT tbl_issue_goods.*,tbl_issue_goods_detail.* ,tblmaterial_coding.* FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` INNER JOIN `tblmaterial_coding` ON `tbl_issue_goods_detail`.`itemid` = `tblmaterial_coding`.`materialcode`WHERE `issuedate` BETWEEN '$fromdate' AND '$todate' $condj ORDER BY STR_TO_DATE(`issuedate`, '%d-%m-%y') DESC";
        
        $query = $this->db->query($sql);
        
        return $query->result_array();
    }

    public function get_details_item_wise_report($data,$materialcode=''){
        
        $fromdate=$data['from_date'];
        $todate=$data['to_date'];

        if(!$data){
            $fromdate=date("Y-m-d");
            $todate=date("Y-m-d");
        }

        $condj = "";
        if(!empty($data['transaction'])){
            $condj .= " AND tbl_issue_goods.sale_type='".$data['transaction']."'";
        }
        if(!empty($data['segment'])){
            $condj .= " AND tblacode.segment='".$data['segment']."'";
        }
        if(!empty($data['items'])){
            $condj .= " AND `tbl_issue_goods_detail`.`itemid`='".$data['items']."'";
        }

           $sql = "SELECT tbl_issue_goods.*,tbl_issue_goods_detail.*,tblacode.* FROM `tbl_issue_goods` INNER JOIN `tblacode` ON `tbl_issue_goods`.`issuedto` = `tblacode`.`acode` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE `tbl_issue_goods_detail`.`itemid`='$materialcode' AND `issuedate` BETWEEN '$fromdate' AND '$todate' $condj ORDER BY STR_TO_DATE(`issuedate`, '%d-%m-%y') DESC";


          //exit();

           // echo $sql = "SELECT tblmaterial_coding.*,tbl_issue_goods_detail.* ,tbl_issue_goods.* FROM `tblmaterial_coding` LEFT JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods_detail`.`itemid` = `tblmaterial_coding`.`materialcode` LEFT JOIN `tbl_issue_goods` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`  WHERE `issuedate` BETWEEN '$fromdate' AND '$todate' $condj ORDER BY STR_TO_DATE(`issuedate`, '%d-%m-%y') DESC";
       // exit();
        $query = $this->db->query($sql);
        
        return $query->result_array();
    }
    
    public function get_details($data){
        
        // $daterange= $data['daterange'];
        // $sr=explode("/",($daterange));
        $userid=$data['user'];
     


       $sql = "SELECT * FROM `tbl_user_log`  WHERE `user_id`='$userid'   ORDER BY trans_id DESC";
     
        $query = $this->db->query($sql);
        
    
        return $query->result_array();
    }
   
}

?>