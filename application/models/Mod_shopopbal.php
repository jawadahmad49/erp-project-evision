<?php

class Mod_shopopbal extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }

	
	public function edit_record($rid){		
		#------------ get record------------#
        $table = "tbl_resturant_reg";
        $where = "restaurant_id='" . $rid . "'";
        $result = $this->mod_common->select_single_records($table, $where);
		return $result;
	}

	public function get_itemname(){
		$this->db->select('*');    
		$this->db->from('tbl_shop_opening');
		$this->db->join('tblmaterial_coding', 'tbl_shop_opening.materialcode = tblmaterial_coding.materialcode','INNER');
		$this->db->join('tbl_sales_point', 'tbl_shop_opening.location = tbl_sales_point.sale_point_id');


		$this->db->order_by("trans_id", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}


	public function get_item_stock($itemid,$acode){
              
			  
			  
			  
			  	$sqls ="SELECT  COALESCE(SUM(`tbl_customer_opening`.`qty`),0) as open_qty
			from `tbl_customer_opening` WHERE  acode ='$acode' and  `materialcode`='$itemid'";
			$querys = $this->db->query($sqls)->row_array();
 
 
			  $sqlsc = "SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty` - `tbl_issue_goods_detail`.`returns`),0) as igsumq   FROM `tbl_issue_goods` 
			INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
			WHERE  tbl_issue_goods.issuedto ='$acode'   and  tbl_issue_goods.decanting!='Yes'
			AND `tbl_issue_goods_detail`.`itemid`='$itemid'  AND `tbl_issue_goods_detail`.`wrate`=0";
			$querysc = $this->db->query($sqlsc);
			$saltcusf = $querysc->row_array();

			  $sqlsc = "SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty` - `tbl_issue_goods_detail`.`returns`),0) as igsumq   FROM `tbl_issue_goods` 
			INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id`
			WHERE  tbl_issue_goods.issuedto ='$acode'   and  tbl_issue_goods.decanting!='Yes'
			AND `tbl_issue_goods_detail`.`item_return`='$itemid'  AND `tbl_issue_goods_detail`.`wrate`=0";
			$querysc = $this->db->query($sqlsc);
			$saltcusf = $querysc->row_array();


			$sqlreturnf = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returnqtyf  
			FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON 
			`tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` 
			WHERE  `scode` ='$acode'  and  `tbl_issue_return`.`type`='salereturn' AND `tbl_issue_return_detail`.`itemid`='$itemid'";
			$queryreturnf = $this->db->query($sqlreturnf);
			$return_qtyf = $queryreturnf->row_array();


 
 
                $total_opening_balance = $querys['open_qty']+$saltcusf['igsumq']-$return_qtyf['returnqtyf'];
   
       return $total_opening_balance;
	   
	   
	    
				
	}


	public function check_already($item,$type){
		//echo $item;
		//echo $type;
		//$this->db->select('*');    
		//$this->db->from('tbl_shop_opening');
		//$this->db->join('tblmaterial_coding', 'tbl_shop_opening.materialcode = tblmaterial_coding.materialcode');
		//$this->db->order_by("trans_id", "desc");
		//$query = $this->db->get();
		$query = $this->db->select('*')
                        ->from('tbl_shop_opening')
                        ->where('materialcode', $item)
                        ->where('type', $type)
                        ->get();
        //pm($query->num_rows());
		return $query->num_rows();
	}

  	public function check_already_edit($item,$type,$id) {
  		//echo $item;echo $type;echo $id;exit;
        $query = $this->db->select('*')
                        ->from('tbl_shop_opening')
                        ->where('materialcode', $item)
                        ->where('type', $type)
                        ->where('trans_id!=', $id)
                        ->get();
              //pm($query->result_array());          
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }
 
}

?>