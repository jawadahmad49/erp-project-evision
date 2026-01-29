<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
	
	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_common","mod_admin","mod_customerstockledger","mod_salelpg","mod_user"
        ));
    }
	public function index($id) {			
			
		if($this->session->userdata('email')=='')
		{
			redirect(SURL.'login');
		}

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



		//cash position query starts here
		
		// $data['new_balance_new']=  $this->mod_customerstockledger->get_total_balance1();
		// foreach ($data['new_balance_new'] as $key => $value) { 
		// 	if($value['optype']=='Credit')continue;
		// 	$net_balace=$net_balace+$value['new_balance'];
		// }
		// $data['new_balance']=$net_balace;
		
		// $Receivables = $this->db->query("select sum(damount)- sum(camount) as Receivables from tbltrans_detail where LEFT(acode,7)='2004001'")->result_array()[0]['Receivables'];
		
		// $sql = "select opngbl,optype as Receivables from tblacode where LEFT(acode,7)='2004001'";
		// $tblacode = $this->db->query($sql)->result_array();
		// foreach ($tblacode as $key => $value) {
		// 	if($value['optype'] == "Debit"){
		// 		$customer_opnng += $value['opngbl'];
		// 	}else{
		// 		$customer_opnng -= $value['opngbl'];
		// 	}
		// }
		
		// echo $data['new_balance'] = $customer_opnng + $Receivables; exit();

		$date['cash_today'] = $this->db->query("select (sum(damount)-sum(camount)) as todaycashposition from tbltrans_detail where acode='2003013001' and vdate='".date("Y-m-d")."'")->result_array()[0]['todaycashposition'];

		$cash_position_query = $this->db->query("select (sum(damount)-sum(camount)) as todaycashposition from tbltrans_detail where acode='2003013001'")->result_array()[0]['todaycashposition'];
		
		
		$acode_query = $this->db->query("SELECT * FROM `tblacode` WHERE `acode` = 2003013001")->result_array();
		foreach ($acode_query as $key => $value) {
			if($value['optype'] == "Debit"){
				$cashposition += $value['opngbl'];
			}else{
				$cashposition -= $value['opngbl'];
			}
		}

		//echo $cashposition; exit();
		$data['cash_position'] = $cash_position_query - $cashposition; 


		
		
		
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
				// $security_cylinder=$this->mod_admin->getcurrent_security_cylinder($id,'All',date('Y-m-d'),'Market');
				//echo "<pre>";var_dump($security_cylinder);
				// foreach ($security_cylinder as $key => $values) {
					
				// echo $security_total+=$values['opening'];
				// }
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
				// $all_brand[$brand_count]['item'][$item_count]['security_cylin']=$security_total;
				$all_brand[$brand_count]['item'][$item_count]['access_cylinder']=$acces_total;
				$all_brand[$brand_count]['item'][$item_count++]['empty']=$empty;
		}
		$brand_count++;
	}	

	 
		$total_tonnage=0;
		$month = $_POST["month"];
		$year = $_POST["year"];	
		if($month!="" && $year!=""){
		
			//echo $month = "0".$month;
		if($year == date('Y')){
			$date = "$year-$month-01";
			$last_date = date('Y-m-t', strtotime("$date"));
		}else{
			$date = "$year-$month-01";
			$last_date = date('Y-m-t', strtotime("$date"));
		}
			//echo $last_date;
		
		 $where_item = array('catcode' =>1,'brandname' =>$brand_id);
		 $all_brand[$brand_count]['item'] = $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_item);

		 $new_i=0;
		 $item_count=0; 
		 foreach ($all_brand[$brand_count]['item'] as $key => $value) {
				 $id=$value['materialcode'];
				 $itemnameint=$value['itemnameint'];


$from_date =  date("Y-m-01",strtotime($last_date));


				 $today_stock=$this->mod_common->stock($id,'empty',$from_date,1); //i have change to $from_date
				
				
				 $empty_filled= explode('_', $today_stock);
				//print_r(($today_stock));
				 $filled=$empty_filled[0] ; 
				 
				$total_tonnage+=($itemnameint*$filled)/1000; 
		 }
		//$res = array("result"=>$total_tonnage);
		echo ($total_tonnage); 
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


		$data['new_balance_new']=  $this->mod_customerstockledger->get_total_balance1();
		foreach ($data['new_balance_new'] as $key => $value) { 
			if($value['optype']=='Credit')continue;
			$net_balace=$net_balace+$value['new_balance'];
		}
		 $data['new_balance']=$net_balace;

		////////////////////////////////// *********** Receivables  **********************///////////////////////
		////////////////////////////////// *********** Receivables  **********************///////////////////////
		
		

		
		
		
		$trans_amount = $this->db->query("SELECT (sum(damount)-sum(camount)) as amount FROM `tbltrans_detail` WHERE LEFT(acode,7) in ('1001001','2004001','4001002') AND acode not in ('1001001000' ,'2004001000','4001002000')")->result_array()[0]['amount'];

		$tblacode_query = $this->db->query("SELECT opngbl,optype FROM `tblacode` WHERE LEFT(acode,7) in ('1001001','2004001','4001002') AND acode not in ('1001001000' ,'2004001000','4001002000')")->result_array();
		
		foreach($tblacode_query as $key => $value){

			if($value['optype'] == "Debit"){
				$tblacode_amount += $value['opngbl'];
			}else{
				$tblacode_amount -= $value['opngbl'];
			}
			
		}
		
		
		
		////////////////////////////////// *********** Payables  **********************///////////////////////
		////////////////////////////////// *********** Payables  **********************///////////////////////
		$data['new_balance_new_pay']=  $this->mod_customerstockledger->get_total_balance_pay1();

		foreach ($data['new_balance_new_pay'] as $key => $value) { 
		if($value['optype']=='Debit')continue;
		$net_balace_pay=$net_balace_pay+$value['new_balance_pay'];
		}
		$data['new_balance_pay']=$net_balace_pay;

		////////////////////////////////// *********** Payables  **********************///////////////////////
		////////////////////////////////// *********** Payables  **********************///////////////////////

		$net_balace_exp=0;
			$data['new_balance_expenses_is']=  $this->mod_customerstockledger->get_total_balance_expenses1();
			//pm($data['new_balance_expenses_is']);die;
			foreach ($data['new_balance_expenses_is'] as $key => $value) { 
			$net_balace_exp=$net_balace_exp+$value['new_balance_pay'];
			}
			$data['new_balance_expenses']=$net_balace_exp;
			
			$net_balace_exp_tot=0;
			$data['new_balance_expenses_is']=  $this->mod_customerstockledger->get_total_balance_expenses_current();
			//pm($data['new_balance_expenses_is']);die;
			foreach ($data['new_balance_expenses_is'] as $key => $value) { 
			$net_balace_exp_tot=$net_balace_exp_tot+$value['new_balance_pay'];
			}
			$data['new_balance_expenses_tot']=$net_balace_exp_tot;






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
		// $data['cash_position_today']=  $this->mod_admin->cash_position_today();

		// foreach ($data['cash_position_today'] as $key => $value) { 
		// $cash_today= $value['damount']-$value['camount'];
		// }
		// $data['cash_today']=$cash_today;
		///////////////////////////////// * *** ***** TODAY CASH ************ //////////////////////////////////		
		///////////////////////////////// * *** ***** TODAY CASH ************ //////////////////////////////////		



		
		////////////////////////////// ********************ORDERS BOOKED *********************************///////////////////////////
		////////////////////////////// ********************ORDERS BOOKED *********************************///////////////////////////
		//$data['bookorder'] = $this->mod_admin->manage_bookorder();		
		////////////////////////////// ********************ORDERS BOOKED *********************************///////////////////////////
		////////////////////////////// ********************ORDERS BOOKED *********************************///////////////////////////

  }
		//echo "<pre>";var_dump($data);
		$data["title"] = " Admin "; 
		if($this->session->userdata('language')!='')
		{
		  	$this->load->view($this->session->userdata('language')."/admin/home", $data);
	  	}
	  	else
	  	{
	  		$this->load->view("en/admin/home", $data);
	  	}
    }

    public function home() {

    	$table='tbl_resturant_reg';
        $data['restaurant_list'] = $this->mod_restaurant->get_all_restaurants($table,"*");

        //pm($data['restaurant_list']);
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Admin";   		
	  $this->load->view($this->session->userdata('language')."/admin/manage_company", $data);

	}

	public function get_stock_dashboard() { 

		 
		 
		 
		 	$all_brand=$this->mod_admin->get_all_brand();
		$brand_count=0;
		foreach ($all_brand as $key => $value) {
		$brand_id=$value['brand_id'];
		$date=date('Y-m-d', strtotime("+1 day"));
		$where_item = array('catcode' =>1,'brandname' =>$brand_id);
		$all_brand[$brand_count]['item'] = $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_item);

		$new_i=0;
		$item_count=0;

		foreach ($all_brand[$brand_count]['item'] as $key => $value) {
				$id=$value['materialcode'];
				$today_stock=$this->mod_common->stock($id,'empty',$date,1);
				$empty_filled= explode('_', $today_stock); //echo "<pre>";pm($empty_filled);
				$filled=$empty_filled[0] ;
				$empty=$empty_filled[1];
				$stock_in_market=$this->mod_admin->getcurrent_stock_new_access($id,'All',date('Y-m-d'),'Market');
				$security_cylinder=$this->mod_admin->getcurrent_security_cylinder($id,'All',date('Y-m-d'),'Market');
					 // echo "<pre>";var_dump($security_cylinder);
				foreach ($security_cylinder as $key => $values) {
					
				 $security_total+=$values['opening']; 
				}
				// echo $security_total;  echo "<br>";
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
				$all_brand[$brand_count]['item'][$item_count]['security_cylin']=$security_total;
				$all_brand[$brand_count]['item'][$item_count]['access_cylinder']=$acces_total;
				$all_brand[$brand_count]['item'][$item_count++]['empty']=$empty;
		}
		$brand_count++;
	}	

	 
		$total_tonnage=0;
		$month = $_POST["month"];
		$year = $_POST["year"];	
		if($month!="" && $year!=""){
		
			//echo $month = "0".$month;
		if($year == date('Y')){
			$date = "$year-$month-01";
			$last_date = date('Y-m-t', strtotime("$date"));
		}else{
			$date = "$year-$month-01";
			$last_date = date('Y-m-t', strtotime("$date"));
		}
			//echo $last_date;
		
		 $where_item = array('catcode' =>1,'brandname' =>$brand_id);
		 $all_brand[$brand_count]['item'] = $this->mod_common->select_array_records('tblmaterial_coding',"*",$where_item);

		 $new_i=0;
		 $item_count=0;
		 foreach ($all_brand[$brand_count]['item'] as $key => $value) {
				 $id=$value['materialcode'];
				 $itemnameint=$value['itemnameint'];

				 $today_stock=$this->mod_common->stock($id,'empty',$last_date,1);
				
				
				 $empty_filled= explode('_', $today_stock);
				 $filled=$empty_filled[0] ;
				$total_tonnage+=($itemnameint*$filled)/1000;	
		 }
		//$res = array("result"=>$total_tonnage);
		echo ($total_tonnage); 
		exit;
		}
		$data['new_stock_brand']=$all_brand;
		$new_stock_brand=$all_brand;
	?>
	<div class="widget-main no-padding">	<div class="dialogs">
													
													
													
													
													<table class="table table-bordered table-striped">
														<thead class="thin-border-bottom">
															<tr>
																<th  class="hidden-480">
																	<i class="ace-icon fa fa-caret-right blue"></i>Brand
																</th>
																<th>
																	<i class="ace-icon fa fa-caret-right blue"></i>Item Name
																</th>

																<th>
																	<i class="ace-icon fa fa-caret-right blue"></i>Filled Stock
																</th>

																<th >
																	<i class="ace-icon fa fa-caret-right blue"></i>Empty Stock
																</th>
																<th >
																	<i class="ace-icon fa fa-caret-right blue"></i>Damage Stock Filled
																</th>
																<th >
																	<i class="ace-icon fa fa-caret-right blue"></i>Damage Stock Empty
																</th>
																<th class="hidden-480">
																	<i class="ace-icon fa fa-caret-right blue"></i>
																		<a href="Customerstockledger/customerSale" style="text-decoration:underline;">Stock in Market</a>
																</th>
																<th class="hidden-480">
																	<i class="ace-icon fa fa-caret-right blue"></i>
																		<a href="Accesscylinders/details" style="text-decoration:underline;">Access Cylinder</a>
																</th>
																<th class="hidden-480">
																	<i class="ace-icon fa fa-caret-right blue"></i>
																		<a href="SecurityReceipt/details" style="text-decoration:underline;">Issue On Security</a>
																</th>
															</tr>
														</thead>   
											 
														<tbody>
															<?php  $count=0; foreach ($new_stock_brand as $key=>$value) {



																?>
															
																<td  class="hidden-480"><b class="green">
																	<?php echo $value['brand_name']; ?>
																</b>
																</td>

															<?php foreach ($value['item'] as $key=>$value_sub) { ?>
															<tr>
																<td  class="hidden-480"></td>
																<td>
																	<?php 
																		echo $itemname_final=$value_sub['itemname'];
																	?>
																
																</td>

																<td>
																<span class="label label-info arrowed-right arrowed-in"><?php echo $cbb=$value_sub['filled']; ?></span>
																</td>

																<td><span class="label label-danger arrowed-right arrowed-in">
																	<?php
																		echo $cb=$value_sub['empty'];
																	?>
																</span>
																</td>
																<td>
	<?php
	////////////////////////////// Filled CYLINDER Fresh to damage /////////////////////////////////
	$query = "SELECT sum(qty) as damagecylinder_f from tbl_exchange_condition where from_itemcode='".$value_sub['materialcode']."' and cyl_condition_to='Damage' and cyl_type='Filled'";
			$result = $this->db->query($query);
			$convert_to_f_row1 = $result->row_array(); 

	
	////////////////////////////// Filled CYLINDER Damage to Fresh /////////////////////////////////
            $query = "SELECT sum(qty) as freshcylinder_f from tbl_exchange_condition where from_itemcode='".$value_sub['materialcode']."' and cyl_condition_to='Fresh' and cyl_type='Filled'";
			$result = $this->db->query($query);
			$convert_to_f_row2 = $result->row_array();
           	


	?>
<span class="label label-success arrowed arrowed-right">
			<?php
            echo intval($convert_to_f_row1['damagecylinder_f']-$convert_to_f_row2['freshcylinder_f']);
	?>																
</span>
																</td>
																<td>
	<?php
	////////////////////////////// Empty CYLINDER Fresh to damage /////////////////////////////////
	$query = "SELECT sum(qty) as damagecylinder_e from tbl_exchange_condition where from_itemcode='".$value_sub['materialcode']."' and cyl_condition_to='Damage' and cyl_type='Empty'";
			$result = $this->db->query($query);
			$convert_to_f_row2 = $result->row_array(); 


	////////////////////////////// Empty CYLINDER Damage to Fresh /////////////////////////////////
            $query = "SELECT sum(qty) as freshcylinder_e from tbl_exchange_condition where from_itemcode='".$value_sub['materialcode']."' and cyl_condition_to='Fresh' and cyl_type='Empty'";
			$result = $this->db->query($query);
			$convert_to_f_row4 = $result->row_array();



	 ////////////////////////////// sale damage cylinder  /////////////////////////////////
            $query = "select sum(qty) as saledamagecylinder from tbl_issue_goods_detail where itemid='".$value_sub['materialcode']."' and salestatus='Damage'";
            $result = $this->db->query($query);
			$saledamagecylinderquery = $result->row_array();
            $damagecylindersale = $saledamagecylinderquery['saledamagecylinder'];		
            


	?>

			<span class="label label-success arrowed arrowed-right">

			<?php
            echo intval($convert_to_f_row2['damagecylinder_e'] - $convert_to_f_row4['freshcylinder_e'] -  $damagecylindersale);
	?>	
</span>
																</td>
																<td  class="hidden-480">	<span class="label label-warning arrowed arrowed-right">
																	<?php
																		echo $cb=$value_sub['item_market'];
																	?>
																</span>
																</td>
																<td  class="hidden-480">	<span class="label label-success arrowed arrowed-right">
																	<?php
																		echo $cb=$value_sub['access_cylinder'];
																	?>
																</span>
																</td>

																	<td  class="hidden-480">	<span class="label label-primary arrowed arrowed-right">
																	<?php
																		echo $cb=$value_sub['security_cylin'];
																	?>
																</span>
																</td>
															</tr>
															<?php } } if(!$new_stock_brand){ ?>
																	<tr>
																		
																		<td colspan="3" class="red" style="text-align: center;">No Record Found!</td>
																		
																	</tr>

															<?php }?>
														
														</tbody>
													</table>
													
													
													 
													
													
													
													
												</div><!-- /.widget-main -->
												</div>

<?php
 }

	public function get_chart() {

		$data['monthly_stock']=  $this->mod_admin->getmonthly_stock($this->input->post());


            $month=$this->input->post('chart_month');
            $year=$this->input->post('chart_year');
        
            $timestamp    = strtotime("$month" . "$year");


            $start_date = date('Y-m-01', $timestamp);
             $end_date  = date('Y-m-t', $timestamp); 



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
	?>
	<div id="chartContainer" style="height: 300px; width: 100%;"></div>
<div class="over" style="height: 20px;margin-top: -14px;width: 60px;background-color: white;position: absolute;"></div>
	<?php

		array_multisort( array_column($data['monthly_stock'], "issuedate"), SORT_ASC, $data['monthly_stock'] );

		 ?>
	<script type="text/javascript">
		var chart = new CanvasJS.Chart("chartContainer", {
			axisY:{
		   viewportMinimum: 0,
		   viewportMaximum: 20,
		   title: "Tonnage" ,
		   interval: 2         
		
		 },
		axisX:{
		  title : "Days",
		  interval: 1

		 },
		data: [
		{
			// Change type to "doughnut", "line", "splineArea", etc.
			type: "line",
			dataPointWidth: 20,
			click: onClick,
			dataPoints: [
				<?php foreach ($data['monthly_stock'] as $key => $value) { 
					$timestamp = strtotime($value['issuedate'])
					?>

				{ label:'<?php echo date("d", $timestamp);  ?>', y: <?php echo $value['totala']; ?>  },
			<?php } ?>

			]
		}
		]
	});
	chart.render();
	function onClick(e) {

  	var month =$('#chart_month').val();
  	var year =$('#chart_year').val();

    var url = "<?php echo SURL ?>SaleDateReport/item_report_detail";
	var form = $('<form target="_blank" action="' + url + '" method="post">' +
 
  	'<input type="hidden" name="day" value="' + e.dataPoint.x + '" />' +
  	'<input type="hidden" name="month" value="' +month + '" />' +
  	'<input type="hidden" name="year" value="' +year+ '" />' +

		  '</form>');
		$('body').append(form);
		form.submit();
	}
</script>

<?php
 }

 public function get_urchart() {

		$data['monthly_stock']=  $this->mod_admin->getmonthly_stock($this->input->post());


            $month=$this->input->post('chart_month');
            $year=$this->input->post('chart_year');
        
            $timestamp    = strtotime("$month" . "$year");


            $start_date = date('Y-m-01', $timestamp);
             $end_date  = date('Y-m-t', $timestamp); 



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

	?>
	<div id="chartContainer" style="height: 300px; width: 100%;"></div>
<div class="over" style="height: 20px;margin-top: -14px;width: 60px;background-color: white;position: absolute;"></div>
	<?php

		array_multisort( array_column($data['monthly_stock'], "issuedate"), SORT_ASC, $data['monthly_stock'] );


		 ?>
	<script type="text/javascript">
		var chart = new CanvasJS.Chart("chartContainer", {
			axisY:{
		   viewportMinimum: 0,
		   viewportMaximum: 20,
		   title: "ٹننیج" ,
		   interval: 2         
		
		 },
		axisX:{
		  title : "دن",
		  interval: 1

		 },
		data: [
		{
			// Change type to "doughnut", "line", "splineArea", etc.
			type: "line",
			dataPointWidth: 20,
			click: onClick,
			dataPoints: [
				<?php foreach ($data['monthly_stock'] as $key => $value) { 
					$timestamp = strtotime($value['issuedate'])
					?>

				{ label:'<?php echo date("d", $timestamp);  ?>', y: <?php echo $value['totala']; ?>  },
			<?php } ?>

			]
		}
		]
	});
	chart.render();

	function onClick(e) {

  	var month =$('#chart_month').val();
  	var year =$('#chart_year').val();

    var url = "<?php echo SURL ?>SaleDateReport/item_report_detail";
	var form = $('<form target="_blank" action="' + url + '" method="post">' +
 
  	'<input type="hidden" name="day" value="' + e.dataPoint.x + '" />' +
  	'<input type="hidden" name="month" value="' +month + '" />' +
  	'<input type="hidden" name="year" value="' +year+ '" />' +

		  '</form>');
		$('body').append(form);
		form.submit();
	}

</script>

<?php
 }

	public function edit ($rid)
	{
		$data['restaurant'] = $this->mod_restaurant->edit_record($rid);
		if(empty($data['restaurant']))
		{
 			$this->session->set_flashdata('err_message', '-recode Restaurant not exist!');			
			redirect(SURL.'restaurant');
		}
		//pm($data['restaurant']);
    	$table='tbl_country';       
        $data['country_list'] = $this->mod_common->get_all_records($table,"*");    	
        
        $where_id = array('country_id' => $data['restaurant']['restaurant_country']);

        $table='tbl_city';       
        $data['city_list']= $this->mod_common->select_array_records($table,"*",$where_id);


        $data['form_data'] = 'sss';
	  	$this->load->view($this->session->userdata('language')."/role/edit", $data);
	}
	public function detail($rid) {
			#---------- detail restaurant record---------------#
		$where = array('restaurant_id' =>$rid);
		$data['restaurant'] =  $this->mod_restaurant->select_single_restaurant($where);
		if(empty($data['restaurant']))
		{
 			$this->session->set_flashdata('err_message', '-recode Restaurant not exist!');			
			redirect(SURL.'company');
		}
	  	$this->load->view('company/detail', $data);
    }
	public function update($rid) {
        #------------- if post--------------#
        if ($this->input->post("update_restaurant_submit")) {
			#---------- update restaurant record---------------#
			 $update_restaurant =  $this->mod_restaurant->update_restaurant($this->input->post());
            
				if ($update_restaurant) {
					$this->session->set_flashdata('ok_message', '- restaurant updated successfully!');
					redirect(SURL . 'company');
				} else {
                $this->session->set_flashdata('err_message', '- Error in adding restaurant please try again!');
                redirect(SURL . 'company/edit/'.$rid);
            	}
        }

    }
    public function changeStatus($id){
		$status = 1 - $this->input->post('status');
		$update_data = array("status"=>$status,"approve_date"=>date('Y-m-d'),"approve_by"=>$this->session->userdata('id'),"approve_time"=>date('h:i:s'));
		$where = array("restaurant_id"=>$id);
		
		$update = $this->mod_common->update_table('tbl_resturant_reg',$where,$update_data);
			
			$this->session->set_flashdata('ok_message', 'Status changed successfully!');
			 redirect(SURL . 'company/');
	}
	public function delete($id) {
		#-------------delete record--------------#
        $table = "tbl_resturant_reg";
        $where = "restaurant_id = '" . $id . "'";
        $delete_restaurant = $this->mod_common->delete_record($table, $where);

        if ($delete_restaurant) {
            $this->session->set_flashdata('ok_message', '- Restaurant deleted successfully!');
            redirect(SURL . 'company/');
        } else {
            $this->session->set_flashdata('err_message', '- Error in deleteting Restaurant please try again!');
            redirect(SURL . 'company/');
        }
    }
	function email_exist()
	{
	    $table='tbl_resturant_reg';
		$email=	$this->input->post('email');
		$where = array('restaurant_email' => $email);
		$data['restaurant_list'] = $this->mod_common->select_array_records($table,"restaurant_email",$where);
		if (!empty($data['restaurant_list'])) {
			echo '1';
		 	exit;
		 }
		 else {
		 	echo '0';
		 	exit;
		 }
	}
	function website_exist()
	{
	    $table='tbl_resturant_reg';
		$website_name=	$this->input->post('website_name');
		$where = array('restaurant_website_name' => $website_name);
		$data['restaurant_list'] = $this->mod_common->select_array_records($table,"restaurant_website_name",$where);
		if (!empty($data['restaurant_list'])) {
			echo '1';
		 	exit;
		 } 
		 else {
		 	echo '0';
		 	exit;
		 }
	}

	function get_city()
	{
	    $table='tbl_city';
		$country_id=	$this->input->post('country_id');
		$where = array('country_id' => $country_id);
		$data['city_list'] = $this->mod_common->select_array_records($table,"*",$where);

		foreach ($data['city_list'] as $key => $value) {
			?>
			<option value="<?php echo  $value['city_id']; ?>"><?php echo  $value['city_name']; ?></option>
			
		<?php }
		
	}


	function get_stock()
	{
		$data['report']=  $this->mod_admin->getcurrent_stock($this->input->post());
		//pm($data['report']);
		foreach ($data['report'] as $key => $value) {
			//pm($value);
		 	//echo $value['filled'];
		 	//print $value;
		 	echo json_encode($value);
		}
		
	}

	public function cancel($id) {
		#-------------cancel order--------------#
		$ins_array = array(
		     "status" =>"cancelled", 
		);
        $table = "tbl_orderbooking";
        $where = "id = '" . $id . "'";
        $delete = $this->mod_common->update_table($table,$where,$ins_array);

        if ($delete) {
            $this->session->set_flashdata('ok_message', 'You have succesfully cancel the order.');
            redirect(SURL . 'admin/');
        } else {
            $this->session->set_flashdata('err_message', 'Operation Failed!');
            redirect(SURL . 'admin/');
        }
    }

}