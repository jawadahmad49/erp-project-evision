<?php

class Mod_common extends CI_Model {

    function __construct() {

        parent::__construct();

    }

public function newconnection($databasename){
	                $config['hostname'] = 'localhost';
					$config['username'] = 'root';
					$config['password'] = '';
					$config['database'] = $databasename;
					$config['dbdriver'] = 'mysqli';
					$config['dbprefix'] = '';
					$config['pconnect'] = FALSE;
					$config['db_debug'] = TRUE;
					$config['cache_on'] = FALSE;
					$config['cachedir'] = '';
					$config['char_set'] = 'utf8';
					$config['dbcollat'] = 'utf8_general_ci';


					$this->db->db_select($databasename); 
}
	function shop_opening($mid, $type='',$to_date='',$closing='',$sale_point_id) {

		    $login_user=$this->session->userdata('id');
            $location = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
            $sub_type='Opening';
            if ($location=='0') {
            	$location=$sale_point_id;
            }
			$where_filled = array('itemid' => $mid,'type =' => $type,'sale_point_id ='=>$location,'sub_type ='=>sub_typesub_type);
			//$where_filled = array('materialcode' => $mid,'type =' => $type,'date <=' => $to_date);
			$this->db->select('COALESCE(sum(quantity),0) as total_qty');
			$this->db->where($where_filled);
			$get = $this->db->get('tbl_goodsreceiving_detail');
			$sale_filled=$get->row_array();
		 
		
			return $sale_filled['total_qty'];
	}


	function getblncsheetrecords($table,$fromdate,$lastdate){

		$this->db->select('tblacode.acode,tblacode.aname,tblacode.atype,tblacode.opngbl,tblacode.optype,damount,camount');
		$this->db->from($table);
		$this->db->join('tbltrans_detail','tblacode.acode = tbltrans_detail.acode');
		$this->db->where("tblacode.isplaccount",1);
		$this->db->where("tbltrans_detail.vdate >=",$fromdate);
		$this->db->where("tbltrans_detail.vdate <=",$lastdate);
		$get = $this->db->get();
		return $get->result();
	}

	public function custom($from,$to){
		

			set_time_limit(0);

			$this->db->trans_start();

			$this->db->query("DROP VIEW goodrecv"); 
			
			$this->db->query("CREATE VIEW goodrecv AS SELECT tbl_goodsreceiving_detail.* FROM tbl_goodsreceiving_detail inner join tblmaterial_coding on tbl_goodsreceiving_detail.itemid=tblmaterial_coding.materialcode inner join tbl_goodsreceiving on tbl_goodsreceiving.receiptnos=tbl_goodsreceiving_detail.receipt_detail_id where tblmaterial_coding.catcode='1' and tbl_goodsreceiving.trans_typ='purchasefilled'");

			$this->db->query("update goodrecv set batch_status='0',Batch_stock=quantity");

			$sales = $this->db->query("select * from tbl_issue_goods_detail inner join tblmaterial_coding on tbl_issue_goods_detail.itemid=tblmaterial_coding.materialcode inner join tbl_issue_goods on tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id where tblmaterial_coding.catcode='1' and tbl_issue_goods.type='Fill' and tbl_issue_goods.issuedate between '$from' and '$to'")->result_array();

			
			$totalprofit=0;
			foreach ($sales as $key => $value){
				$totalsaledamt = $value['qty']*$value['sprice'];

				$purchasequery = $this->db->query("SELECT * FROM `goodrecv` where batch_status='0' and itemid='".$value['itemid']."' order by receipt_id asc limit 1")->result_array()[0];

				if($purchasequery['Batch_stock']>$value['qty']){
					$batch_stock_left = $purchasequery['Batch_stock']-$value['qty'];

					$this->db->query("update goodrecv set Batch_stock='$batch_stock_left' where receipt_id='".$purchasequery['receipt_id']."'");

					$totalpurchasedamt = $value['qty']*$purchasequery['rate'];

				}else if($purchasequery['Batch_stock']==$value['qty']){
					$this->db->query("update goodrecv set batch_status='1',Batch_stock='0' where receipt_id='".$purchasequery['receipt_id']."'");

					$totalpurchasedamt = $value['qty']*$purchasequery['rate'];

				}else{
					$halfamt=0;
					$sale_Qty_left = $value['qty']-$purchasequery['Batch_stock'];
					$this->db->query("update goodrecv set batch_status='1',Batch_stock='0' where receipt_id='".$purchasequery['receipt_id']."'");

					$halfamt = $purchasequery['Batch_stock']*$purchasequery['rate'];

					$loop=2;
					while(1<$loop){
							

						$purchasequery = $this->db->query("select * from goodrecv where batch_status='0' and itemid='".$value['itemid']."' and receipt_id > '".$purchasequery['receipt_id']."' order by receipt_id asc limit 1")->result_array()[0];

						if($sale_Qty_left>$purchasequery['Batch_stock']){

							$sale_Qty_left = $sale_Qty_left - $purchasequery['Batch_stock'];

							$this->db->query("update goodrecv set batch_status='1',Batch_stock='0' where receipt_id='".$purchasequery['receipt_id']."'");

							$halfamt = $halfamt + ($purchasequery['Batch_stock']*$purchasequery['rate']);

						}else if($sale_Qty_left==$purchasequery['Batch_stock']){

							$this->db->query("update goodrecv set batch_status='1',Batch_stock='0' where receipt_id='".$purchasequery['receipt_id']."'");

							$halfamt = $halfamt + ($purchasequery['Batch_stock']*$purchasequery['rate']);
							$loop=0;

						}else{
							
							$Batch_stock_left = $purchasequery['Batch_stock'] - $sale_Qty_left;
								$this->db->query("update goodrecv set batch_status='0',Batch_stock='$Batch_stock_left' where receipt_id='".$purchasequery['receipt_id']."'");

							$halfamt = $halfamt + ($sale_Qty_left*$purchasequery['rate']);
							
							$loop=0;
						}

					}

					$totalpurchasedamt = $halfamt;
				}


				$totalprofit = $totalprofit + ($totalsaledamt-$totalpurchasedamt);

			}

			$this->db->trans_complete();
			return $totalprofit;
	}

	public function closeprofitforitem($from,$to,$itemid,$transaction_id){
		

			set_time_limit(0);


			$sales = $this->db->query("select * from tbl_issue_goods_detail inner join tblmaterial_coding on tbl_issue_goods_detail.itemid=tblmaterial_coding.materialcode inner join tbl_issue_goods on tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id where tblmaterial_coding.materialcode='$itemid' and tbl_issue_goods.type='Fill' and tbl_issue_goods.issuedate between '$from' and '$to'")->result_array();

			$this->db->query("DROP VIEW goodrecv"); 
			
			$this->db->query("CREATE VIEW goodrecv AS SELECT tbl_goodsreceiving_detail.* FROM tbl_goodsreceiving_detail inner join tblmaterial_coding on tbl_goodsreceiving_detail.itemid=tblmaterial_coding.materialcode inner join tbl_goodsreceiving on tbl_goodsreceiving.receiptnos=tbl_goodsreceiving_detail.receipt_detail_id where tblmaterial_coding.materialcode='$itemid' and tbl_goodsreceiving.trans_typ='purchasefilled' and receipt_id > $transaction_id");

			$this->db->query("update goodrecv set batch_status='0',Batch_stock=quantity");


			
			$totalprofit=0;
			if(!empty($sales)){
				foreach ($sales as $key => $value){
					$totalsaledamt = $value['qty']*$value['sprice'];

					$purchasequery = $this->db->query("SELECT * FROM `goodrecv` where batch_status='0' and itemid='".$value['itemid']."' order by receipt_id asc limit 1")->result_array()[0];

					if($purchasequery['Batch_stock']>$value['qty']){
						$batch_stock_left = $purchasequery['Batch_stock']-$value['qty'];

						$this->db->query("update goodrecv set Batch_stock='$batch_stock_left' where receipt_id='".$purchasequery['receipt_id']."'");

						$totalpurchasedamt = $value['qty']*$purchasequery['rate'];

					}else if($purchasequery['Batch_stock']==$value['qty']){
						$this->db->query("update goodrecv set batch_status='1',Batch_stock='0' where receipt_id='".$purchasequery['receipt_id']."'");

						$totalpurchasedamt = $value['qty']*$purchasequery['rate'];

					}else{
						$halfamt=0;
						$sale_Qty_left = $value['qty']-$purchasequery['Batch_stock'];
						$this->db->query("update goodrecv set batch_status='1',Batch_stock='0' where receipt_id='".$purchasequery['receipt_id']."'");

						$halfamt = $purchasequery['Batch_stock']*$purchasequery['rate'];

						$loop=2;
						while(1<$loop){
								

							$purchasequery = $this->db->query("select * from goodrecv where batch_status='0' and itemid='".$value['itemid']."' and receipt_id > '".$purchasequery['receipt_id']."' order by receipt_id asc limit 1")->result_array()[0];

							if($sale_Qty_left>$purchasequery['Batch_stock']){

								$sale_Qty_left = $sale_Qty_left - $purchasequery['Batch_stock'];

								$this->db->query("update goodrecv set batch_status='1',Batch_stock='0' where receipt_id='".$purchasequery['receipt_id']."'");

								$halfamt = $halfamt + ($purchasequery['Batch_stock']*$purchasequery['rate']);

							}else if($sale_Qty_left==$purchasequery['Batch_stock']){

								$this->db->query("update goodrecv set batch_status='1',Batch_stock='0' where receipt_id='".$purchasequery['receipt_id']."'");

								$halfamt = $halfamt + ($purchasequery['Batch_stock']*$purchasequery['rate']);
								$loop=0;

							}else{
								
								$Batch_stock_left = $purchasequery['Batch_stock'] - $sale_Qty_left;
									$this->db->query("update goodrecv set batch_status='0',Batch_stock='$Batch_stock_left' where receipt_id='".$purchasequery['receipt_id']."'");

								$halfamt = $halfamt + ($sale_Qty_left*$purchasequery['rate']);
								
								$loop=0;
							}

						}

						$totalpurchasedamt = $halfamt;
					}


					$totalprofit = $totalprofit + ($totalsaledamt-$totalpurchasedamt);

				}

				$response = (array("profit"=>$totalprofit,"receipt_id"=>$purchasequery['receipt_id']));
				
			}
			
			
			return $response;
	}




	function getallblncsheetrecords($table,$fromdate){

		$this->db->select('*');
		$this->db->from($table);
		$this->db->join('tbltrans_detail','tblacode.acode = tbltrans_detail.acode');
		$this->db->where("tblacode.isplaccount",1);
		$this->db->where("tbltrans_detail.vdate <",$fromdate);
		$get = $this->db->get();
		return $get->result();
	}



    function stock($mid, $fromdate='',$todate='',$closing='',$location) { 
    	//pm($fromdate);
    	//pm($todate);
       // pm($closing); 
    	
 $login_user=$this->session->userdata('id');
 $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
 if ($sale_point_id=='0') {
 	$sale_point_id=$location;
 }
	if($closing==date('Y-m-d') && $todate==date('Y-m-d')){ 

	 $newdate = strtotime ( '-1 day' , strtotime ( $closing ) ) ;
     $todate = date ('Y-m-d' , $newdate );

	 } 
 

	$new_filled=0;
	$new_empty=0; 
      $where_enter_date = "post_date < '" . $todate . "' AND itemcode = '" . $mid . "'AND sale_point_id = '" . $sale_point_id . "'";

	$last_day_enter=$this->select_orderby('tbl_posting_stock', $where_enter_date,"*", "1", "post_date","Desc");
	

     // if(empty($last_day_enter))
     // { 
		
          $new_filled=$this->mod_common->shop_opening($mid,'Filled',$fromdate,$sale_point_id);
//echo $new_filled; 
         $new_empty=$this->mod_common->shop_opening($mid,'Empty',$fromdate,$sale_point_id);
        //$fromdate_a=$this->select_orderby('tbl_shop_opening', '',"*", "1", "date","Desc");
        $fromdate_a=$this->db->query("SELECT * FROM `tbl_goodsreceiving_detail` where sale_point_id='$sale_point_id' and sub_type='Opening' ORDER BY `recvd_date` DESC LIMIT 1")->row_array();

 		 $fromdate=$fromdate_a['recvd_date'];  
		 $fromdate = strtotime($fromdate)-86400;
		  $fromdate = date("Y-m-d",($fromdate)); 
		   //echo $fromdate;exit();
    //  }
    //  else
		
    // { 
	  	//    $new_filled=$last_day_enter['closing_filled']; 
		  // // echo $new_filled;exit;
    //      $new_empty=$last_day_enter['closing_empty'];
		  //   $fromdate=$last_day_enter['post_date'];
			
    // }
	 
	 $itemid=$mid;
	   $fromdate;

	

  // echo  $fromdate; echo "<br>"; echo  $todate; echo "<br>";
  
           $sql="SELECT * from `tblmaterial_coding` WHERE materialcode=$itemid";

       
        $query = $this->db->query($sql);
         
        if($query->num_rows()>0){
            foreach($query->result_array() as $key => $value) {
              
                $catcode = $value['catcode'];

          
		}} 

		    //////////////////////////////GET LOCATION//////////////////////////////////////
            // $login_user=$this->session->userdata('id');
            // $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			////////////////////////////// PURCHASE Filled /////////////////////////////////
	        $query = "  select COALESCE(SUM(tbl_goodsreceiving_detail.quantity),0) as recv_from_vendor_f
			from tbl_goodsreceiving_detail, tbl_goodsreceiving
			where  tbl_goodsreceiving_detail.receipt_detail_id=tbl_goodsreceiving.receiptnos
			and tbl_goodsreceiving_detail.itemid='$itemid'
			and tbl_goodsreceiving.trans_typ in('purchasefilled','salereturn')
			and tbl_goodsreceiving.receiptdate > '$fromdate' and tbl_goodsreceiving.receiptdate < '$todate' and tbl_goodsreceiving.sale_point_id='$sale_point_id' and tbl_goodsreceiving_detail.type='Filled'";
			
			$result = $this->db->query($query);
			$recv_from_vendor_f_row = $result->row_array();

				
			////////////////////////////// PURCHASE Empty /////////////////////////////////
		 	$query = "select COALESCE(SUM(tbl_goodsreceiving_detail.quantity),0) as recv_from_vendor_e
			from tbl_goodsreceiving_detail, tbl_goodsreceiving
			where  tbl_goodsreceiving_detail.receipt_detail_id=tbl_goodsreceiving.receiptnos
			and tbl_goodsreceiving_detail.itemid='$itemid'
			and tbl_goodsreceiving.trans_typ in('purchaseempty','salereturn') and tbl_goodsreceiving.receiptdate > '$fromdate' and tbl_goodsreceiving.receiptdate < '$todate' and tbl_goodsreceiving.sale_point_id='$sale_point_id' and tbl_goodsreceiving_detail.type='Empty'";
		
			$result = $this->db->query($query);
			$recv_from_vendor_e_row = $result->row_array();

			//pm($recv_from_vendor_e_row);exit();

				
				
			////////////////////////////// RECV FROM CUSTOMER FILLED /////////////////////////////////
			$recv_from_customer_f=0;
			
			
			
			////////////////////////////// RECV FROM CUSTOMER EMPTY /////////////////////////////////
		 	  $query = "  select COALESCE(SUM(tbl_issue_goods_detail.returns),0) as recv_from_customer_e
			from tbl_issue_goods,tbl_issue_goods_detail where 
			tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id 
			and tbl_issue_goods_detail.item_return='$itemid' and tbl_issue_goods.issuedate
			> '$fromdate'  and tbl_issue_goods.issuedate < '$todate'
			and tbl_issue_goods.decanting!='Yes' and tbl_issue_goods.sale_point_id='$sale_point_id'";
			$result = $this->db->query($query);
			$recv_from_customer_e_row = $result->row_array();

				 

			
				
				
				
			if($catcode==1){
			////////////////////////////// OUT to vendor filled /////////////////////////////////
			$query = " select COALESCE(SUM(tbl_issue_return_detail.qty),0) as out_to_vendor_f
			from tbl_issue_return_detail, tbl_issue_return
			where  tbl_issue_return_detail.irnos=tbl_issue_return.irnos
			and tbl_issue_return_detail.itemid='$itemid'
			and tbl_issue_return_detail.type='Filled'
			and tbl_issue_return.type='purchasereturn' and  tbl_issue_return.irdate  > '$fromdate' and  tbl_issue_return.irdate < '$todate' and tbl_issue_return.sale_point_id='$sale_point_id' ";
			$result = $this->db->query($query);
			$out_to_vendor_f_row = $result->row_array();
				}else{
					//$fromdate='2018-05-27';
					//echo  $fromdate; echo "<br>"; echo  $todate; echo "<br>";
					
					//echo $new_filled;exit;
			
			////////////////////////////// OUT to vendor filled /////////////////////////////////
			  $query = " select COALESCE(SUM(tbl_issue_return_detail.qty),0) as out_to_vendor_f
			from tbl_issue_return_detail, tbl_issue_return
			where  tbl_issue_return_detail.irnos=tbl_issue_return.irnos
			and tbl_issue_return_detail.itemid='$itemid'
			and tbl_issue_return_detail.type='Filled'
			and tbl_issue_return.type='purchasereturnother' and  tbl_issue_return.irdate >
			'$fromdate' and tbl_issue_return.irdate  <   '$todate' and tbl_issue_return.sale_point_id='$sale_point_id' ";
			$result = $this->db->query($query);
			$out_to_vendor_f_row = $result->row_array();
			}
			 
			
			////////////////////////////// OUT to vendor empty /////////////////////////////////
		  	$query = "  select (select COALESCE(SUM(tbl_goodsreceiving_detail.ereturn),0) 
			from tbl_goodsreceiving_detail,tbl_goodsreceiving
			where  tbl_goodsreceiving_detail.receipt_detail_id=tbl_goodsreceiving.receiptnos
			and tbl_goodsreceiving_detail.itemid='$itemid'
			and tbl_goodsreceiving.trans_typ='purchasefilled' and tbl_goodsreceiving.receiptdate > '$fromdate' and tbl_goodsreceiving.receiptdate < '$todate' and tbl_goodsreceiving.sale_point_id='$sale_point_id'
			)
			+
			(
			select COALESCE(SUM(tbl_issue_return_detail.qty),0) as out_to_vendor_f
			from tbl_issue_return_detail, tbl_issue_return
			where  tbl_issue_return_detail.irnos=tbl_issue_return.irnos
			and tbl_issue_return_detail.itemid='$itemid'
			and tbl_issue_return_detail.type='Empty'
			and tbl_issue_return.type='purchasereturn' and  tbl_issue_return.irdate  > '$fromdate' and  tbl_issue_return.irdate < '$todate' and tbl_issue_return.sale_point_id='$sale_point_id'
			)
			as out_to_vendor_e ";
	 
			$result = $this->db->query($query);
			$out_to_vendor_e_row = $result->row_array();


				
						
			////////////////////////////// SALE EMPTY /////////////////////////////////
			$query = "  select COALESCE(SUM(tbl_issue_goods_detail.qty),0) as sale_out_f
			from tbl_issue_goods,tbl_issue_goods_detail where 
			tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id 
			and tbl_issue_goods_detail.itemid='$itemid' and tbl_issue_goods.type='Empty' and tbl_issue_goods.issuedate > '$fromdate' and tbl_issue_goods.issuedate < '$todate'
			and tbl_issue_goods.decanting!='Yes' and tbl_issue_goods.sale_point_id='$sale_point_id'";
			$result = $this->db->query($query);
			$sale_out_e_row = $result->row_array();

			
			
			
			
			////////////////////////////// SALE FILLED /////////////////////////////////
		 	 $query = "  select COALESCE(SUM(tbl_issue_goods_detail.qty),0) as sale_out_f
			from tbl_issue_goods,tbl_issue_goods_detail where 
			tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id 
			and tbl_issue_goods_detail.itemid='$itemid' and tbl_issue_goods.type='Fill' and tbl_issue_goods.issuedate > '$fromdate' and tbl_issue_goods.issuedate < '$todate'
			and tbl_issue_goods.decanting!='Yes' and tbl_issue_goods.sale_point_id='$sale_point_id' ";
			$result = $this->db->query($query);
			$sale_out_f_row = $result->row_array(); 

				
				
			
			////////////////////////////// SALE RETURN FILLED /////////////////////////////////
			$query = " select COALESCE(SUM(tbl_issue_return_detail.qty),0) as sale_return_f
			from tbl_issue_return_detail, tbl_issue_return
			where  tbl_issue_return_detail.irnos=tbl_issue_return.irnos
			and tbl_issue_return_detail.itemid='$itemid'
			and tbl_issue_return_detail.type='Filled'
			and tbl_issue_return.type='salereturn' and  tbl_issue_return.irdate  > '$fromdate' AND tbl_issue_return.irdate < '$todate' and tbl_issue_return.sale_point_id='$sale_point_id' ";
			$result = $this->db->query($query);
			$sale_return_f_row = $result->row_array();

				  
			
			////////////////////////////// SALE RETURN EMPTY /////////////////////////////////
			$query = " select COALESCE(SUM(tbl_issue_return_detail.qty),0) as sale_return_e
			from tbl_issue_return_detail, tbl_issue_return
			where  tbl_issue_return_detail.irnos=tbl_issue_return.irnos
			and tbl_issue_return_detail.itemid='$itemid'
			and tbl_issue_return_detail.type='Empty'
			and tbl_issue_return.type='salereturn' and  tbl_issue_return.irdate  > '$fromdate' AND tbl_issue_return.irdate <'$todate' and tbl_issue_return.sale_point_id='$sale_point_id'";
			$result = $this->db->query($query);
			$sale_return_e_row = $result->row_array();

				  
                  

			
			////////////////////////////// DECANT SALE EMPTY /////////////////////////////////
			$decant_sale_e=0;
			
			
			
			////////////////////////////// DECANT SALE FILLED /////////////////////////////////
			$query = "  select COALESCE(SUM(tbl_issue_goods_detail.qty),0) as decant_sale_f
			from tbl_issue_goods,tbl_issue_goods_detail where 
			tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id 
			and tbl_issue_goods_detail.itemid='$itemid' and tbl_issue_goods.issuedate  > '$fromdate' AND tbl_issue_goods.issuedate < '$todate'
			and tbl_issue_goods.decanting='Yes' and tbl_issue_goods.sale_point_id='$sale_point_id'";
			$result = $this->db->query($query);
			$decant_sale_f_row = $result->row_array();				  
                   
            



			////////////////////////////// DECANT RECV FILLED /////////////////////////////////
			$decant_empty_f=0;
			
			
			
			////////////////////////////// RECV FROM CUSTOMER EMPTY /////////////////////////////////
			$query = "  select COALESCE(SUM(tbl_issue_goods_detail.returns),0) as decant_empty_e
			from tbl_issue_goods,tbl_issue_goods_detail where 
			tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id 
			and tbl_issue_goods_detail.itemid='$itemid' and tbl_issue_goods.issuedate  > '$fromdate' AND   tbl_issue_goods.issuedate < '$todate'
			and tbl_issue_goods.decanting='Yes' and tbl_issue_goods.sale_point_id='$sale_point_id'";
			$result = $this->db->query($query);
			$decant_empty_e_row = $result->row_array();

 
			
			////////////////////////////// CYLINDER CONVERSTION From /////////////////////////////////

			$query = " select COALESCE(SUM(tbl_cylinderconversion_detail.qty),0) as convert_from_f
			from tbl_cylinderconversion_detail,tbl_cylinderconversion_master where 
			tbl_cylinderconversion_master.trans_id=tbl_cylinderconversion_detail.trans_id 
			and tbl_cylinderconversion_detail.itemcode='$itemid' and tbl_cylinderconversion_master.trans_date > '$fromdate' AND tbl_cylinderconversion_master.trans_date < '$todate'
			and tbl_cylinderconversion_detail.`type`='from' and  tbl_cylinderconversion_detail.sale_point_id='$sale_point_id'";
			$result = $this->db->query($query);
			$convert_from_f_row = $result->row_array();
            $convert_to_e=$convert_from_f_row['convert_from_f'];
			
			
			////////////////////////////// CYLINDER CONVERSTION to /////////////////////////////////
			$query = " select COALESCE(SUM(tbl_cylinderconversion_detail.qty),0) as convert_to_f
			from tbl_cylinderconversion_detail,tbl_cylinderconversion_master where 
			tbl_cylinderconversion_master.trans_id=tbl_cylinderconversion_detail.trans_id 
			and tbl_cylinderconversion_detail.itemcode='$itemid' and tbl_cylinderconversion_master.trans_date > '$fromdate' AND  tbl_cylinderconversion_master.trans_date < '$todate'
			and tbl_cylinderconversion_detail.`type`='to' and  tbl_cylinderconversion_detail.sale_point_id='$sale_point_id'";
			$result = $this->db->query($query);
			$convert_to_f_row = $result->row_array();
            $convert_from_e=$convert_to_f_row['convert_to_f'];



            ////////////////////////////// Filled CYLINDER Fresh to damage /////////////////////////////////
            $query = "SELECT sum(qty) as damagecylinder_f from tbl_exchange_condition where from_itemcode='$itemid' and cyl_condition_to='Damage' and cyl_type='Filled' and dt > '$fromdate' AND dt < '$todate'";
			$result = $this->db->query($query);
			$convert_to_f_row1 = $result->row_array();
            $damagecylinder_f = $convert_to_f_row1['damagecylinder_f'];

             ////////////////////////////// Filled CYLINDER Damage to Fresh /////////////////////////////////
            $query = "SELECT sum(qty) as freshcylinder_f from tbl_exchange_condition where from_itemcode='$itemid' and cyl_condition_to='Fresh' and cyl_type='Filled'  and dt > '$fromdate' AND dt < '$todate'";
			$result = $this->db->query($query);
			$convert_to_f_row2 = $result->row_array();
            $freshcylinder_f = $convert_to_f_row2['freshcylinder_f'];


            ////////////////////////////// Empty CYLINDER Fresh to damage /////////////////////////////////
            $query = "SELECT sum(qty) as damagecylinder_e from tbl_exchange_condition where from_itemcode='$itemid' and cyl_condition_to='Damage' and cyl_type='Empty' and dt > '$fromdate' AND dt < '$todate'";
			$result = $this->db->query($query);
			$convert_to_f_row3 = $result->row_array();
            $damagecylinder_e = $convert_to_f_row3['damagecylinder_e'];

             ////////////////////////////// Empty CYLINDER Damage to Fresh /////////////////////////////////
            $query = "SELECT sum(qty) as freshcylinder_e from tbl_exchange_condition where from_itemcode='$itemid' and cyl_condition_to='Fresh' and cyl_type='Empty'  and dt > '$fromdate' AND dt < '$todate'";
			$result = $this->db->query($query);
			$convert_to_f_row4 = $result->row_array();
            $freshcylinder_e = $convert_to_f_row4['freshcylinder_e'];


            ////////////////////////////// sale damage cylinder  /////////////////////////////////
   //          $query = "select sum(qty) as saledamagecylinder from tbl_issue_goods_detail where itemid='$itemid' and salestatus='Damage'";
   //          $result = $this->db->query($query);
			// $saledamagecylinderquery = $result->row_array();
   //          $damagecylindersale = $saledamagecylinderquery['saledamagecylinder'];	




             $query = "select COALESCE(SUM(tbl_issue_goods_detail.qty),0) as saledamagecylinder
			from tbl_issue_goods,tbl_issue_goods_detail where 
			tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id 
			and tbl_issue_goods_detail.itemid='$itemid' and tbl_issue_goods.type='Empty' and tbl_issue_goods.issuedate > '$fromdate' and tbl_issue_goods.issuedate < '$todate'
			and tbl_issue_goods.decanting!='Yes' and tbl_issue_goods_detail.salestatus = 'Damage' and tbl_issue_goods_detail.sale_point_id='$sale_point_id'";

			$result = $this->db->query($query);
			$saledamagecylinderquery = $result->row_array();
            $damagecylindersale = $saledamagecylinderquery['saledamagecylinder'];


			
            
			 $filled_final=$new_filled+$recv_from_vendor_f_row['recv_from_vendor_f']+$recv_from_customer_f-$out_to_vendor_f_row['out_to_vendor_f']
			-$sale_out_f_row['sale_out_f']+$sale_return_f_row['sale_return_f']-$decant_sale_f_row['decant_sale_f']
			+$decant_empty_f+$convert_to_f_row['convert_to_f']-$convert_from_f_row['convert_from_f']+ $freshcylinder_f - $damagecylinder_f;
			//echo $filled_final;exit;
			//echo $recv_from_vendor_f_row['recv_from_vendor_f']; echo "<br>";


			$empty_final=$new_empty+$recv_from_vendor_e_row['recv_from_vendor_e']+$recv_from_customer_e_row['recv_from_customer_e']-$out_to_vendor_e_row['out_to_vendor_e']
			-$sale_out_e_row['sale_out_f']+$sale_return_e_row['sale_return_e']-$decant_sale_e
			+$decant_empty_e_row['decant_empty_e']+$convert_to_e-$convert_from_e - $damagecylinder_e + $freshcylinder_e-$damagecylindersale; 


		 
		  
        
			$price=0;
			$query = "  select  saleprice from tblmaterial_coding where materialcode='$itemid' ";
			$result = $this->db->query($query);
			$saleprice = $result->row_array();
			$price=$saleprice['saleprice'];
				//var_dump($filled_final.'_'.$empty_final.'_'.$price);
				
    return $filled_final.'_'.$empty_final.'_'.$price; 

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

        // pm($data);exit();


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
            $sql="SELECT * from `tblmaterial_coding` $category_id WHERE materialcode IN(SELECT itemid FROM tbl_issue_goods as main_table INNER JOIN tbl_issue_goods_detail as detail_new ON main_table.issuenos=detail_new.ig_detail_id WHERE `issuedate`<= '$fdate') and catcode='1' $where";
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

    function other_stock($mid, $fromdate='',$todate='',$closing='') { 

	if($closing==date('Y-m-d') && $todate==date('Y-m-d')){ 

	 $newdate = strtotime ( '-1 day' , strtotime ( $closing ) ) ;
     $todate = date ('Y-m-d' , $newdate );

	 } 
 

	$new_filled=0;
	$new_empty=0; 
      $where_enter_date = "post_date < '" . $todate . "' AND itemcode = '" . $mid . "'";

	$last_day_enter=$this->select_orderby('tbl_posting_stock', $where_enter_date,"*", "1", "post_date","Desc");
	//pm($last_day_enter);

     if(empty($last_day_enter))
     { 
		
          $new_filled=$this->mod_common->shop_opening($mid,'Other',$fromdate);
//echo $new_filled;exit;		  
         $new_empty=$this->mod_common->shop_opening($mid,'Empty',$fromdate);
       // $fromdate_a=$this->select_orderby('tbl_shop_opening', '',"*", "1", "date","Desc");
         // $fromdate_a=$this->db->query("SELECT * FROM `tbl_shop_opening` where location='$sale_point_id' ORDER BY `date` DESC LIMIT 1")->row_array();
         $fromdate_a=$this->db->query("SELECT * FROM `tbl_goodsreceiving_detail` where sale_point_id='$sale_point_id' and sub_type='Opening' ORDER BY `recvd_date` DESC LIMIT 1")->row_array();

 		 $fromdate=$fromdate_a['recvd_date'];  
		 $fromdate = strtotime($fromdate)-86400;
		  $fromdate = date("Y-m-d",($fromdate)); 
     }
     else
		
    { 
	  	   $new_filled=$last_day_enter['closing_filled']; 
		  // echo $new_filled;exit;
         $new_empty=$last_day_enter['closing_empty'];
		    $fromdate=$last_day_enter['post_date'];
			
    }
	 
	 $itemid=$mid;
	   $fromdate;

	

 // echo  $todate; echo "<br>";
  
           $sql="SELECT * from `tblmaterial_coding` WHERE materialcode=$itemid";

       
        $query = $this->db->query($sql);
         
        if($query->num_rows()>0){
            foreach($query->result_array() as $key => $value) {
              
                $catcode = $value['catcode'];

          
		}} 
  
             //////////////////////////////GET LOCATION//////////////////////////////////////
            $login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			////////////////////////////// PURCHASE Filled /////////////////////////////////
	        $query = "  select COALESCE(SUM(tbl_goodsreceiving_detail.quantity),0) as recv_from_vendor_f
			from tbl_goodsreceiving_detail, tbl_goodsreceiving
			where  tbl_goodsreceiving_detail.receipt_detail_id=tbl_goodsreceiving.receiptnos
			and tbl_goodsreceiving_detail.itemid='$itemid'
			and tbl_goodsreceiving.trans_typ in('purchaseother')
			 and tbl_goodsreceiving.receiptdate < '$todate' and tbl_goodsreceiving.sale_point_id='$sale_point_id'";
			$result = $this->db->query($query);
			$recv_from_vendor_f_row = $result->row_array();

			

			
			
			////////////////////////////// SALE FILLED /////////////////////////////////
		 	 $query = "  select COALESCE(SUM(tbl_issue_goods_detail.qty),0) as sale_out_f
			from tbl_issue_goods,tbl_issue_goods_detail where 
			tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id 
			and tbl_issue_goods_detail.itemid='$itemid' and tbl_issue_goods.type='Fill'  and tbl_issue_goods.issuedate < '$todate'
			and tbl_issue_goods.decanting!='Yes' and tbl_issue_goods.sale_point_id='$sale_point_id'";
			$result = $this->db->query($query);
			$sale_out_f_row = $result->row_array(); 

				
		
			 $filled_final=$new_filled+$recv_from_vendor_f_row['recv_from_vendor_f']-$sale_out_f_row['sale_out_f'];
			
        
			$price=0;
			$query = "  select  saleprice from tblmaterial_coding where materialcode='$itemid' ";
			$result = $this->db->query($query);
			$saleprice = $result->row_array();
			$price=$saleprice['saleprice'];
				//var_dump($filled_final.'_'.$empty_final.'_'.$price);
				
    return $filled_final.'_'.$empty_final.'_'.$price; 

    }
     function other_cylinder_stock($mid, $fromdate='',$todate='',$closing='') { 

	if($closing==date('Y-m-d') && $todate==date('Y-m-d')){ 

	 $newdate = strtotime ( '-1 day' , strtotime ( $closing ) ) ;
     $todate = date ('Y-m-d' , $newdate );

	 } 
 

	$new_filled=0;
	$new_empty=0; 
      $where_enter_date = "post_date < '" . $todate . "' AND itemcode = '" . $mid . "'";

	$last_day_enter=$this->select_orderby('tbl_posting_stock', $where_enter_date,"*", "1", "post_date","Desc");
	//pm($last_day_enter);

     if(empty($last_day_enter))
     { 
		
          $new_filled=$this->mod_common->shop_opening($mid,'Other',$fromdate);
//echo $new_filled;exit;		  
         $new_empty=$this->mod_common->shop_opening($mid,'Empty',$fromdate);
        $fromdate_a=$this->select_orderby('tbl_shop_opening', '',"*", "1", "date","Desc");
 		 $fromdate=$fromdate_a['date'];  
		 $fromdate = strtotime($fromdate)-86400;
		  $fromdate = date("Y-m-d",($fromdate)); 
     }
     else
		
    { 
	  	   $new_filled=$last_day_enter['closing_filled']; 
		  // echo $new_filled;exit;
         $new_empty=$last_day_enter['closing_empty'];
		    $fromdate=$last_day_enter['post_date'];
			
    }
	 
	 $itemid=$mid;
	   $fromdate;

	

 // echo  $todate; echo "<br>";
  
           $sql="SELECT * from `tblmaterial_coding` WHERE materialcode=$itemid";

       
        $query = $this->db->query($sql);
         
        if($query->num_rows()>0){
            foreach($query->result_array() as $key => $value) {
              
                $catcode = $value['catcode'];

          
		}} 
  
             //////////////////////////////GET LOCATION//////////////////////////////////////
            $login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			////////////////////////////// PURCHASE Filled /////////////////////////////////
	        $query = "  select COALESCE(SUM(tbl_swap_recv_detail.qty),0) as recv_from_vendor_f
			from tbl_swap_recv_detail, tbl_swap_recv
			where  tbl_swap_recv_detail.irnos=tbl_swap_recv.irnos
			and tbl_swap_recv_detail.itemid='$itemid'
			and tbl_swap_recv.type in('SCR')
			 and tbl_swap_recv.irdate < '$todate' and tbl_swap_recv.sale_point_id='$sale_point_id'";
			$result = $this->db->query($query);
			$recv_from_vendor_f_row = $result->row_array();

			

			
			
			////////////////////////////// SALE FILLED /////////////////////////////////
		 	 $query = "  select COALESCE(SUM(tbl_swap_recv_detail.qty),0) as sale_out_f
			from tbl_swap_recv,tbl_swap_recv_detail where 
			tbl_swap_recv.irnos=tbl_swap_recv_detail.irnos 
			and tbl_swap_recv_detail.itemid='$itemid' and tbl_swap_recv.type='SCS'  and tbl_swap_recv.irdate < '$todate' and tbl_swap_recv.sale_point_id='$sale_point_id'";
			$result = $this->db->query($query);
			$sale_out_f_row = $result->row_array(); 

				
		
			 $filled_final=$new_filled+$recv_from_vendor_f_row['recv_from_vendor_f']-$sale_out_f_row['sale_out_f'];
			
        
			$price=0;
			$query = "  select  saleprice from tblmaterial_coding where materialcode='$itemid' ";
			$result = $this->db->query($query);
			$saleprice = $result->row_array();
			$price=$saleprice['saleprice'];
				//var_dump($filled_final.'_'.$empty_final.'_'.$price);
				
    return $filled_final; 

    }	
}

?>