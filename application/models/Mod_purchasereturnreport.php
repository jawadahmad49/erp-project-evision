<?php

class Mod_purchasereturnreport extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }

    public function get_details($data){
        //pm($data);
        // $daterange= $data['daterange'];
        // $sr=explode("/",($daterange));
        // $fromdate=trim($sr[0]);
        // $todate=trim($sr[1]);

            $fromdate=$data['from_date'];
            $todate=$data['to_date'];

            
        $condj = "";
        
        if(!empty($data['acode'])){
            $condj .= " AND tblacode.acode='".$data['acode']."'";
        }
        if(!empty($data['items'])){
            $condj .= " AND `tbl_issue_return_detail`.`itemid`='".$data['items']."'";
        }

       $sql = "SELECT tbl_issue_return.*,tblacode.*,SUM(`tbl_issue_return_detail`.`total_amount`) as amounttotal,SUM(`tbl_issue_return_detail`.`qty`) as qty FROM `tbl_issue_return` INNER JOIN `tblacode` ON `tbl_issue_return`.`scode` = `tblacode`.`acode` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `irdate` BETWEEN '$fromdate' AND '$todate' $condj GROUP BY `irnos` ORDER BY STR_TO_DATE(`irdate`, '%d-%m-%y') DESC";
       //WHERE start >= '2013-07-22' AND end <= '2013-06-13'
        $query = $this->db->query($sql);
        
 
        return $query->result_array();
    }
   
}

?>