<?php

class Mod_basestockreport extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }

       public function get_details($data){

        $date=$data['date'];
       
        $sql="SELECT * from `tblmaterial_coding`";
        $query = $this->db->query($sql);
         
        if($query->num_rows()>0){
            foreach($query->result_array() as $key => $value) {
                $itemname = $value['itemname'];
                $itemid = $value['materialcode'];

                /* here is code for base stock */
                /*   opening balnace start     */
                
				   $sqlo ="SELECT  COALESCE(SUM(`qty`),0) as opening  from `tbl_shop_opening` WHERE `date`<='$date' AND `materialcode`=$itemid";
                $queryo = $this->db->query($sqlo)->row_array();

                  $sqlpf = "SELECT  COALESCE(SUM(`tbl_goodsreceiving_detail`.`quantity`),0) as purfilled FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<='$date' AND `tbl_goodsreceiving_detail`.`type`='Filled' AND `tbl_goodsreceiving_detail`.`ereturn`=0 AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                  $querypf = $this->db->query($sqlpf)->row_array();


                  $sqlpe = "SELECT  COALESCE(SUM(`tbl_goodsreceiving_detail`.`quantity`),0) as purempty FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<='$date' AND `tbl_goodsreceiving_detail`.`type`='Empty' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                  $querype = $this->db->query($sqlpe)->row_array();


                
    
                  $sqls = "SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as sale   FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE `issuedate`<='$date' AND `tbl_issue_goods_detail`.`returns`=0 AND `tbl_issue_goods_detail`.`itemid`=$itemid";
                  $querys = $this->db->query($sqls)->row_array();



                $sqlr = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returns  FROM `tbl_issue_return`
				INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos`
				WHERE `irdate`<='$date' AND `tbl_issue_return_detail`.`itemid`=$itemid 	AND `tbl_issue_return`.`type`='salereturn'";
                $queryr = $this->db->query($sqlr)->row_array();


                $sqlrp = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as preturns  FROM `tbl_issue_return`
				INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos`
				WHERE `irdate`<='$date' AND `tbl_issue_return_detail`.`itemid`=$itemid	AND `tbl_issue_return`.`type`='purchasereturn'";
                $queryrp = $this->db->query($sqlrp)->row_array();


                $sql_con = "SELECT  COALESCE(SUM(`tbl_cylinderconversion_detail`.`qty`),0) as from_qty FROM `tbl_cylinderconversion_master` INNER JOIN `tbl_cylinderconversion_detail` ON `tbl_cylinderconversion_master`.`trans_id` = `tbl_cylinderconversion_detail`.`trans_id` WHERE `trans_date` <= '$date' AND `tbl_cylinderconversion_detail`.`type`='from' AND `tbl_cylinderconversion_detail`.`itemcode`=$itemid";
                $query_con = $this->db->query($sql_con);
                $recfrmvenf_con = $query_con->row_array();
                
                $sql_con_to = "SELECT  COALESCE(SUM(`tbl_cylinderconversion_detail`.`qty`),0) as to_qty FROM `tbl_cylinderconversion_master` INNER JOIN `tbl_cylinderconversion_detail` ON `tbl_cylinderconversion_master`.`trans_id` = `tbl_cylinderconversion_detail`.`trans_id` WHERE `trans_date` <= '$date' AND `tbl_cylinderconversion_detail`.`type`='to' AND `tbl_cylinderconversion_detail`.`itemcode`=$itemid";
                $query_con_to = $this->db->query($sql_con_to);
                $recfrmvenf_con_to = $query_con_to->row_array();




                /* end here is code for base stock */

                $datas[] = array(
                    'itemid' => $itemname,
                    'from_qty' => $recfrmvenf_con['from_qty'],
                    'to_qty' => $recfrmvenf_con_to['to_qty'],
                    'opening' => $queryo['opening'],
                    'purchased' =>$querypf['purfilled']+$querype['purempty'],
                    'sale'=>$querys['sale'],
                    'return'=>$queryr['returns'],
                    'preturn'=>$queryrp['preturns'],
                    'fromdate'=>$date,
                );
            
            }
        }
        //pm($datas);
        return $datas;
    }
}

?>