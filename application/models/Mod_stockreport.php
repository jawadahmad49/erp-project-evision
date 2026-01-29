<?php

class Mod_stockreport extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }
       public function get_details($data){ 

        if($data['date']){

            $fromdate=$data['date'];
            $sale_point_id=$data['location'];
        
        }else{
            $fromdate=$data['from_date'];
            $todate=$data['to_date'];
            $sale_point_id=$data['location'];
        }

         $category_id=$data['category'];   

       
         $sql="SELECT * from `tblmaterial_coding` WHERE catcode=$category_id";

       
        $query = $this->db->query($sql);
         
        if($query->num_rows()>0){
            foreach($query->result_array() as $key => $value) {
                $materialcode = $value['materialcode'];
                $itemname = $value['itemname'];
                $itemid = $value['materialcode'];
                $catcode = $value['catcode'];

          
			////////////////////////////// PURCHASE Filled /////////////////////////////////
			  $query = "  select COALESCE(SUM(tbl_goodsreceiving_detail.quantity),0) as recv_from_vendor_f
			from tbl_goodsreceiving_detail, tbl_goodsreceiving
			where  tbl_goodsreceiving_detail.receipt_detail_id=tbl_goodsreceiving.receiptnos
			and tbl_goodsreceiving_detail.itemid='$itemid'
			and tbl_goodsreceiving.trans_typ in('purchasefilled','salereturn') 
			and tbl_goodsreceiving.receiptdate  >= '$fromdate' and tbl_goodsreceiving.receiptdate <= '$todate' AND tbl_goodsreceiving.sale_point_id='$sale_point_id' and tbl_goodsreceiving_detail.type='Filled'";
			$result = $this->db->query($query);
			$recv_from_vendor_f_row = $result->row_array();

			 
			
				
			////////////////////////////// PURCHASE Empty /////////////////////////////////
		 	$query = "  select COALESCE(SUM(tbl_goodsreceiving_detail.quantity),0) as recv_from_vendor_e
			from tbl_goodsreceiving_detail, tbl_goodsreceiving
			where  tbl_goodsreceiving_detail.receipt_detail_id=tbl_goodsreceiving.receiptnos
			and tbl_goodsreceiving_detail.itemid='$itemid'
			and tbl_goodsreceiving.trans_typ in('purchaseempty','salereturn') and tbl_goodsreceiving.receiptdate
            >= '$fromdate' and tbl_goodsreceiving.receiptdate <='$todate' AND tbl_goodsreceiving.sale_point_id='$sale_point_id' and tbl_goodsreceiving_detail.type='Empty'
			 ";
			$result = $this->db->query($query);
			$recv_from_vendor_e_row = $result->row_array();

				
				
			////////////////////////////// RECV FROM CUSTOMER FILLED /////////////////////////////////
			$recv_from_customer_f=0;
			
			
			
			////////////////////////////// RECV FROM CUSTOMER EMPTY /////////////////////////////////
			$query = "  select COALESCE(SUM(tbl_issue_goods_detail.returns),0) as recv_from_customer_e
			from tbl_issue_goods,tbl_issue_goods_detail where 
			tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id 
			and tbl_issue_goods_detail.item_return='$itemid' and tbl_issue_goods.issuedate 
			           >= '$fromdate' and tbl_issue_goods.issuedate  <= '$todate' 
			 AND tbl_issue_goods.sale_point_id='$sale_point_id'";
			$result = $this->db->query($query);
			$recv_from_customer_e_row = $result->row_array();

				
				 

					
				
			if($catcode==1){
			////////////////////////////// OUT to vendor filled /////////////////////////////////
			  $query = " select COALESCE(SUM(tbl_issue_return_detail.qty),0) as out_to_vendor_f
			from tbl_issue_return_detail, tbl_issue_return
			where  tbl_issue_return_detail.irnos=tbl_issue_return.irnos
			and tbl_issue_return_detail.itemid='$itemid'
			and tbl_issue_return_detail.type='Filled'
			and tbl_issue_return.type='purchasereturn' and  tbl_issue_return.irdate >=
			'$fromdate' and tbl_issue_return.irdate  <= '$todate'  AND tbl_issue_return.sale_point_id='$sale_point_id'";
			$result = $this->db->query($query);
			$out_to_vendor_f_row = $result->row_array();

			}else{
			
			////////////////////////////// OUT to vendor filled /////////////////////////////////
			  $query = " select COALESCE(SUM(tbl_issue_return_detail.qty),0) as out_to_vendor_f
			from tbl_issue_return_detail, tbl_issue_return
			where  tbl_issue_return_detail.irnos=tbl_issue_return.irnos
			and tbl_issue_return_detail.itemid='$itemid'
			and tbl_issue_return_detail.type='Filled'
			and tbl_issue_return.type='purchasereturnother' and  tbl_issue_return.irdate >=
			'$fromdate' and tbl_issue_return.irdate  <=    '$todate'  AND tbl_issue_return.sale_point_id='$sale_point_id'";
			$result = $this->db->query($query);
			$out_to_vendor_f_row = $result->row_array();
			}
			
			////////////////////////////// OUT to vendor empty /////////////////////////////////
			$query = "  select (select COALESCE(SUM(tbl_goodsreceiving_detail.ereturn),0) 
			from tbl_goodsreceiving_detail,tbl_goodsreceiving
			where  tbl_goodsreceiving_detail.receipt_detail_id=tbl_goodsreceiving.receiptnos
			and tbl_goodsreceiving_detail.itemid='$itemid'
			and tbl_goodsreceiving.trans_typ='purchasefilled' and tbl_goodsreceiving.receiptdate
			 >= '$fromdate' and tbl_goodsreceiving.receiptdate  <=    '$todate' AND tbl_goodsreceiving.sale_point_id='$sale_point_id'
			)
			+
			(
			select COALESCE(SUM(tbl_issue_return_detail.qty),0) as out_to_vendor_f
			from tbl_issue_return_detail, tbl_issue_return
			where  tbl_issue_return_detail.irnos=tbl_issue_return.irnos
			and tbl_issue_return_detail.itemid='$itemid'
			and tbl_issue_return_detail.type='Empty'
			and tbl_issue_return.type='purchasereturn' and  tbl_issue_return.irdate   >= '$fromdate' and tbl_issue_return.irdate <=    '$todate' AND tbl_issue_return.sale_point_id='$sale_point_id'
			)
			as out_to_vendor_e ";
			$result = $this->db->query($query);
			$out_to_vendor_e_row = $result->row_array();


				 
									
			////////////////////////////// SALE EMPTY /////////////////////////////////
			$query = "  select COALESCE(SUM(tbl_issue_goods_detail.qty),0) as sale_out_f
			from tbl_issue_goods,tbl_issue_goods_detail where 
			tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id 
			and tbl_issue_goods_detail.itemid='$itemid' and tbl_issue_goods_detail.type='Empty' and tbl_issue_goods.issuedate >= '$fromdate' and tbl_issue_goods.issuedate<= '$todate'
			 AND tbl_issue_goods.sale_point_id='$sale_point_id'";
		
			$result = $this->db->query($query);
			$sale_out_e_row = $result->row_array();

			
			$sale_out_e=$sale_out_e_row['sale_out_f'];
			
			

			
			////////////////////////////// SALE FILLED /////////////////////////////////
			$query = "select COALESCE(SUM(tbl_issue_goods_detail.qty),0) as sale_out_f
			from tbl_issue_goods,tbl_issue_goods_detail where 
			tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id 
			and tbl_issue_goods_detail.itemid='$itemid' and tbl_issue_goods.type='Fill' and tbl_issue_goods.issuedate >= '$fromdate' and tbl_issue_goods.issuedate<= '$todate'
			and tbl_issue_goods.decanting!='Yes' AND tbl_issue_goods.sale_point_id='$sale_point_id' ";
			
			$result = $this->db->query($query);
			$sale_out_f_row = $result->row_array();

				
				
			
			////////////////////////////// SALE RETURN FILLED /////////////////////////////////
			$query = " select COALESCE(SUM(tbl_issue_return_detail.qty),0) as sale_return_f
			from tbl_issue_return_detail, tbl_issue_return
			where  tbl_issue_return_detail.irnos=tbl_issue_return.irnos
			and tbl_issue_return_detail.itemid='$itemid'
			and tbl_issue_return_detail.type='Filled'
			and tbl_issue_return.type='salereturn' and  tbl_issue_return.irdate   >= '$fromdate' and tbl_issue_return.irdate  <=    '$todate' AND tbl_issue_return.sale_point_id='$sale_point_id'";
			$result = $this->db->query($query);
			$sale_return_f_row = $result->row_array();
 
				  
			
			////////////////////////////// SALE RETURN EMPTY /////////////////////////////////
			$query = " select COALESCE(SUM(tbl_issue_return_detail.qty),0) as sale_return_e
			from tbl_issue_return_detail, tbl_issue_return
			where  tbl_issue_return_detail.irnos=tbl_issue_return.irnos
			and tbl_issue_return_detail.itemid='$itemid'
			and tbl_issue_return_detail.type='Empty'
			and tbl_issue_return.type='salereturn' and  tbl_issue_return.irdate     >= '$fromdate' and tbl_issue_return.irdate  <=    '$todate' AND tbl_issue_return.sale_point_id='$sale_point_id'";
			$result = $this->db->query($query);
			$sale_return_e_row = $result->row_array();

				   
                  

			
			////////////////////////////// DECANT SALE EMPTY /////////////////////////////////
			$decant_sale_e=0;
			
			
			
			////////////////////////////// DECANT SALE FILLED /////////////////////////////////
			$query = "  select COALESCE(SUM(tbl_issue_goods_detail.qty),0) as decant_sale_f
			from tbl_issue_goods,tbl_issue_goods_detail where 
			tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id 
			and tbl_issue_goods_detail.itemid='$itemid' and tbl_issue_goods.issuedate  >= '$fromdate' and tbl_issue_goods.issuedate  <=    '$todate'
			and tbl_issue_goods.decanting='Yes' AND tbl_issue_goods.sale_point_id='$sale_point_id'";
			$result = $this->db->query($query);
			$decant_sale_f_row = $result->row_array();				  
                   
             


			////////////////////////////// DECANT RECV FILLED /////////////////////////////////
			$decant_empty_f=0;
			
			
			
			////////////////////////////// RECV FROM CUSTOMER EMPTY /////////////////////////////////
			$query = "  select COALESCE(SUM(tbl_issue_goods_detail.returns),0) as decant_empty_e
			from tbl_issue_goods,tbl_issue_goods_detail where 
			tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id 
			and tbl_issue_goods_detail.itemid='$itemid' and tbl_issue_goods.issuedate >= '$fromdate' and tbl_issue_goods.issuedate  <=    '$todate'
			and tbl_issue_goods.decanting='Yes'  AND tbl_issue_goods.sale_point_id='$sale_point_id' ";
			$result = $this->db->query($query);
			$decant_empty_e_row = $result->row_array();

			 
			
			
			
 
			////////////////////////////// CYLINDER CONVERSTION From /////////////////////////////////
				$query = " select COALESCE(SUM(tbl_cylinderconversion_detail.qty),0) as convert_from_f
			from tbl_cylinderconversion_detail,tbl_cylinderconversion_master where 
			tbl_cylinderconversion_master.trans_id=tbl_cylinderconversion_detail.trans_id 
			and tbl_cylinderconversion_detail.itemcode='$itemid' and tbl_cylinderconversion_master.trans_date >= '$fromdate' AND tbl_cylinderconversion_master.trans_date <= '$todate'
			and tbl_cylinderconversion_detail.`type`='from' AND tbl_cylinderconversion_detail.sale_point_id='$sale_point_id'";
			$result = $this->db->query($query);
			$convert_from_f_row = $result->row_array();
            $convert_to_e=$convert_from_f_row['convert_from_f'];
			
			
			////////////////////////////// CYLINDER CONVERSTION to /////////////////////////////////
			$query = " select COALESCE(SUM(tbl_cylinderconversion_detail.qty),0) as convert_to_f
			from tbl_cylinderconversion_detail,tbl_cylinderconversion_master where 
			tbl_cylinderconversion_master.trans_id=tbl_cylinderconversion_detail.trans_id 
			and tbl_cylinderconversion_detail.itemcode='$itemid' and tbl_cylinderconversion_master.trans_date >= '$fromdate' AND  tbl_cylinderconversion_master.trans_date <='$todate'
			and tbl_cylinderconversion_detail.`type`='to' AND tbl_cylinderconversion_detail.sale_point_id='$sale_point_id'";
			$result = $this->db->query($query);
			$convert_to_f_row = $result->row_array();
            $convert_from_e=$convert_to_f_row['convert_to_f'];
			
			
			
			//code to calculate damage stock starts here

			 ////////////////////////////// Filled CYLINDER Fresh to damage /////////////////////////////////
            $query = "SELECT sum(qty) as damagecylinder_f from tbl_exchange_condition where from_itemcode='$itemid' and cyl_condition_to='Damage' and cyl_type='Filled' and dt >= '$fromdate' AND dt <= '$todate'";
			$result = $this->db->query($query);
			$convert_to_f_row1 = $result->row_array();
            $damagecylinder_f = $convert_to_f_row1['damagecylinder_f'];

             ////////////////////////////// Filled CYLINDER Damage to Fresh /////////////////////////////////
            $query = "SELECT sum(qty) as freshcylinder_f from tbl_exchange_condition where from_itemcode='$itemid' and cyl_condition_to='Fresh' and cyl_type='Filled'  and dt >= '$fromdate' AND dt <= '$todate'";
			$result = $this->db->query($query);
			$convert_to_f_row2 = $result->row_array();
            $freshcylinder_f = $convert_to_f_row2['freshcylinder_f'];


            ////////////////////////////// Empty CYLINDER Fresh to damage /////////////////////////////////
            $query = "SELECT sum(qty) as damagecylinder_e from tbl_exchange_condition where from_itemcode='$itemid' and cyl_condition_to='Damage' and cyl_type='Empty' and dt >= '$fromdate' AND dt <= '$todate'";
			$result = $this->db->query($query);
			$convert_to_f_row3 = $result->row_array();
            $damagecylinder_e = $convert_to_f_row3['damagecylinder_e'];

             ////////////////////////////// Empty CYLINDER Damage to Fresh /////////////////////////////////
            $query = "SELECT sum(qty) as freshcylinder_e from tbl_exchange_condition where from_itemcode='$itemid' and cyl_condition_to='Fresh' and cyl_type='Empty'  and dt >= '$fromdate' AND dt <= '$todate'";
			$result = $this->db->query($query);
			$convert_to_f_row4 = $result->row_array();
            $freshcylinder_e = $convert_to_f_row4['freshcylinder_e'];


            ////////////////////////////// sale damage cylinder  /////////////////////////////////
            // $query = "select sum(qty) as saledamagecylinder from tbl_issue_goods_detail inner join tbl_issue_goods on tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id where itemid='$itemid' and salestatus='Damage' and tbl_issue_goods.issuedate >='$fromdate' and tbl_issue_goods.issuedate <='$todate'";
            



            $query = "select COALESCE(SUM(tbl_issue_goods_detail.qty),0) as saledamagecylinder
			from tbl_issue_goods,tbl_issue_goods_detail where 
			tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id 
			and tbl_issue_goods_detail.itemid='$itemid' and tbl_issue_goods.type='Empty' and tbl_issue_goods.issuedate >= '$fromdate' and tbl_issue_goods.issuedate<= '$todate'
			and tbl_issue_goods.decanting!='Yes' and salestatus = 'Damage' AND tbl_issue_goods.sale_point_id='$sale_point_id'";

			$result = $this->db->query($query);
			$saledamagecylinderquery = $result->row_array();
            $damagecylindersale = $saledamagecylinderquery['saledamagecylinder'];
			
         
		     //code to calculate damage stock starts here
		 
		            
		 
		 
		 
		 
		 
		 
                $datas[] = array(
                    'itemid' => $itemname,
                    'materialcode' => $materialcode,
                    'recv_from_vendor_f'=>$recv_from_vendor_f_row['recv_from_vendor_f'],
                    'recv_from_customer_f'=>$recv_from_customer_f,      
                    'out_to_vendor_f'=>$out_to_vendor_f_row['out_to_vendor_f'],
                    'sale_out_f'=>$sale_out_f_row['sale_out_f'],
                    'sale_return_f'=>$sale_return_f_row['sale_return_f'],
                    'decant_sale_f'=>$decant_sale_f_row['decant_sale_f'],
                    'decant_empty_f'=>$decant_empty_f,
                    'convert_to_f'=>$convert_to_f_row['convert_to_f'],
                    'convert_from_f'=>$convert_from_f_row['convert_from_f'],
					
                    'recv_from_vendor_e'=>$recv_from_vendor_e_row['recv_from_vendor_e'],
                    'recv_from_customer_e'=>$recv_from_customer_e_row['recv_from_customer_e'],      
                    'out_to_vendor_e'=>$out_to_vendor_e_row['out_to_vendor_e'],
                    'sale_out_e'=>$sale_out_e,
                    'sale_return_e'=>$sale_return_e_row['sale_return_e'],
                    'decant_sale_e'=>$decant_sale_e,
                    'decant_empty_e'=>$decant_empty_e_row['decant_empty_e'],
                    'convert_to_e'=>$convert_to_e,
                    'convert_from_e'=>$convert_from_e,
                    'damage_to_new_e'=>$freshcylinder_e,
                    'new_to_damage_e'=>$damagecylinder_e,
                    'damage_to_new_f'=>$freshcylinder_f,
                    'new_to_damage_f'=>$damagecylinder_f,
                    'Damage_sale'=>$damagecylindersale,                    
                    
					'fromdate'=>$fromdate,
                    'todate'=>$todate,
                    //'filledstock'=>$filledstock,
                );
               // pm($datas);
            
            }
        }
       
        //pm($datas);
        return $datas;
    }
}

?>