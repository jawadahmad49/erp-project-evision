<?php
 
class Mod_plantreport extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }

       public function get_details($data){


		$date=$data['from_date'];
        $to_date=$data['to_date'];
        $sale_point_id=$data['location'];
		$baseon = $data['baseon'];
       	$opening=0;
		$lpg_balance_is=0; 
  
			$stock_date = date ("Y-m-d", strtotime("-1 day", strtotime($date)));  
		$this->load->model('mod_common'); // Load Model
		// materialcode=d.itemid) *d.qty),0)/1000 
					$bal_tons=0;
					///////////////////////////// for 11.8 kg ////////////////////////////////////////////////////////
					///////////////////////////// for 11.8 kg ////////////////////////////////////////////////////////
					$qty_11=0; $qty_11_e=0;
					$sql_purchases="select * from tblmaterial_coding where catcode='1' and itemnameint='11.8' ";	
					$query = $this->db->query($sql_purchases);
					if($query->num_rows()>0){
					foreach($query->result_array() as $key => $value) {
					$itemnameint = $value['itemnameint'];
					$materialcode = $value['materialcode'];
					$today_stock=$this->mod_common->stock($materialcode,'empty',$stock_date,1); 
					$empty_filled= explode('_', $today_stock);
				 			$qty_11	  += $empty_filled[0] ;
				 			$qty_11_e += $empty_filled[1] ;
					}
					}
					$bal_tons+=($qty_11*$itemnameint)/1000;
					///////////////////////////// for 15 kg ////////////////////////////////////////////////////////
					///////////////////////////// for 15 kg ////////////////////////////////////////////////////////
					$qty_15=0; $qty_15_e=0;
					$sql_purchases="select * from tblmaterial_coding where catcode='1' and itemnameint='15' ";	
					$query = $this->db->query($sql_purchases);
					if($query->num_rows()>0){
					foreach($query->result_array() as $key => $value) {
					$materialcode = $value['materialcode'];
					$itemnameint = $value['itemnameint'];
					$today_stock=$this->mod_common->stock($materialcode,'empty',$stock_date,1); 
					$empty_filled= explode('_', $today_stock);
				 			$qty_15	  += $empty_filled[0] ;
				 			$qty_15_e += $empty_filled[1] ;
					}
					}
					$bal_tons+=($qty_15*$itemnameint)/1000;
					///////////////////////////// for 45 kg ////////////////////////////////////////////////////////
					///////////////////////////// for 45 kg ////////////////////////////////////////////////////////
					$qty_45=0; $qty_45_e=0;
					$sql_purchases="select * from tblmaterial_coding where catcode='1' and itemnameint='45.4' ";	
					$query = $this->db->query($sql_purchases);
					if($query->num_rows()>0){
					foreach($query->result_array() as $key => $value) {
					$materialcode = $value['materialcode'];
					$itemnameint = $value['itemnameint'];
					$today_stock=$this->mod_common->stock($materialcode,'empty',$stock_date,1); 
					$empty_filled= explode('_', $today_stock);
				 			$qty_45	  += $empty_filled[0] ;
				 			$qty_45_e += $empty_filled[1] ;
					}
					}
					$bal_tons+=($qty_45*$itemnameint)/1000;
					///////////////////////////// for qty_6 kg ////////////////////////////////////////////////////////
					///////////////////////////// for qty_6 kg ////////////////////////////////////////////////////////
					$qty_6=0; $qty_6_e=0;
					$sql_purchases="select * from tblmaterial_coding where catcode='1' and itemnameint='6' ";	
					$query = $this->db->query($sql_purchases);
					if($query->num_rows()>0){
					foreach($query->result_array() as $key => $value) {
					$materialcode = $value['materialcode'];
					$itemnameint = $value['itemnameint'];
					$today_stock=$this->mod_common->stock($materialcode,'empty',$stock_date,1); 
					$empty_filled= explode('_', $today_stock);
				 			$qty_6	  += $empty_filled[0] ;
				 			$qty_6_e += $empty_filled[1] ;
					}
					}
					$bal_tons+=($qty_6*$itemnameint)/1000;
					///////////////////////////// for qty_35 kg ////////////////////////////////////////////////////////
					///////////////////////////// for qty_35 kg ////////////////////////////////////////////////////////
					$qty_35=0; $qty_35_e=0;
					$sql_purchases="select * from tblmaterial_coding where catcode='1' and itemnameint='35' ";	
					$query = $this->db->query($sql_purchases);
					if($query->num_rows()>0){
					foreach($query->result_array() as $key => $value) {
					$materialcode = $value['materialcode'];
					$itemnameint = $value['itemnameint'];
					$today_stock=$this->mod_common->stock($materialcode,'empty',$stock_date,1); 
					$empty_filled= explode('_', $today_stock);
				 			$qty_35	  += $empty_filled[0] ;
				 			$qty_35_e += $empty_filled[1] ;
					}
					}
					$bal_tons+=($qty_35*$itemnameint)/1000;
					///////////////////////////// for qty_18 kg ////////////////////////////////////////////////////////
					///////////////////////////// for qty_18 kg ////////////////////////////////////////////////////////
					$qty_18=0; $qty_18_e=0;
					$sql_purchases="select * from tblmaterial_coding where catcode='1' and itemnameint='18' ";	
					$query = $this->db->query($sql_purchases);
					if($query->num_rows()>0){
					foreach($query->result_array() as $key => $value) {
					$materialcode = $value['materialcode'];
					$itemnameint = $value['itemnameint'];
					$today_stock=$this->mod_common->stock($materialcode,'empty',$stock_date,1); 
					$empty_filled= explode('_', $today_stock);
				 			$qty_18	  += $empty_filled[0] ;
				 			$qty_18_e += $empty_filled[1] ;
					}
					}
					$bal_tons+=($qty_18*$itemnameint)/1000;
					///////////////////////////// for qty_30 kg ////////////////////////////////////////////////////////
					///////////////////////////// for qty_30 kg ////////////////////////////////////////////////////////
					$qty_30=0; $qty_30_e=0;
					$sql_purchases="select * from tblmaterial_coding where catcode='1' and itemnameint='30' ";	
					$query = $this->db->query($sql_purchases);
					if($query->num_rows()>0){
					foreach($query->result_array() as $key => $value) {
					$materialcode = $value['materialcode'];
					$itemnameint = $value['itemnameint'];
					$today_stock=$this->mod_common->stock($materialcode,'empty',$stock_date,1); 
					$empty_filled= explode('_', $today_stock);
				 			$qty_30	  += $empty_filled[0] ;
				 			$qty_30_e += $empty_filled[1] ;
					}
					}
					$bal_tons+=($qty_30*$itemnameint)/1000;
 
	 
                $datas[] = array(
					'for_date' => $stock_date,
                    'party_name' => 'Opening',
                    'cell_number'=>$cell_number,
                    'trans_type'=> 'Opening',
                    'price_11'=>$price_11,
                    'qty_11'=>$qty_11,
                    'qty_15'=>$qty_15,
                    'qty_45'=>$qty_45,
                    'qty_6'=>$qty_6,
                    'qty_35'=>$qty_35,
                    'qty_18'=>$qty_18,
                    'qty_30'=>$qty_30,
                    'qty_11_e'=>$qty_11_e,
                    'qty_15_e'=>$qty_15_e,
                    'qty_45_e'=>$qty_45_e,
                    'qty_6_e'=>$qty_6_e,
                    'qty_35_e'=>$qty_35_e,
                    'qty_18_e'=>$qty_18_e,
                    'qty_30_e'=>$qty_30_e,
                     
                    'total_sale'=>$total_sale,
                    'bal_tons'=>$bal_tons,
                    'bill'=>$bill,
                    'amount_recv'=>$amount_recv,
                    'amount_paid'=>$amount_paid,
                    'bal'=>$bal,
                    'remarks'=>$remarks,
                    'from_date'=>$date,
                    'to_date'=>$to_date,
					
				);
				
 
  
 
	
	while (strtotime($date) <= strtotime($to_date)) {
                	 
					 
			 
				///////////////////////////////////////////////////   PURCHASE FILLED ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////   PURCHASE FILLED ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////   PURCHASE FILLED ////////////////////////////////////////////////////////////
 
				$sql_purchases="select m.receiptnos, m.suppliercode, m.remarks, m.net_payable, m.total_paid, a.aname, a.cell,m.11_kg_price 
				FROM tbl_goodsreceiving m,   tblacode a
				where  m.receiptdate='$date'
				and m.suppliercode=a.acode
				and m.trans_typ='purchasefilled'
				and m.sale_point_id='$sale_point_id'";

				$query = $this->db->query($sql_purchases);
				if($query->num_rows()>0){
				foreach($query->result_array() as $key => $value) {	
				$total_sale =0;  $bill =0; $amount_recv =0; $amount_paid =0; $bal =0; 
				$qty_11 =0; $qty_15 =0; $qty_45 =0; $qty_6 =0;  $qty_35 =0;  $qty_18 =0;  $qty_30 =0; 
				$qty_11_e =0; $qty_15_e =0; $qty_45_e =0; $qty_6_e =0;  $qty_35_e =0;  $qty_18_e =0;  $qty_30_e =0; 
				$remarks =''; 
				$itemnameint =''; 
				$receiptnos = $value['receiptnos'];
	
				$sql_purchases_sub="select i.itemnameint,d.itemid ,d.ereturn,	d.quantity,d.rate,d.inc_vat_amount  from tblmaterial_coding i ,tbl_goodsreceiving_detail d 
				where i.materialcode=d.itemid
				and d.receipt_detail_id='$receiptnos' and d.sale_point_id='$sale_point_id'";	
				
				$query_sub = $this->db->query($sql_purchases_sub);
				foreach($query_sub->result_array() as $key_sub => $value_sub) { $itemnameint = $value_sub['itemnameint']; 
				$itemid = $value_sub['itemid']; $quantity = $value_sub['quantity']; $ereturn = $value_sub['ereturn']; 
				if($itemnameint=='11.8'){  $qty_11 +=$quantity; $qty_11_e +=$ereturn; $bal_tons+=($qty_11*$itemnameint)/1000; }
				if($itemnameint=='15')	 {  $qty_15 +=$quantity; $qty_15_e +=$ereturn; $bal_tons+=($qty_15*$itemnameint)/1000; }
				if($itemnameint=='45.4'){  $qty_45 +=$quantity; $qty_45_e +=$ereturn; $bal_tons+=($qty_45*$itemnameint)/1000; }
				if($itemnameint=='6')	 {  $qty_6  +=$quantity; $qty_6_e +=$ereturn; $bal_tons+=($qty_6*$itemnameint)/1000; }
				if($itemnameint=='35')  {  $qty_35 +=$quantity; $qty_35_e +=$ereturn; $bal_tons+=($qty_35*$itemnameint)/1000; }
				if($itemnameint=='18')  {  $qty_18 +=$quantity; $qty_18_e +=$ereturn; $bal_tons+=($qty_18*$itemnameint)/1000; }
				if($itemnameint=='30')  {  $qty_30 +=$quantity; $qty_30_e +=$ereturn; $bal_tons+=($qty_30*$itemnameint)/1000; }
				
						
				}
				$price_11 = $value['11_kg_price'];
				$cell_number = $value['cell'];
				$party_name = $value['aname'];
				$recv_weight = $value['quantity'];
				$purchase_rate = $value['rate'];
				$total = $value['inc_vat_amount'];
				$remarks = $value['remarks']; 
				$bill = $value['net_payable']; 
				$amount_paid = $value['total_paid']; 
				$bal = $bill-$amount_paid; 
				$lpg_balance_is+=$recv_weight;
				$datas[] = array(
					'for_date' => $date,   'transno' => $receiptnos,
                    'party_name' => $party_name,
                    'cell_number'=>$cell_number,
                    'trans_type'=> 'Purchase Filled',
                    'price_11'=>$price_11,
                    'qty_11'=>$qty_11,
                    'qty_15'=>$qty_15,
                    'qty_45'=>$qty_45,
                    'qty_6'=>$qty_6,
                    'qty_35'=>$qty_35,
                    'qty_18'=>$qty_18,
                    'qty_30'=>$qty_30,
                    'qty_11_e'=>-$qty_11_e,
                    'qty_15_e'=>-$qty_15_e,
                    'qty_45_e'=>-$qty_45_e,
                    'qty_6_e'=>-$qty_6_e,
                    'qty_35_e'=>-$qty_35_e,
                    'qty_18_e'=>-$qty_18_e,
                    'qty_30_e'=>-$qty_30_e,
                    'total_sale'=>$total_sale,
                    'bal_tons'=>$bal_tons,
                    'bill'=>$bill,
                    'amount_recv'=>$amount_recv,
                    'amount_paid'=>$amount_paid,
                    'bal'=>$bal,
                    'remarks'=>$remarks,
                    'from_date'=>$date,
                    'to_date'=>$to_date,
				);

				}




				} 
				
				/////////////////////////////////////////////////// END   PURCHASE FILLED ////////////////////////////////////////////////////////////	 
				/////////////////////////////////////////////////// END   PURCHASE FILLED ////////////////////////////////////////////////////////////	 
				/////////////////////////////////////////////////// END   PURCHASE FILLED ////////////////////////////////////////////////////////////	 
			 
	
					
					 
				///////////////////////////////////////////////////  START EMPTY PURCHASE ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  START EMPTY PURCHASE ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  START EMPTY PURCHASE ////////////////////////////////////////////////////////////	 
			  
				$sql_purchases="select m.receiptnos, m.suppliercode, m.remarks, m.net_payable, m.total_paid, a.aname, a.cell,m.11_kg_price 
				FROM tbl_goodsreceiving m,   tblacode a
				where  m.receiptdate='$date'
				and m.suppliercode=a.acode
				and m.trans_typ='purchaseempty'
				and m.sale_point_id='$sale_point_id'";	
				$query = $this->db->query($sql_purchases);
				if($query->num_rows()>0){
				foreach($query->result_array() as $key => $value) {
				$total_sale =0;	 $bill =0; $amount_recv =0; $amount_paid =0; $bal =0; 
				$qty_11 =0; $qty_15 =0; $qty_45 =0; $qty_6 =0;  $qty_35 =0;  $qty_18 =0;  $qty_30 =0; 
				$qty_11_e =0; $qty_15_e =0; $qty_45_e =0; $qty_6_e =0;  $qty_35_e =0;  $qty_18_e =0;  $qty_30_e =0; 
				$remarks =''; 
				$itemnameint =''; 
				$receiptnos = $value['receiptnos'];
		
				$sql_purchases_sub="select i.itemnameint,d.itemid ,d.ereturn,	d.quantity,d.rate,d.inc_vat_amount  from tblmaterial_coding i ,tbl_goodsreceiving_detail d 
				where i.materialcode=d.itemid
				and d.receipt_detail_id='$receiptnos' and d.sale_point_id='$sale_point_id'";	
				$query_sub = $this->db->query($sql_purchases_sub);
				foreach($query_sub->result_array() as $key_sub => $value_sub) { $itemnameint = $value_sub['itemnameint']; 
				$itemid = $value_sub['itemid']; $quantity = $value_sub['quantity']; $ereturn = $value_sub['ereturn']; 
				if($itemnameint=='11.8'){  $qty_11_e +=$quantity; }
				if($itemnameint=='15')	 {  $qty_15_e +=$quantity;  }
				if($itemnameint=='45.4'){  $qty_45_e +=$quantity; }
				if($itemnameint=='6')	 {  $qty_6_e  +=$quantity; }
				if($itemnameint=='35')  {  $qty_35_e +=$quantity; }
				if($itemnameint=='18')  {  $qty_18_e +=$quantity;  }
				if($itemnameint=='30')  {  $qty_30_e +=$quantity; }
				}
				$price_11 = $value['11_kg_price'];
				$cell_number = $value['cell'];
				$party_name = $value['aname'];
				$recv_weight = $value['quantity'];
				$purchase_rate = $value['rate'];
				$total = $value['inc_vat_amount'];
				$remarks = $value['remarks']; 
				$bill = $value['net_payable']; 
				$amount_paid = $value['total_paid']; 
				$bal = $bill-$amount_paid; 
				$lpg_balance_is+=$recv_weight;
				$datas[] = array(
					'for_date' => $date,     'transno' => $receiptnos,
                    'party_name' => $party_name,
                    'cell_number'=>$cell_number,
                    'trans_type'=> 'Purchase Empty',
                    'price_11'=>$price_11,
                    'qty_11'=>$qty_11,
                    'qty_15'=>$qty_15,
                    'qty_45'=>$qty_45,
                    'qty_6'=>$qty_6,
                    'qty_35'=>$qty_35,
                    'qty_18'=>$qty_18,
                    'qty_30'=>$qty_30,
                    'qty_11_e'=>$qty_11_e,
                    'qty_15_e'=>$qty_15_e,
                    'qty_45_e'=>$qty_45_e,
                    'qty_6_e'=>$qty_6_e,
                    'qty_35_e'=>$qty_35_e,
                    'qty_18_e'=>$qty_18_e,
                    'qty_30_e'=>$qty_30_e,
                    'total_sale'=>$total_sale,
                    'bal_tons'=>$bal_tons,
                    'bill'=>$bill,
                    'amount_recv'=>$amount_recv,
                    'amount_paid'=>$amount_paid,
                    'bal'=>$bal,
                    'remarks'=>$remarks,
                    'from_date'=>$date,
                    'to_date'=>$to_date,
				);

				}




				} 
				
				/////////////////////////////////////////////////// END   PURCHASE EMPTY ////////////////////////////////////////////////////////////	 
				/////////////////////////////////////////////////// END   PURCHASE EMPTY ////////////////////////////////////////////////////////////	 
				/////////////////////////////////////////////////// END   PURCHASE EMPTY ////////////////////////////////////////////////////////////	 
			 
	
					
					 
				///////////////////////////////////////////////////  START PURCHASE RETURN ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  START PURCHASE RETURN  ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  START PURCHASE RETURN  ////////////////////////////////////////////////////////////	 
			 
				
			 	$sql_purchases="select m.irnos, m.scode, m.remarks,  a.aname, a.cell 
				FROM tbl_issue_return m, tblacode a
				where  m.irdate='$date'
				and m.scode=a.acode
				and m.type='purchasereturn'
				and m.sale_point_id='$sale_point_id'";	
				$query = $this->db->query($sql_purchases);
				if($query->num_rows()>0){
				foreach($query->result_array() as $key => $value) {
				$total_sale =0;	  $bill =0; $amount_recv =0; $amount_paid =0; $bal =0; 
				$qty_11 =0; $qty_15 =0; $qty_45 =0; $qty_6 =0;  $qty_35 =0;  $qty_18 =0;  $qty_30 =0; 
				$qty_11_e =0; $qty_15_e =0; $qty_45_e =0; $qty_6_e =0;  $qty_35_e =0;  $qty_18_e =0;  $qty_30_e =0; 
				$remarks =''; 
				$itemnameint =''; 
				$irnos = $value['irnos'];
		
				  $sql_purchases_sub="select i.itemnameint, d.qty as quantity,d.gas_amount as rate,  d.total_amount ,d.itemid ,d.type as fill_emp
				  from tblmaterial_coding i , tbl_issue_return_detail d 
				  where   i.materialcode=d.itemid and d.irnos='$irnos' and d.sale_point_id='$sale_point_id'";	
				$query_sub = $this->db->query($sql_purchases_sub);
				foreach($query_sub->result_array() as $key_sub => $value_sub) { $itemnameint = $value_sub['itemnameint']; 
						$itemid = $value_sub['itemid']; $quantity = $value_sub['quantity']; $type = $value_sub['fill_emp'];$total_amount = $value_sub['total_amount'];
						$bill+=$total_amount;
						
				if($itemnameint=='11.8'){ if($type=='Filled'){  $qty_11 +=$quantity; $bal_tons-=($qty_11*$itemnameint)/1000;  } if($type=='Empty'){  $qty_11_e +=$quantity; }}
				if($itemnameint=='15'  ){ if($type=='Filled'){  $qty_15 +=$quantity; $bal_tons-=($qty_15*$itemnameint)/1000;  } if($type=='Empty'){  $qty_15_e +=$quantity; }}
				if($itemnameint=='45.4'){ if($type=='Filled'){  $qty_45 +=$quantity; $bal_tons-=($qty_45*$itemnameint)/1000;  } if($type=='Empty'){  $qty_45_e +=$quantity; }}
				if($itemnameint=='6')	{ if($type=='Filled'){  $qty_6  +=$quantity; $bal_tons-=($qty_6*$itemnameint)/1000;   } if($type=='Empty'){  $qty_6_e +=$quantity;  }}
				if($itemnameint=='35')  { if($type=='Filled'){  $qty_35 +=$quantity; $bal_tons-=($qty_35*$itemnameint)/1000;  } if($type=='Empty'){  $qty_35_e +=$quantity; }}
				if($itemnameint=='18')  { if($type=='Filled'){  $qty_18 +=$quantity; $bal_tons-=($qty_18*$itemnameint)/1000;  } if($type=='Empty'){  $qty_18_e +=$quantity; }}
				if($itemnameint=='30')  { if($type=='Filled'){  $qty_30 +=$quantity; $bal_tons-=($qty_30*$itemnameint)/1000;  } if($type=='Empty'){  $qty_30_e +=$quantity; }}
				}
				$price_11 = $value['11_kg_price'];
				$cell_number = $value['cell'];
				$party_name = $value['aname'];
				$recv_weight = $value['quantity'];
				$purchase_rate = $value['rate'];
				$total = $value['inc_vat_amount'];
				$remarks = $value['remarks']; 
				 
				$amount_paid = $value['total_paid']; 
				$bal = $bill-$amount_paid; 
				$lpg_balance_is+=$recv_weight;
				
				 
				$datas[] = array(
					'for_date' => $date,
                    'transno' => $irnos,
                    'party_name' => $party_name,
                    'cell_number'=>$cell_number,
                    'trans_type'=> 'Purchase Return',
                    'price_11'=>$price_11,
                    'qty_11'=>-$qty_11,
                    'qty_15'=>-$qty_15,
                    'qty_45'=>-$qty_45,
                    'qty_6'=>-$qty_6,
                    'qty_35'=>-$qty_35,
                    'qty_18'=>-$qty_18,
                    'qty_30'=>-$qty_30,
                    'qty_11_e'=>-$qty_11_e,
                    'qty_15_e'=>-$qty_15_e,
                    'qty_45_e'=>-$qty_45_e,
                    'qty_6_e'=>-$qty_6_e,
                    'qty_35_e'=>-$qty_35_e,
                    'qty_18_e'=>-$qty_18_e,
                    'qty_30_e'=>-$qty_30_e,
                    'total_sale'=>$total_sale,
                    'bal_tons'=>$bal_tons,
                    'bill'=>$bill,
                    'amount_recv'=>$amount_recv,
                    'amount_paid'=>$amount_paid,
                    'bal'=>$bal,
                    'remarks'=>$remarks,
                    'from_date'=>$date,
                    'to_date'=>$to_date,
				);

				}




				} 
				
				/////////////////////////////////////////////////// END PURCHASE RETURN ////////////////////////////////////////////////////////////	 
				/////////////////////////////////////////////////// END PURCHASE RETURN ////////////////////////////////////////////////////////////	 
				/////////////////////////////////////////////////// END PURCHASE RETURN ////////////////////////////////////////////////////////////	 
			 
	
					 
				///////////////////////////////////////////////////  START SALE FILLED ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  START SALE FILLED ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  START SALE FILLED ////////////////////////////////////////////////////////////	 
				 
				
			 	$sql_purchases="select m.issuenos, m.issuedto, m.remarks,  a.aname, a.cell ,m.11_kg_price,m.after_discount_amt,
				m.total_received
				FROM tbl_issue_goods m, tblacode a
				where  m.issuedate='$date'
				and m.issuedto=a.acode
				and m.type='Fill'
				and m.sale_point_id='$sale_point_id'";	
				$query = $this->db->query($sql_purchases);
				if($query->num_rows()>0){
				foreach($query->result_array() as $key => $value) {
				$total_sale =0;	  $bill =0; $amount_recv =0; $amount_paid =0; $bal =0; 
				$qty_11 =0; $qty_15 =0; $qty_45 =0; $qty_6 =0;  $qty_35 =0;  $qty_18 =0;  $qty_30 =0; 
				$qty_11_e =0; $qty_15_e =0; $qty_45_e =0; $qty_6_e =0;  $qty_35_e =0;  $qty_18_e =0;  $qty_30_e =0; 
				$remarks =''; 
				$itemnameint =''; 
				$issuenos = $value['issuenos'];
		
				$sql_purchases_sub="select i.itemnameint,d.itemid,d.item_return,d.returns ,	d.qty
				from tblmaterial_coding i ,tbl_issue_goods_detail d 
				where i.materialcode=d.itemid
				and d.ig_detail_id='$issuenos' and d.sale_point_id='$sale_point_id'";	
				$query_sub = $this->db->query($sql_purchases_sub);
				foreach($query_sub->result_array() as $key_sub => $value_sub) {
					$itemnameint = $value_sub['itemnameint']; 
				$itemid = $value_sub['itemid'];	$item_return = $value_sub['item_return'];
				$quantity = $value_sub['qty']; $ereturn = $value_sub['returns']; 
				
					if($itemnameint=='11.8') {  $qty_11 +=$quantity;  $bal_tons-=($qty_11*$itemnameint)/1000;  $total_sale+=($qty_11*$itemnameint)/1000; }
					if($itemnameint=='15')	 {  $qty_15 +=$quantity;  $bal_tons-=($qty_15*$itemnameint)/1000;  $total_sale+=($qty_15*$itemnameint)/1000;}
					if($itemnameint=='45.4') {  $qty_45 +=$quantity;  $bal_tons-=($qty_45*$itemnameint)/1000;  $total_sale+=($qty_45*$itemnameint)/1000;}
					if($itemnameint=='6')	 {  $qty_6  +=$quantity;  $bal_tons-=($qty_6*$itemnameint)/1000;  $total_sale+=($qty_6*$itemnameint)/1000;}
					if($itemnameint=='35')   {  $qty_35 +=$quantity;  $bal_tons-=($qty_35*$itemnameint)/1000;  $total_sale+=($qty_35*$itemnameint)/1000;}
					if($itemnameint=='18')   {  $qty_18 +=$quantity;  $bal_tons-=($qty_18*$itemnameint)/1000;  $total_sale+=($qty_18*$itemnameint)/1000;}
					if($itemnameint=='30')   {  $qty_30 +=$quantity;  $bal_tons-=($qty_30*$itemnameint)/1000;  $total_sale+=($qty_30*$itemnameint)/1000;}
					
					//////////////////////////////////// for empty //////////////////////////////////////
					
				  	  $query_return = "  select itemnameint from tblmaterial_coding where  materialcode='$item_return' ";
					  $result_return = $this->db->query($query_return);
					  $recv_from_vendor_e_row = $result_return->row_array();

					  $itemnameint_return=$recv_from_vendor_e_row['itemnameint'];
					
					if($itemnameint_return=='11.8') {  $qty_11_e +=$ereturn; }
					if($itemnameint_return=='15')	 {  $qty_15_e +=$ereturn; }
					if($itemnameint_return=='45.4') {  $qty_45_e +=$ereturn; }
					if($itemnameint_return=='6')	 {  $qty_6_e  +=$ereturn; }
					if($itemnameint_return=='35')   {  $qty_35_e +=$ereturn; }
					if($itemnameint_return=='18')   {  $qty_18_e +=$ereturn; }
					if($itemnameint_return=='30')   {  $qty_30_e +=$ereturn; }
					
				 }
				$price_11 = $value['11_kg_price'];
				$cell_number = $value['cell'];
				$party_name = $value['aname'];
				$recv_weight = $value['quantity'];
				$purchase_rate = $value['rate'];
				$total = $value['inc_vat_amount'];
				$remarks = $value['remarks']; 
				$bill = $value['after_discount_amt']; 
				$amount_recv = $value['total_received']; 
				  
				$bal = $bill-$amount_recv; 
				$lpg_balance_is+=$recv_weight;
				
				 
				$datas[] = array(
					'for_date' => $date,
                    'transno' => $issuenos,
                    'party_name' => $party_name,
                    'cell_number'=>$cell_number,
                    'trans_type'=> 'Sale',
                    'price_11'=>$price_11,
                    'qty_11'=>-$qty_11,
                    'qty_15'=>-$qty_15,
                    'qty_45'=>-$qty_45,
                    'qty_6'=>-$qty_6,
                    'qty_35'=>-$qty_35,
                    'qty_18'=>-$qty_18,
                    'qty_30'=>-$qty_30,
                    'qty_11_e'=>$qty_11_e,
                    'qty_15_e'=>$qty_15_e,
                    'qty_45_e'=>$qty_45_e,
                    'qty_6_e'=>$qty_6_e,
                    'qty_35_e'=>$qty_35_e,
                    'qty_18_e'=>$qty_18_e,
                    'qty_30_e'=>$qty_30_e,
                    'total_sale'=>$total_sale,
                    'bal_tons'=>$bal_tons,
                    'bill'=>$bill,
                    'amount_recv'=>$amount_recv,
                    'amount_paid'=>$amount_paid,
                    'bal'=>$bal,
                    'remarks'=>$remarks,
                    'from_date'=>$date,
                    'to_date'=>$to_date,
				);

				}




				} 
				
				/////////////////////////////////////////////////// END SALE ////////////////////////////////////////////////////////////	 
				/////////////////////////////////////////////////// END SALE ////////////////////////////////////////////////////////////	 
				/////////////////////////////////////////////////// END SALE ////////////////////////////////////////////////////////////	 
			 
	
					
	
					 
				///////////////////////////////////////////////////  START SALE EMPTY ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  START SALE EMPTY ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  START SALE EMPTY ////////////////////////////////////////////////////////////	 
				 
				
			 	$sql_purchases="select m.issuenos, m.issuedto, m.remarks,  a.aname, a.cell ,m.11_kg_price,m.after_discount_amt,
				m.total_received
				FROM tbl_issue_goods m, tblacode a
				where  m.issuedate='$date'
				and m.issuedto=a.acode
				and m.type='Empty'
				and m.sale_point_id='$sale_point_id'";	
				$query = $this->db->query($sql_purchases);
				if($query->num_rows()>0){
				foreach($query->result_array() as $key => $value) {
				$total_sale =0;	  $bill =0; $amount_recv =0; $amount_paid =0; $bal =0; 
				$qty_11 =0; $qty_15 =0; $qty_45 =0; $qty_6 =0;  $qty_35 =0;  $qty_18 =0;  $qty_30 =0; 
				$qty_11_e =0; $qty_15_e =0; $qty_45_e =0; $qty_6_e =0;  $qty_35_e =0;  $qty_18_e =0;  $qty_30_e =0; 
				$remarks =''; 
				$itemnameint =''; 
				$issuenos = $value['issuenos'];
		
				$sql_purchases_sub="select i.itemnameint,d.itemid,d.item_return,d.returns ,	d.qty,d.total_amount
				from tblmaterial_coding i ,tbl_issue_goods_detail d 
				where i.materialcode=d.itemid
				and d.ig_detail_id='$issuenos' and d.sale_point_id='$sale_point_id'";	
				$query_sub = $this->db->query($sql_purchases_sub);
				foreach($query_sub->result_array() as $key_sub => $value_sub) {
					$itemnameint = $value_sub['itemnameint']; 
				$itemid = $value_sub['itemid'];	$item_return = $value_sub['item_return'];	$bill+= $value_sub['total_amount'];
				$quantity = $value_sub['qty']; $ereturn = $value_sub['returns']; 
				
					if($itemnameint=='11.8') {  $qty_11_e +=$quantity;    }
					if($itemnameint=='15')	 {  $qty_15_e +=$quantity;   }
					if($itemnameint=='45.4') {  $qty_45_e +=$quantity;  }
					if($itemnameint=='6')	 {  $qty_6_e  +=$quantity;  }
					if($itemnameint=='35')   {  $qty_35_e +=$quantity;  }
					if($itemnameint=='18')   {  $qty_18_e +=$quantity;  }
					if($itemnameint=='30')   {  $qty_30_e +=$quantity;  }
					
					//////////////////////////////////// for empty //////////////////////////////////////
					
				  	  // $query_return = "  select itemnameint from tblmaterial_coding where  materialcode='$item_return' ";
					  // $result_return = $this->db->query($query_return);
					  // $recv_from_vendor_e_row = $result_return->row_array();

					  // $itemnameint_return=$recv_from_vendor_e_row['itemnameint'];
					
					// if($itemnameint_return=='11.8') {  $qty_11_e +=$ereturn; }
					// if($itemnameint_return=='15')	 {  $qty_15_e +=$ereturn; }
					// if($itemnameint_return=='45.4') {  $qty_45_e +=$ereturn; }
					// if($itemnameint_return=='6')	 {  $qty_6_e  +=$ereturn; }
					// if($itemnameint_return=='35')   {  $qty_35_e +=$ereturn; }
					// if($itemnameint_return=='18')   {  $qty_18_e +=$ereturn; }
					// if($itemnameint_return=='30')   {  $qty_30_e +=$ereturn; }
					
				 }
				$price_11 = $value['11_kg_price'];
				$cell_number = $value['cell'];
				$party_name = $value['aname'];
				$recv_weight = $value['quantity'];
				$purchase_rate = $value['rate'];
				$total = $value['inc_vat_amount'];
				$remarks = $value['remarks']; 
				//$bill = $value['after_discount_amt']; 
				$amount_recv = $value['total_received']; 
				  
				$bal = $bill-$amount_recv; 
				$lpg_balance_is+=$recv_weight;
				
				 
				$datas[] = array(
					'for_date' => $date,
                    'transno' => $issuenos,
                    'party_name' => $party_name,
                    'cell_number'=>$cell_number,
                    'trans_type'=> 'Empty Sale',
                    'price_11'=>$price_11,
                    'qty_11'=>$qty_11,
                    'qty_15'=>$qty_15,
                    'qty_45'=>$qty_45,
                    'qty_6'=>$qty_6,
                    'qty_35'=>$qty_35,
                    'qty_18'=>$qty_18,
                    'qty_30'=>$qty_30,
                    'qty_11_e'=>-$qty_11_e,
                    'qty_15_e'=>-$qty_15_e,
                    'qty_45_e'=>-$qty_45_e,
                    'qty_6_e'=>-$qty_6_e,
                    'qty_35_e'=>-$qty_35_e,
                    'qty_18_e'=>-$qty_18_e,
                    'qty_30_e'=>-$qty_30_e,
                    'total_sale'=>$total_sale,
                    'bal_tons'=>$bal_tons,
                    'bill'=>$bill,
                    'amount_recv'=>$amount_recv,
                    'amount_paid'=>$amount_paid,
                    'bal'=>$bal,
                    'remarks'=>$remarks,
                    'from_date'=>$date,
                    'to_date'=>$to_date,
				);

				}




				} 
				
				/////////////////////////////////////////////////// END SALE EMPTY ////////////////////////////////////////////////////////////	 
				/////////////////////////////////////////////////// END SALE EMPTY ////////////////////////////////////////////////////////////	 
				/////////////////////////////////////////////////// END SALE EMPTY ////////////////////////////////////////////////////////////	 
			 
	
					
					 
					 
					 
					 
				
				///////////////////////////////////////////////////  START SALE RETURN ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  START SALE RETURN  ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  START SALE RETURN  ////////////////////////////////////////////////////////////	 
			 
				
			 	$sql_purchases="select m.irnos, m.scode, m.remarks,  a.aname, a.cell 
				FROM tbl_issue_return m, tblacode a
				where  m.irdate='$date'
				and m.scode=a.acode
				and m.type='salereturn'
				and m.sale_point_id='$sale_point_id'";	
				$query = $this->db->query($sql_purchases);
				if($query->num_rows()>0){
				foreach($query->result_array() as $key => $value) {
				$total_sale =0;	  $bill =0; $amount_recv =0; $amount_paid =0; $bal =0; 
				$qty_11 =0; $qty_15 =0; $qty_45 =0; $qty_6 =0;  $qty_35 =0;  $qty_18 =0;  $qty_30 =0; 
				$qty_11_e =0; $qty_15_e =0; $qty_45_e =0; $qty_6_e =0;  $qty_35_e =0;  $qty_18_e =0;  $qty_30_e =0; 
				$remarks =''; 
				$itemnameint =''; 
				$irnos = $value['irnos'];
		
				  $sql_purchases_sub="select i.itemnameint, d.qty as quantity,d.gas_amount as rate,  d.total_amount ,d.itemid ,d.type as fill_emp
				  from tblmaterial_coding i , tbl_issue_return_detail d 
				  where   i.materialcode=d.itemid and d.irnos='$irnos' and d.sale_point_id='$sale_point_id'";	
				$query_sub = $this->db->query($sql_purchases_sub);
				foreach($query_sub->result_array() as $key_sub => $value_sub) { $itemnameint = $value_sub['itemnameint']; 
						$itemid = $value_sub['itemid']; $quantity = $value_sub['quantity']; $type = $value_sub['fill_emp'];$total_amount = $value_sub['total_amount'];
						$bill+=$total_amount;
						
				if($itemnameint=='11.8'){ if($type=='Filled'){  $qty_11 +=$quantity; $bal_tons+=($qty_11*$itemnameint)/1000;  } if($type=='Empty'){  $qty_11_e +=$quantity; }}
				if($itemnameint=='15'  ){ if($type=='Filled'){  $qty_15 +=$quantity; $bal_tons+=($qty_15*$itemnameint)/1000;  } if($type=='Empty'){  $qty_15_e +=$quantity; }}
				if($itemnameint=='45.4'){ if($type=='Filled'){  $qty_45 +=$quantity; $bal_tons+=($qty_45*$itemnameint)/1000;  } if($type=='Empty'){  $qty_45_e +=$quantity; }}
				if($itemnameint=='6')	{ if($type=='Filled'){  $qty_6  +=$quantity; $bal_tons+=($qty_6*$itemnameint)/1000;   } if($type=='Empty'){  $qty_6_e +=$quantity;  }}
				if($itemnameint=='35')  { if($type=='Filled'){  $qty_35 +=$quantity; $bal_tons+=($qty_35*$itemnameint)/1000;  } if($type=='Empty'){  $qty_35_e +=$quantity; }}
				if($itemnameint=='18')  { if($type=='Filled'){  $qty_18 +=$quantity; $bal_tons+=($qty_18*$itemnameint)/1000;  } if($type=='Empty'){  $qty_18_e +=$quantity; }}
				if($itemnameint=='30')  { if($type=='Filled'){  $qty_30 +=$quantity; $bal_tons+=($qty_30*$itemnameint)/1000;  } if($type=='Empty'){  $qty_30_e +=$quantity; }}
				}
				$price_11 = $value['11_kg_price'];
				$cell_number = $value['cell'];
				$party_name = $value['aname'];
				$recv_weight = $value['quantity'];
				$purchase_rate = $value['rate'];
				$total = $value['inc_vat_amount'];
				$remarks = $value['remarks']; 
				 
				$amount_paid = $value['total_paid']; 
				$bal = $bill-$amount_paid; 
				$lpg_balance_is+=$recv_weight;
				
				 
				$datas[] = array(
					'for_date' => $date,
                    'transno' => $irnos,
                    'party_name' => $party_name,
                    'cell_number'=>$cell_number,
                    'trans_type'=> 'Sale Return',
                    'price_11'=>$price_11,
                    'qty_11'=>$qty_11,
                    'qty_15'=>$qty_15,
                    'qty_45'=>$qty_45,
                    'qty_6'=>$qty_6,
                    'qty_35'=>$qty_35,
                    'qty_18'=>$qty_18,
                    'qty_30'=>$qty_30,
                    'qty_11_e'=>$qty_11_e,
                    'qty_15_e'=>$qty_15_e,
                    'qty_45_e'=>$qty_45_e,
                    'qty_6_e'=>$qty_6_e,
                    'qty_35_e'=>$qty_35_e,
                    'qty_18_e'=>$qty_18_e,
                    'qty_30_e'=>$qty_30_e,
                    'total_sale'=>$total_sale,
                    'bal_tons'=>$bal_tons,
                    'bill'=>$bill,
                    'amount_recv'=>$amount_recv,
                    'amount_paid'=>$amount_paid,
                    'bal'=>$bal,
                    'remarks'=>$remarks,
                    'from_date'=>$date,
                    'to_date'=>$to_date,
				);

				}




				} 
				
				/////////////////////////////////////////////////// END SALE RETURN ////////////////////////////////////////////////////////////	 
				/////////////////////////////////////////////////// END SALE RETURN ////////////////////////////////////////////////////////////	 
				/////////////////////////////////////////////////// END SALE RETURN ////////////////////////////////////////////////////////////	 
			 
		 
					 
					 
				
				///////////////////////////////////////////////////  START CASH PAYMENT ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  START CASH PAYMENT ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  START CASH PAYMENT ////////////////////////////////////////////////////////////	 
			 	$sql_purchases="select m.vno, m.acode, m.remarks,  a.aname, a.cell ,m.damount as amount
				FROM tbltrans_detail m, tblacode a where  m.vdate='$date' and m.acode!='2003013001' and m.acode=a.acode and m.vtype='CP' and m.sale_point_id='$sale_point_id'";	
				$query = $this->db->query($sql_purchases);
				if($query->num_rows()>0){
				foreach($query->result_array() as $key => $value) {
				$total_sale =0;	  $bill =0; $amount_recv =0; $amount_paid =0; $bal =0; 
				$qty_11 =0; $qty_15 =0; $qty_45 =0; $qty_6 =0;  $qty_35 =0;  $qty_18 =0;  $qty_30 =0; 	$bal = 0; 
				$qty_11_e =0; $qty_15_e =0; $qty_45_e =0; $qty_6_e =0;  $qty_35_e =0;  $qty_18_e =0;  $qty_30_e =0; 
				$recv_weight =0; $price_11 = 0; $remarks =''; $itemnameint ='';  $purchase_rate =0; $total = 0;	$lpg_balance_is=0;
				$irnos = $value['vno'];
				$cell_number = $value['cell'];
				$party_name = $value['aname'];
				$remarks = $value['remarks']; 
				$amount_paid = $value['amount']; 
				$datas[] = array(
					'for_date' => $date,
                    'transno' => $irnos,
                    'party_name' => $party_name,
                    'cell_number'=>$cell_number,
                    'trans_type'=> 'Cash Payment',
                    'price_11'=>$price_11,
                    'qty_11'=>$qty_11,
                    'qty_15'=>$qty_15,
                    'qty_45'=>$qty_45,
                    'qty_6'=>$qty_6,
                    'qty_35'=>$qty_35,
                    'qty_18'=>$qty_18,
                    'qty_30'=>$qty_30,
                    'qty_11_e'=>$qty_11_e,
                    'qty_15_e'=>$qty_15_e,
                    'qty_45_e'=>$qty_45_e,
                    'qty_6_e'=>$qty_6_e,
                    'qty_35_e'=>$qty_35_e,
                    'qty_18_e'=>$qty_18_e,
                    'qty_30_e'=>$qty_30_e,
                    'total_sale'=>$total_sale,
                    'bal_tons'=>$bal_tons,
                    'bill'=>$bill,
                    'amount_recv'=>$amount_recv,
                    'amount_paid'=>$amount_paid,
                    'bal'=>$bal,
                    'remarks'=>$remarks,
                    'from_date'=>$date,
                    'to_date'=>$to_date,
				);
				}
				} 
				///////////////////////////////////////////////////  END CASH PAYMENT ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  END CASH PAYMENT ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  END CASH PAYMENT ////////////////////////////////////////////////////////////	 
			 
					
				///////////////////////////////////////////////////  START CASH RECEIPT ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  START CASH RECEIPT ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  START CASH RECEIPT ////////////////////////////////////////////////////////////	 
			 	$sql_purchases="select m.vno, m.acode, m.remarks,  a.aname, a.cell ,m.camount as amount
				FROM tbltrans_detail m, tblacode a where  m.vdate='$date' and m.acode!='2003013001' and m.acode=a.acode and m.vtype='CR' and m.sale_point_id='$sale_point_id'";	
				$query = $this->db->query($sql_purchases);
				if($query->num_rows()>0){
				foreach($query->result_array() as $key => $value) {
				$total_sale =0;	  $bill =0; $amount_recv =0; $amount_paid =0; $bal =0; 
				$qty_11 =0; $qty_15 =0; $qty_45 =0; $qty_6 =0;  $qty_35 =0;  $qty_18 =0;  $qty_30 =0; 	$bal = 0; 
				$qty_11_e =0; $qty_15_e =0; $qty_45_e =0; $qty_6_e =0;  $qty_35_e =0;  $qty_18_e =0;  $qty_30_e =0; 
				$recv_weight =0; $price_11 = 0; $remarks =''; $itemnameint ='';  $purchase_rate =0; $total = 0;	$lpg_balance_is=0;
				$irnos = $value['vno'];
				$cell_number = $value['cell'];
				$party_name = $value['aname'];
				$remarks = $value['remarks']; 
				$amount_recv = $value['amount']; 
				$datas[] = array(
					'for_date' => $date,
                    'transno' => $irnos,
                    'party_name' => $party_name,
                    'cell_number'=>$cell_number,
                    'trans_type'=> 'Cash Receipt',
                    'price_11'=>$price_11,
                    'qty_11'=>$qty_11,
                    'qty_15'=>$qty_15,
                    'qty_45'=>$qty_45,
                    'qty_6'=>$qty_6,
                    'qty_35'=>$qty_35,
                    'qty_18'=>$qty_18,
                    'qty_30'=>$qty_30,
                    'qty_11_e'=>$qty_11_e,
                    'qty_15_e'=>$qty_15_e,
                    'qty_45_e'=>$qty_45_e,
                    'qty_6_e'=>$qty_6_e,
                    'qty_35_e'=>$qty_35_e,
                    'qty_18_e'=>$qty_18_e,
                    'qty_30_e'=>$qty_30_e,
                    'total_sale'=>$total_sale,
                    'bal_tons'=>$bal_tons,
                    'bill'=>$bill,
                    'amount_recv'=>$amount_recv,
                    'amount_paid'=>$amount_paid,
                    'bal'=>$bal,
                    'remarks'=>$remarks,
                    'from_date'=>$date,
                    'to_date'=>$to_date,
				);
				}
				} 
				///////////////////////////////////////////////////  END CASH RECEIPT ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  END CASH RECEIPT ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  END CASH RECEIPT ////////////////////////////////////////////////////////////	 
			 
					
				///////////////////////////////////////////////////  START SECURITY RECEIPT ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  START SECURITY RECEIPT ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  START SECURITY RECEIPT ////////////////////////////////////////////////////////////	 
			 	$sql_purchases="select m.trans_id, m.customercode, m.remarks,  a.aname, a.cell ,m.security_recv as amount
				FROM tbl_security_receipt m, tblacode a where  m.dt='$date' and   m.customercode=a.acode  ";	
				$query = $this->db->query($sql_purchases);
				if($query->num_rows()>0){
				foreach($query->result_array() as $key => $value) {
				$total_sale =0;	  $bill =0; $amount_recv =0; $amount_paid =0; $bal =0; 
				$qty_11 =0; $qty_15 =0; $qty_45 =0; $qty_6 =0;  $qty_35 =0;  $qty_18 =0;  $qty_30 =0; 	$bal = 0; 
				$qty_11_e =0; $qty_15_e =0; $qty_45_e =0; $qty_6_e =0;  $qty_35_e =0;  $qty_18_e =0;  $qty_30_e =0; 
				$recv_weight =0; $price_11 = 0; $remarks =''; $itemnameint ='';  $purchase_rate =0; $total = 0;	$lpg_balance_is=0;
				$irnos = $value['trans_id'];
				$cell_number = $value['cell'];
				$party_name = $value['aname'];
				$remarks = $value['remarks']; 
				$amount_recv = $value['amount']; 
				$datas[] = array(
					'for_date' => $date,
                    'transno' => $irnos,
                    'party_name' => $party_name,
                    'cell_number'=>$cell_number,
                    'trans_type'=> 'Security Receipt',
                    'price_11'=>$price_11,
                    'qty_11'=>$qty_11,
                    'qty_15'=>$qty_15,
                    'qty_45'=>$qty_45,
                    'qty_6'=>$qty_6,
                    'qty_35'=>$qty_35,
                    'qty_18'=>$qty_18,
                    'qty_30'=>$qty_30,
                    'qty_11_e'=>$qty_11_e,
                    'qty_15_e'=>$qty_15_e,
                    'qty_45_e'=>$qty_45_e,
                    'qty_6_e'=>$qty_6_e,
                    'qty_35_e'=>$qty_35_e,
                    'qty_18_e'=>$qty_18_e,
                    'qty_30_e'=>$qty_30_e,
                    'total_sale'=>$total_sale,
                    'bal_tons'=>$bal_tons,
                    'bill'=>$bill,
                    'amount_recv'=>$amount_recv,
                    'amount_paid'=>$amount_paid,
                    'bal'=>$bal,
                    'remarks'=>$remarks,
                    'from_date'=>$date,
                    'to_date'=>$to_date,
				);
				}
				} 
				///////////////////////////////////////////////////  END SECURITY RECEIPT ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  END SECURITY RECEIPT ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  END SECURITY RECEIPT ////////////////////////////////////////////////////////////	 
			 
				
		 
					 
					 
				
				///////////////////////////////////////////////////  START BANK PAYMENT ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  START BANK PAYMENT ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  START BANK PAYMENT ////////////////////////////////////////////////////////////	 
			 	$sql_purchases="select m.vno, m.acode, m.remarks,  a.aname, a.cell ,m.damount as amount
				FROM tbltrans_detail m, tblacode a where  m.vdate='$date' 
				and a.general!='2004002000' and m.acode=a.acode and m.vtype='BP' and m.sale_point_id='$sale_point_id'";	
				$query = $this->db->query($sql_purchases);
				if($query->num_rows()>0){
				foreach($query->result_array() as $key => $value) {
				$total_sale =0;	  $bill =0; $amount_recv =0; $amount_paid =0; $bal =0; 
				$qty_11 =0; $qty_15 =0; $qty_45 =0; $qty_6 =0;  $qty_35 =0;  $qty_18 =0;  $qty_30 =0; 	$bal = 0; 
				$qty_11_e =0; $qty_15_e =0; $qty_45_e =0; $qty_6_e =0;  $qty_35_e =0;  $qty_18_e =0;  $qty_30_e =0; 
				$recv_weight =0; $price_11 = 0; $remarks =''; $itemnameint ='';  $purchase_rate =0; $total = 0;	$lpg_balance_is=0;
				$irnos = $value['vno'];
				$cell_number = $value['cell'];
				$party_name = $value['aname'];
				$remarks = $value['remarks']; 
				$amount_paid = $value['amount']; 
				$datas[] = array(
					'for_date' => $date,
                    'transno' => $irnos,
                    'party_name' => $party_name,
                    'cell_number'=>$cell_number,
                    'trans_type'=> 'Bank Payment',
                    'price_11'=>$price_11,
                    'qty_11'=>$qty_11,
                    'qty_15'=>$qty_15,
                    'qty_45'=>$qty_45,
                    'qty_6'=>$qty_6,
                    'qty_35'=>$qty_35,
                    'qty_18'=>$qty_18,
                    'qty_30'=>$qty_30,
                    'qty_11_e'=>$qty_11_e,
                    'qty_15_e'=>$qty_15_e,
                    'qty_45_e'=>$qty_45_e,
                    'qty_6_e'=>$qty_6_e,
                    'qty_35_e'=>$qty_35_e,
                    'qty_18_e'=>$qty_18_e,
                    'qty_30_e'=>$qty_30_e,
                    'total_sale'=>$total_sale,
                    'bal_tons'=>$bal_tons,
                    'bill'=>$bill,
                    'amount_recv'=>$amount_recv,
                    'amount_paid'=>$amount_paid,
                    'bal'=>$bal,
                    'remarks'=>$remarks,
                    'from_date'=>$date,
                    'to_date'=>$to_date,
				);
				}
				} 
				///////////////////////////////////////////////////  END BANK PAYMENT ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  END BANK PAYMENT ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  END BANK PAYMENT ////////////////////////////////////////////////////////////	 
			 

				
				///////////////////////////////////////////////////  START BANK RECEIPT ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  START BANK RECEIPT ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  START BANK RECEIPT ////////////////////////////////////////////////////////////	 
			 	$sql_purchases="select m.vno, m.acode, m.remarks,  a.aname, a.cell ,m.camount as amount
				FROM tbltrans_detail m, tblacode a where  m.vdate='$date' 
				and a.general!='2004002000' and m.acode=a.acode and m.vtype='BR' and m.sale_point_id='$sale_point_id'";	
				$query = $this->db->query($sql_purchases);
				if($query->num_rows()>0){
				foreach($query->result_array() as $key => $value) {
				$total_sale =0;	  $bill =0; $amount_recv =0; $amount_paid =0; $bal =0; 
				$qty_11 =0; $qty_15 =0; $qty_45 =0; $qty_6 =0;  $qty_35 =0;  $qty_18 =0;  $qty_30 =0; 	$bal = 0; 
				$qty_11_e =0; $qty_15_e =0; $qty_45_e =0; $qty_6_e =0;  $qty_35_e =0;  $qty_18_e =0;  $qty_30_e =0; 
				$recv_weight =0; $price_11 = 0; $remarks =''; $itemnameint ='';  $purchase_rate =0; $total = 0;	$lpg_balance_is=0;
				$irnos = $value['vno'];
				$cell_number = $value['cell'];
				$party_name = $value['aname'];
				$remarks = $value['remarks']; 
				$amount_recv = $value['amount']; 
				$datas[] = array(
					'for_date' => $date,
                    'transno' => $irnos,
                    'party_name' => $party_name,
                    'cell_number'=>$cell_number,
                    'trans_type'=> 'Bank Receipt',
                    'price_11'=>$price_11,
                    'qty_11'=>$qty_11,
                    'qty_15'=>$qty_15,
                    'qty_45'=>$qty_45,
                    'qty_6'=>$qty_6,
                    'qty_35'=>$qty_35,
                    'qty_18'=>$qty_18,
                    'qty_30'=>$qty_30,
                    'qty_11_e'=>$qty_11_e,
                    'qty_15_e'=>$qty_15_e,
                    'qty_45_e'=>$qty_45_e,
                    'qty_6_e'=>$qty_6_e,
                    'qty_35_e'=>$qty_35_e,
                    'qty_18_e'=>$qty_18_e,
                    'qty_30_e'=>$qty_30_e,
                    'total_sale'=>$total_sale,
                    'bal_tons'=>$bal_tons,
                    'bill'=>$bill,
                    'amount_recv'=>$amount_recv,
                    'amount_paid'=>$amount_paid,
                    'bal'=>$bal,
                    'remarks'=>$remarks,
                    'from_date'=>$date,
                    'to_date'=>$to_date,
				);
				}
				} 
				///////////////////////////////////////////////////  END BANK RECEIPT ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  END BANK RECEIPT ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  END BANK RECEIPT ////////////////////////////////////////////////////////////	 
			 
				///////////////////////////////////////////////////  START JV ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  START JV ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  START JV ////////////////////////////////////////////////////////////	 
			 	$sql_purchases="select m.vno, m.acode, m.remarks,  a.aname, a.cell ,m.camount as amount,m.damount as damount
				FROM tbltrans_detail m, tblacode a where  m.vdate='$date' 
				 and m.acode=a.acode and m.vtype='JV' and m.sale_point_id='$sale_point_id'";	
				$query = $this->db->query($sql_purchases);
				if($query->num_rows()>0){
				foreach($query->result_array() as $key => $value) {
				$total_sale =0;	  $bill =0; $amount_recv =0; $amount_paid =0; $bal =0; 
				$qty_11 =0; $qty_15 =0; $qty_45 =0; $qty_6 =0;  $qty_35 =0;  $qty_18 =0;  $qty_30 =0; 	$bal = 0; 
				$qty_11_e =0; $qty_15_e =0; $qty_45_e =0; $qty_6_e =0;  $qty_35_e =0;  $qty_18_e =0;  $qty_30_e =0; 
				$recv_weight =0; $price_11 = 0; $remarks =''; $itemnameint ='';  $purchase_rate =0; $total = 0;	$lpg_balance_is=0;
				$irnos = $value['vno'];
				$cell_number = $value['cell'];
				$party_name = $value['aname'];
				$remarks = $value['remarks']; 
				$amount_paid = $value['damount']; 
				$amount_recv = $value['amount']; 
				$datas[] = array(
					'for_date' => $date,
                    'transno' => $irnos,
                    'party_name' => $party_name,
                    'cell_number'=>$cell_number,
                    'trans_type'=> 'Journal Voucher',
                    'price_11'=>$price_11,
                    'qty_11'=>$qty_11,
                    'qty_15'=>$qty_15,
                    'qty_45'=>$qty_45,
                    'qty_6'=>$qty_6,
                    'qty_35'=>$qty_35,
                    'qty_18'=>$qty_18,
                    'qty_30'=>$qty_30,
                    'qty_11_e'=>$qty_11_e,
                    'qty_15_e'=>$qty_15_e,
                    'qty_45_e'=>$qty_45_e,
                    'qty_6_e'=>$qty_6_e,
                    'qty_35_e'=>$qty_35_e,
                    'qty_18_e'=>$qty_18_e,
                    'qty_30_e'=>$qty_30_e,
                    'total_sale'=>$total_sale,
                    'bal_tons'=>$bal_tons,
                    'bill'=>$bill,
                    'amount_recv'=>$amount_recv,
                    'amount_paid'=>$amount_paid,
                    'bal'=>$bal,
                    'remarks'=>$remarks,
                    'from_date'=>$date,
                    'to_date'=>$to_date,
				);
				}
				} 
				///////////////////////////////////////////////////  END JV   ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  END JV   ////////////////////////////////////////////////////////////	 
				///////////////////////////////////////////////////  END JV   ////////////////////////////////////////////////////////////	 
			 

                $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
				
			
	}


	
     //pm($datas);
        return $datas;
    }
}

?>