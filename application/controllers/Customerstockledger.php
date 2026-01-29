<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Customerstockledger extends CI_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->load->model(array(
			"mod_customerstockledger",
			"mod_common",
			"mod_admin",
			"mod_customer",
			"mod_customerledger",
			"mod_salelpg"
		));
	}

	public function index()
	{
		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
		$customer_code = $fix_code['customer_code'];
		if ($customer_code != '') {
			$where_customer = " and tblacode.general='$customer_code'  ";
		} else {
			$where_customer = "";
		}
		$data['customer_list'] = $this->db->query("select * from tblacode where atype='Child' $where_customer")->result_array();
		$data['sale_point_id'] = $sale_point_id = $fix_code['sale_point_id'];

		if ($sale_point_id != '') {
			$where_sale_point_id = "where sale_point_id='$sale_point_id'  ";
		} else {
			$where_sale_point_id = "";
		}
		$data['location'] = $this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();
		$table = 'tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table, "*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Stock Ledger";
		$this->load->view($this->session->userdata('language') . "/Customerstockledger/search", $data);
	}


	public function openBalance_expenses_current()
	{



		if ($this->input->server('REQUEST_METHOD') == 'POST') {

			$data['report'] = $this->input->post();
			$data['total_balance'] = $this->mod_customerstockledger->get_total_balance_expenses_current($this->input->post());
			$table = 'tbl_company';
			$data['company'] = $this->mod_common->get_all_records($table, "*");
			$table = 'tblmaterial_coding';
			$data['items'] = $this->mod_common->get_all_records($table, "*");
			$data["filter"] = '';
			#----load view----------#
			$data["title"] = "Expenses Balance";
			$this->load->view($this->session->userdata('language') . "/Customerstockledger/net_balance_expenses_current", $data);
		} else {

			$login_user = $this->session->userdata('id');
			$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			$date_array = array('from_date' => date('Y-m-d'), 'to_date' => date('Y-m-d'), 'sale_point_id' => $sale_point_id);
			$data['report'] = $date_array;
			$data['total_balance'] = $this->mod_customerstockledger->get_total_balance_expenses_current($date_array);
			$table = 'tbl_company';
			$data['company'] = $this->mod_common->get_all_records($table, "*");

			$table = 'tblmaterial_coding';
			$data['items'] = $this->mod_common->get_all_records($table, "*");
			$data["filter"] = '';
			#----load view----------#
			$data["title"] = "Expenses Balance";
			$this->load->view($this->session->userdata('language') . "/Customerstockledger/net_balance_expenses_current", $data);
		}

	}
	public function openBalance_expenses()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {

			//pm($this->input->post());exit;
			$data['report'] = $this->input->post();
			$data['total_balance'] = $this->mod_customerstockledger->get_total_balance_expenses($this->input->post());
			//pm($data);
			//die;


			$table = 'tbl_company';
			$data['company'] = $this->mod_common->get_all_records($table, "*");

			$table = 'tblmaterial_coding';
			$data['items'] = $this->mod_common->get_all_records($table, "*");
			$data["filter"] = '';
			#----load view----------#
			$data["title"] = "Expenses Balance";
			$this->load->view($this->session->userdata('language') . "/Customerstockledger/net_balance_expenses", $data);
		} else {

			$date_array = array('from_date' => date('Y-m-d'), 'to_date' => date('Y-m-d'));
			$data['report'] = $date_array;
			$data['total_balance'] = $this->mod_customerstockledger->get_total_balance_expenses($date_array);


			$table = 'tbl_company';
			$data['company'] = $this->mod_common->get_all_records($table, "*");

			$table = 'tblmaterial_coding';
			$data['items'] = $this->mod_common->get_all_records($table, "*");
			$data["filter"] = '';
			#----load view----------#
			$data["title"] = "Expenses Balance";
			$this->load->view($this->session->userdata('language') . "/Customerstockledger/net_balance_expenses", $data);
		}

	}
	public function customerSale()
	{


		$data['total_balance'] = $this->mod_customerstockledger->get_total_customer_stock();
		//pm($data['total_balance']);

		$table = 'tbl_company';
		$data['company'] = $this->mod_common->get_all_records($table, "*");

		$table = 'tblmaterial_coding';
		$data['items'] = $this->mod_common->select_array_records($table, "*", "catcode='1' ");

		//pm($data['items']);
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Sale";

		//pm($data);

		$this->load->view($this->session->userdata('language') . "/Customerstockledger/customer_sale", $data);


	}

	public function openBalance()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {

			$data['report'] = $this->input->post();
			$data['total_balance'] = $this->mod_customerstockledger->get_total_balance_date($this->input->post());

			//pm($data['total_balance']);
			$data['to_date'] = $to_date = $this->input->post("to_date");

			$table = 'tbl_company';
			$data['company'] = $this->mod_common->get_all_records($table, "*");

			$table = 'tblmaterial_coding';
			$data['items'] = $this->mod_common->get_all_records($table, "*");
			$data["filter"] = '';
			#----load view----------#
			$data["title"] = "Receivable";
			$this->load->view($this->session->userdata('language') . "/Customerstockledger/net_balance", $data);

		} else {



			$date_array = array('from_date' => date('Y-m-d'), 'to_date' => date('Y-m-d'));
			$data['report'] = $date_array;
			// pm($data);
			// die;

			$data['total_balance'] = $this->mod_customerstockledger->get_total_balance1($date_array);
			$data['to_date'] = $to_date = date('Y-m-d');
			//pm($data['total_balance']);

			$table = 'tbl_company';
			$data['company'] = $this->mod_common->get_all_records($table, "*");

			$table = 'tblmaterial_coding';
			$data['items'] = $this->mod_common->get_all_records($table, "*");
			$data["filter"] = '';
			#----load view----------#
			$data["title"] = "Receivable";
			$this->load->view($this->session->userdata('language') . "/Customerstockledger/net_balance", $data);

		}
	}

	public function newpdf()
	{

		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$data['to_date'] = $to_date = $this->input->post("to_datee");

			$date_array = array('from_date' => date('Y-m-d'), 'to_date' => $to_date);
			$data['report'] = $date_array;


			$data['total_balance'] = $this->mod_customerstockledger->get_total_balance_date($date_array);



			$table = 'tbl_company';
			$data['company'] = $this->mod_common->get_all_records($table, "*");



		}


		$profilename = $from_date;
		// $profilename1 =  $to_date;
		// $profilename2 =  $type;

		//pm($data);


		$this->load->view($this->session->userdata('language') . "/Customerstockledger/pdffile", $data);

		$this->load->library('pdf');
		$html = $this->output->get_output();
		$this->dompdf->loadHtml($html);
		$this->dompdf->setPaper('A4', 'landscape');
		$this->dompdf->render();



		$this->dompdf->stream($profilename . ".pdf", array("Attachment" => 0));
	}

	public function openBalance_pay()
	{

		if ($this->input->server('REQUEST_METHOD') == 'POST') {


			$data['report'] = $this->input->post();



			$data['total_balance'] = $this->mod_customerstockledger->get_total_balance_pay($this->input->post());

			$table = 'tbl_company';
			$data['company'] = $this->mod_common->get_all_records($table, "*");

			$table = 'tblmaterial_coding';
			$data['items'] = $this->mod_common->get_all_records($table, "*");
			$data["filter"] = '';
			#----load view----------#


			$data["title"] = "Payables";
			$this->load->view($this->session->userdata('language') . "/Customerstockledger/net_balance_pay", $data);


		} else {




			$date_array = array('from_date' => date('Y-m-d'), 'to_date' => date('Y-m-d'));
			$data['report'] = $date_array;
			// pm($data);
			// die;

			$data['total_balance'] = $this->mod_customerstockledger->get_total_balance_pay($date_array);


			$table = 'tbl_company';
			$data['company'] = $this->mod_common->get_all_records($table, "*");

			$table = 'tblmaterial_coding';
			$data['items'] = $this->mod_common->get_all_records($table, "*");
			$data["filter"] = '';
			#----load view----------#
			$data["title"] = "Payables";
			$this->load->view($this->session->userdata('language') . "/Customerstockledger/net_balance_pay", $data);

		}
	}
	public function report()
	{
		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
		$customer_code = $fix_code['customer_code'];
		if ($customer_code != '') {
			$where_customer = " and tblacode.general='$customer_code'  ";
		} else {
			$where_customer = "";
		}
		$data['customer_list'] = $this->db->query("select * from tblacode where atype='Child' $where_customer")->result_array();
		$data['sale_point_id'] = $sale_point_id = $fix_code['sale_point_id'];

		if ($sale_point_id != '') {
			$where_sale_point_id = "where sale_point_id='$sale_point_id'  ";
		} else {
			$where_sale_point_id = "";
		}
		$data['location'] = $this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();
		//pm($this->input->post());exit;
		if ($this->input->server('REQUEST_METHOD') == 'POST') {

			$data['customer_ledger_report'] = $this->mod_customerledger->get_report($this->input->post());
			$total_debit_view = 0;
			$total_credit_view = 0;
			$total_balance_view = 0;
			$total_record = count($data['customer_ledger_report']) - 1;

			foreach ($data['customer_ledger_report'] as $key => $value) {

				$total_debit_view += $value['debit'];
				$total_credit_view += $value['credit'];
				$total_balance_view += $value['balance'];
			}
			$data['total_debit_view'] = $total_debit_view;
			$data['total_credit_view'] = $total_credit_view;
			$data['total_balance_view'] = $data['customer_ledger_report'][$total_record]['tbalance'];

			$data['acode'] = $this->input->post('acode');

			$data['daterange'] = trim($this->input->post('from_date') . '/' . $this->input->post('to_date'));

			$data['name'] = $this->input->post('name');

			$table = 'tblacode';
			$where = "acode='" . $data['acode'] . "'";
			$data['name'] = $this->mod_common->select_single_records($table, $where);



			$data['report'] = $this->mod_customerstockledger->get_opening($this->input->post(), 1);

			$data['sale'] = $this->mod_customerstockledger->getsale($this->input->post());
			//pm($data['sale']);exit();

			$data['return'] = $this->mod_customerstockledger->getreturn($this->input->post());
			$data['return_wo_sec'] = $this->mod_customerstockledger->getreturn_wo_sec($this->input->post());


			if ($data['sale'] && $data['return'] && $data['return_wo_sec']) {
				$data['salereturn'] = array_merge($data['sale'], $data['return'], $data['return_wo_sec']);
			} elseif ($data['sale']) {
				$data['salereturn'] = $data['sale'];
			} else {
				$data['salereturn'] = $data['return'];
			}


			$table = 'tbl_company';
			$data['company'] = $this->mod_common->get_all_records($table, "*");

			$tables = 'tblmaterial_coding';

			$where_cat_id = array('catcode' => 1);

			$data['itemname'] = $this->mod_common->select_array_records('tblmaterial_coding', "*", $where_cat_id);
			$data['itemname_return'] = $this->mod_common->get_all_records($tables, "*");
			$data['scode'] = $this->input->post('scode');
			$data['location'] = $this->input->post('location');
			if ($data['opening']) {

				$data["title"] = "Customer Stock Ledger Report";
				$this->load->view($this->session->userdata('language') . "/Customerstockledger/single", $data);
			} else {
				$data["title"] = "Customer Stock Ledger Report";
				$this->load->view($this->session->userdata('language') . "/Customerstockledger/single", $data);
			}
		} else {
			//$data["filter"] = 'add';
			$data["title"] = "Customer Stock Ledger Report";
			$this->load->view($this->session->userdata('language') . "/Customerstockledger/single", $data);
		}
	}


	public function detail($id)
	{
		if ($id) {
			$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
			$table = 'tblmaterial_coding';
			$data['item_list'] = $this->mod_common->get_all_records($table, "*");
			$table = 'tbl_issue_goods';
			$where = "issuenos='$id'";
			$data['single_edit'] = $this->mod_common->select_single_records($table, $where);

			$data['edit_list'] = $this->mod_salelpg->edit_salelpg($id);
			//echo '<pre>';print_r($data['edit_list']);exit;
			$data["filter"] = '';
			#----load view----------#
			$data["title"] = "Return Report Detail";
			$this->load->view($this->session->userdata('language') . "/Customerstockledger/single", $data);
		}
	}


}
