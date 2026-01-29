<?php

defined('BASEPATH') or exit('No direct script access allowed');



class Account_bank_payment extends CI_Controller

{



	public function __construct()

	{

		parent::__construct();



		$this->load->model(array(

			"mod_transaction", "mod_common", "mod_admin", "mod_customerledger", "mod_voucher" 

		));

	}



	public function index()

	{

		if (isset($_POST['submit'])) {

			$from_date = $data["from_date"] = date("Y-m-d", strtotime($_POST['from']));



			$to_date = $data["to_date"] = date("Y-m-d", strtotime($_POST['to']));

		} else {

			$from_date = $data["from_date"] = date('Y-m-d', strtotime('-7 day'));

			$to_date = $data["to_date"] = date('Y-m-d');

		}

		$login_user = $this->session->userdata('id');

		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

		if ($sale_point_id == '') {

			$where_sale_point_id = "and isnull(sale_point_id)";

		} else {

			$where_sale_point_id = "and sale_point_id='$sale_point_id'";

		}



		$data['paymentreceipt_list'] = $this->db->query("select * from tbltrans_master where vtype in ('BP','BR') and created_date between '$from_date' and '$to_date' $where_sale_point_id order by masterid  desc")->result_array();



		//pm($data['paymentreceipt_list']);



		$data["filter"] = '';

		#----load view----------#

		$data["title"] = "Manage Bank Payment/Receipt";

		$this->load->view($this->session->userdata('language') . "/Account_bank_payment/manage_paymentreceipt", $data);

	}





	public function add()

	{





		if ($this->input->server('REQUEST_METHOD') == 'POST') {

			$login_user = $this->session->userdata('id');

			$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

			$fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();

			$cash_code = $fix_code['cash_code'];





			///pm($this->input->post());

			$this->db->trans_start();



			if (empty($this->input->post("acode"))) {

				$this->session->set_flashdata('err_message', 'Something went wrong.');

				redirect(SURL . 'Account_bank_payment/');

			}



			$totalvalue = array_sum($this->input->post("amount"));

			$type = $this->input->post("type");

			$bank = $this->input->post("bank");





			$array = array(

				"vno" => $this->input->post("transcode"),

				"vtype" => $type,

				"damount" => $totalvalue,

				"camount" => $totalvalue,

				"created_date" => $this->input->post("date"),

				"sale_point_id" => $sale_point_id,

			);



			if (!empty($this->input->post("edit"))) {



				$insert = $this->input->post("edit");

				$this->mod_common->update_table("tbltrans_master", array("masterid" => $insert), $array);
 
				$this->db->query("delete from tbltrans_detail where ig_detail_id='$insert'");

			 
				$vno = $this->input->post("transcode");

			} else {

				$insert = $this->mod_common->insert_into_table("tbltrans_master", $array);
 
				$userid = $this->session->userdata('id');





				$data['cash_in_hand'] = $this->db->query("select sum(damount)-sum(camount) as cash_in_hand from tbltrans_detail where acode='$cash_code'")->row_array()['cash_in_hand'];

				// pm($data['cash_in_hand']);

				$trans_id = $this->db->query("select max(trans_id) as trans_id from tbltrans_detail where sale_point_id='$sale_point_id' and vtype='$type'")->row_array()['trans_id'];

				if ($trans_id == '') {

					$trans_id = 1;

				} else {

					$trans_id = $trans_id + 1;

				}



				$vno = $sale_point_id . "-" . $type . "-" . $trans_id;

				$this->db->query("update tbltrans_master set vno='$vno' where masterid='$insert'");

			}

			//echo $vno;exit;









			$transid = explode('-', $vno);

			$trans_id = $transid[2];

			$i = 0;

			$j = 1;



			foreach ($this->input->post("acode") as $key => $value) {

				$acode = $this->input->post("acode")[$i];

				$aname = $this->db->query("select aname  from tblacode where acode='$acode'")->row_array()['aname'];

				if ($type == 'BP') {

					$camout = $this->input->post("amount")[$i];

					$damout = '0';

				} else {

					$damout = $this->input->post("amount")[$i];

					$camout = '0';

				}

				$array = array(

					"vno" => $vno,

					"ig_detail_id" => $insert,

					"srno" => $j,

					"acode" => $this->input->post("acode")[$i],



					"direct_customer"=> $this->input->post('d_customer')[$i],

					"damount" => $camout,

					"camount" => $damout,

					"chequeno" => $this->input->post("chequeno")[$i],

					"remarks" => $this->input->post("remarks")[$i],

					"vtype" => $type,

					"trans_id" => $trans_id,

					"sale_point_id" => $sale_point_id,

					"vdate" => $this->input->post("date")

				);



				$this->mod_common->insert_into_table("tbltrans_detail", $array);
 
				$j++;

				if ($type == 'BP') {

					$nar = $this->input->post("remarks")[$i] . " PAID TO " . $aname;

				} else {

					$nar = $this->input->post("remarks")[$i] . " Receved From " . $aname;

				}



				$array = array(

					"vno" => $vno,

					"ig_detail_id" => $insert,

					"srno" => $j,

					"acode" => $bank,

					"damount" => $damout,

					"camount" => $camout,

					"chequeno" => $this->input->post("chequeno")[$i],

					"remarks" => $nar,

					"vtype" => $type,

					"trans_id" => $trans_id,

					"sale_point_id" => $sale_point_id,

					"vdate" => $this->input->post("date")

				);



				$add = $this->mod_common->insert_into_table("tbltrans_detail", $array);

		 

				$i++;

				$j++;

			}



			$_SESSION["vno"] = $vno;



			$this->db->trans_complete(); 

			



			if ($add) {

				if (!empty($this->input->post("edit"))) {

					$this->session->set_flashdata('ok_message', 'Updated Successfully!');

					redirect(SURL . 'Account_bank_payment/');

				} else {

					$this->session->set_flashdata('ok_message', 'Added Successfully!');

					redirect(SURL . 'Account_bank_payment/add');

				}

			} else {

				$this->session->set_flashdata('err_message', 'Adding Operation Failed.');

				redirect(SURL . 'Account_bank_payment/');

			}

		}

		$login_user = $this->session->userdata('id');

		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '309' limit 1")->row_array();

		if ($role['add'] != 1) {

			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');

			redirect(SURL . 'Account_bank_payment/index/');

		}

		$login_user = $this->session->userdata('id');

		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

		if ($sale_point_id == '0') {

			$this->session->set_flashdata('err_message', '- Admin Has No Rights To Add Bank Payment!');

			redirect(SURL . 'Account_bank_payment');

			exit();

		}

		$fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();

		$bank_code = $fix_code['bank_code'];

		if ($sale_point_id == '') {

			$data['bank'] =  $this->db->query("select * from tblacode  where atype='Child' and left(acode,6)='200401'")->result_array();

		} else {

			$data['bank'] =  $this->db->query("select * from tblacode  where atype='Child' and general in ('$bank_code')")->result_array();

		}





		$fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();

		$customer_code = $fix_code['customer_code'];

		$vendor_code = $fix_code['vendor_code'];

		$cash_code = $fix_code['cash_code'];

		$tax_pay = $fix_code['tax_pay'];

		$tax_receive = $fix_code['tax_receive'];

		$sales_code = $fix_code['sales_code'];

		$stock_code = $fix_code['stock_code'];

		$bank_code = $fix_code['bank_code'];

		$expense_code = $fix_code['expense_code'];

		$cost_of_goods_code = $fix_code['cost_of_goods_code'];

		$empty_stock_code = $fix_code['empty_stock_code'];

		$empty_sale_code = $fix_code['empty_sale_code'];

		$security_code = $fix_code['security_code'];



		$sale_point_id = $fix_code['sale_point_id'];

		$exp_code = $expense_code[0] . $expense_code[1] . $expense_code[2] . $expense_code[3] . $expense_code[4] . $expense_code[5];

		if ($sale_point_id == '') {

			// $data['aname'] =  $this->db->query("select * from tblacode  where atype='Child' ")->result_array();



		} else {

			$data['aname'] =  $this->db->query("select * from tblacode  where atype='Child' and general in('$customer_code','$vendor_code','$bank_code','$expense_code','$empty_stock_code','$empty_sale_code','$security_code') or left(acode,6)='$exp_code' or left(acode,6)='200100' or general in ('1002003000','2006001000','1003000000','1002004000','1004001000','2002001000') or tblacode.acode in ('$cash_code','$sale_point_id','$tax_pay','$tax_receive','$sales_code','$stock_code','$cost_of_goods_code')")->result_array();

		}



		//q();





		$data["filter"] = 'add';

		$data["title"] = "Add Bank Payment/Receipt";

		$this->load->view($this->session->userdata('language') . "/Account_bank_payment/add", $data);

	}

	public function detail($id)

	{



		if ($id) {

			//echo $id;exit;



			$table = 'tbl_company';

			$data['company'] = $this->mod_common->get_all_records($table, "*");





			$table = 'tbltrans_master';

			$where = "vno='$id'";

			$data['single_edit'] = $this->mod_common->select_single_records($table, $where);

			//pm($data['single_edit'] );

			$type = $data['single_edit']['vtype'];



			if ($type == 'BP') {

				$wheres = "vno='$id' and tbltrans_detail.damount>0";

				$wheress = "vno='$id' and tbltrans_detail.camount>0";

			} else {

				$wheres = "vno='$id' and tbltrans_detail.camount>0";

				$wheress = "vno='$id' and tbltrans_detail.damount>0";

			}

			$tables = 'tbltrans_detail';

			$data['paymentreceipt_list'] = $this->mod_voucher->select_trans_print_records($wheres);

			$tables = 'tbltrans_detail';



			$data['paymentreceipt_list_name'] = $this->mod_voucher->select_trans_print_records($wheress);



			//pm($data['paymentreceipt_list_name']);



			$data["filter"] = 'edit';

			$data["title"] = "Voucher Payment/Receipt";

			$this->load->view($this->session->userdata('language') . "/Account_bank_payment/single_new", $data);

		} else {

			redirect(SURL . 'Account_bank_payment');

		}

	}

	public function edit($id)

	{

		$login_user = $this->session->userdata('id');

		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '309' limit 1")->row_array();

		if ($role['edit'] != 1) {

			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');

			redirect(SURL . 'Account_bank_payment/index/');

		}



		$login_user = $this->session->userdata('id');

		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

		$fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();

		$bank_code = $fix_code['bank_code'];



		if ($sale_point_id == '') {

			$data['bank'] =  $this->db->query("select * from tblacode  where atype='Child' and left(acode,6)='200401'")->result_array();

		} else {

			$data['bank'] =  $this->db->query("select * from tblacode  where atype='Child' and general in ('$bank_code')")->result_array();

		}





		$customer_code = $fix_code['customer_code'];

		$vendor_code = $fix_code['vendor_code'];

		$cash_code = $fix_code['cash_code'];

		$tax_pay = $fix_code['tax_pay'];

		$tax_receive = $fix_code['tax_receive'];

		$sales_code = $fix_code['sales_code'];

		$stock_code = $fix_code['stock_code'];

		$bank_code = $fix_code['bank_code'];

		$expense_code = $fix_code['expense_code'];

		$cost_of_goods_code = $fix_code['cost_of_goods_code'];

		$empty_stock_code = $fix_code['empty_stock_code'];

		$empty_sale_code = $fix_code['empty_sale_code'];

		$security_code = $fix_code['security_code'];



		$sale_point_id = $fix_code['sale_point_id'];

		$exp_code = $expense_code[0] . $expense_code[1] . $expense_code[2] . $expense_code[3] . $expense_code[4] . $expense_code[5];

		if ($sale_point_id == '') {

			// $data['aname'] =  $this->db->query("select * from tblacode  where atype='Child' ")->result_array();



		} else {

			$data['aname'] =  $this->db->query("select * from tblacode  where atype='Child' and general in('$customer_code','$vendor_code','$bank_code','$expense_code','$empty_stock_code','$empty_sale_code','$security_code') or left(acode,6)='$exp_code' or left(acode,6)='200100' or general in ('1002003000','2006001000','1003000000','1002004000','1004001000','2002001000') or tblacode.acode in ('$cash_code','$sale_point_id','$tax_pay','$tax_receive','$sales_code','$stock_code','$cost_of_goods_code')")->result_array();

		}



		//q();

		$table = 'tbltrans_master';

		$where = "masterid='$id'";

		$data['single_edit'] = $this->mod_common->select_single_records($table, $where);

		$type = $data['single_edit']['vtype'];



		if ($type == 'BP') {

			$wheres = "ig_detail_id='$id' and tbltrans_detail.damount>0";

			$wheress = "ig_detail_id='$id' and tbltrans_detail.camount>0";

		} else {

			$wheres = "ig_detail_id='$id' and tbltrans_detail.camount>0";

			$wheress = "ig_detail_id='$id' and tbltrans_detail.damount>0";

		}



		$data['record'] = $this->db->query("select tbltrans_detail.*,tblacode.aname from tbltrans_detail inner join tblacode on tblacode.acode=tbltrans_detail.acode where $wheres ")->result_array();

		$data['record1'] = $this->db->query("select tbltrans_detail.*,tblacode.aname from tbltrans_detail inner join tblacode on tblacode.acode=tbltrans_detail.acode where $wheress")->result_array();

		//pm($data['record']);



		$data["filter"] = 'add';

		$data["title"] = "Edit Bank Payment/Receipt";

		$this->load->view($this->session->userdata('language') . "/Account_bank_payment/add", $data);

	}







	public function delete($id)

	{

		$login_user = $this->session->userdata('id');

		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '309' limit 1")->row_array();

		if ($role['delete'] != 1) {

			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');

			redirect(SURL . 'Account_bank_payment/index/');

		}



		$this->db->trans_start();

		$table = "tbltrans_master";

		$where = array("masterid" => $id);

		$delete = $this->mod_common->delete_record($table, $where);
 
		$table = "tbltrans_detail";

		$where = array("ig_detail_id" => $id);

		$delete = $this->mod_common->delete_record($table, $where);
 
		$this->db->trans_complete();



		if ($this->db->trans_status() === TRUE) {

			$this->session->set_flashdata('ok_message', 'You have succesfully deleted.');

			redirect(SURL . 'Account_bank_payment/');

		} else {

			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');

			redirect(SURL . 'Account_bank_payment/');

		}

	}



	public function get_Direct_Customer()                // get_Direct_Customer from ajax

	{



		$customer = $this->input->post('customer');

		//$salepoint_id = $this->input->post('salepoint_id');

		$login_user = $this->session->userdata('id');

		$salepoint_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];





		$segment = $this->db->query("select segment from tblacode where acode='$customer'")->row_array()['segment'];



		if ($segment == 'walkin' || $segment == 'home') {

			$direct_customer_detail = $this->db->query("select id,name,cell_no from tbl_direct_customer where sale_point_id='$salepoint_id' and type= '$segment' ")->result_array();

			foreach ($direct_customer_detail as $key => $data) {

?>

				<option value="<?php echo $data['id']; ?>"><?php echo ucwords($data['cell_no'] . " " . $data['name']); ?></option>



<?php }

		}

	}

}

