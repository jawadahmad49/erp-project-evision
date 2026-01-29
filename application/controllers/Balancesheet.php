<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Balancesheet extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_profitreport","mod_common","mod_admin"
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
	{							
		//pm($this->input->post());
		$data['daterange'] = "2019-01-01 to ".$this->input->post("fdate");
		$data['from_date'] = "2019-01-01";
		$data['to_date'] = $this->input->post("fdate");

		 $data['purchasefilled']=$this->filled_purchases($data);

		$data['emptyfilled']=$this->empty_purchases($data);
		 $data['purchaseother']=$this->purchases_other($data);

	   $data['netprofit']= (-1)*$this->netprofit($data);

		$this->load->view($this->session->userdata('language')."/balancesheet/detail_report",$data);
	        

	         
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

?>
