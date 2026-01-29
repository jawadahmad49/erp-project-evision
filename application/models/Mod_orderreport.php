<?php

class Mod_orderreport extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }

    public function get_details($data){

            $fromdate=$data['from_date'];
            $todate=$data['to_date'];


            
        // $daterange= $data['daterange'];
        // $sr=explode("/",($daterange));
        // $fromdate=trim($sr[0]);
        // $todate=trim($sr[1]);

        $condj = "";
        if(!empty($data['status'])){
            $condj .= " AND tbl_orderbooking.status='".$data['status']."'";
        }

       $sql = "SELECT tbl_orderbooking.*,tblacode.*,`tbl_orderbooking_detail`.`quantity` as quantity,`tbl_orderbooking`.`id` as masterid FROM `tbl_orderbooking` INNER JOIN `tblacode` ON `tbl_orderbooking`.`acode` = `tblacode`.`acode` INNER JOIN `tbl_orderbooking_detail` ON `tbl_orderbooking`.`id` = `tbl_orderbooking_detail`.`orderid` WHERE `date` BETWEEN '$fromdate' AND '$todate' $condj GROUP BY `orderid` ORDER BY STR_TO_DATE(`date`, '%d-%m-%y') DESC";


               // $sql = "SELECT tbl_orderbooking.*,tblacode.*,`tbl_orderbooking_detail`.*,`tbl_orderbooking`.`id` as masterid FROM `tbl_orderbooking` INNER JOIN `tblacode` ON `tbl_orderbooking`.`acode` = `tblacode`.`acode` INNER JOIN `tbl_orderbooking_detail` ON `tbl_orderbooking`.`id` = `tbl_orderbooking_detail`.`orderid` WHERE `date` BETWEEN '$fromdate' AND '$todate' $condj GROUP BY `orderid` ORDER BY STR_TO_DATE(`date`, '%d-%m-%y') DESC";



       //WHERE start >= '2013-07-22' AND end <= '2013-06-13'
        $query = $this->db->query($sql);
        //pm($query->result_array());
        return $query->result_array();
    }
   
}

?>