<?php

class Mod_complaints_queries extends CI_Model
{

	function __construct()
	{

		parent::__construct();
		error_reporting(0);
	}


	public function add_complaint_query($data)
	{
		$fwd = $data['forwarded'];

		if ($data['complaint_queries_list'] == "17") {
			$fdate = $data['follow_date'];
		} else {
			$fdate = '';
		}
		$cus = $data['customer'];
		if ($cus == '') {
			$this->load->model('mod_customer');
			$general = trim($_POST["general"]);
			$data['datas'] = $this->mod_customer->accountcode_forcustomer($general);

			$code = explode("|", $data['datas']);
			$code1 = $code[0];
			//echo $code1;
			//echo "<pre>";print_r($data['datas']);exit;

			$datacode = $data['datas'];
			$adata['acode'] = $code1;
			$adata['aname'] = trim($_POST["customername"]);
			$adata['email'] = trim($_POST["email"]);
			$adata['cell'] = trim($_POST["cellno"]);
			//	$adata['ptcl'] = trim($_POST["ptcl"]);
			//$adata['saleman_id'] = '26';
			//$adata['loccode'] = '1';
			$adata['reg_date'] = date('Y-m-d');
			$adata['ac_status'] = 'Active';
			$adata['general'] = trim($_POST["general"]);
			$adata['atype'] = "Child";
			$adata['family'] = "L";
			$adata['sledger'] = "No";
			$adata['dlimit'] = 0;
			$adata['climit'] = 0;
			$table = 'tblacode';
			$res = $this->mod_common->insert_into_table($table, $adata);
		} else {
			$code1 = $data['customer'];
		}
		$ins_array = array(
			"customer_code" => $code1,
			"reg_dt" => $data['date'],
			'reg_time' => $data['time'],
			"query_complaint" => $data["type"],
			"complaint_id" => $data['complaint_queries_list'],
			"direct_customer" => $data['d_customer'],
			"forwarded_to" => $fwd,
			"forwarded_dt" => $data['date'],
			"forwarded_time" => $data['time'],
			"sts" => $data["sts"],
			"remarks" => $data['remarks'],
			"Via" => $data['via'],
			"follow_date" => $fdate,
			"current_remarks" => $data['remarks'],
			"created_dt" => date('Y-m-d'),
			"created_by" => $this->session->userdata('id')
		);

		$edit = $_POST['reg_id'];
		//echo $edit;exit;
		if ($edit > 0) {
			$table = "tbl_complaint_registration";
			$where = "reg_id= '$edit'";
			// Add the SET clause to specify the update operation
			$this->mod_common->update_table($table, $where, $ins_array);
			$this->db->query("delete from tbl_complaint_registration_cors where reg_id = '$edit'");
		} else {
			#----------- add record---------------#
			$table = "tbl_complaint_registration";
			$add_goods = $this->mod_common->insert_into_table($table, $ins_array);
		}
		$lastid = $this->db->insert_id();

		$cors_fwd_array = array(
			'reg_id' => $lastid,
			'forwarded_by' => $this->session->userdata('id'),
			'forwarded_to' => $fwd,
			'forward_dt' => $data['date'],
			'forward_time' => $data['time'],
			'remarks' => $data['remarks'],
			'sts' => $data['sts'],
			'created_by' => $this->session->userdata('id'),
			'created_dt' => $data['date']
		);

		$cors_fwd_ins = $this->mod_common->insert_into_table("tbl_complaint_registration_cors", $cors_fwd_array);

		return $add_goods;
	}


	public function manage_bookorder()
	{
		$this->db->select('tbl_orderbooking.*,tbl_orderbooking_detail.orderid,tblacode.aname');
		$this->db->from('tbl_orderbooking');
		$this->db->join('tbl_orderbooking_detail', 'tbl_orderbooking.id = tbl_orderbooking_detail.orderid');
		$this->db->join('tblacode', ' tbl_orderbooking.acode= tblacode.acode');
		$this->db->group_by('tbl_orderbooking.id');
		$this->db->order_by("tbl_orderbooking.id", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function edit_bookorder($id)
	{
		$this->db->select('tbl_orderbooking.*,tbl_orderbooking_detail.id as detailid,tbl_orderbooking_detail.itemid,tbl_orderbooking_detail.quantity,tbl_orderbooking_detail.refillnew,tblacode.aname');
		$this->db->from('tbl_orderbooking');
		$this->db->join('tbl_orderbooking_detail', 'tbl_orderbooking.id = tbl_orderbooking_detail.orderid');
		$this->db->join('tblacode', 'tbl_orderbooking.acode = tblacode.acode');
		$this->db->where('tbl_orderbooking.id=', $id);
		$this->db->order_by("tbl_orderbooking.id", "desc");
		$query = $this->db->get();
		//pm($query->result_array());
		return $query->result_array();
	}

	public function update_bookorder($data)
	{
		$ins_array = array(
			"acode" => $data['customer'],
			"date" => $data['date'],
			'order_time' => $data['o_time'],
			'delivery_date' => $data['d_date'],
			'delivery_time' => $data['d_time'],
			"bookingsource" => "Direct",
			"delivery" => $data['delivery'],
			"salepoint" => $data['salepoint'],
			"status" => "New",
			"cellno" => $data['cellno'],
			"address" => $data['address'],
			"remarks" => $data['remarks'],

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
}
