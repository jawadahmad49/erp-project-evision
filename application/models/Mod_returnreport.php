<?php

class Mod_returnreport extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }

    public function get_details($data){
        $daterange= $data['daterange'];
        $sr=explode("/",($daterange));
        $fromdate=trim($sr[0]);
        $todate=trim($sr[1]);

        $condj = "";
        
        if(!empty($data['customer'])){
            $condj .= " AND tblacode.acode='".$data['customer']."'";
        }
        if(!empty($data['items'])){
            $condj .= " AND `tbl_issue_return_detail`.`itemid`='".$data['items']."'";
        }

       $sql = "SELECT tbl_issue_return.*,tblacode.*,SUM(`tbl_issue_return_detail`.`total_amount`) as amounttotal,SUM(`tbl_issue_return_detail`.`qty`) as qty FROM `tbl_issue_return` INNER JOIN `tblacode` ON `tbl_issue_return`.`scode` = `tblacode`.`acode` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `irdate` BETWEEN '$fromdate' AND '$todate' $condj GROUP BY `irnos` ORDER BY STR_TO_DATE(`irdate`, '%d-%m-%y') DESC";
       //WHERE start >= '2013-07-22' AND end <= '2013-06-13'
        $query = $this->db->query($sql);
        
       // $this->db->join('tbl_issue_return_detail', ' tbl_issue_return_detail.irnos= tbl_issue_return.issuenos');
        //$this->db->group_by('irnos');
//SELECT * FROM `tbl_issue_return` ORDER BY STR_TO_DATE(`irdate`, '%d-%m-%y') DESC where irdate between start and end
    // $query = $this->db->select('*')
    //                       ->from('tbl_issue_return')
    //                       //->where('irdate',$data['start'],$data['end'])
    //                       //->order_by('irdate', "DESC")
    //                       //->order_by(date_format('irdate','%d-%m-%y'),'DESC')
    //                       //->order_by(date_format(STR_TO_DATE('irdate', '%d-%m-%y'),'%d-%m-%y'))
    //                       ->order_by(DATE_FORMAT('%d-%m-%y','irdate'),'DESC')
    //                       ->get();
//echo "<pre>";print_r($query->result_array());
//pm($data);
//exit;
       //pm($data); exit;
//return $query->result();
        return $query->result_array();
    }
   
}

?>