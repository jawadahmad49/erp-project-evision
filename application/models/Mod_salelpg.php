<?php

class Mod_salelpg extends CI_Model
{

	function __construct()
	{

		parent::__construct();
		error_reporting(0);
	}

	public function add_sale_lpg($data)
	{
		// pm($data);exit();
		$stockleft = "";
		$newstockleft = "";
		date_default_timezone_set("Asia/Karachi");
		$today = date('Y-m-d h:i:sa');
		$uid = $this->session->userdata('id');
		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		//my code to get left stock starts from here

		$z = 0;

		foreach ($data['item'] as $value) {




			$id = $value;
			$date = date('Y-m-d');
			$cate_id = 5;



			$where_item = "materialcode = '" . $id . "'";

			$item_value = $this->mod_common->select_single_records('tblmaterial_coding', $where_item);

			if (!empty($item_value)) {
				$cate_id = $item_value['catcode'];
			}


			$today_stock = $this->mod_common->stock($id, 'empty', $date, 1) . '_' . $item_value['itemnameint'] . '_' . $item_value['catcode'];
			$stockleft = explode("_", $today_stock);
			$totalstockleft = $stockleft[0];
			$newstockleft[] = $stockleft[0];



			$this->db->select('*');
			$this->db->from('tbl_goodsreceiving_detail');
			$this->db->where("itemid = $id");
			$this->db->where("sale_point_id = $sale_point_id");
			$this->db->order_by("recvd_date", "desc");
			$query = $this->db->get();
			$goodreceingdetails = $query->result();

			foreach ($goodreceingdetails as $newvalue) {

				if ($totalstockleft <= $newvalue->quantity) {
					$data['cost_of_Sale'][] = $newvalue->rate;
					$data['total_cost_of_Sale'][] = $newvalue->rate * $data['qty'][$z];
					break;
				} else {

					$totalstockleft = $totalstockleft - $newvalue->quantity;
				}
			}

			$z++;
			//echo "<pre>";var_dump($data);

		}
		$fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
		$cash_code = $fix_code['cash_code'];
		$trans_id = $data['trans_id'];
		if ($trans_id == '') {

			$trans_id = $this->db->query("select max(trans_id) as trans_id from tbl_issue_goods where sale_point_id='$sale_point_id'")->row_array()['trans_id'];

			if ($trans_id == '') {
				$trans_id = 1;
			} else {
				$trans_id = $trans_id + 1;
			}
		}

		$this->db->trans_start();
		$sagment = $data['segment'];
		if ($sagment == 'walkin' || $sagment == 'home') {

			$d_customer = $data['d_customer'];
		} else {
			$d_customer = 0;
		}


		$ins_array = array(
			"issuedto" => $data['customer'],
			"issuedate" => $data['date'],
			"remarks" => $data['remarks'],
			"sale_type" => $data['saletype'],
			"security_amt" => $data['securityamt'],
			"cylinder_sale_amt" => $data['cylinder_sale_amt'],
			"scode" => $data['scode'],
			"delivery_charges" => $data['delivery_charges'],
			"vat_percentage" => $data['gstp'],
			"vat_amount" => $data['vat_amount'],
			"inc_vat_amount" => $data['inc_vat_amount'],
			"gas_amt" => $data['gasamt'],
			"c_phone" => $data['cell'],
			"type" => 'Fill',
			"total_received" => $data['totalrecv'],
			"total_discount" => $data['total_discount'],
			"after_discount_amt" => $data['after_discount_amt'],
			"return_rate" => $data['return_rate'],
			"return_gas" => $data['return_gas'],
			"return_amount" => $data['return_amount'],
			"pay_mode" => $data['pay_mode'],
			"balance" => $data['balance'],
			"bank_code" => $data['bank_code'],
			"cheque_no" => $data['cheque_no'],
			"cheque_date" => $data['cheque_date'],
			"sale_point_id" => $sale_point_id,
			"trans_id" => $trans_id,
			"11_kg_price" => $data['kg_11_price'],
			"direct_customer" => $d_customer

		);
		//pm($ins_array);exit;
		if (!empty($this->input->post("id"))) {

			$trans_id = $data['trans_id'];
			$id = $_POST['id'];
			$table = "tbl_issue_goods";
			$where = "issuenos= '$id'";

			$update_goods = $this->mod_common->update_table($table, $where, $ins_array);



			if ($update_goods) {
				return $this->multipleitems_againstid($data, $id, $trans_id, 'tbl_issue_goods_detail', '34');
			} else {
				return false;
			}
		} else {
			$table = "tbl_issue_goods";
			//echo "<pre>";var_dump($ins_array); exit();
			$add_goods = $this->mod_common->insert_into_table($table, $ins_array);


			$insert_id = $add_goods;
			if ($add_goods) {
				return $this->multipleitems_againstid($data, $insert_id, $trans_id, 'tbl_issue_goods_detail');
			} else {
				return false;
			}
		}
	}
	public function closemonthwithscript($id)
	{

		set_time_limit(0);
		ini_set('memory_limit', '-1');


		//$sales = $this->db->query("select tbl_issue_goods_detail.*,tbl_issue_goods.issuedto from tbl_issue_goods_detail inner join tblmaterial_coding on tbl_issue_goods_detail.itemid=tblmaterial_coding.materialcode  where tbl_issue_goods_detail.ig_detail_id='$id' order by issuedate asc")->result_array();
		echo "select * from tbl_issue_goods_detail  where tbl_issue_goods_detail.ig_detail_id='$id'";
		exit;
		//pm($sales);exit;

		if (!empty($sales)) {
			foreach ($sales as $key => $value) {
				$totalsaledamt = $value['qty'] * $value['sprice'];
				$returnqty = 0;


				$purchasequery = $this->db->query("
						select * from
							(
								SELECT receiptdate,Batch_stock,receipt_id,rate,case
									when receipt_id > 0 then 'issuegoods'
									end as
									tablename
									FROM `tbl_goodsreceiving_detail`
									inner join tbl_goodsreceiving on
									tbl_goodsreceiving.receiptnos=tbl_goodsreceiving_detail.receipt_detail_id
									where
									batch_status='0' and itemid='" . $value['itemid'] . "' and
									type='Filled'

								union

								SELECT irdate as receiptdate,Batch_stock,sr_no as receipt_id,(total_amount/qty) as rate,case
								when tbl_issue_return_detail.sr_no > 0 then 'return'
								end as
								tablename
								 FROM `tbl_issue_return` inner join tbl_issue_return_detail on tbl_issue_return.irnos=tbl_issue_return_detail.irnos where tbl_issue_return_detail.itemid='" . $value['itemid'] . "' and batch_status='0' and tbl_issue_return_detail.type='Filled') as newtable order by receiptdate asc limit 1
						")->result_array()[0];

				if ($purchasequery['tablename'] == "issuegoods" || $purchasequery['tablename'] == "return") {
				} else {
					//pm($purchasequery);
				}


				if ($purchasequery['Batch_stock'] > $value['qty']) {
					$batch_stock_left = $purchasequery['Batch_stock'] - $value['qty'];
					$stocktaken = $value['qty'];



					if ($purchasequery['tablename'] == "issuegoods") {

						$this->db->query("update tbl_goodsreceiving_detail set Batch_stock='$batch_stock_left' where receipt_id='" . $purchasequery['receipt_id'] . "'");
						$purchase_batch_no = "IG-" . $purchasequery['receipt_id'];
					} else {
						$this->db->query("update tbl_issue_return_detail set Batch_stock='$batch_stock_left' where sr_no='" . $purchasequery['receipt_id'] . "'");
						$purchase_batch_no = "R-" . $purchasequery['receipt_id'];
					}


					$totalpurchasedamt = $value['qty'] * $purchasequery['rate'];
				} else if ($purchasequery['Batch_stock'] == $value['qty']) {

					if ($purchasequery['tablename'] == "issuegoods") {

						$this->db->query("update tbl_goodsreceiving_detail set batch_status='1',Batch_stock='0' where receipt_id='" . $purchasequery['receipt_id'] . "'");

						$purchase_batch_no = "IG-" . $purchasequery['receipt_id'];
					} else {
						$this->db->query("update tbl_issue_return_detail set batch_status='1',Batch_stock='0' where sr_no='" . $purchasequery['receipt_id'] . "'");

						$purchase_batch_no = "R-" . $purchasequery['receipt_id'];
					}

					$stocktaken = $value['qty'];

					$totalpurchasedamt = $value['qty'] * $purchasequery['rate'];
				} else {
					$halfamt = 0;
					$sale_Qty_left = $value['qty'] - $purchasequery['Batch_stock'];

					if ($purchasequery['tablename'] == "issuegoods") {

						$this->db->query("update tbl_goodsreceiving_detail set batch_status='1',Batch_stock='0' where receipt_id='" . $purchasequery['receipt_id'] . "'");
						$purchase_batch_no = "IG-" . $purchasequery['receipt_id'];
					} else {
						$this->db->query("update tbl_issue_return_detail set batch_status='1',Batch_stock='0' where sr_no='" . $purchasequery['receipt_id'] . "'");

						$purchase_batch_no = "R-" . $purchasequery['receipt_id'];
					}

					$halfamt = $purchasequery['Batch_stock'] * $purchasequery['rate'];

					$stocktaken = $purchasequery['Batch_stock'];

					$loop = 2;
					while (1 < $loop) {

						//starts here

						$purchasequery = $this->db->query("
							select * from
							(
								SELECT receiptdate,Batch_stock,receipt_id,rate,case
									when receipt_id > 0 then 'issuegoods'
									end as
									tablename
									FROM `tbl_goodsreceiving_detail`
									inner join tbl_goodsreceiving on
									tbl_goodsreceiving.receiptnos=tbl_goodsreceiving_detail.receipt_detail_id
									where
									batch_status='0' and itemid='" . $value['itemid'] . "' and
									type='Filled'

								union

								SELECT irdate as receiptdate,Batch_stock,sr_no as receipt_id,(total_amount/qty) as rate,case
								when tbl_issue_return_detail.sr_no > 0 then 'return'
								end as
								tablename
								 FROM `tbl_issue_return` inner join tbl_issue_return_detail on tbl_issue_return.irnos=tbl_issue_return_detail.irnos where tbl_issue_return_detail.itemid='" . $value['itemid'] . "' and batch_status='0' and tbl_issue_return_detail.type='Filled') as newtable order by receiptdate asc limit 1
							")->result_array()[0];

						if ($purchasequery['tablename'] == "issuegoods" || $purchasequery['tablename'] == "return") {
						} else {
							//pm($purchasequery);
						}

						//ends here

						if ($sale_Qty_left > $purchasequery['Batch_stock']) {

							$sale_Qty_left = $sale_Qty_left - $purchasequery['Batch_stock'];

							$stocktaken = $stocktaken . "," . $purchasequery['Batch_stock'];

							if ($purchasequery['tablename'] == "issuegoods") {

								$this->db->query("update tbl_goodsreceiving_detail set batch_status='1',Batch_stock='0' where receipt_id='" . $purchasequery['receipt_id'] . "'");

								$purchase_batch_no = $purchase_batch_no . ",IG-" . $purchasequery['receipt_id'];
							} else {
								$this->db->query("update tbl_issue_return_detail set batch_status='1',Batch_stock='0' where sr_no='" . $purchasequery['receipt_id'] . "'");

								$purchase_batch_no = $purchase_batch_no . ",R-" . $purchasequery['receipt_id'];
							}



							$halfamt = $halfamt + ($purchasequery['Batch_stock'] * $purchasequery['rate']);
						} else if ($sale_Qty_left == $purchasequery['Batch_stock']) {


							if ($purchasequery['tablename'] == "issuegoods") {

								$this->db->query("update tbl_goodsreceiving_detail set batch_status='1',Batch_stock='0' where receipt_id='" . $purchasequery['receipt_id'] . "'");

								$purchase_batch_no = $purchase_batch_no . ",IG-" . $purchasequery['receipt_id'];
							} else {
								$this->db->query("update tbl_issue_return_detail set batch_status='1',Batch_stock='0' where sr_no='" . $purchasequery['receipt_id'] . "'");

								$purchase_batch_no = $purchase_batch_no . ",R-" . $purchasequery['receipt_id'];
							}



							$halfamt = $halfamt + ($purchasequery['Batch_stock'] * $purchasequery['rate']);
							$loop = 0;

							$stocktaken = $stocktaken . "," . $purchasequery['Batch_stock'];
						} else {

							$Batch_stock_left = $purchasequery['Batch_stock'] - $sale_Qty_left;

							if ($purchasequery['tablename'] == "issuegoods") {

								$this->db->query("update tbl_goodsreceiving_detail set batch_status='0',Batch_stock='$Batch_stock_left' where receipt_id='" . $purchasequery['receipt_id'] . "'");

								$purchase_batch_no = $purchase_batch_no . ",IG-" . $purchasequery['receipt_id'];
							} else {
								$this->db->query("update tbl_issue_return_detail set batch_status='0',Batch_stock='$Batch_stock_left' where sr_no='" . $purchasequery['receipt_id'] . "'");

								$purchase_batch_no = $purchase_batch_no . ",R-" . $purchasequery['receipt_id'];
							}

							$halfamt = $halfamt + ($sale_Qty_left * $purchasequery['rate']);

							$loop = 0;

							$stocktaken = $stocktaken . "," . $sale_Qty_left;
						}
					}

					$totalpurchasedamt = $halfamt;
				}

				$this->db->query("update tbl_issue_goods_detail set purchase_batch_no='$purchase_batch_no',purchase_amt='$totalpurchasedamt',qty_taken='$stocktaken' where srno='" . $value['srno'] . "'");
				$totalprofit = $totalprofit + ($totalsaledamt - $totalpurchasedamt);
			}
		}
	}

	public function add_sale_lpg_new($data)
	{
		$ins_array = array(
			"issuedto" => $data['customer'],
			"issuedate" => $data['date'],
			"remarks" => $data['remarks'],
			"sale_type" => 'direct',
			"gas_amt" => $data['totalrecv'],
			"type" => 'Fill',
			"total_received" => $data['totalrecv'],
			"total_received" => $data['totalrecv'],
			"total_discount" => $data['total_discount'],
			"after_discount_amt" => $data['amount_payed'],
			"c_phone" => $data['cell'],
			"pay_mode" => $data['pay_mode'],
			"balance" => $data['balance'],
			"bank_code" => $data['bank_code'],
			"cheque_no" => $data['cheque_no'],
			"cheque_date" => $data['cheque_date'],
			"total_amount" => $data['amounttotal']
		);

		$table = "tbl_issue_goods";
		$add_goods = $this->mod_common->insert_into_table($table, $ins_array);
		$insert_id = $add_goods;
		if ($add_goods) {
			return $this->add_multiple_items($data, $insert_id, 'tbl_issue_goods_detail');
		} else {
			return false;
		}
	}



	public function add_multiple_items($data, $goodsid, $table, $updated_value = '')
	{

		$tax_amount = 0;
		$netamount_ex = 0;

		$this->db->trans_start();

		$sale_code = '3001001001';
		$cash_inhand = '2003013001';
		$tax_acode = '1001003001';
		$security_code = '1001002001';

		$datas1 = array();

		if ($data['item'] != "") {
			$getCostPrice = $this->db->get_where("tblmaterial_coding", array("materialcode" => $data['item']))->row();
			$cost_price = $getCostPrice->cost_price;

			$datas1 = array(
				'ig_detail_id' => $goodsid,
				'itemid' => $data['item'],
				'item_return' => $data['item'],
				'qty' => $data['qty1'],
				'sprice' => $data['price1'],
				'returns' => $data['return_qty1'],
				'total_amount' => $data['total_p1'],
				'cost_price' => $cost_price,
				'Posted_Date' => $data['date'],
				'ex_vat_total_amount' => $data['total_p1'],
				'wrate' => '0.00'
			);

			$this->db->insert($table, $datas1);
		}


		if ($data['item2'] != "") {
			$getCostPrice = $this->db->get_where("tblmaterial_coding", array("materialcode" => $data['item2']))->row();
			$cost_price = $getCostPrice->cost_price;

			$datas2 = array(
				'ig_detail_id' => $goodsid,
				'itemid' => $data['item2'],
				'item_return' => $data['item2'],
				'qty' => $data['qty2'],
				'sprice' => $data['price2'],
				'returns' => $data['return_qty2'],
				'total_amount' => $data['total_p2'],
				'cost_price' => $cost_price,
				'Posted_Date' => $data['date'],
				'ex_vat_total_amount' => $data['total_p2'],
				'wrate' => '0.00'
			);

			$this->db->insert($table, $datas2);
		}

		if ($data['item3'] != "") {
			$getCostPrice = $this->db->get_where("tblmaterial_coding", array("materialcode" => $data['item3']))->row();
			$cost_price = $getCostPrice->cost_price;

			$datas3 = array(
				'ig_detail_id' => $goodsid,
				'itemid' => $data['item3'],
				'item_return' => $data['item3'],
				'qty' => $data['qty3'],
				'sprice' => $data['price3'],
				'returns' => $data['return_qty3'],
				'total_amount' => $data['total_p3'],
				'cost_price' => $cost_price,
				'Posted_Date' => $data['date'],
				'ex_vat_total_amount' => $data['total_p3'],
				'wrate' => '0.00'
			);

			$this->db->insert($table, $datas3);
		}

		$netamountr = $data['totalrecv'];
		$netamount = $data['amounttotal'];
		$receiptdate = $data['date'];
		$vendorcode = $data['customer'];
		$user = $this->session->userdata('id');
		$goodsidt = $goodsid . "-Sale";
		$goodsidr = $goodsid . "-Receive";
		$goodsidss = $goodsid . "-Sale Security";
		$goodsidgasreturn = $goodsid . "-Returned Gas";

		$check_exists = "SELECT * FROM `tbltrans_master` WHERE `vno` = '$goodsidt' and `vtype`='SV'";
		$query = $this->db->query($check_exists);

		if ($query->num_rows() != 0) {
			$sqld = "DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsidt' and `vtype`='SV'";
			$this->db->query($sqld);
			$sqlm = "DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsidt' and `vtype`='SV'";
			$this->db->query($sqlm);
			$sqld = "DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsidr' and `vtype`='SV'";
			$this->db->query($sqld);
			$sqlm = "DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsidr' and `vtype`='SV'";
			$this->db->query($sqlm);
			$sqld = "DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsidgasreturn' and `vtype`='SV'";
			$this->db->query($sqld);
			$sqlm = "DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsidgasreturn' and `vtype`='SV'";
			$this->db->query($sqlm);
		}

		$sale_code = '3001001001';
		$cash_inhand = '2003013001';
		$gas_return_acc = '2003001002';
		$security_code = '1001002001';
		$items_detail = '';
		$tax_acode = '1001003001';

		$sql_in = "SELECT  m.security_amt,d.itemid,d.qty,i.itemname,d.amount,d.sprice,d.wrate,d.total_amount,d.returns
			FROM  tbl_issue_goods m,tbl_issue_goods_detail d ,tblmaterial_coding i where m.issuenos=d.ig_detail_id and
				d.itemid=i.materialcode and m.issuenos ='$goodsidt' ";


		$resul = $this->db->query($sql_in);
		$rw = $resul->result_array();
		foreach ($rw as $key => $value) {
			$item_amount = 0;
			$items_detail = "";
			$nar1 = "";

			$returns = $value['returns'];
			$gate_pas = $value['ref1'];
			$item_amount = $value['total_amount'];
			$security_amts = $value['security_amt'];
			$wrate = $value['wrate'];

			if ($wrate > 0) {

				$items_detail_m .= $value['itemname'] . ' ,  ' . $value['qty'] . '@' . $value['sprice'] . ',security ' . $wrate;
			} else {
				$items_detail_m .= $value['itemname'] . ' ,  ' . $value['qty'] . '@' . $value['sprice'];
			}

			$items_detail_m .= ', empty returned ' . $returns . ':';
		}
		$items_detail_m = substr_replace($items_detail_m, "", -1);

		$nar = 'Sale against #:' . $goodsid . ',  ' . $items_detail_m . '(' . $data['remarks'] . ')';


		$sr++;
		$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate)
			   values('$goodsidt','$sr','$vendorcode','$vendorname','0','$netamount','$nar','SV','SP','$receiptdate')";
		$this->db->query($queryd);

		$sr++;
		$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate)
			   values('$goodsidt','$sr','$sale_code','','$netamount','0','$nar','SV','SP','$receiptdate')";
		$this->db->query($queryd);



		if ($netamountr > 0) {

			$recv_nar = $nar;

			$chequedate = '';
			$chequeno = '';
			if ($data['pay_mode'] == 'Bank') {
				$cash_inhand =	$data['bank_code'];
				$chequedate = $data['cheque_date'];
				$chequeno = $data['cheque_no'];
			}


			$sr++;
			$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequedate,chequeno)
			   values('$goodsidr','$sr','$vendorcode','$vendorname','0','$netamountr','$recv_nar','SV','SP','$receiptdate','$chequedate','$chequeno')";
			$this->db->query($queryd);


			$sr++;
			$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequedate,chequeno)
			   values('$goodsidr','$sr','$cash_inhand','','$netamountr','0','$recv_nar','SV','SP','$receiptdate','$chequedate','$chequeno')";
			$this->db->query($queryd);
		}

		$querys = "INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date) values ('$goodsidt' , 'SV' , '$netamount' , '$netamount' ,'No' ,'No' ,'$user','SP' ,'$receiptdate')";
		$this->db->query($querys);

		$updates = "UPDATE `tbl_issue_goods` set `post_gl`=1 where `issuenos`='$goodsidt'";
		$this->db->query($updates);



		$sql_ins = "SELECT  m.security_amt,d.itemid,d.qty,i.itemname,d.amount,d.sprice,d.total_amount FROM  tbl_issue_goods m,tbl_issue_goods_detail d ,tblmaterial_coding i where m.issuenos=d.ig_detail_id and
				d.itemid=i.materialcode and m.issuenos ='$goodsidr' ";

		$resuls = $this->db->query($sql_ins);
		$rws = $resuls->result_array();
		foreach ($rws as $key => $value) {
			$item_amount = 0;
			$items_detail = "";
			$nar1 = "";
			$gate_pas = $value['ref1'];
			$item_amount = $value['total_amount'];
			$security_amts = $value['security_amt'];
			$items_detail_ms .= $value['itemname'] . ' ,  ' . $value['qty'] . '@' . $value['sprice'] . ':';
		}

		$updates = "UPDATE `tbl_issue_goods` set `post_gl`=1 where `issuenos`='$goodsidr'";
		$q = $this->db->query($updates);
		$this->db->trans_complete();
		return $goodsid;
	}






	public function multipleitems_againstid($data, $goodsid, $trans_id, $table, $updated_value = '')
	{
		//pm($data);exit();
		//echo $cylinder_sale_amt=$data['sale_security_amt'];exit();
		$tax_amount = 0;
		$netamount_ex = 0;
		$return_rate = 0;
		$return_gas = 0;
		$return_amount = 0;
		$total_discount = 0;
		$vat_amount = $data['vat_amount'];
		$return_amountt = $data['return_amount'];
		$delivery_charges = $data['delivery_charges'];
		$total_discount = $data['total_discount'];

		$sagment = $data['segment'];
		if ($sagment == 'walkin' || $sagment == 'home') {

			$d_customer = $data['d_customer'];
		} else {
			$d_customer = 0;
		}
		$gstp = $data['gstp'];
		date_default_timezone_set("Asia/Karachi");
		$today = date('Y-m-d h:i:sa');
		$uid = $this->session->userdata('id');
		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
		$sale_code = $fix_code['sales_code'];
		$cost_code = $fix_code['cost_of_goods_code'];
		$stock_code = $fix_code['stock_code'];
		$cash_inhand = $fix_code['cash_code'];
		$gas_return_acc = $fix_code['gas_return_code'];
		$security_code = $fix_code['security_code'];
		$items_detail = '';
		$tax_acode = $fix_code['tax_pay'];

		$sql_in_m = "SELECT  return_rate,return_gas,return_amount,total_discount FROM  tbl_issue_goods
			where issuenos ='$goodsid' ";

		$resul_m = $this->db->query($sql_in_m);
		$rw_m = $resul_m->result_array();
		foreach ($rw_m as $key => $value_m) {
			$return_rate = $value_m['return_rate'];
			$return_gas = $value_m['return_gas'];
			$return_amount = $value_m['return_amount'];
			$total_discount = $value_m['total_discount'];
		}

		if ($updated_value == '') {

			$login_user = $this->session->userdata('id');
			$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			$fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
			$sale_code = $fix_code['sales_code'];
			$cash_inhand = $fix_code['cash_code'];
			$tax_acode = $fix_code['tax_pay'];
			//$security_code='1001002001';
			$security_code = $fix_code['security_code'];

			$datas = array();
			foreach ($data['item'] as $key => $value) {

				$getCostPrice = $this->db->get_where("tblmaterial_coding", array("materialcode" => $data['item'][$key]))->row();
				$cost_price = $getCostPrice->cost_price;
				if (!$cost_price) {
					$cost_price = 0;
				}

				$datas[] = array(
					'ig_detail_id' => $goodsid,
					'sale_point_id' => $sale_point_id,
					'trans_id' => $trans_id,
					'itemid' => $data['item'][$key],
					'item_return' => $data['item_return'][$key],
					'qty' => $data['qty'][$key],
					'vat_percentage' => $data['gst'][$key],
					'vat_amount' => $data['gst_amounttotal'][$key],
					'ex_vat_total_amount' => $data['ex_amounttotal'][$key],
					'sprice' => $data['price'][$key],
					'wrate' => $data['security'][$key],
					'returns' => $data['returns'][$key],
					'total_amount' => $data['amounttotal'][$key],
					'cost_price' => $cost_price,
					'scode' => $data['scode'],
					'type' => $data['type'][$key],
					'catcode' => $data['catcode'][$key],
					'Posted_Date' => $data['date'],
					"direct_customer" => $d_customer


				);

				$itemid = $data['item'][$key];
				$catcode = $this->db->query("select catcode from tblmaterial_coding where materialcode='$itemid'")->row_array()['catcode'];
				if ($catcode != 1) {
					$appliances_amount += $data['price'][$key] * $data['qty'][$key];
				}

				$tax_amount += $data['gst_amounttotal'][$key];
				$netamount += $data['price'][$key] * $data['qty'][$key];
				$type = $data['type'][$key];
				//echo $type;exit();
				if ($type == 'security') {

					$sale_security += $data['security'][$key] * $data['qty'][$key];
				}
				//$sale_security+=$data['security'][$key]*$data['qty'][$key];
				$gst = $data['gst'][$key];



				$netamountr = $data['totalrecv'];

				$securityamts = $data['securityamt'];
				$cylinder_sale_amt = $data['sale_security_amt'];


				$naritem = $value['item'];
				$narqty = $data['qty'][$key];
				$narprice = $data['price'][$key];
			}

			$netamount_receivable = $data['total_bill'] - $vat_amount + $return_amountt;
			$netamount += $cylinder_sale_amt;
			$netamount_sale_code = $netamount - $cylinder_sale_amt - $appliances_amount;
			$netamount -= $total_discount;
			$netamount += $data['delivery_charges'];
			// $netamount+=$cylinder_sale_amt;
			//$netamount_sale_code=$netamount-$cylinder_sale_amt;


			if ($tax_amount) {
				$netamount_ex = $netamount + $tax_amount;
			} else {
				$netamount_ex = $netamount;
			}


			$quz = $this->db->insert_batch($table, $datas);


			foreach ($data['item'] as $key => $value) {
				$insertIds[$key]  = $this->db->last_query();
				$insdte .= $insertIds[$key];
			}
		} else {
			//pm($data);exit();
			$datas = array();
			$datai = array();
			foreach ($data['item'] as $key => $value) {
				$datas[] = array(
					'srno' => $data['items_detailid'][$key],
					'itemid' => $data['item'][$key],
					'ig_detail_id' => $goodsid,
					'scode' => $data['scode'],
					'sale_point_id' => $sale_point_id,
					'trans_id' => $trans_id,
					'item_return' => $data['item_return'][$key],
					'qty' => $data['qty'][$key],
					'vat_percentage' => $data['gst'][$key],
					'vat_amount' => $data['gst_amounttotal'][$key],
					'ex_vat_total_amount' => $data['ex_amounttotal'][$key],
					'sprice' => $data['price'][$key],
					'wrate' => $data['security'][$key],
					'returns' => $data['returns'][$key],
					'type' => $data['type'][$key],
					'catcode' => $data['catcode'][$key],
					'total_amount' => $data['amounttotal'][$key],
					'Posted_Date' => $data['date'],
					"direct_customer" => $d_customer


				);

				$itemid = $data['item'][$key];
				$catcode = $this->db->query("select catcode from tblmaterial_coding where materialcode='$itemid'")->row_array()['catcode'];
				if ($catcode != 1) {
					$appliances_amount += $data['price'][$key] * $data['qty'][$key];
				}

				$tax_amount += $data['gst_amounttotal'][$key];
				$netamount += $data['price'][$key] * $data['qty'][$key];
				$type = $data['type'][$key];

				if ($type == 'security') {
					$sale_security += $data['security'][$key] * $data['qty'][$key];
				}

				$gst = $data['gst'][$key];


				$netamountr = $data['totalrecv'];
				$cylinder_sale_amt = $data['sale_security_amt'];
				$securityamts = $data['securityamt'];


				$naritem = $value['item'];
				$narqty = $data['qty'][$key];
				$narprice = $data['price'][$key];
			}
			$netamount_receivable = $data['total_bill'] - $vat_amount + $return_amountt;
			$netamount += $cylinder_sale_amt;
			$netamount_sale_code = $netamount - $cylinder_sale_amt - $appliances_amount;
			$netamount -= $total_discount;
			$netamount += $data['delivery_charges'];

			if ($tax_amount) {
				$netamount_ex = $netamount + $tax_amount;
			} else {
				$netamount_ex = $netamount;
			}

			$ex_amount = $netamount - $tax_amount;


			//	print $sale_security;
			foreach ($datas as $key => $value) {
				if ($value['srno']) {
					$datau[] = $value;
				} else {
					$datai[] = $value;
				}
			}
			if ($datau) {
				$this->db->update_batch($table, $datau, 'srno');
				$tdsd = $this->db->last_query();



				// print_r($tdsd);exit;




			}
			if ($datai) {
				$this->db->insert_batch($table, $datai);


				foreach ($data['item'] as $key => $value) {
					$insertIds[$key]  = $this->db->last_query();
					$insdtedd .= $insertIds[$key];
				}
			}
		}






		/////////////////////////// here is code//////////////////
		$receiptdate = $data['date'];
		$vendorcode = $data['customer'];
		$user = $this->session->userdata('id');
		$goodsidt = $sale_point_id . "-Sale-" . $trans_id;





		$nar_return = 'Gas Return ' . $return_gas . 'KG@' . $return_rate;


		$check_exists = "SELECT * FROM `tbltrans_master` WHERE `vno` = '$goodsidt'";

		$query = $this->db->query($check_exists);

		if ($query->num_rows() != 0) {
			$sqld = "DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsidt'";
			$this->db->query($sqld);


			$sqlm = "DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsidt'";
			$this->db->query($sqlm);
		}

		$sale_code = $fix_code['sales_code'];
		$cost_code = $fix_code['cost_of_goods_code'];
		$stock_code = $fix_code['stock_code'];
		$cash_inhand = $fix_code['cash_code'];
		$sale_cylinder_code = $fix_code['sale_cylinder_code'];
		$cash_type = 'CR';
		$gas_return_acc = $fix_code['gas_return_code'];
		$security_code = $fix_code['security_code'];
		$items_detail = '';
		$tax_acode = $fix_code['tax_pay'];
		$appliances_code = $fix_code['appliances_code'];
		$delivery_charges_code = $fix_code['delivery_charges_code'];
		$discount_code = $fix_code['discount_code'];


		$sql_in = "SELECT  m.security_amt,d.itemid,d.qty,i.itemname,d.amount,d.sprice,d.wrate,d.total_amount,d.returns,d.vat_percentage FROM  tbl_issue_goods m,tbl_issue_goods_detail d ,tblmaterial_coding i where m.issuenos=d.ig_detail_id and d.itemid=i.materialcode and m.issuenos ='$goodsid' ";


		$resul = $this->db->query($sql_in);
		$rw = $resul->result_array();
		foreach ($rw as $key => $value) {
			$item_amount = 0;
			$items_detail = "";
			$nar1 = "";

			$returns = $value['returns'];
			$gate_pas = $value['ref1'];
			$item_amount = $value['total_amount'];
			$security_amts = $value['security_amt'];
			$wrate = $value['wrate'];

			if ($wrate > 0) {

				$items_detail_m .= $value['itemname'] . ' ,  ' . $value['qty'] . '@' . $value['sprice'] . ',security ' . $wrate;
			} else {
				$items_detail_m .= $value['itemname'] . ' ,  ' . $value['qty'] . '@' . $value['sprice'];
			}

			$items_detail_m .= ', empty returned ' . $returns . ':';
		}
		$items_detail_m = substr_replace($items_detail_m, "", -1);

		$nar = 'Sale against #:' . $goodsid . ',  ' . $items_detail_m . '(' . $data['remarks'] . ')';
		$nar_return_gas = 'Return Gas Against Sale #:' . $goodsid . ',  ' . $items_detail_m . '(' . $data['remarks'] . ')';
		$nar_tax = $gstp . ' % Sale against #:' . $goodsid . ',  ' . $items_detail_m . '(' . $data['remarks'] . ')';
		$nar_sale = 'Cylinder Sale Amount against Sale #:' . $goodsid . ',  ' . $items_detail_m . '(' . $data['remarks'] . ')';
		$scode = $data['scode'];
		$after_discount_amt = $data['after_discount_amt'];
		$querys = "INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date,sale_point_id,trans_id)
			values
			('$goodsidt' , 'SV' , '$after_discount_amt' , '$after_discount_amt' ,'No' ,'No' ,'$user','SP' ,'$receiptdate','$sale_point_id','$trans_id')";
		$this->db->query($querys);

		$master_id = $this->db->query("select masterid from tbltrans_master where vno='$goodsidt'")->row_array()['masterid'];
		//echo $master_id;exit;


		$sr++;
		$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode, direct_customer)
			   values('$goodsidt','$master_id','$sr','$vendorcode','$vendorname','0','$netamount_receivable','$nar','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode','$d_customer')";
		$this->db->query($queryd);


		$debit += $netamount_receivable;
		$query = "insert into tbl_user_log (user_id,trans_reference,dt,trans_type,form_name,query_exec,trans_dt )
				values
			('$uid' , '$goodsidt' , now() , 'INSERT tbltrans_detail' ,'SaleLPg.php' ,\"$queryd\",'$today')";
		$this->db->query($query);
		$lastqqf = $this->db->last_query();

		$sr++;
		$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode)
			   values('$goodsidt','$master_id','$sr','$sale_code','','$netamount_sale_code','0','$nar','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
		$this->db->query($queryd);
		$lastqqf = $this->db->last_query();

		$credit += $netamount_sale_code;

		$query = "insert into tbl_user_log (user_id,trans_reference,dt,trans_type,form_name,query_exec,trans_dt )
				values
			('$uid' , '$goodsidt' , now() , 'INSERT tbltrans_detail' ,'SaleLPg.php' ,\"$queryd\",'$today')";
		$this->db->query($query);
		$lastqqf = $this->db->last_query();



		$sr++;
		$totalcostofsallee = $data['total_cost_of_Sale'][$key];

		if ($cylinder_sale_amt > 0) {

			$sr++;
			$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode)
					values('$goodsidt','$master_id','$sr','$sale_cylinder_code','','0','$cylinder_sale_amt','$nar_sale','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
			$this->db->query($queryd);
			$lastqqf = $this->db->last_query();

			$credit += $cylinder_sale_amt;
			// 		 $query="insert into tbl_user_log (user_id,trans_reference,dt,trans_type,form_name,query_exec,trans_dt )
			// 	values
			// ('$uid' , '$goodsidt' , now() , 'INSERT tbltrans_detail' ,'SaleLPg.php' ,\"$queryd\",'$today')";
			// $this->db->query($query);


		}

		if ($appliances_amount > 0) {
			$sr++;
			$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode)
				values('$goodsidt','$master_id','$sr','$appliances_code','','$appliances_amount','0','$nar','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
			$this->db->query($queryd);
			$lastqqf = $this->db->last_query();

			$credit += $appliances_amount;
		}
		if ($delivery_charges > 0) {
			$nar_d_charges = 'Delivery Charges Against Sale #:' . $goodsid . ',  ' . $items_detail_m . '(' . $data['remarks'] . ')';
			$sr++;
			$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode)
				values('$goodsidt','$master_id','$sr','$delivery_charges_code','','$delivery_charges','0','$nar_d_charges','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
			$this->db->query($queryd);
			$lastqqf = $this->db->last_query();

			$credit += $delivery_charges;
		}
		if ($total_discount > 0) {
			$nar_disc = 'Discount Against Sale #:' . $goodsid . ',  ' . $items_detail_m . '(' . $data['remarks'] . ')';
			$sr++;
			$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode)
				values('$goodsidt','$master_id','$sr','$discount_code','','0','$total_discount','$nar_disc','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
			$this->db->query($queryd);
			$lastqqf = $this->db->last_query();

			$debit += $total_discount;
		}
		if ($vat_amount > 0) {
			$sr++;
			$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode,direct_customer)
				values('$goodsidt','$master_id','$sr','$vendorcode','$vendorname','0','$vat_amount','Tax : $nar_tax','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode','$d_customer')";
			$this->db->query($queryd);
			$lastqqf = $this->db->last_query();

			$debit += $vat_amount;
			$query = "insert into tbl_user_log (user_id,trans_reference,dt,trans_type,form_name,query_exec,trans_dt )
				values
			('$uid' , '$goodsidt' , now() , 'INSERT tbltrans_detail' ,'SaleLPg.php' ,\"$queryd\",'$today')";
			$this->db->query($query);
			$lastqqf = $this->db->last_query();


			$sr++;
			$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode)
				values('$goodsidt','$master_id','$sr','$tax_acode','','$vat_amount','0','Tax : $nar_tax','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
			$this->db->query($queryd);
			$lastqqf = $this->db->last_query();

			$credit += $vat_amount;
			$query = "insert into tbl_user_log (user_id,trans_reference,dt,trans_type,form_name,query_exec,trans_dt )
				values
			('$uid' , '$goodsidt' , now() , 'INSERT tbltrans_detail' ,'SaleLPg.php' ,\"$queryd\",'$today')";
			$this->db->query($query);
		}

		// 			if($return_amount>0 && $netamountr>0) {

		// 				$recv_nar='';
		// 				if($return_amount>0) {
		// 					$recv_nar='Return against #:'.$goodsid.',  '.$items_detail_m.'('.$data['remarks'].')';
		// 				}


		// 			$sr++;
		// 	       $queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode)
		// 		   values('$goodsidt','$master_id','$sr','$gas_return_acc','','$return_amount','0','$recv_nar','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
		// 			$this->db->query($queryd);
		// 			$debit+=$return_amount;
		// 			 $query="insert into tbl_user_log (user_id,trans_reference,dt,trans_type,form_name,query_exec,trans_dt )
		// 			values
		// 		('$uid' , '$goodsidt' , now() , 'INSERT tbltrans_detail' ,'SaleLPg.php' ,\"$queryd\",'$today')";
		// 		$this->db->query($query);

		// 			$sr++;
		// 	       $queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode)
		// 		   values('$goodsidt','$master_id','$sr','$vendorcode','$vendorname','0','$return_amount','$recv_nar','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
		// 			$this->db->query($queryd);
		// 			$credit+=$return_amount;
		// $query="insert into tbl_user_log (user_id,trans_reference,dt,trans_type,form_name,query_exec,trans_dt )
		// 			values
		// 		('$uid' , '$goodsidt' , now() , 'INSERT tbltrans_detail' ,'SaleLPg.php' ,\"$queryd\",'$today')";
		// 		$this->db->query($query);
		// 		}


		// 		if($netamountr==0 && $return_amount>0) {
		// 			$nar_return='Receive  against #:'.$goodsid.',  '.$items_detail_m.'('.$data['remarks'].')';


		// 				$sr++;
		// 				$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode)
		// 				values('$goodsidt','$master_id','$sr','$cash_inhand','','$return_amount','0','$nar_return','SV','$cash_type','$receiptdate','$sale_point_id','$trans_id','$scode')";
		// 				$this->db->query($queryd);
		// 				$debit+=$return_amount;
		// 				 $query="insert into tbl_user_log (user_id,trans_reference,dt,trans_type,form_name,query_exec,trans_dt )
		// 			values
		// 		('$uid' , '$goodsidt' , now() , 'INSERT tbltrans_detail' ,'SaleLPg.php' ,\"$queryd\",'$today')";
		// 		$this->db->query($query);

		// 		$sr++;
		// 				$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode)
		// 				values('$goodsidt','$master_id','$sr','$vendorcode','$vendorname','0','$return_amount','$nar_return','SV','$cash_type','$receiptdate','$sale_point_id','$trans_id','$scode')";
		// 				$this->db->query($queryd);
		// 				$credit+=$return_amount;
		// $query="insert into tbl_user_log (user_id,trans_reference,dt,trans_type,form_name,query_exec,trans_dt )
		// 			values
		// 		('$uid' , '$goodsidt' , now() , 'INSERT tbltrans_detail' ,'SaleLPg.php' ,\"$queryd\",'$today')";
		// 		$this->db->query($query);
		// 		}

		if ($sale_security > 0) {
			//echo $sale_security;exit();
			$nar_security = $nar;

			// 		$sr++;
			// 		$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode)
			// 		values('$goodsidt','$master_id','$sr','$vendorcode','$vendorname','$sale_security','0','$nar_security','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
			// 		$this->db->query($queryd);
			// 		$debit+=$sale_security;
			// 		 $query="insert into tbl_user_log (user_id,trans_reference,dt,trans_type,form_name,query_exec,trans_dt )
			// 	values
			// ('$uid' , '$goodsidt' , now() , 'INSERT tbltrans_detail' ,'SaleLPg.php' ,\"$queryd\",'$today')";
			// $this->db->query($query);

			$sr++;
			$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode)
					values('$goodsidt','$master_id','$sr','$security_code','$vendorname','0','$sale_security','$nar_security','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
			$this->db->query($queryd);
			$lastqqf = $this->db->last_query();

			$credit += $sale_security;
			$query = "insert into tbl_user_log (user_id,trans_reference,dt,trans_type,form_name,query_exec,trans_dt )
				values
			('$uid' , '$goodsidt' , now() , 'INSERT tbltrans_detail' ,'SaleLPg.php' ,\"$queryd\",'$today')";
			$this->db->query($query);
			$lastqqf = $this->db->last_query();
		}
		// 			if($data['securityamt']>0 && $sale_security>0){
		// $sr++;
		// $nar='Receive Security against #:'.$goodsid.',  '.$items_detail_m.'('.$data['remarks'].')';
		// 		        $queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode)
		// 			   values('$goodsidt','$master_id','$sr','$cash_inhand','$vendorname','$securityamts','0','$nar','SV','$cash_type','$receiptdate','$sale_point_id','$trans_id','$scode')";
		// 				$this->db->query($queryd);
		// 				$debit+=$securityamts;
		// 				 $query="insert into tbl_user_log (user_id,trans_reference,dt,trans_type,form_name,query_exec,trans_dt )
		// 				values
		// 			('$uid' , '$goodsidt' , now() , 'INSERT tbltrans_detail' ,'SaleLPg.php' ,\"$queryd\",'$today')";
		// 			$this->db->query($query);

		// $sr++;
		// 		        $queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode)
		// 			   values('$goodsidt','$master_id','$sr','$vendorcode','$vendorname','0','$securityamts','$nar','SV','$cash_type','$receiptdate','$sale_point_id','$trans_id','$scode')";
		// 				$this->db->query($queryd);
		// 				$credit+=$securityamts;
		//             $query="insert into tbl_user_log (user_id,trans_reference,dt,trans_type,form_name,query_exec,trans_dt )
		// 				values
		// 			('$uid' , '$goodsidt' , now() , 'INSERT tbltrans_detail' ,'SaleLPg.php' ,\"$queryd\",'$today')";
		// 			$this->db->query($query);

		// 			}
		if ($netamountr > 0) {

			$recv_nar = 'Receive against #:' . $goodsid . ',  ' . $items_detail_m . '(' . $data['remarks'] . ')';
			if ($return_amount > 0) {
				$recv_nar = $nar . ',' . $nar_return;
			}


			$chequedate = '';
			$chequeno = '';
			if ($data['pay_mode'] == 'Bank') {
				$cash_inhand =	$data['bank_code'];
				$cash_type = 'BR';

				$chequedate = $data['cheque_date'];
				$chequeno = $data['cheque_no'];
			}





			$sr++;
			$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequedate,chequeno,sale_point_id,trans_id,scode)
			   values('$goodsidt','$master_id','$sr','$cash_inhand','','$netamountr','0','$recv_nar','SV','$cash_type','$receiptdate','$chequedate','$chequeno','$sale_point_id','$trans_id','$scode')";
			$this->db->query($queryd);
			$lastqqf = $this->db->last_query();

			$debit += $netamountr;
			$query = "insert into tbl_user_log (user_id,trans_reference,dt,trans_type,form_name,query_exec,trans_dt )
				values
			('$uid' , '$goodsidt' , now() , 'INSERT tbltrans_detail' ,'SaleLPg.php' ,\"$queryd\",'$today')";
			$this->db->query($query);
			$lastqqf = $this->db->last_query();


			$sr++;
			$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequedate,chequeno,sale_point_id,trans_id,scode,direct_customer)
			   values('$goodsidt','$master_id','$sr','$vendorcode','$vendorname','0','$netamountr','$recv_nar','SV','$cash_type','$receiptdate','$chequedate','$chequeno','$sale_point_id','$trans_id','$scode', '$d_customer')";
			$this->db->query($queryd);
			$lastqqf = $this->db->last_query();

			$credit += $netamountr;

			$query = "insert into tbl_user_log (user_id,trans_reference,dt,trans_type,form_name,query_exec,trans_dt )
				values
			('$uid' , '$goodsidt' , now() , 'INSERT tbltrans_detail' ,'SaleLPg.php' ,\"$queryd\",'$today')";
			$this->db->query($query);
			$lastqqf = $this->db->last_query();
		}
		if ($return_amountt > 0) {
			$sr++;
			$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode)
				values('$goodsidt','$master_id','$sr','$stock_code','','0','$return_amountt','$nar_return_gas','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode')";
			$this->db->query($queryd);
			$lastqqf = $this->db->last_query();

			$credit += $return_amountt;
			$query = "insert into tbl_user_log (user_id,trans_reference,dt,trans_type,form_name,query_exec,trans_dt )
				values
			('$uid' , '$goodsidt' , now() , 'INSERT tbltrans_detail' ,'SaleLPg.php' ,\"$queryd\",'$today')";
			$this->db->query($query);
			$lastqqf = $this->db->last_query();


			$sr++;
			$queryd = "INSERT into `tbltrans_detail` (vno,ig_detail_id,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate,sale_point_id,trans_id,scode,direct_customer)
				values('$goodsidt','$master_id','$sr','$vendorcode','$vendorname','$return_amountt','0','$nar_return_gas','SV','SP','$receiptdate','$sale_point_id','$trans_id','$scode','$d_customer')";
			$this->db->query($queryd);
			$lastqqf = $this->db->last_query();

			$debit += $return_amountt;
			$query = "insert into tbl_user_log (user_id,trans_reference,dt,trans_type,form_name,query_exec,trans_dt )
				values
			('$uid' , '$goodsidt' , now() , 'INSERT tbltrans_detail' ,'SaleLPg.php' ,\"$queryd\",'$today')";
			$this->db->query($query);
			$lastqqf = $this->db->last_query();
		}



		$sale_code = $fix_code['sales_code'];
		$cash_inhand = $fix_code['cash_code'];



		$sr++;

		$sql_in = "SELECT  m.security_amt,d.itemid,d.qty,i.itemname,d.amount,d.sprice,d.total_amount FROM  tbl_issue_goods m,tbl_issue_goods_detail d ,tblmaterial_coding i where m.issuenos=d.ig_detail_id and
				d.itemid=i.materialcode and m.issuenos ='$goodsidt' ";

		$resul = $this->db->query($sql_in);
		$rw = $resul->result_array();
		foreach ($rw as $key => $value) {
			$item_amount = 0;
			$items_detail = "";
			$nar1 = "";

			$gate_pas = $value['ref1'];
			$item_amount = $value['total_amount'];
			$security_amts = $value['security_amt'];
			$items_detail .= $value['itemname'] . ' ,  ' . $value['qty'] . '@' . $value['sprice'];
			$nar1 = 'Sale against #:' . $trans_id . ',  ' . $items_detail;
		}

		$updates = "UPDATE `tbl_issue_goods` set `post_gl`=1 where `issuenos`='$goodsidt'";
		$this->db->query($updates);



		$sql_ins = "SELECT  m.security_amt,d.itemid,d.qty,i.itemname,d.amount,d.sprice,d.total_amount FROM  tbl_issue_goods m,tbl_issue_goods_detail d ,tblmaterial_coding i where m.issuenos=d.ig_detail_id and
				d.itemid=i.materialcode and m.issuenos ='$goodsidt' ";


		$resuls = $this->db->query($sql_ins);
		$rws = $resuls->result_array();
		foreach ($rws as $key => $value) {
			$item_amount = 0;
			$items_detail = "";
			$nar1 = "";

			$gate_pas = $value['ref1'];

			$item_amount = $value['total_amount'];

			$security_amts = $value['security_amt'];



			$items_detail_ms .= $value['itemname'] . ' ,  ' . $value['qty'] . '@' . $value['sprice'] . ':';
		}

		$nar = 'Receive against Sale #:' . $trans_id . ',  ' . $items_detail_ms . '(' . $data['remarks'] . ')';




		$updates = "UPDATE `tbl_issue_goods` set `post_gl`=1 where `issuenos`='$goodsidt'";

		if ($data['makenew']) {

			$makenew = $data['makenew'];
			$updates = "UPDATE `tbl_orderbooking` set `status`='delivered' where `id`='$makenew'";
		}

		$this->db->query($query);
		$q = $this->db->query($updates);
		$this->db->trans_complete();
		return $q;
	}

	public function repost_sale($goodsid)
	{
		$tax_acode = '2004003001';
		$sale_code = '2003001003';

		$user = $this->session->userdata('id');
		$goodsidt = $goodsid . "-Sale";
		$goodsecurity = $goodsid . "-Receive Security";
		$goodsidr = $goodsid . "-Receive";
		$goodsidss = $goodsid . "-Sale Security";
		$goodsidgasreturn = $goodsid . "-Returned Gas";

		$netamount = 0;
		$gstAmt = 0;




		$check_exists = "SELECT * FROM `tbltrans_master` WHERE `vno` = '$goodsidt' and `vtype`='SV'";

		$query = $this->db->query($check_exists);

		if ($query->num_rows() != 0) {

			$sqld = "DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsidt' and `vtype`='SV'";
			$this->db->query($sqld);
			$sqlm = "DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsidt' and `vtype`='SV'";
			$this->db->query($sqlm);

			$sqld = "DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsecurity' and `vtype`='SV'";
			$this->db->query($sqld);
			$sqlm = "DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsecurity' and `vtype`='SV'";
			$this->db->query($sqlm);

			$sqld = "DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsidr' and `vtype`='SV'";
			$this->db->query($sqld);
			$sqlm = "DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsidr' and `vtype`='SV'";
			$this->db->query($sqlm);


			$sqld = "DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsidss' and `vtype`='SV'";
			$this->db->query($sqld);
			$sqlm = "DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsidss' and `vtype`='SV'";
			$this->db->query($sqlm);


			$sqld = "DELETE FROM `tbltrans_detail` WHERE `vno` = '$goodsidgasreturn' and `vtype`='SV'";
			$this->db->query($sqld);
			$sqlm = "DELETE FROM `tbltrans_master` WHERE `vno` = '$goodsidgasreturn' and `vtype`='SV'";
			$this->db->query($sqlm);
		}





		$check_exists = "SELECT * FROM `tbl_issue_goods_detail` WHERE `ig_detail_id` = '$goodsid' ";

		$query = $this->db->query($check_exists);

		if ($query->num_rows() != 0) {










			$goodsItemsData = $this->db->get_where("tbl_issue_goods_detail", array("ig_detail_id" => $goodsid))->result();

			foreach ($goodsItemsData as $key => $value) {
				$netamount += $value->total_amount;
				$tax_amount += $value->vat_amount;
				$ex_vat_total_amount += $value->ex_vat_total_amount;
				$returns = $value->returns;

				$wrate = $value->wrate;
				if ($wrate > 0) {
					$items_detail_m .= $value->itemname . ' ,  ' . $value->qty . '@' . $value->sprice . ',security ' . $wrate;
				} else {
					$items_detail_m .= $value->itemname . ' ,  ' . $value->qty . '@' . $value->sprice;
				}
				$items_detail_m .= ', empty returned ' . $returns . ':';
			}
			$items_detail_m = substr_replace($items_detail_m, "", -1);
			$nar = 'Sale against #:' . $goodsid . ',  ' . $items_detail_m . '(' . $data->remarks . ')';

			$uData['total_amount'] = $netamount;
			$uData['after_discount_amt'] = $netamount - $goodsData->total_discount;

			$this->db->where("issuenos", $goodsid);
			$this->db->update("tbl_issue_goods", $uData);

			$goodsData = $this->db->get_where("tbl_issue_goods", array("issuenos" => $goodsid))->row();

			$security_amt = $goodsData->security_amt;
			$total_discount = $goodsData->total_discount;
			$vendorcode = $goodsData->issuedto;
			$receiptdate = $goodsData->issuedate;
			$netamountr = $goodsData->gas_amt;
			$return_amount = $goodsData->return_amount;

			$net_payable = $netamount;
			$vendorname = "";


			$sale_code = '3001001001';
			$cash_inhand = '2003013001';
			$gas_return_acc = '2003001002';
			$security_code = '1001002001';
			$items_detail = '';
			$tax_acode = '1001003001';

			if ($goodsData->pay_mode == 'Bank') {
				$cash_inhand = $goodsData->bank_code;
				$chequedate = $goodsData->cheque_date;
				$chequeno = $goodsData->cheque_no;
			}

			$netamount = $netamount - $total_discount - $security_amt - $tax_amount;

			$nar_return = "";
			$sr++;
			$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate)
			   values('$goodsidt','$sr','$vendorcode','$vendorname','0','$netamount','$nar','SV','SP','$receiptdate')";
			$this->db->query($queryd);

			$sr++;
			$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate)
			   values('$goodsidt','$sr','$sale_code','','$netamount','0','$nar','SV','SP','$receiptdate')";
			$this->db->query($queryd);


			if ($tax_amount > 0) {
				$sr++;
				$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate)
				values('$goodsidt','$sr','$tax_acode','','$tax_amount','0','Tax:$nar','SV','SP','$receiptdate' )";
				$this->db->query($queryd);
			}

			if ($tax_amount > 0) {
				$sr++;
				$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,camount,damount,remarks,vtype,svtype,vdate)
				values('$goodsidt','$sr','$vendorcode','$vendorname','0','$tax_amount','Tax:$nar','SV','SP','$receiptdate' )";
				$this->db->query($queryd);
			}
			if ($netamountr > 0) {

				$recv_nar = $nar;
				if ($return_amount > 0) {
					$recv_nar = $nar . ',' . $nar_return;
				}
				$chequedate = '';
				$chequeno = '';
				$sr++;
				$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequedate,chequeno)
			   values('$goodsidr','$sr','$vendorcode','$vendorname','0','$netamountr','$recv_nar','SV','SP','$receiptdate','$chequedate','$chequeno')";
				$this->db->query($queryd);


				$sr++;
				$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate,chequedate,chequeno)
			   values('$goodsidr','$sr','$cash_inhand','','$netamountr','0','$recv_nar','SV','SP','$receiptdate','$chequedate','$chequeno')";
				$this->db->query($queryd);
			}
			if ($return_amount > 0 && $netamountr > 0) {

				$recv_nar = '';
				if ($return_amount > 0) {
					$recv_nar = $nar_return;
				}
				$sr++;
				$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate)
			   values('$goodsidgasreturn','$sr','$vendorcode','$vendorname','0','$return_amount','$recv_nar','SV','SP','$receiptdate')";
				$this->db->query($queryd);


				$sr++;
				$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate)
			   values('$goodsidgasreturn','$sr','$gas_return_acc','','$return_amount','0','$recv_nar','SV','SP','$receiptdate')";
				$this->db->query($queryd);
			}


			if ($netamountr == 0 && $return_amount > 0) {

				$sr++;
				$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate)
					values('$goodsidr','$sr','$vendorcode','$vendorname','0','$return_amount','$nar_return','SV','SP','$receiptdate')";
				$this->db->query($queryd);


				$sr++;
				$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate)
					values('$goodsidr','$sr','$cash_inhand','','$return_amount','0','$nar_return','SV','SP','$receiptdate')";
				$this->db->query($queryd);
			}

			$sale_security = $security_amt;
			$securityamts = $security_amt;
			$nar_security = $nar;

			if ($sale_security > 0) {

				$nar_security = $nar;

				$sr++;
				$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate)
					values('$goodsidss','$sr','$security_code','$vendorname','0','$sale_security','$nar_security','SV','SP','$receiptdate')";
				$this->db->query($queryd);
				$sr++;
				$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate)
					values('$goodsidss','$sr','$vendorcode','$vendorname','$sale_security','0','$nar_security','SV','SP','$receiptdate')";
				$this->db->query($queryd);

				$sr++;

				$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate)
			   values('$goodsecurity','$sr','$cash_inhand','$vendorname','$securityamts','0','$nar','SV','SP','$receiptdate')";
				$this->db->query($queryd);

				$sr++;
				$queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate)
			   values('$goodsecurity','$sr','$vendorcode','$vendorname','0','$securityamts','$nar','SV','SP','$receiptdate')";
				$this->db->query($queryd);
			}

			$querys = "INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date)
			values
			('$goodsidt' , 'SV' , '$netamount' , '$netamount' ,'No' ,'No' ,'$user','SP' ,'$receiptdate')";
			$this->db->query($querys);

			$sale_code = '3001001001';
			$cash_inhand = '2003013001';


			if ($security_amt != 0) {

				$querys = "INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date)
				values
				('$goodsecurity' , 'SV' , '$securityamts' , '$securityamts' ,'No' ,'No' ,'$user','SP' ,'$receiptdate')";
				$this->db->query($querys);

				$sr++;
			}

			$sr++;

			$updates = "UPDATE `tbl_issue_goods` set `post_gl`=1 where `issuenos`='$goodsidt'";
			$this->db->query($updates);

			if ($security_amt != 0) {


				$querys = "INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date)
			values
			('$goodsidr' , 'SV' , '$netamountr' , '$netamountr' ,'No' ,'No' ,'$user','SP' ,'$receiptdate')";
				$this->db->query($querys);


				$querys = "INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date)
			values
			('$goodsidss' , 'SV' , '$netamountr' , '$netamountr' ,'No' ,'No' ,'$user','SP' ,'$receiptdate')";
				$this->db->query($querys);
			}
		}
	}



	public function today_amount_recv($dt)
	{
		$dt = date('Y-m-d');
		$this->db->select('tbl_issue_goods.total_received');    //,SUM(tbl_issue_goods_detail.total_amount)
		$this->db->from('tbl_issue_goods');

		$this->db->where('tbl_issue_goods.issuedate=', $dt);


		$query = $this->db->get();

		return $query->result_array();
	}
	public function manage_salelpg($from, $to, $sale_point_id)
	{
		$this->db->select('tbl_issue_goods.*,tblacode.*,SUM(tbl_issue_goods_detail.total_amount) as amounttotal');    //,SUM(tbl_issue_goods_detail.total_amount)
		$this->db->from('tbl_issue_goods');
		$this->db->join('tblacode', 'tbl_issue_goods.issuedto = tblacode.acode');
		$this->db->join('tbl_issue_goods_detail', ' tbl_issue_goods_detail.ig_detail_id= tbl_issue_goods.issuenos');
		$this->db->where('tbl_issue_goods.decanting=', '');
		$this->db->where('tbl_issue_goods.type=', 'Fill');

		$this->db->where('tbl_issue_goods.issuedate >=', $from);
		$this->db->where('tbl_issue_goods.issuedate <=', $to);
		$this->db->where('tbl_issue_goods.sale_point_id =', $sale_point_id);

		$this->db->group_by('ig_detail_id');
		$this->db->order_by("issuenos", "DESC");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function edit_salelpg($id = '')
	{
		$this->db->select('tbl_issue_goods.*,tbl_issue_goods_detail.*,tblacode.*');
		$this->db->from('tbl_issue_goods');
		$this->db->join('tbl_issue_goods_detail', 'tbl_issue_goods.issuenos = tbl_issue_goods_detail.ig_detail_id');
		$this->db->join('tblacode', 'tbl_issue_goods.issuedto = tblacode.acode');
		$this->db->where('tbl_issue_goods.issuenos=', $id);
		$this->db->order_by("issuedate", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function edit_makeneworder($id)
	{
		$this->db->select('tbl_orderbooking.*,tbl_orderbooking_detail.*,tblacode.*');
		$this->db->from('tbl_orderbooking');
		$this->db->join('tbl_orderbooking_detail', 'tbl_orderbooking.id = tbl_orderbooking_detail.orderid');
		$this->db->join('tblacode', 'tbl_orderbooking.acode = tblacode.acode');
		$this->db->where('tbl_orderbooking.id=', $id);
		$this->db->order_by("tbl_orderbooking.id", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function update_sale_lpg($data)
	{
		//pm($data);
		date_default_timezone_set("Asia/Karachi");
		$today = date('Y-m-d h:i:sa');
		$uid = $this->session->userdata('id');
		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$this->db->trans_start();
		$ins_array = array(
			"issuedto" => $data['customer'],
			"issuedate" => $data['date'],
			"remarks" => $data['remarks'],
			"sale_type" => $data['saletype'],
			"return_gas" => $data['return_gas'],
			"return_rate" => $data['return_rate'],
			"return_amount" => $data['return_amount'],
			"security_amt" => $data['securityamt'],
			"gas_amt" => $data['gasamt'],
			"total_received" => $data['totalrecv'],
			"total_discount" => $data['total_discount'],
			"after_discount_amt" => $data['after_discount_amt'],
			"vat_percentage" => $data['gstp'],
			"vat_amount" => $data['vat_amount'],
			"inc_vat_amount" => $data['inc_vat_amount'],
			"cylinder_sale_amt" => $data['sale_security_amt'],
			"delivery_charges" => $data['delivery_charges'],
			"type" => 'Fill',
			"scode" => $data['scode'],
			"pay_mode" => $data['pay_mode'],
			"balance" => $data['balance'],
			"bank_code" => $data['bank_code'],
			"cheque_no" => $data['cheque_no'],
			"cheque_date" => $data['cheque_date'],
			"sale_point_id" => $sale_point_id,
			"trans_id" => $data['trans_id'],
			"11_kg_price" => $data['kg_11_price']
		);
		#----------- add record---------------#`
		$trans_id = $data['trans_id'];
		$id = $_POST['id'];
		$table = "tbl_issue_goods";
		$where = "issuenos= '$id'";
		$update_goods = $this->mod_common->update_table($table, $where, $ins_array);



		if ($update_goods) {
			return $this->multipleitems_againstid($data, $id, $trans_id, 'tbl_issue_goods_detail', '34');
		} else {
			return false;
		}
	}


	public function get_details($data)
	{
		//pm()
		if (is_array($data) && isset($data['date'])) {
			$fromdate = $data['date'];
			$itemid = $data['item_id'];
		}

		$sql = "SELECT * from `tblmaterial_coding` WHERE `materialcode`=$itemid";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $key => $value) {
				$itemname = $value['itemname'];
				$catcode = $value['catcode'];
				//$itemid = $data['item_id'];

				/* here is code for filled */
				/*   opening balnace start     */


				$sqls = "SELECT  * from `tbl_shop_opening` WHERE `date`<='$fromdate' AND `type`='Filled' AND `materialcode`=$itemid";
				$querys = $this->db->query($sqls)->row_array();


				$sqlv = "SELECT  COALESCE(SUM(`tbl_goodsreceiving_detail`.`quantity`),0) as Dgsumq, COALESCE(SUM(`tbl_goodsreceiving_detail`.`ereturn`),0) as otvendor FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<='$fromdate' AND `tbl_goodsreceiving_detail`.`type`='Filled' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
				$queryv = $this->db->query($sqlv);
				$recfrmvenf = $queryv->row_array();

				//$sqlv ="SELECT SUM(`quantity`) as Dgsumq,SUM(`ereturn`) as otvendor from `tbl_goodsreceiving_detail` WHERE `type`='Filled' AND `itemid`=$itemid";
				//$queryv = $this->db->query($sqlv);
				//$recfrmvenf = $queryv->row_array();

				//$sqlsc ="SELECT SUM(`qty`) as igsumq,SUM(`returns`) as rfcustomer from `tbl_issue_goods_detail` WHERE `returns`!='' AND `itemid`=$itemid";

				/* $sqlsc = "SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as igsumq,COALESCE(SUM(`tbl_issue_goods_detail`.`returns`),0) as rfcustomer  FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE `issuedate`<='$fromdate' AND  `tbl_issue_goods_detail`.`returns`!='' AND `tbl_issue_goods_detail`.`itemid`=$itemid";
                $querysc = $this->db->query($sqlsc);
                $saltcusf = $querysc->row_array();*/


				$sql_con = "SELECT  COALESCE(SUM(`tbl_cylinderconversion_detail`.`qty`),0) as from_qty FROM `tbl_cylinderconversion_master` INNER JOIN `tbl_cylinderconversion_detail` ON `tbl_cylinderconversion_master`.`trans_id` = `tbl_cylinderconversion_detail`.`trans_id` WHERE `trans_date`<='$fromdate' AND `tbl_cylinderconversion_detail`.`type`='from' AND `tbl_cylinderconversion_detail`.`itemcode`=$itemid";
				$query_con = $this->db->query($sql_con);
				$recfrmvenf_con = $query_con->row_array();



				$sql_con_to = "SELECT  COALESCE(SUM(`tbl_cylinderconversion_detail`.`qty`),0) as to_qty FROM `tbl_cylinderconversion_master` INNER JOIN `tbl_cylinderconversion_detail` ON `tbl_cylinderconversion_master`.`trans_id` = `tbl_cylinderconversion_detail`.`trans_id` WHERE `trans_date`<='$fromdate' AND `tbl_cylinderconversion_detail`.`type`='to' AND `tbl_cylinderconversion_detail`.`itemcode`=$itemid";
				$query_con_to = $this->db->query($sql_con_to);
				$recfrmvenf_con_to = $query_con_to->row_array();




				$sqlsc = "SELECT COALESCE(SUM(`tbl_issue_goods_detail`.`qty`),0) as igsumq   FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE `issuedate`<='$fromdate'   AND `tbl_issue_goods_detail`.`itemid`=$itemid";
				$querysc = $this->db->query($sqlsc);
				$saltcusf = $querysc->row_array();



				if ($catcode != 1) {

					$sqlreturnf = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returnqtyf  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `irdate`<='$fromdate' AND `tbl_issue_return_detail`.`type`='Filled' AND `tbl_issue_return`.`type`='purchasereturnother' AND `tbl_issue_return_detail`.`itemid`=$itemid";
					$queryreturnf = $this->db->query($sqlreturnf);
					$return_qtyf = $queryreturnf->row_array();
				} else {

					$sqlreturnf = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returnqtyf  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `irdate`<='$fromdate' AND `tbl_issue_return_detail`.`type`='Filled' AND `tbl_issue_return`.`type`='purchasereturn' AND `tbl_issue_return_detail`.`itemid`=$itemid";
					$queryreturnf = $this->db->query($sqlreturnf);
					$return_qtyf = $queryreturnf->row_array();
				}



				$sqlreturnf_sale = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returnqtyf  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `irdate`<='$fromdate' AND `tbl_issue_return_detail`.`type`='Filled' AND `tbl_issue_return`.`type`='salereturn' AND `tbl_issue_return_detail`.`itemid`=$itemid";
				$queryreturnf_sale = $this->db->query($sqlreturnf_sale);
				$return_qtyf_sale = $queryreturnf_sale->row_array();

				//echo $querys['qty']."<br>";
				/*echo $recfrmvenf['Dgsumq']."<br>";
echo $saltcusf['igsumq'];

exit;*/
				//echo $querys['qty'];
				//echo $recfrmvenf['Dgsumq'];
				//echo $saltcusf['igsumq'];


				$opgbalfilled = $querys['qty'] - $return_qtyf['returnqtyf'] + $return_qtyf_sale['returnqtyf'] + $recfrmvenf['Dgsumq'] - $saltcusf['igsumq'] - $recfrmvenf_con['from_qty'] + $recfrmvenf_con_to['to_qty'];



				/* $sqls ="SELECT  * from `tbl_shop_opening` WHERE `date`<='$fromdate' AND `type`='Filled' AND `materialcode`=$itemid";
                $querys = $this->db->query($sqls)->row_array();

                $sqlv = "SELECT tbl_goodsreceiving.*,SUM(`tbl_goodsreceiving_detail`.`quantity`) as Dgsumq,SUM(`tbl_goodsreceiving_detail`.`ereturn`) as otvendor  FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<='$fromdate' AND `tbl_goodsreceiving_detail`.`type`='Filled' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryv = $this->db->query($sqlv);
                $recfrmvenf = $queryv->row_array();

// just this query less and equal
                $sqlsc = "SELECT tbl_issue_goods.*,SUM(`tbl_issue_goods_detail`.`qty`) as igsumq,SUM(`tbl_issue_goods_detail`.`returns`) as rfcustomer  FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE `issuedate`<='$fromdate' AND `tbl_issue_goods_detail`.`returns`!='' AND `tbl_issue_goods_detail`.`itemid`=$itemid";
                $querysc = $this->db->query($sqlsc);
                $saltcusf = $querysc->row_array();
/*echo $querys['qty']."<br>";
echo $recfrmvenf['Dgsumq']."<br>";
echo $saltcusf['igsumq'];
exit;*/
				// $opgbalfilled = $querys['qty']+$recfrmvenf['Dgsumq']-$saltcusf['igsumq'];
				//$filledstock = $querys['qty'];*/

				/*   opening balnace end     */
				/*   rest four columns b/w date for filled     */

				$sqlbdf = "SELECT * from `tbl_shop_opening` WHERE `date` BETWEEN '$fromdate' AND '$todate' AND `type`='Filled' AND `materialcode`=$itemid";
				$querybdf = $this->db->query($sqlbdf)->row_array();

				$sqlvv = "SELECT tbl_goodsreceiving.*,SUM(`tbl_goodsreceiving_detail`.`quantity`) as Dgsumq,SUM(`tbl_goodsreceiving_detail`.`ereturn`) as otvendor  FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate` BETWEEN '$fromdate' AND '$todate' AND `tbl_goodsreceiving_detail`.`type`='Filled' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
				$queryvv = $this->db->query($sqlvv);
				$recfrmvenff = $queryvv->row_array();

				$sqlscc = "SELECT tbl_issue_goods.*,SUM(`tbl_issue_goods_detail`.`qty`) as igsumq,SUM(`tbl_issue_goods_detail`.`returns`) as rfcustomer  FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE `issuedate` BETWEEN '$fromdate' AND '$todate' AND `tbl_issue_goods_detail`.`returns`!='' AND `tbl_issue_goods_detail`.`itemid`=$itemid";
				$queryscc = $this->db->query($sqlscc);
				$saltcusff = $queryscc->row_array();

				/*   end rest four columns b/w date for filled   */
				/* end here is code for filled */
				/* here is code for empty */

				$sqls = "SELECT  * from `tbl_shop_opening` WHERE `date`<='$fromdate' AND  `type`='Empty' AND `materialcode`=$itemid";
				$querys = $this->db->query($sqls)->row_array();

				//$sqlsc ="SELECT SUM(`qty`) as igsumq,SUM(`returns`) as rfcustomer from `tbl_issue_goods_detail` WHERE `returns`='' AND `itemid`=$itemid";
				$sqlsc = "SELECT  COALESCE(SUM(`tbl_issue_goods_detail`.`returns`),0) as rfcustomer  FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE `issuedate`<='$fromdate'   AND `tbl_issue_goods_detail`.`itemid`=$itemid";
				$querysc = $this->db->query($sqlsc);
				$saltcuse = $querysc->row_array();

				//$sqlv ="SELECT SUM(`quantity`) as Dgsumq,SUM(`ereturn`) as otvendor from `tbl_goodsreceiving_detail` WHERE `type`='Empty' AND `itemid`=$itemid";
				$sqlv = "SELECT COALESCE(SUM(`tbl_goodsreceiving_detail`.`quantity`),0) as Dgsumq   FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<='$fromdate' AND  `tbl_goodsreceiving_detail`.`type`='Empty' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
				$queryv = $this->db->query($sqlv);
				$recfrmvene = $queryv->row_array();



				$sqlv_e = "SELECT  COALESCE(SUM(`tbl_goodsreceiving_detail`.`ereturn`),0) as otvendor  FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<='$fromdate'   AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
				$queryv_e = $this->db->query($sqlv_e);
				$recfrmvene_e = $queryv_e->row_array();


				$sqlreturn = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returnqty  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `irdate`<='$fromdate' AND `tbl_issue_return_detail`.`type`='Empty' AND `tbl_issue_return`.`type`='purchasereturn' AND `tbl_issue_return_detail`.`itemid`=$itemid";
				$queryreturn = $this->db->query($sqlreturn);
				$return_qty = $queryreturn->row_array();


				$sqlreturn_sale = "SELECT  COALESCE(SUM(`tbl_issue_return_detail`.`qty`),0) as returnqty  FROM `tbl_issue_return` INNER JOIN `tbl_issue_return_detail` ON `tbl_issue_return`.`irnos` = `tbl_issue_return_detail`.`irnos` WHERE `irdate`<='$fromdate' AND `tbl_issue_return_detail`.`type`='Empty' AND `tbl_issue_return`.`type`='salereturn' AND `tbl_issue_return_detail`.`itemid`=$itemid";
				$queryreturn_sale = $this->db->query($sqlreturn_sale);
				$return_qty_sale = $queryreturn_sale->row_array();


				//echo $querys['qty'];
				//echo $saltcuse['igsumq'];
				//echo $recfrmvene['otvendor'];
				//echo $recfrmvene['Dgsumq'];
				//exit;
				//pm($return_qty['returnqty']);
				//pm($recfrmvene_e['otvendor']);
				//pm($return_qty['returnqty']);
				$opgbalempty = $querys['qty'] + $saltcuse['rfcustomer'] - $return_qty['returnqty'] + $return_qty_sale['returnqty'] + $recfrmvene['Dgsumq'] - $recfrmvene_e['otvendor'] + $recfrmvenf_con['from_qty'] - $recfrmvenf_con_to['to_qty'];
				//$opgbalempty = $querys['qty'];



				/*$sqls ="SELECT  * from `tbl_shop_opening` WHERE `date`<='$fromdate' AND `type`='Empty' AND `materialcode`=$itemid";
                $querys = $this->db->query($sqls)->row_array();

                //$sqlsc ="SELECT SUM(`qty`) as igsumq,SUM(`returns`) as rfcustomer from `tbl_issue_goods_detail` WHERE `returns`='' AND `itemid`=$itemid";
                $sqlsc = "SELECT tbl_issue_goods.*,SUM(`tbl_issue_goods_detail`.`qty`) as igsumq,SUM(`tbl_issue_goods_detail`.`returns`) as rfcustomer  FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE `issuedate`<='$fromdate' AND `tbl_issue_goods_detail`.`returns`='' AND `tbl_issue_goods_detail`.`itemid`=$itemid";
                $querysc = $this->db->query($sqlsc);
                $saltcuse = $querysc->row_array();

                //$sqlv ="SELECT SUM(`quantity`) as Dgsumq,SUM(`ereturn`) as otvendor from `tbl_goodsreceiving_detail` WHERE `type`='Empty' AND `itemid`=$itemid";
                $sqlv = "SELECT tbl_goodsreceiving.*,SUM(`tbl_goodsreceiving_detail`.`quantity`) as Dgsumq,SUM(`tbl_goodsreceiving_detail`.`ereturn`) as otvendor  FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<='$fromdate' AND `tbl_goodsreceiving_detail`.`type`='Empty' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
                $queryv = $this->db->query($sqlv);
                $recfrmvene=$queryv->row_array();

                //$opgbalempty = $querys['qty']+$saltcuse['igsumq']-$recfrmvene['Dgsumq'];
                $opgbalempty = $querys['qty']+$saltcuse['igsumq']+$recfrmvene['Dgsumq']-$recfrmvene['otvendor'];
                */

				/* end here is code for empty */
				/*   rest four columns b/w date for empty    */

				$sqlbdf = "SELECT * from `tbl_shop_opening` WHERE `date` BETWEEN '$fromdate' AND '$todate' AND `type`='Empty' AND `materialcode`=$itemid";
				$querybdf = $this->db->query($sqlbdf)->row_array();

				$sqlsccc = "SELECT tbl_issue_goods.*,SUM(`tbl_issue_goods_detail`.`qty`) as igsumq,SUM(`tbl_issue_goods_detail`.`returns`) as rfcustomer  FROM `tbl_issue_goods` INNER JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods`.`issuenos` = `tbl_issue_goods_detail`.`ig_detail_id` WHERE `issuedate` BETWEEN '$fromdate' AND '$todate' AND `tbl_issue_goods_detail`.`returns`='' AND `tbl_issue_goods_detail`.`itemid`=$itemid";
				$querysccc = $this->db->query($sqlsccc);
				$saltcusee = $querysccc->row_array();



				$sqlvvv = "SELECT tbl_goodsreceiving.*,SUM(`tbl_goodsreceiving_detail`.`quantity`) as Dgsumq,SUM(`tbl_goodsreceiving_detail`.`ereturn`) as otvendor  FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate` BETWEEN '$fromdate' AND '$todate' AND `tbl_goodsreceiving_detail`.`type`='Empty' AND `tbl_goodsreceiving_detail`.`itemid`=$itemid";
				$queryvvv = $this->db->query($sqlvvv);
				$recfrmvenee = $queryvvv->row_array();


				/*   end rest four columns b/w date for empty    */
				/* end here is code for empty */

				$datas[] = array(
					'itemid' => $itemname,
					'catcode' => $catcode,
					'filled' => $opgbalfilled,
					'empty' => $opgbalempty,
					'RFVF' => $recfrmvenff['Dgsumq'],
					'otvendorf' => $recfrmvenff['otvendor'],
					'saleoutf' => $saltcusff['igsumq'],
					'rfcustomerf' => $saltcusff['rfcustomer'],
					'RFVE' => $recfrmvenee['Dgsumq'],
					'otvendore' => $recfrmvenee['otvendor'],
					'saleoute' => $saltcusee['igsumq'],
					'rfcustomere' => $saltcusee['rfcustomer'],
					'fromdate' => $fromdate,
					'todate' => $todate,
					//'filledstock'=>$filledstock,
				);
			}
		}

		return $datas;
	}
}
