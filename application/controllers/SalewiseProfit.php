<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SalewiseProfit extends CI_Controller {


	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_salereport","mod_common","mod_customerstockledger","mod_customer","mod_salelpg"
        ));
        
    }

	public function index()
	{

		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
        $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

        if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Profit B/W Date Report";	
		$this->load->view($this->session->userdata('language')."/SalewiseProfit/search_report_item",$data);

	}


	public function report()
	{																			
		//pm($this->input->post());
		$data['from'] = $this->input->post("from_date"); 
		$data['to'] = $this->input->post("to_date");
		$data['sale_point_id'] = $this->input->post("location");
		$data['sale_type'] = $this->input->post("sale_type");

		$data['daterange'] = $data['from']."-".$data['to'];

		$explodedate = explode("-", $data['from']);
		$data['year'] = $explodedate['0'];
		$data['mnth'] = $explodedate['1']; 
		

		$this->load->view($this->session->userdata('language')."/SalewiseProfit/detail_report_item",$data);
	}
	public function newpdf(){

		if($this->input->server('REQUEST_METHOD') == 'POST'){
     
      $data['from'] = $this->input->post("from"); 

		$data['to'] = $this->input->post("to");
		$data['sale_point_id'] = $this->input->post("sale_point_id");
	
		$data['sale_type'] = $this->input->post("sale_type");

		$data['daterange'] = $data['from']."-".$data['to'];
	
		$explodedate = explode("-", $data['from']);
		$data['year'] = $explodedate['0'];
		$data['mnth'] = $explodedate['1']; 
		
		// pm($data['report']);
			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
			
		
			
	    }

	  
	    	 $profilename =  $from_date;
	    	 // $profilename1 =  $to_date;
	    	 // $profilename2 =  $type;
	  
	  //pm($data);


		$this->load->view($this->session->userdata('language')."/SalewiseProfit/pdffile",$data);

		$this->load->library('pdf');
			 $html = $this->output->get_output();
			 $this->dompdf->loadHtml($html);
			 $this->dompdf->setPaper('A4', 'landscape');
	        $this->dompdf->render();


	        
	        $this->dompdf->stream( $profilename.".pdf", array("Attachment"=>0));	
	}

	public function newmethod(){

		$from = "2020-03-01";
		$to = "2020-04-30";
		set_time_limit(0);

		$this->db->trans_start();
		$sales = $this->db->query("select * from tbl_issue_goods_detail inner join tblmaterial_coding on tbl_issue_goods_detail.itemid=tblmaterial_coding.materialcode inner join tbl_issue_goods on tbl_issue_goods.issuenos=tbl_issue_goods_detail.ig_detail_id where tblmaterial_coding.catcode='1' and tbl_issue_goods.type='Fill' and tbl_issue_goods.issuedate between '$from' and '$to'")->result_array();

		if(!empty($sales)){
			foreach ($sales as $key => $value){
					$totalsaledamt = $value['qty']*$value['sprice'];

					$purchasequery = $this->db->query("SELECT * FROM `tbl_goodsreceiving_detail` where batch_status='0' and itemid='".$value['itemid']."' order by receipt_id asc limit 1")->result_array()[0];

					if($purchasequery['Batch_stock']>$value['qty']){
						$batch_stock_left = $purchasequery['Batch_stock']-$value['qty'];

						$purchase_batch_no = $purchasequery['receipt_id'];
						$this->db->query("update tbl_goodsreceiving_detail set Batch_stock='$batch_stock_left' where receipt_id='".$purchasequery['receipt_id']."'");

						$totalpurchasedamt = $value['qty']*$purchasequery['rate'];

					}else if($purchasequery['Batch_stock']==$value['qty']){
						$purchase_batch_no = $purchasequery['receipt_id'];
						$this->db->query("update tbl_goodsreceiving_detail set batch_status='1',Batch_stock='0' where receipt_id='".$purchasequery['receipt_id']."'");

						$totalpurchasedamt = $value['qty']*$purchasequery['rate'];

					}else{
						$halfamt=0;
						$sale_Qty_left = $value['qty']-$purchasequery['Batch_stock'];
						$this->db->query("update tbl_goodsreceiving_detail set batch_status='1',Batch_stock='0' where receipt_id='".$purchasequery['receipt_id']."'");

						$halfamt = $purchasequery['Batch_stock']*$purchasequery['rate'];
						$purchase_batch_no = $purchasequery['receipt_id'];

						$loop=2;
						while(1<$loop){
								

							$purchasequery = $this->db->query("select * from tbl_goodsreceiving_detail where batch_status='0' and itemid='".$value['itemid']."' and receipt_id > '".$purchasequery['receipt_id']."' order by receipt_id asc limit 1")->result_array()[0];

							if($sale_Qty_left>$purchasequery['Batch_stock']){

								$sale_Qty_left = $sale_Qty_left - $purchasequery['Batch_stock'];

								$this->db->query("update tbl_goodsreceiving_detail set batch_status='1',Batch_stock='0' where receipt_id='".$purchasequery['receipt_id']."'");

								$halfamt = $halfamt + ($purchasequery['Batch_stock']*$purchasequery['rate']);
								$purchase_batch_no = $purchase_batch_no.",".$purchasequery['receipt_id'];

							}else if($sale_Qty_left==$purchasequery['Batch_stock']){

								$this->db->query("update tbl_goodsreceiving_detail set batch_status='1',Batch_stock='0' where receipt_id='".$purchasequery['receipt_id']."'");

								$halfamt = $halfamt + ($purchasequery['Batch_stock']*$purchasequery['rate']);
								$loop=0;
								$purchase_batch_no = $purchase_batch_no.",".$purchasequery['receipt_id'];

							}else{
								
								$Batch_stock_left = $purchasequery['Batch_stock'] - $sale_Qty_left;
									$this->db->query("update tbl_goodsreceiving_detail set batch_status='0',Batch_stock='$Batch_stock_left' where receipt_id='".$purchasequery['receipt_id']."'");

								$halfamt = $halfamt + ($sale_Qty_left*$purchasequery['rate']);
								
								$loop=0;

								$purchase_batch_no = $purchase_batch_no.",".$purchasequery['receipt_id'];
							}

						}

						$totalpurchasedamt = $halfamt;
					}

					$this->db->query("update tbl_issue_goods_detail set purchase_batch_no='$purchase_batch_no',purchase_amt='$totalpurchasedamt' where ig_detail_id='".$value['ig_detail_id']."'");
					$totalprofit = $totalprofit + ($totalsaledamt-$totalpurchasedamt);

			}
		}
		$this->db->trans_complete();

	}

}
