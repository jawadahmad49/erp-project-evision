<?php

class Mod_bookorder extends CI_Model
{

	function __construct()
	{

		parent::__construct();
		error_reporting(0);
	}

	public function add_bookorder($data)
	{
		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("SELECT location from tbl_admin where id='$login_user'")->row_array()['location'];
		$ins_array = array(
			"acode" => $data['customer'],
			"date" => $data['date'],
			'order_time' => $data['o_time'],
			"bookingsource" => "Direct",
			"salepoint" => $sale_point_id,
			"status" => $data['status'],
			"d_customer" => $data['d_customer'],
			"remarks" => $data['remarks'],
			//"amount" => $data['amount'],
			"created_dt" => date("Y-m-d"),
			"created_by" => $this->session->userdata('id'),

		);
		#------------add record---------------#
		$table = "tbl_orderbooking";
		$add_goods = $this->mod_common->insert_into_table($table, $ins_array);
		$insert_id = $add_goods;
		if ($add_goods) {
			return $this->multipleitems_againstid($this->input->post(), $insert_id, 'tbl_orderbooking_detail');
		} else {
			return false;
		}
	}

	public function multipleitems_againstid($data, $goodsid, $table)
	{
		$datas = array();
		foreach ($data['item'] as $key => $value) {
			$datas[] = array(
				'orderid' => $goodsid,
				'itemid' => $data['item'][$key],
				'quantity' => $data['qty'][$key],
				'refillnew' => $data['refillnew'][$key],
				'unit_price' => $data['unit_price'][$key],
				'amount' => $data['amount'][$key],
			);
		
			$netamount += $data['amounttotal'][$key];
			$netamountr += $data['amountreceived'][$key];
		}
		return $this->db->insert_batch($table, $datas);
	}

	// SELECT `tbl_issue_goods`.*, `tblacode`.*, SUM(`tbl_issue_goods_detail`.`total_amount`) FROM `tbl_issue_goods` JOIN `tblacode` ON `tbl_issue_goods`.`issuedto` = `tblacode`.`acode` JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods_detail`.`ig_detail_id`= `tbl_issue_goods`.`issuenos` GROUP BY `ig_detail_id` ORDER BY `issuenos` DESC
	public function manage_bookorder($from, $to, $sale_point_id, $status)
	{
		$this->db->select('tbl_orderbooking.*,tbl_orderbooking_detail.orderid,tblacode.aname');
		$this->db->from('tbl_orderbooking');
		$this->db->join('tbl_orderbooking_detail', 'tbl_orderbooking.id = tbl_orderbooking_detail.orderid');
		$this->db->join('tblacode', ' tbl_orderbooking.acode= tblacode.acode');
		$this->db->where('tbl_orderbooking.date >=', $from);
		$this->db->where('tbl_orderbooking.date <=', $to);
		$this->db->where('tbl_orderbooking.salepoint =', $sale_point_id);
		$this->db->where('tbl_orderbooking.status =', $status);
		$this->db->group_by('tbl_orderbooking.id');
		$this->db->order_by("tbl_orderbooking.id", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function edit_bookorder($id)
	{
		$this->db->select('tbl_orderbooking.*,tbl_orderbooking_detail.id as detailid, tbl_orderbooking_detail.*');
		$this->db->from('tbl_orderbooking');
		$this->db->join('tbl_orderbooking_detail', 'tbl_orderbooking.id = tbl_orderbooking_detail.orderid');
		$this->db->join('tblacode', 'tbl_orderbooking.acode = tblacode.acode');
		$this->db->where('tbl_orderbooking.id=', $id);
		$this->db->order_by("tbl_orderbooking.id", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function update_bookorder($data)
	{
		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$ins_array = array(
			"acode" => $data['customer'],
			"d_customer" => $data['d_customer'],
			"date" => $data['date'],
			'order_time' => $data['o_time'],
			'delivery_dt' => $data['d_date'],
			'delivery_time' => $data['d_time'],
			"bookingsource" => "Direct",
			"delivery" => $data['delivery'],
			"salepoint" => $sale_point_id,
			"status" => $data['status'],
			"remarks" => $data['remarks'],
			"paymode" => $data['pay_mode'],
			"received_amount" => $data['enter_amount_cash'],
			"updated_date" => date('Y-m-d h:i:sa'),
			"updated_by_user" => $sale_point_id,
			// "unit_price" => $data['unit_price'],

		);

		#----------- add record---------------#
		$id = $_POST['id'];
		$table = "tbl_orderbooking";
		$where = "id= '$id'";
		$update_goods = $this->mod_common->update_table($table, $where, $ins_array);

		if ($update_goods) {
			return $this->updatemultiple_againstid($data, $id, 'tbl_orderbooking_detail');
		} else {
			return false;
		}
	}

	public function updatemultiple_againstid($data, $goodsid, $table)
	{ 
		$datas = array();
		$datai = array();
		foreach ($data['item'] as $key => $value) {
			$datas[] = array(
				'id' => $data['items_detailid'][$key],
				'orderid' => $goodsid,
				'itemid' => $data['item'][$key],
				'quantity' => $data['qty'][$key],
				'refillnew' => $data['refillnew'][$key],
				'unit_price' => $data['unit_price'][$key],
				'amount' => $data['amount'][$key],
				'return_qty' => $data['return_qty'][$key],
			);
		
			$netamount += $data['amounttotal'][$key];
			$netamountr += $data['amountreceived'][$key];
		}
		
		foreach ($datas as $key => $value) {
			if ($value['id']) {
				$datau[] = $value;
			} else {
				$datai[] = $value;
			}
		}

		if ($datau) {
			$this->db->update_batch($table, $datau, 'id');
		}
		if ($datai) {
			$this->db->insert_batch($table, $datai);
		}

		return true;
		//return $this->db->query($updates);

	}
}
