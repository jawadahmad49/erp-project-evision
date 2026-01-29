<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProfitReport extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_profitreport","mod_common"
        ));
        
    }

	public function index()
	{
		$table='tbltrans_detail';
		$data['stock_report_list'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Profit Loss Report";	
		$table='tblcategory';       
        $data['category_list'] = $this->mod_common->get_all_records($table,"*");
        $this->load->view($this->session->userdata('language')."/profit_report/search",$data);       	
	}


	public function detail_report()
	{							
		    $month = $_POST["month"];
		   $year = $_POST["year"]; 

		
		if($year == date('Y')){
			

if($month == "June"){
	$mnthno = "06";
	$first_date = date('Y-06-01');
	$last_date = date('Y-06-t');
}else{

	$first_date = date('Y-m-01', strtotime($month));
			 $last_date = date('Y-m-t', strtotime($month));

} 
	
	
 		
		//pm($this->input->post());
		$tbltankquery = $this->db->query("select tbl_shop_opening.*,tblmaterial_coding.itemnameint from tbl_shop_opening  inner join tblmaterial_coding on tblmaterial_coding.materialcode = tbl_shop_opening.materialcode where tbl_shop_opening.type ='Filled'");
		//pm($tbltankquery->result_array());
		foreach ($tbltankquery->result_array() as $key2 => $value2) {
			$totaltnkqtyy +=  (($value2['itemnameint'] * $value2['qty']))/1000;
			if($value2['cost_price'] != ""){
				$tankvaluee =  $value2['cost_price'];
			}
			
		}


		
		$totalamountshopopening = $tankvaluee * $totaltnkqtyy;


		
			  
		}elseif($year==date('Y')+1){
			$first_date = date('Y-m-01', strtotime("$month, +1 year"));
			$last_date = date('Y-m-t', strtotime("$month, +1 year"));
		}
	
		$productdetails = $this->get_detailsssssss($first_date,$last_date);

		$newwvalue = 0;
		foreach($productdetails as $key => $Productdetails){ //echo "<pre>";var_dump($Productdetails);
			  $qtyy = explode("-",$Productdetails['itemid']);
			 
			$totallpurchasee += $qtyy[0] * $Productdetails['recv_from_vendor_f'];

			//function to calculate purchase value starts here(waqas)

			$querynew = $this->db->query("SELECT * FROM `tbl_goodsreceiving` inner join tbl_goodsreceiving_detail on receiptnos= receipt_detail_id where receiptdate BETWEEN '".$Productdetails['fromdate']."' and '".$Productdetails['todate']."' and trans_typ='purchasefilled' and itemid='".$Productdetails['materialcode']."'");
			//echo "<pre>"; var_dump(expression)
			foreach ($querynew->result_array() as $key1 => $value2) {
				 $totallpurchaseamount += $value2['inc_vat_amount']; 
			}
			


			//function to calculate purchase value starts here(waqas)


			//waqas wriiten function starts here
			$newitemid = $Productdetails['materialcode'];

				$query = " select COALESCE(SUM(tbl_issue_return_detail.qty),0) as sale_return_f
			from tbl_issue_return_detail, tbl_issue_return
			where  tbl_issue_return_detail.irnos=tbl_issue_return.irnos
			and tbl_issue_return_detail.itemid='$newitemid'
			and tbl_issue_return_detail.type='Filled'
			and tbl_issue_return.type='salereturn' and  tbl_issue_return.irdate  >= '$first_date' AND tbl_issue_return.irdate <= '$last_date' ";
			$result = $this->db->query($query);
			 

			 foreach ($result->row_array() as $newwvalue) { //echo "<br>";var_dump($newwvalue);
			 	
			 	  if($newwvalue == 0){ 
			 		
				 	}else{ 
				 		
				 		$salenewvalue += $newwvalue * $qtyy[0];
				 	}

			 }
		
			
			 	

		} //pm($newvalue);
		$data['totallpurchaseamount'] = $totallpurchaseamount;
		    $data['salereturnnew'] = $salenewvalue/1000; 
		  $data['totallpurchasee'] = $totallpurchasee/1000; 

				$table='tbl_company';       
				$data['company'] = $this->mod_common->get_all_records($table,"*");

				$data["title"] = "Profit Loss for Period";
		 		$data['from_date'] = $first_date;
				$data['to_date'] = $last_date;
				$data["title"] = "Profit Loss for Period";
				$data['c_month'] = $month;
		
				$data['c_year'] = $year;


				$table='tbl_company';       
				$data['company'] = $this->mod_common->get_all_records($table,"*");

				//$from_date=$data['from_date'];
				//$to_date=$data['to_date'];
				$from_date = $first_date;
				$to_date = $last_date;
		
				$data_posted = array('from_date' => $from_date, 'to_date' => $to_date);
				$data['daterange'] =  $from_date.' to '.$to_date;
				$new_date['from_date']=$from_date;
				$new_date['to_date']=$to_date;
				//$data['one_date_report'] = $this->mod_profitreport->getdate_stock_report($new_date,2);
				$date_for_item['to_date']=$to_date;
				$data['report_type'] = 2;

				$data['sale']=  $this->mod_profitreport->getsales($data_posted,2);
				
				

				$where_cat_id =''; // array('catcode=' => 1);
				$data['itemname']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_cat_id);
				//$tables='tblmaterial_coding';       
				//$data['itemname'] = $this->mod_common->get_all_records($tables,"*");
				//$data['itemname_return'] = $this->mod_common->get_all_records($tables,"*");
				//////////////////////////////////    SALES //////////////////////////////////////////////////////////////////////////////////////////
				//////////////////////////////////    SALES //////////////////////////////////////////////////////////////////////////////////////////
		 
		 
				//////////////////////////////////    SALES RETURN //////////////////////////////////////////////////////////////////////////////////////////
				$data['sale_return']=  $this->mod_profitreport->getsales_return($data_posted,2);
				//pm($data['sale_return']);



				//////////////////////////////////    PURCHASES //////////////////////////////////////////////////////////////////////////////////////////
				
				
				///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//////////////////////////////////    PURCHASES //////////////////////////////////////////////////////////////////////////////////////////
				//pm($data_posted);
				
				$data['purchases']=  $this->mod_profitreport->getpurchases($data_posted,2);

				//pm($data['purchases']);

				$j=0; 
				foreach ($data['purchases'] as $key => $value) {
					$purchases_total_tonnage = 0;
					
					foreach ($value['purchases'] as $key => $value_sub) {

						if($value_sub['catcode']=='1'){
							
						  $purchases_total_tonnage+=($value_sub['quantity']*$value_sub['itemnameint'])/1000;

							
						}
						
					}

					 if($value['purchases'][0]['catcode'] == 1){
						
							  $pertonpriceee = $value['total_amount']/$purchases_total_tonnage; 
							  $finalpertonprice += ($pertonpriceee); 

					}


					if($value_sub['catcode']=='1'){
						$j++;
					}
				}

				
				  $data['onlypurchaserate'] = $finalpertonprice / $j;		




				 
				   $last_mnth = date("Y-m-d",strtotime("-1 Month", strtotime($from_date)));
				   $last_mnth = date("Y-m-d",strtotime("-1 day", strtotime($last_mnth)));
				 
				 $new_postedddd_d = array('from_date' => '2019-01-01', 'to_date' => $last_mnth);

	


					$pre_of_last_month = date("Y-m-d",strtotime("-1 day", strtotime($from_date)));

					 $data_postedddd = array('from_date' => '2019-01-01', 'to_date' => $pre_of_last_month);

					 $data['purchaseopeinigrate'] = $this->getpurchaseratess($data_postedddd,$tankvaluee);

				

					 	 $data_postedddd = array('from_date' => '2019-01-01', 'to_date' => $to_date);
						 $data['netpurchaserate'] = $this->getpurchaseratess($data_postedddd,$tankvaluee);
						
						//pm( $data['netpurchaserate']);


				

				
				

				
				
				
				//////////////////////////////////    PURCHASES RETURN //////////////////////////////////////////////////////////////////////////////////////////
				$data['purchases_return']=  $this->mod_profitreport->getpurchases_return($data_posted,2);

				//////////////////////////////////    PURCHASES RETURN //////////////////////////////////////////////////////////////////////////////////////////

				
				
				//////////////////////////////////    PURCHASES RETURN //////////////////////////////////////////////////////////////////////////////////////////
				$data['payments1']=  $this->mod_profitreport->getpayments_new_old($data_posted,2);
				//pm($data['payments1']);
				//////////////////////////////////    PURCHASES RETURN //////////////////////////////////////////////////////////////////////////////////////////
			
				//////////////////////////////////    PURCHASES RETURN //////////////////////////////////////////////////////////////////////////////////////////

				$data['get_income']=  $this->mod_profitreport->get_income($data_posted,2);

				//////////////////////////////////    PURCHASES RETURN //////////////////////////////////////////////////////////////////////////////////////////
			
				//////////////////////////////////    receipts RETURN //////////////////////////////////////////////////////////////////////////////////////////
				$data['receipts']=  $this->mod_profitreport->getreceipts($data_posted,2);
				//////////////////////////////////    receipts RETURN //////////////////////////////////////////////////////////////////////////////////////////
		// pm($data['payments']);




$nxtdate = date("Y-m-d", strtotime("+1 month", strtotime($year."-".$month."-"."01")));
 $nxtyear = date("Y",strtotime($nxtdate)); 
$nxtmnth = date("m",strtotime($nxtdate)); 


$dateObj   = DateTime::createFromFormat('!m', $nxtmnth);
  $monthName = $dateObj->format('F'); // March


//code to get opening balace from tbl_shop_opening starts from here
  $qty = 0;
  $total_cost_price = 0;


				  $data['opening_stock'] =  $this->opening_stock($month,$year); 
				 //$data['closing_stocks1'] = $this->opening_stock($monthName,$nxtyear);


				//$data['opening_stock'] = $this->opening_stock($month,$year);
				//pm($data['opening_stock']);

			//$this->load->view($this->session->userdata('language')."/profit_report/newdetailreport",$data);
			$this->load->view($this->session->userdata('language')."/profit_report/detail_report",$data);
	        

	         
	}



	public function getpurchaseratess($data_postedddd,$tankvalue){

		$data['all_purchases']=  $this->mod_profitreport->getpurchases($data_postedddd,2);
		//pm($data['all_purchases']);		

				$i=0; 
				$finalpertonprice = 0;
				foreach ($data['all_purchases'] as $key => $value) {
					$purchases_total_tonnage = 0;
					
					foreach ($value['purchases'] as $key => $value_sub) {

						if($value_sub['catcode']=='1'){
							
						  $purchases_total_tonnage+=($value_sub['quantity']*$value_sub['itemnameint'])/1000;

							
						}
						
					}

					 if($value['purchases'][0]['catcode'] == 1){

							$pertonpriceee = 0;
							$pertonpriceee = $value['total_amount']/$purchases_total_tonnage; 
							$finalpertonprice += ($pertonpriceee); 

					}


					if($value_sub['catcode']=='1'){
						$i++;
					}
				}


				$p_rate = $finalpertonprice / $i;
				if(empty($p_rate)){
					return $p_rate = $tankvalue;
				}else{

					if(empty($tankvalue)){
						return $p_rate = $p_rate;
					}else{
						return $p_rate = ($p_rate + $tankvalue)/2;
					}
					
				}

				
 				//$data['purchaseopeinigrate'] = $p_rate;

	}


	public function getpurchaseratess_old($data_postedddd){

		$data['all_purchases']=  $this->mod_profitreport->getpurchases($data_postedddd,2);
				

				$i=0; 
				$finalpertonprice = 0;
				foreach ($data['all_purchases'] as $key => $value) {
					$purchases_total_tonnage = 0;
					
					foreach ($value['purchases'] as $key => $value_sub) {

						if($value_sub['catcode']=='1'){
							
						  $purchases_total_tonnage+=($value_sub['quantity']*$value_sub['itemnameint'])/1000;

							
						}
						
					}

					 if($value['purchases'][0]['catcode'] == 1){

							$pertonpriceee = 0;
							$pertonpriceee = $value['total_amount']/$purchases_total_tonnage; 
							$finalpertonprice += ($pertonpriceee); 

					}


					if($value_sub['catcode']=='1'){
						$i++;
					}
				}


				return $p_rate = $finalpertonprice / $i;
				//$data['purchaseopeinigrate'] = $p_rate;

	}



	public function get_detailsssssss($from_date,$to_date){
        
            $fromdate=$from_date;
            $todate=$to_date;
        

         $category_id=1;   

       
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
			and tbl_goodsreceiving.trans_typ in('purchasefilled','purchaseother') 
			and tbl_goodsreceiving.receiptdate    >= '$fromdate' and tbl_goodsreceiving.receiptdate <=    '$todate'";
			$result = $this->db->query($query);
			$recv_from_vendor_f_row = $result->row_array();

			 
			
				
			////////////////////////////// PURCHASE Empty /////////////////////////////////
		 	$query = "  select COALESCE(SUM(tbl_goodsreceiving_detail.quantity),0) as recv_from_vendor_e
			from tbl_goodsreceiving_detail, tbl_goodsreceiving
			where  tbl_goodsreceiving_detail.receipt_detail_id=tbl_goodsreceiving.receiptnos
			and tbl_goodsreceiving_detail.itemid='$itemid'
			and tbl_goodsreceiving.trans_typ='purchaseempty' and tbl_goodsreceiving.receiptdate
            >= '$fromdate' and tbl_goodsreceiving.receiptdate <=    '$todate'
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
			           >= '$fromdate' and tbl_issue_goods.issuedate  <=    '$todate' 
			and tbl_issue_goods.decanting!='Yes' ";
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
			'$fromdate' and tbl_issue_return.irdate  <=    '$todate'  ";
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
			'$fromdate' and tbl_issue_return.irdate  <=    '$todate'  ";
			$result = $this->db->query($query);
			$out_to_vendor_f_row = $result->row_array();
			}
			
			////////////////////////////// OUT to vendor empty /////////////////////////////////
			$query = "  select (select COALESCE(SUM(tbl_goodsreceiving_detail.ereturn),0) 
			from tbl_goodsreceiving_detail,tbl_goodsreceiving
			where  tbl_goodsreceiving_detail.receipt_detail_id=tbl_goodsreceiving.receiptnos
			and tbl_goodsreceiving_detail.itemid='$itemid'
			and tbl_goodsreceiving.trans_typ='purchasefilled' and tbl_goodsreceiving.receiptdate
			 >= '$fromdate' and tbl_goodsreceiving.receiptdate  <=    '$todate'
			)
			+
			(
			select COALESCE(SUM(tbl_issue_return_detail.qty),0) as out_to_vendor_f
			from tbl_issue_return_detail, tbl_issue_return
			where  tbl_issue_return_detail.irnos=tbl_issue_return.irnos
			and tbl_issue_return_detail.itemid='$itemid'
			and tbl_issue_return_detail.type='Empty'
			and tbl_issue_return.type='purchasereturn' and  tbl_issue_return.irdate   >= '$fromdate' and tbl_issue_return.irdate <=    '$todate'
			)
			as out_to_vendor_e ";
			$result = $this->db->query($query);
			$out_to_vendor_e_row = $result->row_array();


				 
									
			////////////////////////////// SALE EMPTY /////////////////////////////////
			$query = "  select COALESCE(SUM(tbl_issue_goods_detail.qty),0) as sale_out_f
			from tbl_issue_goods,tbl_issue_goods_detail where 
			tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id 
			and tbl_issue_goods_detail.itemid='$itemid' and tbl_issue_goods.type='Empty' and tbl_issue_goods.issuedate >= '$fromdate' and tbl_issue_goods.issuedate<= '$todate'
			and tbl_issue_goods.decanting!='Yes' ";
			$result = $this->db->query($query);
			$sale_out_e_row = $result->row_array();

			
			$sale_out_e=$sale_out_e_row['sale_out_f'];
			
			
			
			////////////////////////////// SALE FILLED /////////////////////////////////
			$query = "  select COALESCE(SUM(tbl_issue_goods_detail.qty),0) as sale_out_f
			from tbl_issue_goods,tbl_issue_goods_detail where 
			tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id 
			and tbl_issue_goods_detail.itemid='$itemid' and tbl_issue_goods.type='Fill' and tbl_issue_goods.issuedate >= '$fromdate' and tbl_issue_goods.issuedate<= '$todate'
			and tbl_issue_goods.decanting!='Yes' ";
			$result = $this->db->query($query);
			$sale_out_f_row = $result->row_array();

				
				
			
			////////////////////////////// SALE RETURN FILLED /////////////////////////////////
			$query = " select COALESCE(SUM(tbl_issue_return_detail.qty),0) as sale_return_f
			from tbl_issue_return_detail, tbl_issue_return
			where  tbl_issue_return_detail.irnos=tbl_issue_return.irnos
			and tbl_issue_return_detail.itemid='$itemid'
			and tbl_issue_return_detail.type='Filled'
			and tbl_issue_return.type='salereturn' and  tbl_issue_return.irdate   >= '$fromdate' and tbl_issue_return.irdate  <=    '$todate' ";
			$result = $this->db->query($query);
			$sale_return_f_row = $result->row_array();
 
				  
			
			////////////////////////////// SALE RETURN EMPTY /////////////////////////////////
			$query = " select COALESCE(SUM(tbl_issue_return_detail.qty),0) as sale_return_e
			from tbl_issue_return_detail, tbl_issue_return
			where  tbl_issue_return_detail.irnos=tbl_issue_return.irnos
			and tbl_issue_return_detail.itemid='$itemid'
			and tbl_issue_return_detail.type='Empty'
			and tbl_issue_return.type='salereturn' and  tbl_issue_return.irdate     >= '$fromdate' and tbl_issue_return.irdate  <=    '$todate' ";
			$result = $this->db->query($query);
			$sale_return_e_row = $result->row_array();

				   
                  

			
			////////////////////////////// DECANT SALE EMPTY /////////////////////////////////
			$decant_sale_e=0;
			
			
			
			////////////////////////////// DECANT SALE FILLED /////////////////////////////////
			$query = "  select COALESCE(SUM(tbl_issue_goods_detail.qty),0) as decant_sale_f
			from tbl_issue_goods,tbl_issue_goods_detail where 
			tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id 
			and tbl_issue_goods_detail.itemid='$itemid' and tbl_issue_goods.issuedate  >= '$fromdate' and tbl_issue_goods.issuedate  <=    '$todate'
			and tbl_issue_goods.decanting='Yes' ";
			$result = $this->db->query($query);
			$decant_sale_f_row = $result->row_array();				  
                   
             


			////////////////////////////// DECANT RECV FILLED /////////////////////////////////
			$decant_empty_f=0;
			
			
			
			////////////////////////////// RECV FROM CUSTOMER EMPTY /////////////////////////////////
			$query = "  select COALESCE(SUM(tbl_issue_goods_detail.returns),0) as decant_empty_e
			from tbl_issue_goods,tbl_issue_goods_detail where 
			tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id 
			and tbl_issue_goods_detail.itemid='$itemid' and tbl_issue_goods.issuedate >= '$fromdate' and tbl_issue_goods.issuedate  <=    '$todate'
			and tbl_issue_goods.decanting='Yes'   ";
			$result = $this->db->query($query);
			$decant_empty_e_row = $result->row_array();

			 
			
			
			
 
			////////////////////////////// CYLINDER CONVERSTION From /////////////////////////////////
				$query = " select COALESCE(SUM(tbl_cylinderconversion_detail.qty),0) as convert_from_f
			from tbl_cylinderconversion_detail,tbl_cylinderconversion_master where 
			tbl_cylinderconversion_master.trans_id=tbl_cylinderconversion_detail.trans_id 
			and tbl_cylinderconversion_detail.itemcode='$itemid' and tbl_cylinderconversion_master.trans_date >= '$fromdate' AND tbl_cylinderconversion_master.trans_date <= '$todate'
			and tbl_cylinderconversion_detail.`type`='from' ";
			$result = $this->db->query($query);
			$convert_from_f_row = $result->row_array();
            $convert_to_e=$convert_from_f_row['convert_from_f'];
			
			
			////////////////////////////// CYLINDER CONVERSTION to /////////////////////////////////
			$query = " select COALESCE(SUM(tbl_cylinderconversion_detail.qty),0) as convert_to_f
			from tbl_cylinderconversion_detail,tbl_cylinderconversion_master where 
			tbl_cylinderconversion_master.trans_id=tbl_cylinderconversion_detail.trans_id 
			and tbl_cylinderconversion_detail.itemcode='$itemid' and tbl_cylinderconversion_master.trans_date >= '$fromdate' AND  tbl_cylinderconversion_master.trans_date <='$todate'
			and tbl_cylinderconversion_detail.`type`='to' ";
			$result = $this->db->query($query);
			$convert_to_f_row = $result->row_array();
            $convert_from_e=$convert_to_f_row['convert_to_f'];
			
			
			
			
			
			
                    // 'convert_to_f'=>$convert_to_f_row['convert_to_f'],
                    // 'convert_from_f'=>$convert_from_f_row['convert_from_f'],
					 
                    // 'convert_to_e'=>$convert_to_e_row['convert_to_e'],
                    // 'convert_from_e'=>$convert_from_e_row['convert_from_e'],
         
		 
		 
		 
		 
		 
		 
		 
		 
		 
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
                    
					'fromdate'=>$fromdate,
                    'todate'=>$todate,
                    //'filledstock'=>$filledstock,
                );
               // pm($datas);
            
            }
        }
        ///pm($datas);
        //pm($datas);
        return $datas;
    }



    public function opening_stock($month,$year) {		
			
	

 $ournewdate = date('Y-m-01', strtotime("$month, $year"));


	 $this->load->model(array(
            "mod_common","mod_admin","mod_customerstockledger","mod_salelpg","mod_user"
        ));

$check = $this->db->get_where('tbl_admin',array('id'=>$this->session->userdata('id')))->row();
	// if($check->dashboard=="Show"){

		$user_id=$this->session->userdata('id');
		


		$total_tonnage=0;
		  $month = $month;
		  $year = $year;	
		if($month!="" && $year!=""){
		
			
		if($year == date('Y')){ 
			 $date = "$year-$month-01";
			$last_date = date('Y-m-t', strtotime("$date"));
		}else{ 
			$date = "$year-$month-01";
			$last_date = date('Y-m-t', strtotime("$date"));
		}
		


		 echo $last_date = $ournewdate; 

		$all_brand=$this->mod_admin->get_all_brand();
		//pm($all_brand);

		$brand_count=0;
		foreach ($all_brand as $key => $value) {
				$brand_id=$value['brand_id']; 
				$date=date('Y-m-d');
				
				$where_item = array('catcode' =>1,'brandname' =>$brand_id);
				$all_brand[$brand_count]['item'] = $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_item);

				$new_i=0;
				$item_count=0;
			
				foreach ($all_brand[$brand_count]['item'] as $key => $value) {
						 $id=$value['materialcode'];
						 $itemnameint=$value['itemnameint'];

						 $today_stock=$this->mod_common->stock($id,'empty',$last_date,1);
						//print_r(($today_stock));
						
						 $empty_filled= explode('_', $today_stock);
						 $filled=$empty_filled[0] ;
						$total_tonnage+=($itemnameint*$filled)/1000;	
				 }
		
		
		}	
		return ($total_tonnage); 
	 
	
		}


		
		

  // }
		
	
    }



	public function opening_stock1111111($month,$year) {		
			
		//   $new_datee = $year."-".$month;

		// $datestring=$new_datee.'first day of last month';
		// $dt=date_create($datestring);
		//  $dt->format('Y-M-d');

		// $month = $dt->format('M');
		// $year = $dt->format('Y');

 $ournewdate = date('Y-m-01', strtotime("$month, $year"));


	 $this->load->model(array(
            "mod_common","mod_admin","mod_customerstockledger","mod_salelpg","mod_user"
        ));

$check = $this->db->get_where('tbl_admin',array('id'=>$this->session->userdata('id')))->row();
	if($check->dashboard=="Show"){

		$user_id=$this->session->userdata('id');
		$where_right = array('uid' => $user_id,'pageid' => '10');
        $data['bank_right']= $this->mod_common->select_array_records('tbl_user_rights',"*",$where_right);
      	if(!empty($data['bank_right']))
		{
			$data['bank_flage']='yes';
			$data['bank_position'] = $this->mod_admin->bank_position_ledger();
		}
		else
		{
			$data['bank_flage']='no';
		}



		
		
		
		
		
		
		
		
		
		
		/////////////////////************************ STOCK CALCULATION STARTS HERE *******************************/////////////////////////////
		/////////////////////************************ STOCK CALCULATION STARTS HERE *******************************/////////////////////////////
		/////////////////////************************ STOCK CALCULATION STARTS HERE *******************************/////////////////////////////
		/////////////////////************************ STOCK CALCULATION STARTS HERE *******************************/////////////////////////////
		
		$all_brand=$this->mod_admin->get_all_brand();
		$brand_count=0;
		foreach ($all_brand as $key => $value) {
		$brand_id=$value['brand_id'];
		$date=date('Y-m-d');
		//$date=date('Y-m-d', strtotime("+1 day"));
		$where_item = array('catcode' =>1,'brandname' =>$brand_id);
		$all_brand[$brand_count]['item'] = $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_item);

		$new_i=0;
		$item_count=0;
		foreach ($all_brand[$brand_count]['item'] as $key => $value) {
				$id=$value['materialcode'];
				$today_stock=$this->mod_common->stock($id,'empty',$date,1);
				$empty_filled= explode('_', $today_stock);
				$filled=$empty_filled[0] ;
				$empty=$empty_filled[1];
				$stock_in_market=$this->mod_admin->getcurrent_stock_new_access($id,'All',date('Y-m-d'),'Market');
				$market_total=0;
				foreach ($stock_in_market as $key => $values) {
				$market_total+=$values['opening'];
				}
				$access_cylinder=$this->mod_admin->getcurrent_stock_new_access($id,'All',date('Y-m-d'),'Access');
				$acces_total=0;
				foreach ($access_cylinder as $key => $values) {
				$acces_total+=$values['opening'];
				}
				$all_brand[$brand_count]['item'][$item_count]['filled']=$filled;
				$all_brand[$brand_count]['item'][$item_count]['item_market']=$market_total;
				$all_brand[$brand_count]['item'][$item_count]['access_cylinder']=$acces_total;
				$all_brand[$brand_count]['item'][$item_count++]['empty']=$empty;
		}
		$brand_count++;
	}	

	 
		$total_tonnage=0;
		  $month = $month;
		  $year = $year;	
		if($month!="" && $year!=""){
		
			//echo $month = "0".$month;
		if($year == date('Y')){ 
			 $date = "$year-$month-01";
			$last_date = date('Y-m-t', strtotime("$date"));
		}else{ 
			$date = "$year-$month-01";
			$last_date = date('Y-m-t', strtotime("$date"));
		}
			 
		
		 $where_item = array('catcode' =>1,'brandname' =>$brand_id);
		 $all_brand[$brand_count]['item'] = $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_item);

		 $new_i=0;
		 $item_count=0; 
		 //waqas written code 
		 
		    $last_date = $ournewdate; 
		 
		 //waqas written code ends here
		//pm($all_brand[$brand_count]['item']);
		 foreach ($all_brand[$brand_count]['item'] as $key => $value) {
				 $id=$value['materialcode'];
				 $itemnameint=$value['itemnameint'];

				 $today_stock=$this->mod_common->stock($id,'empty',$last_date,1);
				//print_r(($today_stock));
				
				 $empty_filled= explode('_', $today_stock);
				 $filled=$empty_filled[0] ;
				$total_tonnage+=($itemnameint*$filled)/1000;	
		 }
		//pm($total_tonnage);
		return ($total_tonnage); 
		exit;
		}

		$data['new_stock_brand']=$all_brand;
//pm($data['new_stock_brand']);
		
		/////////////////////************************ STOCK CALCULATION ENDS HERE *******************************/////////////////////////////
		/////////////////////************************ STOCK CALCULATION ENDS HERE *******************************/////////////////////////////
		/////////////////////************************ STOCK CALCULATION ENDS HERE *******************************/////////////////////////////
		/////////////////////************************ STOCK CALCULATION ENDS HERE *******************************/////////////////////////////
		
		
		
		
		
		
		
		 

		//////////////////////////////// ***************** GRAPH *******************************///////////////////////////////
		//////////////////////////////// ***************** GRAPH *******************************///////////////////////////////
		//////////////////////////////// ***************** GRAPH *******************************///////////////////////////////
		$table='tblacode';
		$where = array('general' =>1001001000);
		$data['brand'] = $this->mod_common->select_array_records($table,"*",$where);
		$data['monthly_stock']=  $this->mod_admin->getmonthly_stock();
	    $start_date=date('Y-m-d',strtotime('-1 month'));
	    $start_date=date('Y-m-01');
        $end_date=date('Y-m-d');
		$total_date= count($data['monthly_stock'])+1;
		while (strtotime($start_date) <= strtotime($end_date)) {
			if(array_search($start_date, array_column($data['monthly_stock'], 'issuedate')) !== False)
			{
			}
			else
			{

				$data['monthly_stock'][$total_date]['issuedate']=$start_date;
				$data['monthly_stock'][$total_date++]['totala']=0;
			}
            $start_date = date ("Y-m-d", strtotime("+1 day", strtotime($start_date)));
		}
		array_multisort( array_column($data['monthly_stock'], "issuedate"), SORT_ASC, $data['monthly_stock'] );
		// $data['item_filled']= $item_filled;
		// $data['total_balance']=  $this->mod_customerstockledger->get_total_customer_stock();
		// pm($data['total_balance']);
		//////////////////////////////// ***************** GRAPH *******************************///////////////////////////////
		//////////////////////////////// ***************** GRAPH *******************************///////////////////////////////
		//////////////////////////////// ***************** GRAPH *******************************///////////////////////////////
		
		
		
		 
		
		
		
		
		
		
		
		////////////////////////////////// *********** Receivables  **********************///////////////////////
		////////////////////////////////// *********** Receivables  **********************///////////////////////
		$data['new_balance_new']=  $this->mod_customerstockledger->get_total_balance();
		foreach ($data['new_balance_new'] as $key => $value) { 
			if($value['optype']=='Credit')continue;
			$net_balace=$net_balace+$value['new_balance'];
		}
		$data['new_balance']=$net_balace;

		////////////////////////////////// *********** Receivables  **********************///////////////////////
		////////////////////////////////// *********** Receivables  **********************///////////////////////
		
		

		
		
		
		
		
		
		
		
		////////////////////////////////// *********** Payables  **********************///////////////////////
		////////////////////////////////// *********** Payables  **********************///////////////////////
		$data['new_balance_new_pay']=  $this->mod_customerstockledger->get_total_balance_pay();
		foreach ($data['new_balance_new_pay'] as $key => $value) { 
		if($value['optype']=='Debit')continue;
		$net_balace_pay=$net_balace_pay+$value['new_balance_pay'];
		}
		$data['new_balance_pay']=$net_balace_pay;

		////////////////////////////////// *********** Payables  **********************///////////////////////
		////////////////////////////////// *********** Payables  **********************///////////////////////






		// // foreach ($data['total_balance'] as $key => $value) {  
			// // foreach ($value['stock'] as $sub_key => $sub_value) {
				// // $array_sub[$sub_key]=$array_sub[$sub_key]+$sub_value;
			// // }
		// // }


		// // $data['item_market']=$array_sub;
 
		// $net_balace=0;
		// foreach ($data['total_balance'] as $key => $value) {
		 // $net_balace=$net_balace+$value['new_balance'];
		// }
		
		// $data['net_balace']=  $net_balace;
		// $data['opening']=  $this->mod_admin->get_opening();
		// $data['sale']=  $this->mod_admin->getsale();
		// $data['return']=  $this->mod_admin->getreturn();
        // $where_cat_id = array('catcode' => 1);
        // $data['itemname']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_cat_id);
			// $total_return = array();
			// $total_sale = array();
			// $total_return_sale=array();

			// foreach ($data['return'] as $key => $value) {

				// if(count($value['return']>1))
 				// {
			 		// foreach ($value['return'] as $key => $value_sub) {

			 			// $total_return[$value_sub['itemid']]=$total_return[$value_sub['itemid']]+$value_sub['qty'];
			 			// //$total_return['itemid']=$value_sub['itemid'];
			 			// //print_r($total_return[$value_sub['itemid']]);
			 		// //exit();
			 		// }
				// }
			// }
		

			// foreach ($data['sale'] as $key => $value) {

				// if(count($value['sale']>1))
 				// {
			 		// foreach ($value['sale'] as $key => $value_sub) {

			 			// $total_sale[$value_sub['itemid']]=$total_sale[$value_sub['itemid']]+$value_sub['qty'];

			 		// }
				// }
			// }

			// krsort($total_return);
			// //pm($total_sale);

			// //total_item=
			// //pm($data['opening']);

			// for ($i=0; $i <count($data['opening']); $i++) {

				// $item_code=$data['opening'][$i]['itemid'];
				// $opening_array[$item_code]=$data['opening'][$i]['opening'];

			// }
			// //pm($opening_array);

			// for ($i=0; $i <count($data['itemname']); $i++) { 

				 // $item_code= $data['itemname'][$i]['materialcode'];
				// //echo '<br>';
				// $total_return_sale[$item_code]=$total_sale[$item_code]-$total_return[$item_code]+$opening_array[$item_code];
			// }
			// //pm($total_return_sale);
			// $data['total_return_sale']=$total_return_sale;

		 //pm($data['itemname']);

		//pm($data['report']);
		
		
		
		/////////////////////////////// TODAY SALE AND CASH POSITION ///////////////////////////////////////////
		/////////////////////////////// TODAY SALE AND CASH POSITION ///////////////////////////////////////////
		$data['salelpg_list'] = $this->mod_admin->manage_salelpg();
		$data['cash_position'] = $this->mod_admin->cash_position();
		/////////////////////////////// TODAY SALE AND CASH POSITION ///////////////////////////////////////////
		/////////////////////////////// TODAY SALE AND CASH POSITION ///////////////////////////////////////////
		
		
		 
		///////////////////////////////// * *** ***** TODAY CASH ************ //////////////////////////////////		
		///////////////////////////////// * *** ***** TODAY CASH ************ //////////////////////////////////		
		///////////////////////////////// * *** ***** TODAY CASH ************ //////////////////////////////////		
		$data['cash_position_today']=  $this->mod_admin->cash_position_today();
		foreach ($data['cash_position_today'] as $key => $value) { 
		$cash_today= $value['damount']-$value['camount'];
		}
		$data['cash_today']=$cash_today;
		///////////////////////////////// * *** ***** TODAY CASH ************ //////////////////////////////////		
		///////////////////////////////// * *** ***** TODAY CASH ************ //////////////////////////////////		




		
		////////////////////////////// ********************ORDERS BOOKED *********************************///////////////////////////
		////////////////////////////// ********************ORDERS BOOKED *********************************///////////////////////////
		//$data['bookorder'] = $this->mod_admin->manage_bookorder();		
		////////////////////////////// ********************ORDERS BOOKED *********************************///////////////////////////
		////////////////////////////// ********************ORDERS BOOKED *********************************///////////////////////////

  }
		
		// $data["title"] = " Admin ";
		// if($this->session->userdata('language')!='')
		// {
		//   	$this->load->view($this->session->userdata('language')."/admin/home", $data);
	 //  	}
	 //  	else
	 //  	{
	 //  		$this->load->view("en/admin/home", $data);
	 //  	}
    }



    public function get_report_sale_total($data){
        	
        	 $date = date_parse($this->input->post('month'));
 			$mnthno = ($date['month']);
 			$this->input->post('year');


			$cmonth=$mnthno;
			$cyear=$this->input->post('year');
			$from_date_year=$cyear.'-01-01';
			$from_date=$cyear.'-'.$cmonth.'-01';
			$lastday= $cmonth == 2 ? ($cyear % 4 ? 28 : ($cyear % 100 ? 29 : ($cyear %400 ? 28 : 29))) : (($cmonth - 1) % 7 % 2 ? 30 : 31);
 			$to_date=$cyear.'-'.$cmonth.'-'.$lastday;
			$date=$from_date;
			
			
			
			
			
			  
				$recv_weight=0;
					$sql_purchases="select m.suppliercode, a.aname, d.quantity,d.rate,d.inc_vat_amount 
					FROM tbl_goodsreceiving m,tbl_goodsreceiving_detail d, tblacode a
					where m.receiptnos=d.receipt_detail_id and m.receiptdate<'$date'
					and m.suppliercode=a.acode
					and m.trans_typ='purchasefilled'";	
					$query = $this->db->query($sql_purchases);

					 
					if($query->num_rows()>0){
					foreach($query->result_array() as $key => $value) {
						$recv_weight += $value['quantity'];
						$rate += $value['rate'];
						$inc_vat_amount += $value['inc_vat_amount'];
						
					}}

					return $rate;

					//code of waqas starts here
				// if(empty($rate)){

					// $tbltankquery = $this->db->query("select * from tbl_tank");

					// $record = $tbltankquery->result_array();
					// $record[0]['opening_value']/$record[0]['opening_qty'];
				
				// }


					// $alltotalamount = $inc_vat_amount + $record[0]['opening_value'];
					// $alltotalqty = $recv_weight+$record[0]['opening_qty'];



					// $rate = $alltotalamount/$alltotalqty;

				//code of waqas ends here





					

			// 		$qty_total =0;
			// 		$sql_sales="select sm.remarks,sm.issuenos, a.aname,a.cell, sm.issuedto, sm.total_amount,
			// 		sm.total_discount,sm.total_received
			// 		from tbl_issue_goods sm  , tblacode a
			// 		where sm.issuedto=a.acode
			// 		and sm.issuedate<'$date'
			// 		and sm.sale_type='direct'";	
			// 		$query = $this->db->query($sql_sales);
			// 		if($query->num_rows()>0){
			// 		foreach($query->result_array() as $key => $value) {
		 
			// 			$qty_total += $value['total_tonnage'];
			// 		}}

					
			// 		$total_df = '';

			// 		$this->db->select_sum('qty');
			// 		$this->db->select('qty,itemcode,dt');
			// 		$this->db->from('tbl_daily_filling');
			// 		$this->db->where(array('auto_manual'=>"Manual",'dt < '=>$date));
			// 		$df = $this->db->get()->result_array();

			// 		foreach ($df as $key => $value) {
			// 			$qty = $value['qty'];
			// 			$itemcode = $value['itemcode'];

			// 				if($itemcode==1) {  $total_df+=($qty*11.8)/1000; }
			// 				if($itemcode==2) {  $total_df+=($qty*15)/1000; }
			// 				if($itemcode==3) {  $total_df+=($qty*45.4)/1000; }
			// 				if($itemcode==4) {  $total_df+=($qty*6)/1000; }
			// 				if($itemcode==5) {  $total_df+=($qty*35)/1000; }
			// 				if($itemcode==6) {  $total_df+=($qty*18)/1000; }
			// 				if($itemcode==7) {  $total_df+=($qty*30)/1000; }
						
			// 		}

			// 	$this->db->select_sum('opening_qty');
			// 	$tank_opening = $this->db->get("tbl_tank")->row();


			// 	$opening=$tank_opening->opening_qty+$recv_weight-$qty_total-$total_df;
			// 	$lpg_balance_is=$recv_weight-$qty_total-$total_df;


				





		 // $opening_mt=round($rate*$opening);
	 
   //              $datas[] = array(
   //                  'for_date' => $date,
   //                  'total_qty' => $opening,
   //                  'total_amt' => $opening_mt,
   //                  'purchase_rate' => $rate,
                     
   //                  'trans_type'=>'Opening', 
					
			// 	);
				

	 
	
	// while (strtotime($date) <= strtotime($to_date)) {
                	 
			
				
	// 			   $sql_purchases="select  sum(quantity) as total_qty, sum(d.inc_vat_amount) as total_amt
	// 			FROM tbl_goodsreceiving m,tbl_goodsreceiving_detail d, tblacode a
	// 			where m.receiptnos=d.receipt_detail_id and m.receiptdate='$date'
	// 			and m.suppliercode=a.acode
	// 			and m.trans_typ='bulkpurchase'";	
				
				 
	// 			$query = $this->db->query($sql_purchases);
	// 			if($query->num_rows()>0){
	// 			foreach($query->result_array() as $key => $value) {
				 
	// 			$party_name='';
	// 			$recv_weight='';
	// 			$purchase_rate='';
	// 			$total='';
	// 			$cell_number='';
	// 			$gate_pass= $value['receiptnos'];
	// 			$customer_name='';
	// 			$qty_11='';
	// 			$qty_15='';
	// 			$qty_45='';
	// 			$qty_total='';
	// 			$lpg_balance='';
	// 			$total_11_kg='';
	// 			$rate='';
	// 			$gross_sale='';
	// 			$loading='';
	// 			$net_sale='';
	// 			$cash_recv='';
	// 			$bless_traders='Purchase';
	// 			$bless_gas='';
	// 			$others='';
	// 			$bal='';
	// 			$remarks='';
			 

			 
	// 			$total_qty = $value['total_qty'];
	// 			$total_amt = $value['total_amt']; 
	// 			$purchase_rate = round($total_amt/$total_qty); 
 
	// if($total_qty>0){
	// 			 $datas[] = array(
	// 				'for_date' => $date,
 //                    'total_qty' => $total_qty,
 //                    'total_amt' => $total_amt,
 //                    'purchase_rate' => $purchase_rate,
                     
 //                    'trans_type'=>'Purchase', 
					
	// 			);

	// 			}
	// 			}

	// 		} 
			

			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
				

				
				
				
				
					
	// 			  $sql_sales="select sm.remarks,sm.issuenos, a.aname,a.cell, sm.issuedto, sm.total_amount,
	// 			sm.total_discount,sm.total_received,sm.total_tonnage,sm.price_11
	// 			from tbl_issue_goods sm  , tblacode a
	// 			where sm.issuedto=a.acode
	// 			and sm.issuedate='$date' and sm.fill_direct='Fill'
	// 			";	
	// 			$query = $this->db->query($sql_sales);
	// 			if($query->num_rows()>0){


	// 			$total_qty=0;
	// 			$total_amt=0;
	// 			$purchase_rate=0;
	// 		foreach($query->result_array() as $key => $value) {
			 
	// 			$qty_total = $value['total_tonnage'];
	// 			$rate = $value['price_11'];
	// 			$gross_sale = $value['total_amount'];
				 
				 
				 
	// 			$total_qty+=$qty_total;
	// 			$total_amt+=$value['total_amount'];
			
				
	// 		}
	// 			 	$purchase_rate=round($total_amt/$total_qty);
	// 			 $datas[] = array(
	// 				'for_date' => $date,
 //                    'total_qty' => $total_qty,
 //                    'total_amt' => $total_amt,
 //                    'purchase_rate' => $purchase_rate,
                     
 //                    'trans_type'=>'Sales', 
					
	// 			);
				
				
	// 			}
				
				
				
				
				
				
				
				
				
				
				
				
				
				
				
			 
 //                $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
				
			
	// }


	
   //  pm($datas);
        //return $datas;
    }

}

?>
