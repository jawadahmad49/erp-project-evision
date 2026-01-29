<?php

class Mod_cylcondition extends CI_Model {

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

	public function get_itemname($from,$to){
		$this->db->select('*');  
		$this->db->select(' tbl_exchange_condition.dt,tbl_exchange_condition.cyl_type,tbl_exchange_condition.cyl_condition_from, tblmaterial_coding.itemname,tbl_exchange_condition.cyl_condition_to');  
		$this->db->from('tbl_exchange_condition');
		$this->db->join('tblmaterial_coding', 'tbl_exchange_condition.from_itemcode = tblmaterial_coding.materialcode');
		$this->db->order_by("trans_id", "desc");
		
		$this->db->where('tbl_exchange_condition.dt >=', $from);
		$this->db->where('tbl_exchange_condition.dt <=', $to);	

		$query = $this->db->get();
		return $query->result_array();
	}


	public function get_item_stock($itemid,$acode){
              $sqlcot = "SELECT qty as opening FROM `tbl_customer_opening` WHERE acode ='$acode' AND materialcode ='$itemid'";// AND materialcode ='$itemid'COALESCE(SUM(`qty`),0) as opening
                $querycot = $this->db->query($sqlcot);
                $rowcot = $querycot->row_array();

           // exit();

			    $sqls = " SELECT   (SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as sale 
				FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` 
				ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` 
				WHERE `issuedto` ='$acode' AND `tbl_issue_goods_detail`.`wrate`>0 
				AND `tbl_issue_goods_detail`.`returns`=0 AND `tbl_issue_goods_detail`.`itemid`='$itemid') 
				+ 
				(SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as sale   
				FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` 
				WHERE `issuedto` ='$acode' AND `tbl_issue_goods_detail`.`wrate`=0 AND `tbl_issue_goods_detail`.`returns`=0 
				AND `tbl_issue_goods_detail`.`itemid`='$itemid')
				+ 
				(SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as sale   FROM `tbl_issue_goods` 
				INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` 
				WHERE `issuedto` ='$acode' AND `tbl_issue_goods_detail`.`wrate`>0 AND `tbl_issue_goods_detail`.`returns`>0 
				AND `tbl_issue_goods_detail`.`itemid`='$itemid')

				+ 
				(SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`-`tbl_issue_goods_detail`.`returns`),0) as sale   FROM `tbl_issue_goods` 
				INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` 
				WHERE  `issuedto` ='$acode' AND `tbl_issue_goods_detail`.`wrate`=0 
				AND `tbl_issue_goods_detail`.`returns`>0 AND `tbl_issue_goods_detail`.`itemid`='$itemid'
				) as sale" ;
				
				
				 
 
            $querys = $this->db->query($sqls)->row_array();
 
            $sqlr = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returns
			FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` 
			ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `scode` ='$acode' 
			AND `tbl_issue_return_detail`.`itemid`='$itemid' ";
             
            $queryr = $this->db->query($sqlr)->row_array();

 
		// print '---opening:'.$rowcot['opening'];
		// print '---returns:'.$queryr['returns'];
		// print '---sale:'.$querys['sale'];
                $opening_balance= $rowcot['opening']+$querys['sale']-$queryr['returns'];

                //pm($opening_balance);


                $opening_balance_sum=$opening_balance;

                $line[$i]['stock'][$itemid]=$opening_balance;
                
                    if($opening_balance<0)
                    {
                        //$opening_balance_sum=$opening_balance_sum*-1;
                        $total_opening_balance=$total_opening_balance+$opening_balance_sum;
                    }
                    else
                    {
                        $total_opening_balance=$total_opening_balance+$opening_balance_sum;

                    }
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
                        ->from('tbl_exchange_condition')
                        ->where('from_itemcode', $item)
                        ->where('cyl_type', $type)
                        ->get();
        //pm($query->num_rows());
		return $query->num_rows();
	}

  	public function check_already_edit($item,$type,$id) {
  		//echo $item;echo $type;echo $id;exit;
        $query = $this->db->select('*')
                        ->from('tbl_exchange_condition')
                        ->where('cyl_condition_from',$condition)
                        
                        ->where('trans_id!=',$id)
                        ->get();
              //pm($query->result_array());          
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }
     function update_table($table = "", $data = "", $where = "") {

        $this->db->where($where);
      
        $update = $this->db->update($table, $data);
        if ($update) {
            return true;
        } else {
            return false;
        }

    }
    function insert_into_table($table, $data) {
        $insert = $this->db->insert($table, $data);
        $insert_id = $this->db->insert_id();
        if ($insert) {
            return $insert_id;
        } else {
            return false;
        }
    }

 
}

?>