<?php

class Mod_vendorwise extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }

    public function get_details($data){
        
            $fromdate=$data['from_date'];
            $todate=$data['to_date'];
            $sale_point_id=$data['location'];
            if($sale_point_id !=''){ $where_location= "AND tbl_goodsreceiving.sale_point_id='$sale_point_id'  "; }else{ $where_location =""; }

      

        $vendor = $data['vendor'];
        $items = $data['items'];

        $condj = "";
        $condj_detail = "";
        if(!empty($vendor)){
            $condj .= " AND suppliercode='$vendor'";
        }
        if(!empty($items)){
            $condj .= " AND `tbl_goodsreceiving_detail`.`itemid`='$items'";
            $condj_detail .= " AND `tbl_goodsreceiving_detail`.`itemid`='$items'";
        }

        

        $sqlj = "SELECT `tbl_goodsreceiving_detail`.`type`,tbl_goodsreceiving.*,tblmaterial_coding.*,tblacode.*,(`tbl_goodsreceiving_detail`.`ex_vat_amount`)
        as amounttotal,(`tbl_goodsreceiving_detail`.`quantity`) as totalquantity,(`tbl_goodsreceiving_detail`.`rate`) as rate ,(`tbl_goodsreceiving_detail`.`itemid`) as itemid ,(`tbl_goodsreceiving_detail`.`ereturn`) as empty_return FROM `tbl_goodsreceiving` 
        INNER JOIN `tblacode` ON `tbl_goodsreceiving`.`suppliercode` = `tblacode`.`acode` 
        INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` inner join tblmaterial_coding on 
        `tbl_goodsreceiving_detail`.`itemid`=`tblmaterial_coding`.`materialcode`
        WHERE `receiptdate` BETWEEN '$fromdate' AND '$todate' $condj $where_location $condj_detail
        ORDER BY  `receiptdate`   ";

        //  $sqlj = "SELECT `tbl_goodsreceiving_detail`.`type`,tbl_goodsreceiving.*,tblacode.*,SUM(`tbl_goodsreceiving_detail`.`inc_vat_amount`)
        // as amounttotal,SUM(`tbl_goodsreceiving_detail`.`quantity`) as totalquantity FROM `tbl_goodsreceiving` 
        // INNER JOIN `tblacode` ON `tbl_goodsreceiving`.`suppliercode` = `tblacode`.`acode`
        // INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` 
        // WHERE `receiptdate` BETWEEN '$fromdate' AND '$todate' AND `tbl_goodsreceiving_detail`.`sale_point_id`='$sale_point_id' $condj GROUP BY `receipt_detail_id` 
        // ORDER BY  `receiptdate`   ";

		$queryj = $this->db->query($sqlj); 
        if($queryj->num_rows()>0){
            foreach($queryj->result_array() as $key => $value) {
			 
		
		$receiptnos=$value['receiptnos'];
		$receiptdate=$value['receiptdate'];
		$aname=$value['aname'];
		$totalquantity=$value['totalquantity'];
        $empty_return=$value['empty_return'];
		$amounttotal=$value['amounttotal'];
		$amountpaid=$value['total_paid'];
		$type=$value['type'];
        $rate=$value['rate'];
        $total_items=$value['itemname'];
        $itemid=$value['itemid'];
		
		
		
		// $total_items='';
  //       $coding_query1 = "SELECT * FROM `tbl_goodsreceiving_detail`,`tblmaterial_coding` where 
		// tbl_goodsreceiving_detail.itemid=tblmaterial_coding.materialcode and `tbl_goodsreceiving_detail`.`sale_point_id`='$sale_point_id' and receipt_detail_id='$receiptnos'  $condj_detail ";

  //       $coding_result = $this->db->query($coding_query1);
  //       $coding_line = $coding_result->result_array();
  //       $total_opening_balance=0;
  //       for ($j=0; $j<count($coding_line); $j++) {



  //           $itemid=$coding_line[$j]['materialcode'];
  //           $itemname=$coding_line[$j]['itemname'];
  //           $quantity=$coding_line[$j]['quantity'];
  //           $rate=$coding_line[$j]['rate'];
  //          	$total_items.= $itemname.':'.$quantity.' X '.$rate.',';
		   
		// }
         
		 
		 

 
$total_items=rtrim($total_items,',');
            $datas[] = array(
                    'receiptnos' => $receiptnos,
                    'receiptdate' => $receiptdate,
                    'totalquantity' => $totalquantity,
                    'empty_return' => $empty_return,
                    'amounttotal' => $amounttotal,
                    'amountpaid' => $amountpaid,
                    'type' => $type,
                    'aname' => $aname,
                    'items_detail' => $total_items,
                    'rate' => $rate,
                    'itemid' => $itemid,
          
                );
			}
			}
			
		//	pm($datas);
				   return $datas;
    }
     
   
}

?>