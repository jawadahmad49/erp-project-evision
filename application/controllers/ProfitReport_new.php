<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ProfitReport_new extends CI_Controller {

	public function __construct() {
        parent::__construct();
        error_reporting(E_ALL);
        $this->load->model(array(
            "mod_profitreport","mod_common","mod_admin"
        ));
        
    }

	public function index()
	{   // error_reporting(E_ALL);


	ini_set('memory_limit','2048M');

	



		$table='tbltrans_detail';
		$data['stock_report_list'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Profit Loss Report";	
		$table='tblcategory';       
        $data['category_list'] = $this->mod_common->get_all_records($table,"*");
        $this->load->view($this->session->userdata('language')."/profit_report_new/search",$data);       	
	}


	public function detail_report()
	{							
		//pm($this->input->post("checkdate"));

		if(!empty($this->input->post("checkdate"))){
			$from = $this->input->post("from");
			$to = $this->input->post("to");
		}else{
			if($this->input->post("month") == "January"){
				$mnth = "01";
			}else if($this->input->post("month") == "February"){
				$mnth = "02";
			}
			else if($this->input->post("month") == "March"){
				$mnth = "03";
			}
			else if($this->input->post("month") == "April"){
				$mnth = "04";
			}
			else if($this->input->post("month") == "May"){
				$mnth = "05";
			}
			else if($this->input->post("month") == "June"){
				$mnth = "06";
			}
			else if($this->input->post("month") == "July"){
				$mnth = "07";
			}
			else if($this->input->post("month") == "August"){
				$mnth = "08";
			}
			else if($this->input->post("month") == "September"){
				$mnth = "09";
			}
			else if($this->input->post("month") == "October"){
				$mnth = "10";
			}
			else if($this->input->post("month") == "November"){
				$mnth = "11";
			}
			else if($this->input->post("month") == "December"){
				$mnth = "12";
			}

			$from = $this->input->post("year")."-".$mnth."-"."01";
			$to = date("Y-m-t",strtotime($from));
			

		}
		
		//$data['profitreport'] = $this->mod_common->custom($from,$to);

		$data['period'] = $from." to ".$to;
		$data['logo'] = $this->db->query("select logo from tbl_company")->result_array()[0]['logo'];
		//sale query starts here
		$salquery =  $this->db->query("SELECT SUM(sprice*qty) as totalamount,sum(qty*itemnameint/1000) as saleqty FROM `tbl_issue_goods` INNER join tbl_issue_goods_detail on tbl_issue_goods.issuenos=ig_detail_id inner join `tblmaterial_coding` ON `tbl_issue_goods_detail`.`itemid` = `tblmaterial_coding`.`materialcode` WHERE tbl_issue_goods.type='Fill' and issuedate BETWEEN '$from' and '$to'")->result_array()[0];
		//pm($salquery);

		$returnquery = $this->db->query("select sum(tbl_issue_return_detail.qty*itemnameint/1000) as returnqty,sum(tbl_issue_return_detail.total_amount) as returnamount from tblmaterial_coding inner join tbl_issue_return_detail on materialcode=tbl_issue_return_detail.itemid inner join tbl_issue_return on tbl_issue_return.irnos=tbl_issue_return_detail.irnos where tblmaterial_coding.catcode=1 and tbl_issue_return_detail.type='Filled' and tbl_issue_return.type='salereturn' and irdate BETWEEN '$from' and '$to'")->result_array()[0];

		$data['saleqty'] = $salquery['saleqty'] - $returnquery['returnqty'];
		$data['totalsaleamt'] = $salquery['totalamount'] - $returnquery['returnamount'];
		
		$sql = "select sum((sprice*qty)-purchase_amt) as profit from tbl_issue_goods_detail inner join tblmaterial_coding on tbl_issue_goods_detail.itemid=tblmaterial_coding.materialcode inner join tbl_issue_goods on tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id where tblmaterial_coding.catcode='1' and tbl_issue_goods.type='Fill' and tbl_issue_goods.issuedate between '$from' and '$to'";

	    $data['profitreport'] = $this->db->query($sql)->result_array()[0]['profit'];
	   
	    $data['totalclosingamt'] = $this->db->query("select * from close_profit where month='$mnth' and year='".$this->input->post("year")."'")->result_array()[0]['closingstockamt'];
	   $newmnth = $mnth-1;
	    $data['openingvalue'] = $this->db->query("select * from close_profit where month='$newmnth' and year='".$this->input->post("year")."'")->result_array()[0]['closingstockamt'];

		$purchasequery = $this->db->query("SELECT * FROM `tbl_goodsreceiving_detail` where batch_status='1' and itemid='".$value['itemid']."' and type='Filled' order by receipt_id asc limit 1")->result_array()[0];


		// opening stock start here
			$data['opening_stock'] =  $this->opening_stock($from);
			//pm($data['opening_stock']);
			if($data['opening_stock'] <= 0){
				$data['openingpurchaserate'] = 0;
			}else{
				$data['openingpurchaserate'] = $this->purchaserate("2019-01-01",$from);
			}
		// opening stock ends here

		//month purchasing start here
		 $monthpurchases = $this->db->query("SELECT sum(tbl_goodsreceiving_detail.quantity *itemnameint/1000) as qty,SUM(inc_vat_amount) as totalamount FROM `tbl_goodsreceiving` inner join tbl_goodsreceiving_detail on tbl_goodsreceiving.receiptnos=tbl_goodsreceiving_detail.receipt_detail_id INNER join tblmaterial_coding on materialcode=tbl_goodsreceiving_detail.itemid WHERE catcode='1' and trans_typ='purchasefilled' and receiptdate BETWEEN '$from' and '$to'")->result_array()[0];


			 $data['monthpurchasesqty'] = $monthpurchases['qty']; 
			 $data['monthpurchasestotalamount'] = $monthpurchases['totalamount'];

		// month purchasing ends here

		//month purchasing start here
		
		 $data['netpurchaserate'] = $this->purchaserate("2019-01-01",$to);

		// month purchasing ends here	
		 $data_posted['from_date']=$from;
		 $data_posted['to_date']=$to;
		  $data['from_date']=$from;
		 $data['to_date']=$to;

		$data['expenses']=  $this->mod_profitreport->getpayments_new_old($data_posted,2)->expense;
		$data['otherincome']=  $this->mod_profitreport->get_income($data_posted,2)->income;

		$this->load->view($this->session->userdata('language')."/profit_report_new/detail_report",$data);
	        

	         
	}

		public function detail_reports()
	{							
		//pm($this->input->post('from_date'));
		$to_data = $this->input->post('to_date');
		$from_date = $this->input->post('from_date');
		$data['report'] = $this->db->query("select tbl_issue_goods_detail.*,tbl_issue_goods.*,tbl_issue_goods_detail.total_amount as to_am,tblmaterial_coding.itemname from tbl_issue_goods_detail  inner join tblmaterial_coding on tblmaterial_coding.materialcode = tbl_issue_goods_detail.itemid 
			inner join tbl_issue_goods on tbl_issue_goods_detail.ig_detail_id = tbl_issue_goods.issuenos 
		where tbl_issue_goods.issuedate<='$to_data' and tbl_issue_goods.issuedate >= '$from_date'")->result_array();	
		//pm($data['report']);
		$data["title"] = "Profit Loss Report";	
		$data["from_date"] = $this->input->post('from_date');	
		$data["to_date"] = $this->input->post('to_date');	

		
		$this->load->view($this->session->userdata('language')."/profit_report_new/detail",$data);



}
	public function getrec($id){
		echo $id;
	}
	public function purchaserate($from,$to){

		$tankvaluee=0;
		$totaltnkqtyy=0;
		
		$Sql = "select tbl_shop_opening.*,tblmaterial_coding.itemnameint from tbl_shop_opening  inner join tblmaterial_coding on tblmaterial_coding.materialcode = tbl_shop_opening.materialcode where tbl_shop_opening.type ='Filled' and date < '$to'";



		$tbltankquery = $this->db->query($Sql)->result_array();
		//pm($tbltankquery->result_array());
		foreach ($tbltankquery as $key2 => $value2) {
			$totaltnkqtyy +=  (($value2['itemnameint'] * $value2['qty']))/1000;
			if($value2['cost_price'] != ""){
				$tankvaluee =  $value2['cost_price'];
			}
			
		}


		$query = $this->db->query("SELECT sum(tbl_goodsreceiving_detail.quantity *itemnameint/1000) as qty,SUM(inc_vat_amount) as totalamount FROM `tbl_goodsreceiving` inner join tbl_goodsreceiving_detail on tbl_goodsreceiving.receiptnos=tbl_goodsreceiving_detail.receipt_detail_id INNER join tblmaterial_coding on materialcode=tbl_goodsreceiving_detail.itemid WHERE catcode='1' and trans_typ='purchasefilled' and receiptdate < '$to'")->result_array()[0];

		

		
		$purchaseamt = $tankvaluee+$query['totalamount'];
		$purchaseqty = $totaltnkqtyy+$query['qty'];
		 $rate = $purchaseamt/$purchaseqty;

		return $rate;
	}




	public function opening_stock($last_date) {		
			
          //pm($last_date);

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
		
		
		}	//pm($total_tonnage);exit;
		return ($total_tonnage); 
		//pm($total_tonnage);
	 
	
    }



	

}

?>
