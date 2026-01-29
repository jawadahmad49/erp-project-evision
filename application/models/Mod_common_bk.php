<?php

class Mod_common extends CI_Model {

    function __construct() {

        parent::__construct();
    }


function shop_opening($mid, $type='',$to_date='',$closing='') {
        
		$where_filled = array('materialcode' => $mid,'type =' => $type,'date <=' => $to_date);
		$this->db->select('COALESCE(sum(qty),0) as total_qty');
		$this->db->where($where_filled); 
		$get = $this->db->get('tbl_shop_opening');
		$sale_filled=$get->row_array();
	
		return $sale_filled['total_qty'];
}

    function stock($mid, $date='',$to_date='',$closing='') {

	$post_date_last=$date;
	$new_filled=0;
	$new_empty=0;
    $where_enter_date = "post_date < '" . $date . "' AND itemcode = '" . $mid . "'";
   // $last_day_enter=$this->select_single_records('tbl_posting_stock',$where_enter_date,' order by date');
  //  $last_day_enter=$this->select_single_records('tbl_posting_stock',$where_enter_date);
	
	$last_day_enter=$this->select_orderby('tbl_posting_stock', $where_enter_date,"*", "1", "post_date","Desc");
 
    if(empty($last_day_enter))
    {
		
         $new_filled=$this->mod_common->shop_opening($mid,'Filled',$date);
         $new_empty=$this->mod_common->shop_opening($mid,'Empty',$date);
 
    }
    else
		
    { 
	 
		$new_filled=$last_day_enter['closing_filled'];
        $new_empty=$last_day_enter['closing_empty'];
		//$post_date_last=$last_day_enter['post_date'];
    }
	if($to_date){$date=$to_date;}
	// print 'post_date_last'.$post_date_last.'<br>';
	// print 'date'.$to_date.'<br>';
	// print 'new_filled='.$new_filled.'<br>';
	// print 'new_empty='.$new_empty.'<br>';
	// exit;
 
    $exist=$this->select_single_records('tblmaterial_coding',"materialcode=$mid");
 
    $where_filled = array('tbl_goodsreceiving_detail.itemid' => $mid,'tbl_goodsreceiving.receiptdate <=' => $date,'tbl_goodsreceiving.trans_typ' => 'purchasefilled');

    if ($closing==1) {
        
        $where_filled = array('tbl_goodsreceiving_detail.itemid' => $mid,'tbl_goodsreceiving.receiptdate <=' => $date ,'tbl_goodsreceiving.receiptdate >=' => $post_date_last,'tbl_goodsreceiving.trans_typ' => 'purchasefilled');
    }

    if ($closing==2) {
        
        $where_filled = array('tbl_goodsreceiving_detail.itemid' => $mid,'tbl_goodsreceiving.receiptdate <' => $date ,'tbl_goodsreceiving.receiptdate >' => $post_date_last ,'tbl_goodsreceiving.trans_typ' => 'purchasefilled');
    }
    
    $this->db->select('COALESCE(sum(quantity),0) as total_qty');
    $this->db->join('tbl_goodsreceiving', 'tbl_goodsreceiving_detail.receipt_detail_id = tbl_goodsreceiving.receiptnos');
    $this->db->where($where_filled); 
    $get = $this->db->get('tbl_goodsreceiving_detail');
    $purcahse_filled=$get->row_array();
 
    // Puchase filled end

    // purchase other start
    $where_filled = array('tbl_goodsreceiving_detail.itemid' => $mid,'tbl_goodsreceiving.receiptdate <=' => $date,'tbl_goodsreceiving.trans_typ' => 'purchaseother');

    if ($closing==1) {
        
        $where_filled = array('tbl_goodsreceiving_detail.itemid' => $mid,'tbl_goodsreceiving.receiptdate <=' => $date,'tbl_goodsreceiving.receiptdate >=' => $post_date_last,'tbl_goodsreceiving.trans_typ' => 'purchaseother');
    }

    if ($closing==2) {
        
        $where_filled = array('tbl_goodsreceiving_detail.itemid' => $mid,'tbl_goodsreceiving.receiptdate <' => $date,'tbl_goodsreceiving.receiptdate >' => $post_date_last,'tbl_goodsreceiving.trans_typ' => 'purchaseother');
    }


    $this->db->select('COALESCE(sum(quantity),0) as total_qty');
    $this->db->join('tbl_goodsreceiving', 'tbl_goodsreceiving_detail.receipt_detail_id = tbl_goodsreceiving.receiptnos');
    $this->db->where($where_filled); 
    $get = $this->db->get('tbl_goodsreceiving_detail');
    $purchase_other=$get->row_array();

    // Puchase other end

    // Sale filled  start
    $where_filled = array('tbl_issue_goods_detail.itemid' => $mid,'tbl_issue_goods.issuedate <=' => $date,'tbl_issue_goods.decanting!= ' => 'Yes');

    if ($closing==1) {
        
       $where_filled = array('tbl_issue_goods_detail.itemid' => $mid,'tbl_issue_goods.issuedate <=' => $date,'tbl_issue_goods.issuedate >=' => $post_date_last,'tbl_issue_goods.decanting!= ' => 'Yes');
    }

    if ($closing==2) {
        
       $where_filled = array('tbl_issue_goods_detail.itemid' => $mid,'tbl_issue_goods.issuedate <' => $date,'tbl_issue_goods.issuedate >' => $post_date_last,'tbl_issue_goods.decanting!= ' => 'Yes');
    }

    $this->db->select('COALESCE(sum(qty),0) as total_qty');
    $this->db->join('tbl_issue_goods', 'tbl_issue_goods_detail.ig_detail_id = tbl_issue_goods.issuenos');
    $this->db->where($where_filled); 
    $get = $this->db->get('tbl_issue_goods_detail');
    $sale_filled=$get->row_array();
     // Sale filled end          

//Purcahse filled+ purchase other - Sale Filled- Decanting Sale -Cylinder converstion (from)+cylinder converstion(to)+ sale return -purchase return

    // Decanting Sale start
	
	
    $where_filled = array('tbl_issue_goods_detail.itemid' => $mid,'tbl_issue_goods.issuedate <=' => $date ,'tbl_issue_goods.decanting ' => 'Yes');

    if ($closing==1) {
        
     $where_filled = array('tbl_issue_goods_detail.itemid' => $mid,'tbl_issue_goods.issuedate <=' => $date,'tbl_issue_goods.issuedate >=' => $post_date_last ,'tbl_issue_goods.decanting ' => 'Yes');
    }
    if ($closing==2) {
        
     $where_filled = array('tbl_issue_goods_detail.itemid' => $mid,'tbl_issue_goods.issuedate <' => $date,'tbl_issue_goods.issuedate >' => $post_date_last ,'tbl_issue_goods.decanting ' => 'Yes');
    }    

    $this->db->select('COALESCE(sum(qty),0) as total_qty');
    $this->db->join('tbl_issue_goods', 'tbl_issue_goods_detail.ig_detail_id = tbl_issue_goods.issuenos');
    $this->db->where($where_filled); 
    $get = $this->db->get('tbl_issue_goods_detail');
    $decanting_sale=$get->row_array();
  
    /// pm($decanting_sale);

    //$decanting_sale['total_qty']=0;
   //pm($decanting_sale);
    //Decanting Sale end    


    // Cylinder converstion  from start
    $where_filled = array('tbl_cylinderconversion_detail.itemcode' => $mid,'tbl_cylinderconversion_master.trans_date <=' => $date,'tbl_cylinderconversion_detail.type ' => 'from');


    if ($closing==1) {
        
     $where_filled = array('tbl_cylinderconversion_detail.itemcode' => $mid,'tbl_cylinderconversion_master.trans_date <=' => $date,'tbl_cylinderconversion_master.trans_date >=' => $post_date_last,'tbl_cylinderconversion_detail.type ' => 'from');
    }

    if ($closing==2) {
        
     $where_filled = array('tbl_cylinderconversion_detail.itemcode' => $mid,'tbl_cylinderconversion_master.trans_date <' => $date,'tbl_cylinderconversion_master.trans_date >' => $post_date_last,'tbl_cylinderconversion_detail.type ' => 'from');
    }
    $this->db->select('COALESCE(sum(qty),0) as total_qty');
    $this->db->join('tbl_cylinderconversion_master', 'tbl_cylinderconversion_detail.trans_id = tbl_cylinderconversion_master.trans_id');
    $this->db->where($where_filled); 
    $get = $this->db->get('tbl_cylinderconversion_detail');
    $cylinder_converstion_from=$get->row_array();

    //pm($cylinder_converstion_from);
    //Cylinder converstion from end     

    // Cylinder converstion  to start
    $where_filled = array('tbl_cylinderconversion_detail.itemcode' => $mid,'tbl_cylinderconversion_master.trans_date <=' => $date,'tbl_cylinderconversion_detail.type ' => 'to');

    if ($closing==1) {
        
     $where_filled = array('tbl_cylinderconversion_detail.itemcode' => $mid,'tbl_cylinderconversion_master.trans_date <=' => $date,'tbl_cylinderconversion_master.trans_date >=' => $post_date_last,'tbl_cylinderconversion_detail.type ' => 'to');
    }

    if ($closing==2) {
        
     $where_filled = array('tbl_cylinderconversion_detail.itemcode' => $mid,'tbl_cylinderconversion_master.trans_date <' => $date,'tbl_cylinderconversion_master.trans_date >' => $post_date_last,'tbl_cylinderconversion_detail.type ' => 'to');
    }

    $this->db->select('COALESCE(sum(qty),0) as total_qty');
    $this->db->join('tbl_cylinderconversion_master', 'tbl_cylinderconversion_detail.trans_id = tbl_cylinderconversion_master.trans_id');
    $this->db->where($where_filled); 
    $get = $this->db->get('tbl_cylinderconversion_detail');
    $cylinder_converstion_to=$get->row_array();
    //pm($cylinder_converstion_to);
    //Cylinder converstion to end     

  
  
    $where_filled = array('tbl_issue_return_detail.itemid' => $mid,'tbl_issue_return.irdate <=' => $date,'tbl_issue_return_detail.type= ' => 'Filled',  'tbl_issue_return.type!=' =>'salereturn');

    if ($closing==1) {
        
     $where_filled = array('tbl_issue_return_detail.itemid' => $mid,'tbl_issue_return.irdate <=' => $date,'tbl_issue_return.irdate >=' => $post_date_last,'tbl_issue_return_detail.type= ' => 'Filled',  'tbl_issue_return.type!=' =>'salereturn');
    }
    if ($closing==2) {
        
     $where_filled = array('tbl_issue_return_detail.itemid' => $mid,'tbl_issue_return.irdate <' => $date,'tbl_issue_return.irdate >' => $post_date_last,'tbl_issue_return_detail.type= ' => 'Filled',  'tbl_issue_return.type!=' =>'salereturn');
    }
    $this->db->select('COALESCE(sum(qty),0) as total_qty');
    $this->db->join('tbl_issue_return', 'tbl_issue_return_detail.irnos = tbl_issue_return.irnos');
    $this->db->where($where_filled); 
    $get = $this->db->get('tbl_issue_return_detail');
    $purchase_return=$get->row_array();
  


  
  
  
    $where_filled = array('tbl_issue_return_detail.itemid' => $mid,'tbl_issue_return.irdate <=' => $date,'tbl_issue_return_detail.type= ' => 'Filled',  'tbl_issue_return.type=' =>'salereturn');

    if ($closing==1) {
        
     $where_filled = array('tbl_issue_return_detail.itemid' => $mid,'tbl_issue_return.irdate <=' => $date,'tbl_issue_return.irdate >=' => $post_date_last,'tbl_issue_return_detail.type= ' => 'Filled',  'tbl_issue_return.type=' =>'salereturn');
    }
    if ($closing==2) {
        
     $where_filled = array('tbl_issue_return_detail.itemid' => $mid,'tbl_issue_return.irdate <' => $date,'tbl_issue_return.irdate >' => $post_date_last,'tbl_issue_return_detail.type= ' => 'Filled',  'tbl_issue_return.type=' =>'salereturn');
    }
    $this->db->select('COALESCE(sum(qty),0) as total_qty');
    $this->db->join('tbl_issue_return', 'tbl_issue_return_detail.irnos = tbl_issue_return.irnos');
    $this->db->where($where_filled); 
    $get = $this->db->get('tbl_issue_return_detail');
    $issue_return=$get->row_array();
  
   
  
	$sale_return=0;
	  $filled_stock=$purcahse_filled['total_qty']+ $purchase_other['total_qty'] - $sale_filled['total_qty']-$decanting_sale['total_qty'] 
	-$cylinder_converstion_from['total_qty']+$cylinder_converstion_to['total_qty']+$sale_return['total_qty'] -$purchase_return['total_qty']
	+$issue_return['total_qty'];


		// print 'purcahse_filled='.$purcahse_filled['total_qty'].'<br>';
		// print 'sale_filled='.$sale_filled['total_qty'].'<br>';
		// print 'purchase_other='.$purchase_other['total_qty'].'<br>';
		// print 'decanting_sale='.$decanting_sale['total_qty'].'<br>';
		// print 'cylinder_converstion_from='.$cylinder_converstion_from['total_qty'].'<br>';
		// print 'sale_return='.$sale_return['total_qty'].'<br>';
		// print 'cylinder_converstion_to='.$cylinder_converstion_to['total_qty'].'<br>';
		// print 'purchase_return='.$purchase_return['total_qty'].'<br>';
		// print 'issue_return='.$issue_return['total_qty'].'<br>';
 
 
 
 
 
 
 
 
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////// 											  /////////////////////////////////////////////
	//////////////////////////////////////////////////// 											  /////////////////////////////////////////////
	//////////////////////////////////////////////////// 					EMPTY STOCK				  /////////////////////////////////////////////
	//////////////////////////////////////////////////// 											  /////////////////////////////////////////////
	//////////////////////////////////////////////////// 											  /////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 
 
 
 
    //purchase empty start
    $where_filled = array('tbl_goodsreceiving_detail.itemid' => $mid,'tbl_goodsreceiving.receiptdate <=' => $date,'tbl_goodsreceiving.trans_typ' => 'purchaseempty');


    if ($closing==1) {
        
       $where_filled = array('tbl_goodsreceiving_detail.itemid' => $mid,'tbl_goodsreceiving.receiptdate <=' => $date,'tbl_goodsreceiving.receiptdate >=' => $post_date_last,'tbl_goodsreceiving.trans_typ' => 'purchaseempty');
    }

    if ($closing==2) {
        
       $where_filled = array('tbl_goodsreceiving_detail.itemid' => $mid,'tbl_goodsreceiving.receiptdate <' => $date,'tbl_goodsreceiving.receiptdate >' => $post_date_last,'tbl_goodsreceiving.trans_typ' => 'purchaseempty');
    }
    $this->db->select('COALESCE(sum(quantity),0) as total_qty');
    $this->db->join('tbl_goodsreceiving', 'tbl_goodsreceiving_detail.receipt_detail_id = tbl_goodsreceiving.receiptnos');
    $this->db->where($where_filled); 
    $get = $this->db->get('tbl_goodsreceiving_detail');
    $purchase_empty=$get->row_array();
    //pm($purchase_empty);
    //purchase empty end  
 

    //discusss

    // //sale return start
    // $where_filled = array('tbl_issue_goods_detail.itemid' => $mid,'tbl_issue_goods.issuedate <=' => $date,'tbl_issue_goods.decanting!= ' => 'Yes');

    // if ($closing==1) {
        
        // $where_filled = array('tbl_issue_goods_detail.itemid' => $mid,'tbl_issue_goods.issuedate =' => $date,'tbl_issue_goods.decanting !=' => 'Yes');
    // }
    // if ($closing==2) {
        
        // $where_filled = array('tbl_issue_goods_detail.itemid' => $mid,'tbl_issue_goods.issuedate <' => $date,'tbl_issue_goods.decanting !=' => 'Yes');
    // }
    // $this->db->select('COALESCE(sum(returns),0) as total_qty');
    // $this->db->join('tbl_issue_goods', 'tbl_issue_goods_detail.ig_detail_id = tbl_issue_goods.issuenos');
    // $this->db->where($where_filled); 
    // $get = $this->db->get('tbl_issue_goods_detail');
    // $sale_return=$get->row_array();
  

    //Purchase filled where empty returned
    $where_filled = array('tbl_goodsreceiving_detail.itemid' => $mid,'tbl_goodsreceiving.receiptdate <=' => $date,'tbl_goodsreceiving.trans_typ' => 'purchasefilled','tbl_goodsreceiving_detail.ereturn > ' => 0);

    if ($closing==1) {
        
    $where_filled = array('tbl_goodsreceiving_detail.itemid' => $mid,'tbl_goodsreceiving.receiptdate <=' => $date,'tbl_goodsreceiving.receiptdate >=' => $post_date_last,'tbl_goodsreceiving.trans_typ' => 'purchasefilled','tbl_goodsreceiving_detail.ereturn > ' => 0);
    }

    if ($closing==2) {
    $where_filled = array('tbl_goodsreceiving_detail.itemid' => $mid,'tbl_goodsreceiving.receiptdate <' => $date,'tbl_goodsreceiving.receiptdate >' => $post_date_last,'tbl_goodsreceiving.trans_typ' => 'purchasefilled','tbl_goodsreceiving_detail.ereturn > ' => 0);
    }


    $this->db->select('COALESCE(sum(ereturn),0) as total_qty');
    $this->db->join('tbl_goodsreceiving', 'tbl_goodsreceiving_detail.receipt_detail_id = tbl_goodsreceiving.receiptnos');
    $this->db->where($where_filled); 
    $get = $this->db->get('tbl_goodsreceiving_detail');
	 
    $purchase_filled_where_empty=$get->row_array();
    

    //sale empty return
    $where_filled = array('tbl_issue_goods_detail.itemid' => $mid,'tbl_issue_goods.issuedate <=' => $date ,'tbl_issue_goods.decanting !=' => 'Yes');

    if ($closing==1) {
        
         $where_filled = array('tbl_issue_goods_detail.itemid' => $mid,'tbl_issue_goods.issuedate <=' => $date,'tbl_issue_goods.issuedate >=' => $post_date_last,'tbl_issue_goods.decanting !=' => 'Yes');
    }

    if ($closing==2) {
        
         $where_filled = array('tbl_issue_goods_detail.itemid' => $mid,'tbl_issue_goods.issuedate <' => $date,'tbl_issue_goods.issuedate >' => $post_date_last,'tbl_issue_goods.decanting !=' => 'Yes');
    }

    $this->db->select('COALESCE(sum(returns),0) as total_qty');
    $this->db->join('tbl_issue_goods', 'tbl_issue_goods_detail.ig_detail_id = tbl_issue_goods.issuenos');
    $this->db->where($where_filled); 
    $get = $this->db->get('tbl_issue_goods_detail');
    $sale_empty_return=$get->row_array();
    //pm($sale_empty_return);
    //sale empty return

	
	
	
	
	
	
	
	
	 
    $where_emptry = array('tbl_issue_return_detail.itemid' => $mid,'tbl_issue_return.irdate <=' => $date,'tbl_issue_return_detail.type= ' => 'Empty',  'tbl_issue_return.type!=' =>'salereturn');

    if ($closing==1) {
        
     $where_emptry = array('tbl_issue_return_detail.itemid' => $mid,'tbl_issue_return.irdate <=' => $date,'tbl_issue_return.irdate >=' => $post_date_last,'tbl_issue_return_detail.type= ' => 'Empty',  'tbl_issue_return.type!=' =>'salereturn');
    }
    if ($closing==2) {
        
     $where_emptry = array('tbl_issue_return_detail.itemid' => $mid,'tbl_issue_return.irdate <' => $date,'tbl_issue_return.irdate >' => $post_date_last,'tbl_issue_return_detail.type= ' => 'Empty',  'tbl_issue_return.type!=' =>'salereturn');
    }
    $this->db->select('COALESCE(sum(qty),0) as total_qty');
    $this->db->join('tbl_issue_return', 'tbl_issue_return_detail.irnos = tbl_issue_return.irnos');
    $this->db->where($where_emptry); 
    $get = $this->db->get('tbl_issue_return_detail');
    $purchase_return_empty=$get->row_array();
 
	
	
    $where_filled = array('tbl_issue_return_detail.itemid' => $mid,'tbl_issue_return.irdate <=' => $date,'tbl_issue_return_detail.type= ' => 'Empty',  'tbl_issue_return.type=' =>'salereturn');

    if ($closing==1) {
        
     $where_filled = array('tbl_issue_return_detail.itemid' => $mid,'tbl_issue_return.irdate <=' => $date,'tbl_issue_return.irdate >=' => $post_date_last,'tbl_issue_return_detail.type= ' => 'Empty',  'tbl_issue_return.type=' =>'salereturn');
    }
    if ($closing==2) {
        
     $where_filled = array('tbl_issue_return_detail.itemid' => $mid,'tbl_issue_return.irdate <' => $date,'tbl_issue_return.irdate >' => $post_date_last,'tbl_issue_return_detail.type= ' => 'Empty',  'tbl_issue_return.type=' =>'salereturn');
    }
    $this->db->select('COALESCE(sum(qty),0) as total_qty');
    $this->db->join('tbl_issue_return', 'tbl_issue_return_detail.irnos = tbl_issue_return.irnos');
    $this->db->where($where_filled); 
    $get = $this->db->get('tbl_issue_return_detail');
    $issue_return_empty=$get->row_array();
  
	
	
	
 
 
 
 	
	
    $where_filled = array('tbl_issue_goods_detail.itemid' => $mid,'tbl_issue_goods.issuedate <=' => $date ,'tbl_issue_goods.decanting =' => 'Yes');

    if ($closing==1) {
        
     $where_filled = array('tbl_issue_goods_detail.itemid' => $mid,'tbl_issue_goods.issuedate <=' => $date ,'tbl_issue_goods.issuedate >=' => $post_date_last ,'tbl_issue_goods.decanting  =' => 'Yes');
    }
    if ($closing==2) {
        
     $where_filled = array('tbl_issue_goods_detail.itemid' => $mid,'tbl_issue_goods.issuedate <' => $date,'tbl_issue_goods.issuedate >' => $post_date_last ,'tbl_issue_goods.decanting  =' => 'Yes');
    }    

    $this->db->select('COALESCE(sum(returns),0) as total_qty');
    $this->db->join('tbl_issue_goods', 'tbl_issue_goods_detail.ig_detail_id = tbl_issue_goods.issuenos');
    $this->db->where($where_filled); 
    $get = $this->db->get('tbl_issue_goods_detail');
    $decanting_sale_empty_return=$get->row_array();
  
  
  
  
	//  print 'purchase_empty='.$purchase_empty['total_qty'].'<br>';
		// print 'purchase_filled_where_empty='.$purchase_filled_where_empty['total_qty'].'<br>';
		// print 'cylinder_converstion_to='.$cylinder_converstion_to['total_qty'].'<br>';
		// print 'purchase_return_empty='.$purchase_return_empty['total_qty'].'<br>';
		// print 'issue_return_empty='.$issue_return_empty['total_qty'].'<br>';
		// print 'sale_empty_return='.$sale_empty_return['total_qty'].'<br>';
		// print 'decanting_sale_empty_return='.$decanting_sale_empty_return['total_qty'].'<br>';
		// print 'cylinder_converstion_from='.$cylinder_converstion_from['total_qty'].'<br>';
		 
 
 
 
     $empty_stock=$purchase_empty['total_qty']-$purchase_filled_where_empty['total_qty'] -$cylinder_converstion_to['total_qty']-$purchase_return_empty['total_qty']
   +$issue_return_empty['total_qty']
   +$sale_empty_return['total_qty']
   +$decanting_sale_empty_return['total_qty']
   +$cylinder_converstion_from['total_qty'] ;
	 
	
	
	
   $filled_final=$filled_stock+$new_filled;
    $empty_final=$empty_stock+$new_empty;
 
    return $filled_final.'_'.$empty_final;

    }


    function stock_qty($table, $where='',$fields='') {



        $this->db->select('tbl_business_units.*,tbl_companies.company_name,tbl_countries.country_name,tbl_business_nature.nature_name');
        $this->db->join('tbl_companies', 'tbl_business_units.company_id = tbl_companies.company_id'); 
        $this->db->join('tbl_countries', 'tbl_business_units.business_country = tbl_countries.country_id'); 
        $this->db->join('tbl_business_nature', 'tbl_business_units.business_region = tbl_business_nature.nature_id'); 
        $this->db->join('tbl_states', 'tbl_business_units.business_province = tbl_states.state_id'); 
        $this->db->join('tbl_cities', 'tbl_business_units.business_city = tbl_cities.city_id'); 
         $this->db->group_by('tbl_business_units.business_id'); 
        $get = $this->db->get('tbl_business_units');
        return $get->result_array();



        $this->db->select($fields);
        
        if ($where != "") {
            $this->db->where($where);
        }

        $get = $this->db->get($table);

        return $get->row_array();
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


    function update_table($table = "", $where = "", $data = "") {

        $this->db->where($where);
        $update = $this->db->update($table, $data);
        if ($update) {
            return true;
        } else {
            return false;
        }

    }
    
    function get_all_records_nums($table = "", $fields = "*",$where="") {

        $this->db->select($fields);
		if ($where != "") {
            $this->db->where($where);
        }
        $get = $this->db->get($table);
		
        return $get->num_rows();
    }

    function get_all_records($table = "", $fields = "*") {

        $this->db->select($fields);
         $get = $this->db->get($table);
        return $get->result_array();
    }

    function select_array_records($table = "",  $fields = "*",$where = "") {

        $this->db->select($fields);

        if ($where != "") {

            $this->db->where($where);

        }
        $get = $this->db->get($table);

        return $get->result_array();

    }

    function select_array_records_item($table = "",  $fields = "*",$data = "",$new_data,$type) {

        //pm($data);


        $fdate=$data['to_date'];
        //pm($data);


        $where ='';
        $where_one_date ='AND catcode= 1';
        if($new_data['brandname']!='')
        {
            $brandnames=$new_data['brandname'];
            $where =$where . "  AND brandname='$brandnames'";
        }

        if($new_data['items']!='')
        {
            $items=$new_data['items'];
            $where =$where ."   AND materialcode='$items'" ;
        }

        if($type==2)
        {
            $sql="SELECT * from `tblmaterial_coding` $category_id WHERE materialcode IN(SELECT itemid FROM tbl_issue_goods as main_table INNER JOIN tbl_issue_goods_detail as detail_new ON main_table.issuenos=detail_new.ig_detail_id WHERE `issuedate`<= '$fdate') $where";
        }

        else 
        {
            $sql="SELECT * from `tblmaterial_coding` $category_id WHERE materialcode IN(SELECT itemid FROM tbl_issue_goods as main_table INNER JOIN tbl_issue_goods_detail as detail_new ON main_table.issuenos=detail_new.ig_detail_id WHERE `issuedate` = '$fdate') $where_one_date";
        }

            $querycot = $this->db->query($sql);
           return $querycot->result_array();

    }
    function select_array_records_db($table = "",  $fields = "*",$where = "") {

        $this->db2= $this->load->database('dbuser', TRUE);
        $this->db2->select($fields);

        if ($where != "") {

            $this->db2->where($where);

        }
        $get = $this->db2->get($table);

         return $get->result_array();
    }

    function delete_record($table = "", $where = "") {

        $this->db->where($where);
        $delete = $this->db->delete($table);
        if ($delete)
            return true;
        else
            return false;
    }
    function select_single_records($table = "", $where = "", $fields = "*") {

        $this->db->select($fields);
        if ($where != "") {
            $this->db->where($where);
        }
        $get = $this->db->get($table);

        return $get->row_array();
    } 

    function select_last_records($table = "", $where = "", $fields = "*") {

        $this->db->select($fields);
        if ($where != "") {
            $this->db->where($where);
        }
        $this->db->limit(1);
        $this->db->order_by("trans_id", "DESC");
        $get = $this->db->get($table);
        return $get->row_array();
    }
    function select_orderby($table = "", $where = "", $fields = "*", $limit = "", $order_by = "",$order_by_desc_asc = "") {

        $this->db->select($fields);
        if ($where != "") {
            $this->db->where($where);
        }
        $this->db->limit($limit);
        $this->db->order_by($order_by, $order_by_desc_asc);
        $get = $this->db->get($table);
        return $get->row_array();
    }

    function get_all_records_row($table = "", $fields = "*") {

        $this->db->select($fields);
         $get = $this->db->get($table);
        return $get->row_array();
    }     
}

?>