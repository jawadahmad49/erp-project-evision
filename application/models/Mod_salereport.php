<?php

class Mod_salereport extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }

    public function get_details_item_report($data){
        
        $fromdate=$data['from_date'];
        $todate=$data['to_date'];
        $sale_point_id=$data['location'];
        $sale_type=$data['sale_type'];

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
        if($data['type']=='Empty'){ $where_type= " AND `tbl_issue_goods_detail`.`type` in ('sale','security','wo_sec','refill')"; }else{ $where_type =""; }
        if($data['acode']!='All'){ $where_acode= " AND `tbl_issue_goods`.`issuedto`='".$data['acode']."'"; }else{ $where_acode =""; }
        if($data['sale_type']!=''){ $where_sale_type= " AND `tbl_issue_goods_detail`.`type`='$sale_type'"; }else{ $where_sale_type =""; }


           $sql = "SELECT tbl_issue_goods.*,tbl_issue_goods_detail.*,tblmaterial_coding.*,tblacode.* FROM `tbl_issue_goods` INNER JOIN `tblacode` ON `tbl_issue_goods`.`issuedto` = `tblacode`.`acode` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` INNER JOIN `tblmaterial_coding` ON `tbl_issue_goods_detail`.`itemid` = `tblmaterial_coding`.`materialcode` WHERE `issuedate` BETWEEN '$fromdate' AND '$todate' AND `tbl_issue_goods`.`sale_point_id`='$sale_point_id' AND `tblmaterial_coding`.`catcode`='1' $condj $where_type $where_acode $where_sale_type ORDER BY STR_TO_DATE(`issuedate`, '%d-%m-%y') DESC";

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
        $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fromdate=$data['from_date'];
        $todate=$data['to_date'];
        $sale_point_id=$data['sale_point_id'];


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

       $sql = "SELECT tbl_issue_goods.*,tblacode.*,SUM(`tbl_issue_goods_detail`.`total_amount`) as amounttotal FROM `tbl_issue_goods` INNER JOIN `tblacode` ON `tbl_issue_goods`.`issuedto` = `tblacode`.`acode` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE `issuedate`='$todate' AND `tbl_issue_goods_detail`.`sale_point_id`='$sale_point_id' $condj GROUP BY `ig_detail_id` ORDER BY STR_TO_DATE(`issuedate`, '%d-%m-%y') DESC";
       //WHERE start >= '2013-07-22' AND end <= '2013-06-13'
        $query = $this->db->query($sql);
        
       // $this->db->join('tbl_issue_goods_detail', ' tbl_issue_goods_detail.ig_detail_id= tbl_issue_goods.issuenos');
        //$this->db->group_by('ig_detail_id');
//SELECT * FROM `tbl_issue_goods` ORDER BY STR_TO_DATE(`issuedate`, '%d-%m-%y') DESC where issuedate between start and end
    // $query = $this->db->select('*')
    //                       ->from('tbl_issue_goods')
    //                       //->where('issuedate',$data['start'],$data['end'])
    //                       //->order_by('issuedate', "DESC")
    //                       //->order_by(date_format('issuedate','%d-%m-%y'),'DESC')
    //                       //->order_by(date_format(STR_TO_DATE('issuedate', '%d-%m-%y'),'%d-%m-%y'))
    //                       ->order_by(DATE_FORMAT('%d-%m-%y','issuedate'),'DESC')
    //                       ->get();
//echo "<pre>";print_r($query->result_array());
//pm($data);
//exit;
       //pm($data); exit;
//return $query->result();
        return $query->result_array();
    }
    public function get_appliances_report($data){
        
        $fromdate=$data['from_date'];
        $todate=$data['to_date'];
        $sale_point_id=$data['location'];

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
       
        if($data['acode']!='All'){ $where_acode= " AND `tbl_issue_goods`.`issuedto`='".$data['acode']."'"; }else{ $where_acode =""; }


           $sql = "SELECT tbl_issue_goods.*,tbl_issue_goods_detail.*,tblmaterial_coding.*,tblacode.* FROM `tbl_issue_goods` INNER JOIN `tblacode` ON `tbl_issue_goods`.`issuedto` = `tblacode`.`acode` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` INNER JOIN `tblmaterial_coding` ON `tbl_issue_goods_detail`.`itemid` = `tblmaterial_coding`.`materialcode` WHERE `issuedate` BETWEEN '$fromdate' AND '$todate' AND `tbl_issue_goods`.`sale_point_id`='$sale_point_id' AND `tblmaterial_coding`.`catcode`!='1' $condj  $where_acode ORDER BY STR_TO_DATE(`issuedate`, '%d-%m-%y') DESC";

          // $sql = "SELECT tbl_issue_goods.*,tbl_issue_goods_detail.* ,tblmaterial_coding.* FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` INNER JOIN `tblmaterial_coding` ON `tbl_issue_goods_detail`.`itemid` = `tblmaterial_coding`.`materialcode`WHERE `issuedate` BETWEEN '$fromdate' AND '$todate' $condj ORDER BY STR_TO_DATE(`issuedate`, '%d-%m-%y') DESC";
        
        $query = $this->db->query($sql);
        
        return $query->result_array();
    }

   
}

?>