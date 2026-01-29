<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//date_default_timezone_set('Asia/Karachi');

class Balancesheet extends CI_Controller {

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
		$data["title"] = "Balance Sheet Report";	
		$table='tblcategory';       
        $data['category_list'] = $this->mod_common->get_all_records($table,"*");
        $this->load->view($this->session->userdata('language')."/balancesheet/search",$data);       	
	}


	public function detail_report()
	{							//error_reporting(E_ALL);
		 $month = $_POST["month"];
		  $year = $_POST["year"];

		
		if($year == date('Y')){ 
			 $first_date = date('Y-m-01', strtotime("$month, $year"));
			$last_date = date('Y-m-t', strtotime("$month, $year"));
		}elseif($year==date('Y')+1){ 
			$first_date = date('Y-m-01', strtotime("$month, +1 year"));
			$last_date = date('Y-m-t', strtotime("$month, +1 year"));
		}

		$mytable = "tblacode";
		$data['firstdatee'] = $first_date;
		$data['lastdatee'] = $last_date;

		$data['blncsheetrecord'] = $this->mod_common->getblncsheetrecords($mytable,$first_date,$last_date);
		$allblncsheetrecord = $this->mod_common->getallblncsheetrecords($mytable,$first_date);
		
	
		//echo "<pre>";pm($data['blncsheetrecord']);
		$d_amount = 0;
		$c_amount = 0;

		foreach($data['blncsheetrecord'] as $recordnew){
			$d_amount += $recordnew->damount;
			$c_amount += $recordnew->camount;
			
		}

		$totalamountleft = $d_amount - $c_amount;
		


	
		$productdetails = $this->get_detailsssssss($first_date,$last_date);
//pm($productdetails);
		foreach($productdetails as $key => $Productdetails){ //echo "<pre>";var_dump($Productdetails);
			  $qtyy = explode("-",$Productdetails['itemid']);
			 
			$totallpurchasee += $qtyy[0] * $Productdetails['recv_from_vendor_f'];
		}
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
				//	 pm($data['sale']);
				$where_cat_id =''; // array('catcode=' => 1);
				$data['itemname']= $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_cat_id);
				//$tables='tblmaterial_coding';       
				//$data['itemname'] = $this->mod_common->get_all_records($tables,"*");
				//$data['itemname_return'] = $this->mod_common->get_all_records($tables,"*");
				//////////////////////////////////    SALES //////////////////////////////////////////////////////////////////////////////////////////
				//////////////////////////////////    SALES //////////////////////////////////////////////////////////////////////////////////////////
		 
		 
				//////////////////////////////////    SALES RETURN //////////////////////////////////////////////////////////////////////////////////////////
				$data['sale_return']=  $this->mod_profitreport->getsales_return($data_posted,2);
				//////////////////////////////////    PURCHASES //////////////////////////////////////////////////////////////////////////////////////////
				
				
				///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//////////////////////////////////    PURCHASES //////////////////////////////////////////////////////////////////////////////////////////
				$data['purchases']=  $this->mod_profitreport->getpurchases($data_posted,2);
				//////////////////////////////////    PURCHASES //////////////////////////////////////////////////////////////////////////////////////////
				
				
				
				//////////////////////////////////    PURCHASES RETURN //////////////////////////////////////////////////////////////////////////////////////////
				$data['purchases_return']=  $this->mod_profitreport->getpurchases_return($data_posted,2);
				//////////////////////////////////    PURCHASES RETURN //////////////////////////////////////////////////////////////////////////////////////////

				
				
				//////////////////////////////////    PURCHASES RETURN //////////////////////////////////////////////////////////////////////////////////////////
				$data['payments']=  $this->mod_profitreport->getpayments_new($data_posted,2);
				//////////////////////////////////    PURCHASES RETURN //////////////////////////////////////////////////////////////////////////////////////////
			
				//////////////////////////////////    PURCHASES RETURN //////////////////////////////////////////////////////////////////////////////////////////
				$data['get_income']=  $this->mod_profitreport->get_income($data_posted,2);
				//////////////////////////////////    PURCHASES RETURN //////////////////////////////////////////////////////////////////////////////////////////
			
				//////////////////////////////////    receipts RETURN //////////////////////////////////////////////////////////////////////////////////////////
				$data['receipts']=  $this->mod_profitreport->getreceipts($data_posted,2);
				//////////////////////////////////    receipts RETURN //////////////////////////////////////////////////////////////////////////////////////////
		// pm($data['payments']);

				$data['opening_stock'] = $this->opening_stock($month,$year);
				

			//$this->load->view($this->session->userdata('language')."/balancesheet/balancesheetwidparent",$data);
			$this->load->view($this->session->userdata('language')."/balancesheet/detail_report",$data);
	        

	         
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
		//var_dump($all_brand[$brand_count]['item']);
		 foreach ($all_brand[$brand_count]['item'] as $key => $value) {
				 $id=$value['materialcode'];
				 $itemnameint=$value['itemnameint'];

				 $today_stock=$this->mod_common->stock($id,'empty',$last_date,1);
				//print_r(($today_stock));
				
				 $empty_filled= explode('_', $today_stock);
				 $filled=$empty_filled[0] ;
				$total_tonnage+=($itemnameint*$filled)/1000;	
		 }
		//$res = array("result"=>$total_tonnage);
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

}

?>
