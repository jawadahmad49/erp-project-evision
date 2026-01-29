<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trialbalance extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_customerledger","mod_trialbalance","mod_common","mod_profitreport","mod_customer","mod_customerstockledger","mod_salelpg","mod_vendorledger"
        ));
        
    }
	public function index()
	{
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();

		$table='tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
//	pm( $data);
		$data["title"] = "Customer Ledger";	
		$this->load->view($this->session->userdata('language')."/trialbalance/trial_balance",$data);
	}


	public function detail(){
		
 //pm($_POST);
		//$data['response'] = $this->mod_trialbalance->get_report_data($_POST);
		$data['from'] = $this->input->post("fdate");
        $data['to'] = $this->input->post("tdate");

        $data['from_date'] = $data['from'];
		$data['to_date'] = $data['to'];


        $data['daterange'] = $this->input->post("fdate")." to ".$this->input->post("tdate");

       $data['purchasefilled']=$this->filled_purchases($data); 

		// $data['emptyfilled']=$this->empty_purchases($data);
		$data['purchaseother']=$this->purchases_other($data);

	    $data['netprofit']= $this->netprofit($data);



		
		$this->load->view($this->session->userdata('language')."/trialbalance/detail",$data);
	}
	public function ledger_report($id=''){
		
	

		if($this->input->server('REQUEST_METHOD') == 'POST' || $id !=''){
			
			$data['one']=2;
			$data['report']=  $this->mod_vendorledger->get_report($this->input->post(),$id);
			if($id !='')
			{
				$data['one']=1;
			}

			if($this->input->post('t_id'))
			{
				$count=1;foreach ($data['report'] as $key => $value) { 
			
				if(!$value['voucherno']){continue;}

					 $total_opngbl=$value['balance']; 
						 
					$total_debit+=$value['debit'];
					$total_credit+=$value['credit'];

		
					 $count++;} 

					$total_opngbl =str_replace(",", "", $total_opngbl);

					 if($this->input->post('edit_amount'))
					{
					  $total_opngbl=$total_opngbl+$this->input->post('edit_amount'); 
					}

					echo $total_opngbl;

					if(($total_opngbl)>0){echo  ' Dr';}else{ echo ' Cr';}

					echo '|';
					echo $total_opngbl;
					echo '|';


					if(($total_opngbl)>0){echo  'Dr';}else{ echo 'Cr';}

					exit();
				}





			//pm($data['report']); exit;
			#----check name already exist---------#
			// if ($this->mod_city->get_by_title($data['city_name'])) {
			// 	$this->session->set_flashdata('err_message', 'Name Already Exist.');
			// 	redirect(SURL . 'city/add_city');
			// 	exit();
			// }
			//pm($data);
			$table='tbl_company';
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
			if ($data['report']) {
			 	//$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            //redirect(SURL . 'vendorledger/detail',$data);
	            $data["title"] = " Ledger Report";
	            $this->load->view($this->session->userdata('language')."/vendorledger/single",$data);
	        } else {
	            //$this->session->set_flashdata('err_message', 'No Record Found.');
	            //redirect(SURL . 'vendorledger/');
	            $data["title"] = " Ledger Report";
	            $this->load->view($this->session->userdata('language')."/vendorledger/single",$data);
	        }
	    }else{
	        //$data["filter"] = 'add';
	        $data["title"] = " Ledger Report";    			
			$this->load->view($this->session->userdata('language')."/vendorledger/single",$data);
		}
	}

	public function empty_purchases($data){ 

			$query = "select sum(net_payable) as amount from tbl_goodsreceiving where trans_typ='purchaseempty' and tbl_goodsreceiving.receiptdate between '".$data['from_date']."' and '".$data['to_date']."'";	
		 
			return $result = $this->db->query($query)->result_array()[0]['amount'];

	}

	public function netprofit($data){

		$salquery =  $this->db->query("SELECT SUM(sprice*qty) as totalamount,sum(qty*itemnameint/1000) as saleqty FROM `tbl_issue_goods` INNER join tbl_issue_goods_detail on tbl_issue_goods.issuenos=ig_detail_id inner join `tblmaterial_coding` ON `tbl_issue_goods_detail`.`itemid` = `tblmaterial_coding`.`materialcode` WHERE tbl_issue_goods.type='Fill' and issuedate 
			BETWEEN '".$data['from_date']."' and '".$data['to_date']."'")->result_array()[0];

		$returnquery = $this->db->query("select sum(tbl_issue_return_detail.qty*itemnameint/1000) as returnqty,sum(tbl_issue_return_detail.total_amount) as returnamount from tblmaterial_coding inner join tbl_issue_return_detail on materialcode=tbl_issue_return_detail.itemid inner join tbl_issue_return on tbl_issue_return.irnos=tbl_issue_return_detail.irnos where tblmaterial_coding.catcode=1 and tbl_issue_return_detail.type='Filled' and tbl_issue_return.type='salereturn' and irdate BETWEEN '".$data['from_date']."' and '".$data['to_date']."'")->result_array()[0];

		$cost_of_sales = $salquery['saleqty'] - $returnquery['returnqty'];


		$totalsaleamt = $salquery['totalamount'] - $returnquery['returnamount'];


		 $netpurchaserate = $this->purchaserate($data['from_date'],$data['to_date']);

		 $costofsaleamt = $netpurchaserate * $cost_of_sales;

		$grosprofit = $totalsaleamt - $costofsaleamt;


		$data_posted['from_date']=$data['from_date'];
		$data_posted['to_date']=$data['to_date'];

		$expenses=  $this->mod_profitreport->getpayments_new_old($data_posted,2)->expense;
		$otherincome =  $this->mod_profitreport->get_income($data_posted,2)->income;

		return $net_profit= $grosprofit - $expenses + $otherincome;
	}




	public function purchases_other($data){ 


		 $query = "select (sum(net_payable)/sum(quantity)) as purchaserate,sum(quantity) as qty from tbl_goodsreceiving inner join tbl_goodsreceiving_detail on receiptnos=receipt_detail_id where trans_typ='purchaseother' and tbl_goodsreceiving.receiptdate between '".$data['from_date']."' and '".$data['to_date']."'";	


		 $sale_other_query = "select sum(qty) as qty from tbl_issue_goods inner join tbl_issue_goods_detail on issuenos=ig_detail_id inner join tblmaterial_coding on tblmaterial_coding.materialcode = tbl_issue_goods_detail.itemid where tblmaterial_coding.catcode='10' and tbl_issue_goods.issuedate between '".$data['from_date']."' and '".$data['to_date']."'";	
		 //pm( $this->db->query($query)->result_array());
		  $purchaserate = $this->db->query($query)->result_array()[0]['purchaserate'];
		  $qty = $this->db->query($query)->result_array()[0]['qty'];

		 $qty = $this->db->query($query)->result_array()[0]['qty'] - $this->db->query($sale_other_query)->result_array()[0]['qty'];
		 
		 return  ($purchaserate*$qty);

	}

	public function filled_purchases($data){

		$last_date = strtotime($data['to_date'])+86400;
		$last_date = date("Y-m-d",$last_date);
		$closingqty = $this->opening_stock($last_date);
		$closingpurchaserate = $this->purchaserate($data['from_date'],$data['to_date']);
		return $closingpurchaserate*$closingqty;

	}

	public function opening_stock($last_date) {		
			


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
						//print_r(($today_stock)); echo "<br>";
						
						 $empty_filled= explode('_', $today_stock);
						 $filled=$empty_filled[0] ;
						$total_tonnage+=($itemnameint*$filled)/1000;	
				 }
		
		
		}	
		return ($total_tonnage); 
		//pm($total_tonnage);
	 
	
    }

    public function purchaserate($from,$to){

		$tankvaluee=0;
		$totaltnkqtyy=0;
		
		$tbltankquery = $this->db->query("select tbl_shop_opening.*,tblmaterial_coding.itemnameint from tbl_shop_opening  inner join tblmaterial_coding on tblmaterial_coding.materialcode = tbl_shop_opening.materialcode where tbl_shop_opening.type ='Filled' and date BETWEEN '$from' and '$to'");
		//pm($tbltankquery->result_array());
		foreach ($tbltankquery->result_array() as $key2 => $value2) {
			$totaltnkqtyy +=  (($value2['itemnameint'] * $value2['qty']))/1000;
			if($value2['cost_price'] != ""){
				$tankvaluee =  $value2['cost_price'];
			}
			
		}


		$query = $this->db->query("SELECT sum(tbl_goodsreceiving_detail.quantity *itemnameint/1000) as qty,SUM(inc_vat_amount) as totalamount FROM `tbl_goodsreceiving` inner join tbl_goodsreceiving_detail on tbl_goodsreceiving.receiptnos=tbl_goodsreceiving_detail.receipt_detail_id INNER join tblmaterial_coding on materialcode=tbl_goodsreceiving_detail.itemid WHERE catcode='1' and trans_typ='purchasefilled' and receiptdate BETWEEN '$from' and '$to'")->result_array()[0];
		
		$purchaseamt = $tankvaluee+$query['totalamount'];
		$purchaseqty = $totaltnkqtyy+$query['qty'];
		return $purchaseamt/$purchaseqty;
	}




}
