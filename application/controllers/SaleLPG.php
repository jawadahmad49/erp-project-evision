<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SaleLPG extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model(array(
			"mod_customer", "mod_common", "mod_salelpg", "mod_stockreport", "mod_customerledger", "mod_bank", "mod_customerstockledger"

		));
	}
	public function index()
	{

		// $sql = "show tables";
		// $servername = "localhost";
		// $username = "root";
		// $password = "";
		// $dbname = "lpg_testing";
		// $mydbname= $this->db->database;
		// 	// Create connection
		// $conn = new mysqli($servername, $username, $password,$dbname);
		// 	// Check connection
		// if ($conn->connect_error) {
		//     die("Connection failed: " . $conn->connect_error);
		// }else{
		// $hitting_table = "Tables_in_".$dbname;
		// $referenc_table = "Tables_in_".$mydbname;
		// 	$innerquery = mysqli_query($conn,$sql);
		// 	while ($allinertable = mysqli_fetch_array($innerquery)) {
		// 		$inertables[] = $allinertable[$hitting_table];
		// 	}
		// 	//pm($inertables);
		// 	$query = $this->db->query($sql)->result_array();
		// 	//pm($query);
		// 	foreach($query as $key => $value) {
		// 		$tablename = $value[$referenc_table];
		// 		if(in_array($tablename, $inertables)){
		// 		 	$sql = "describe $tablename";
		// 		 	$showtables = $this->db->query($sql)->result_array();
		// 		 	$inertabledefinition="";
		// 		 	$query = mysqli_query($conn,$sql);
		// 		 	while ($allinertable = mysqli_fetch_assoc($query)) {
		// 				$inertabledefinition[] = $allinertable;
		// 			}
		// 			$allinertableindex="";
		// 			foreach ($inertabledefinition as $key => $innewtableindex) {
		// 				$allinertableindex[]=$innewtableindex['Field'];
		// 			}
		// 			if($inertabledefinition==$showtables){
		// 				//echo $tablename." Matched"; echo "<br>";
		// 			}else{
		// 				foreach ($showtables as $key => $mewwvalue) {
		// 					 $index = $mewwvalue['Field'];
		// 					 $type = $mewwvalue['Type'];
		// 					if(in_array($index, $allinertableindex)){
		// 						//echo $index;
		// 					}else{
		// 						$sql = "ALTER TABLE $tablename ADD $index $type";
		// 						mysqli_query($conn,$sql);
		// 					}
		// 				}
		// 			}
		// 		 	//pm($inertabledefinition);
		// 		}
		// 		else{
		// 			echo $sql = "CREATE TABLE $tablename LIKE $mydbname.$tablename";echo "<br>";
		// 			mysqli_query($conn,$sql);
		// 		}
		// 	}
		// }



		// if (isset($_POST['submit'])) {
		// 	$from_date = date("Y-m-d", strtotime($_POST['from']));

		// 	$to_date = date("Y-m-d", strtotime($_POST['to']));
		// } else {
		// 	$from_date = date('Y-m-d', strtotime('-15 day'));
		// 	$to_date = date('Y-m-d');
		// }
		// $login_user = $this->session->userdata('id');
		// $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		// $data['salelpg_list'] = $this->mod_salelpg->manage_salelpg($from_date, $to_date, $sale_point_id);
		///echo "<pre>";print_r($data['salelpg_list']);exit;
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Sale LPG";
		$this->load->view($this->session->userdata('language') . "/sale_lpg/sale_lpg", $data);
	}
	public function your_ajax_endpoint()
	{
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$draw = $this->input->post('draw');
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$searchValue = $this->input->post('search')['value'];
		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$baseQuery = "SELECT COUNT(*) AS count
        FROM tbl_issue_goods ig
        LEFT JOIN tblacode ac ON ig.issuedto = ac.acode
        WHERE ig.issuedate BETWEEN '$from_date' AND '$to_date' AND ig.sale_point_id='$sale_point_id'";
		if (!empty($searchValue)) {
			$baseQuery .= " AND (ig.issuenos LIKE '%$searchValue%' OR ac.aname LIKE '%$searchValue%')";
		}

		if (!empty($searchValue)) {
			$baseQuery .= " and ";
			$baseQuery .= "issuenos LIKE '%$searchValue%' OR ";
			$baseQuery .= "aname LIKE '%$searchValue%'";
		}

		$recordsTotal = $this->db->query($baseQuery)->row()->count;

		$query = "SELECT ig.*, ac.aname
              FROM tbl_issue_goods ig
              LEFT JOIN tblacode ac ON ig.issuedto = ac.acode
              WHERE ig.issuedate BETWEEN '$from_date' AND '$to_date' AND ig.sale_point_id='$sale_point_id'";
		if (!empty($searchValue)) {
			$query .= " AND (ig.issuenos LIKE '%$searchValue%' OR ac.aname LIKE '%$searchValue%')";
		}
		if (!empty($searchValue)) {
			$query .= " and ";
			$query .= "issuenos LIKE '%$searchValue%' OR ";
			$query .= "aname LIKE '%$searchValue%'";
		}

		$query .= " LIMIT $start, $length";

		$data = array();

		$results = $this->db->query($query)->result_array();
		$last = 0;
		$i = 0;
		foreach ($results as $value) {
			$itemsList = '';
			$i++;
			$getItemsList = $this->db->get_where("tbl_issue_goods_detail", array("ig_detail_id" => $value['issuenos']))->result();

			if (count($getItemsList) > 1) {
				$comma = ",";
			} else if (count($getItemsList) == 1) {
				$comma = "";
			}

			foreach ($getItemsList as $key => $item) {
				$material = $this->db->get_where("tblmaterial_coding", array("materialcode" => $item->itemid))->row();

				$itemsList .= $material->itemname . " @ " . $item->qty . " : " . $item->total_amount . $comma;
			}

			$action_buttons = '
    <div class="hidden-sm hidden-xs action-buttons">
        <a class="green" href="' . SURL . 'SaleLPG/edit/' . $value['issuenos'] . '">
            <i class="ace-icon fa fa-pencil bigger-130"></i>
        </a>
		<a class="red" href="javascript:void(0)" onClick="confirmDelete(\'' . SURL . 'SaleLPG/delete/' . $value['trans_id'] . '\');">
				<i class="ace-icon fa fa-trash-o bigger-130"></i>
			</a>


   ';

			if ($last == $i) {
				$action_buttons .= '
        <a id="firstprint" target="blank" class="" title="Print Invoice" href="' . SURL . 'SaleLPG/detail/' . $value['issuenos'] . '">
            <i class="ace-icon fa fa-print bigger-130"></i>
        </a>
        <a id="firstprint" target="blank" class="" title="Print Receipt" href="' . SURL . 'SaleLPG/
		' . $value['issuenos'] . '">
            <i class="ace-icon fa fa-print smaller-130 orange"></i>
        </a>
        <a id="firstprint" target="blank" class="" title="Print Sales Tax Invoice" href="' . SURL . 'SaleLPG/detail_salestax/' . $value['issuenos'] . '">
            <i class="ace-icon fa fa-print bigger-130 green"></i>
        </a>';
			} else {
				$action_buttons .= '
        <a target="blank" class="" title="Print Invoice" href="' . SURL . 'SaleLPG/detail/' . $value['issuenos'] . '">
            <i class="ace-icon fa fa-print bigger-130"></i>
        </a>
        <a target="blank" class="" title="Print Receipt" href="' . SURL . 'SaleLPG/detail_small/' . $value['issuenos'] . '-' . $value['trans_id'] . '">

            <i class="ace-icon fa fa-print smaller-130 orange"></i>
        </a>
        <a target="blank" class="" title="Print Sales Tax Invoice" href="' . SURL . 'SaleLPG/detail_salestax/' . $value['issuenos'] . '">
            <i class="ace-icon fa fa-print bigger-130 green"></i>
        </a>';
			}

			$action_buttons .= '
    <a class="btn btn-xs btn-info" id="firstprint1" target="_blank" href="' . SURL . 'Delivered_sale/item_report_detail/' . $sale_point_id . '-Sale-' . $value['trans_id'] . '">
        P Voucher
    </a> </div>';


			$data[] = array(
				'issuenos' => $value['issuenos'],
				'aname' => $value['aname'],
				'itemsList' => $itemsList,
				'issuedate' => $value['issuedate'],
				'after_discount_amt' => $value['after_discount_amt'],
				'total_received' => $value['total_received'],
				'actions' => $action_buttons
			);
		}

		echo json_encode(array('draw' => $draw, 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $recordsTotal, 'data' => $data));
	}
	public function add_sale_lpg_new()
	{
		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$general = $this->db->query("select customer_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['customer_code'];
		$bank = $this->db->query("select bank_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['bank_code'];
		//$data['vendor_list'] = $this->mod_vendor->getOnlyVendors_only();
		$data['customer_list'] = $this->db->query("select * from tblacode where general='$general'")->result_array();
		$data['banks_list'] = $this->db->query("select * from tblacode where general='$bank'")->result_array();
		$data['item_list'] = $this->db->query("select * from tblmaterial_coding where catcode='1' order by materialcode")->result_array();

		$table = 'tbl_company';
		$data['pricing_centralized'] = $this->mod_common->get_all_records($table, "*");

		$this->load->view($this->session->userdata('language') . "/sale_lpg/add_sale_lpg_new", $data);
	}


	public function add_sale_lpg()
	{

		$login_user = $this->session->userdata('id');
		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '601' limit 1")->row_array();
		if ($role['add'] != 1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'SaleLPG/index/');
		}
		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		if ($sale_point_id == '0') {
			$this->session->set_flashdata('err_message', '- Admin Has No Rights To Add Sale!');
			redirect(SURL . 'SaleLPG');
			exit();
		}
		$general = $this->db->query("select customer_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['customer_code'];
		$bank = $this->db->query("select bank_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['bank_code'];
		//$data['vendor_list'] = $this->mod_vendor->getOnlyVendors_only();
		$c_date = date('Y-m-d');
		$data['customer_list'] = $this->db->query("select * from tblacode where general='$general'")->result_array();
		$data['banks_list'] = $this->db->query("select * from tblacode where general='$bank'")->result_array();
		$data['item_list'] = $this->db->query("select * from tblmaterial_coding order by materialcode")->result_array();
		$data['price'] = $this->db->query("select price from priceconfig where sale_point_id='$sale_point_id' and date>='$c_date'")->row_array()['price'];
		$data['kg_price'] = $data['price'] * 11.8;


		// echo "<pre>";var_dump($data['item_list']);
		$table = 'tbl_company';
		$data['pricing_centralized'] = $this->mod_common->get_all_records($table, "*");
		//pm($data['pricing_centralized']);

		$this->load->view($this->session->userdata('language') . "/sale_lpg/add_sale_lpg", $data);
	}
	public function add()
	{


		if ($this->input->server('REQUEST_METHOD') == 'POST') {


			$login_user = $this->session->userdata('id');
			$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			$sale_date = $this->input->post('date');
			$date_array = array('post_date>=' => $sale_date, 'sale_point_id =' => $sale_point_id);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock', $date_array);

			if (!empty($last_date)) {
				$this->session->set_flashdata('err_message', 'Already closed for this date.');
				redirect(SURL . 'SaleLPG/add_sale_lpg');
			}

			$myexplode = explode("-", $this->input->post('date'));

			$chkrecord = $this->db->query("select * from close_profit where month='" . $myexplode[1] . "' and year='" . $myexplode[0] . "'");
			if ($chkrecord->num_rows() > 0) {
				$this->session->set_flashdata('err_message', 'Already Profit closed for this date');
				redirect(SURL . 'SaleLPG/add_sale_lpg');
			}

			$this->db->trans_start();

			$add =  $this->mod_salelpg->add_sale_lpg($this->input->post());
			$this->db->trans_complete();
			$same_page = $this->db->query("select same_page from tbl_company")->row_array()['same_page'];
			if ($add and $same_page == 'true') {
				$this->session->set_flashdata('ok_message', 'Added Successfully!');
				redirect(SURL . 'SaleLPG/add_sale_lpg');
			} elseif ($add || $add == 0) {
				$this->session->set_flashdata('ok_message', 'Added Successfully!');
				redirect(SURL . 'SaleLPG/');
			} else {
				$this->session->set_flashdata('err_message', '- Error in updating please try again!');
				redirect(SURL . 'SaleLPG/');
			}
			//echo "<pre>";print_r($add);exit;

		}
		//$this->add_direct_girn();
	}

	public function add_sale_new()
	{

		$add = $this->mod_salelpg->add_sale_lpg_new($_POST);
		echo $add;
	}

	public function delete($id = '')
	{
		$login_user = $this->session->userdata('id');
		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '601' limit 1")->row_array();
		if ($role['delete'] != 1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'SaleLPG/index/');
		}
		$trans_id = $id;
		$issuenos = $this->db->query("select issuenos from tbl_issue_goods where trans_id='$trans_id'")->row_array()['issuenos'];
		//echo $id;exit();
		$date_array = array('issuenos' => $issuenos);
		$get_rec_date =  $this->mod_common->select_single_records('tbl_issue_goods', $date_array);

		//$sale_date=$this->input->post('date');
		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$date_array = array('post_date>=' => $get_rec_date['issuedate'], 'sale_point_id =' => $sale_point_id);
		$last_date =  $this->mod_common->select_single_records('tbl_posting_stock', $date_array);

		if (!empty($last_date)) {
			//echo "string";
			$this->session->set_flashdata('err_message', 'Already closed for this date');
			redirect(SURL . 'SaleLPG/');
		}
		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

		$saleid = $sale_point_id . "-Sale-" . $id;

		$this->db->trans_start();
		$table = "tbl_issue_goods";
		$where = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";
		$delete = $this->mod_common->delete_record($table, $where);

		$tables = "tbl_issue_goods_detail";
		$wheres = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";
		$deletes = $this->mod_common->delete_record($tables, $wheres);


		$tablems = "tbltrans_master";
		$wherems = "vno = '" . $saleid . "'";
		$deletems = $this->mod_common->delete_record($tablems, $wherems);

		$tableds = "tbltrans_detail";
		$whereds = "vno = '" . $saleid . "'";
		$deleteds = $this->mod_common->delete_record($tableds, $whereds);




		$this->db->trans_complete();

		if ($delete) {
			$this->session->set_flashdata('ok_message', 'You have successfully deleted.');
			redirect(SURL . 'SaleLPG/');
		} else {
			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
			redirect(SURL . 'SaleLPG/');
		}
	}



	public function delete_row_ajax()
	{

		$id = $_POST['id'];

		$saleid = $id . "-Sale";
		$receiveid = $id . "-Receive";

		// $table = "tbl_issue_goods";
		// $where = "issuenos = '" . $id . "'";
		// $delete = $this->mod_common->delete_record($table, $where);

		$tables = "tbl_issue_goods_detail";
		$wheres = "ig_detail_id = '" . $id . "'";
		$deletes = $this->mod_common->delete_record($tables, $wheres);


		$tablems = "tbltrans_master";
		$wherems = "vno = '" . $saleid . "'";
		$deletems = $this->mod_common->delete_record($tablems, $wherems);

		$tableds = "tbltrans_detail";
		$whereds = "vno = '" . $saleid . "'";
		$deleteds = $this->mod_common->delete_record($tableds, $whereds);

		$tabledr = "tbltrans_detail";
		$wheredr = "vno = '" . $receiveid . "'";
		$deletedr = $this->mod_common->delete_record($tabledr, $wheredr);

		if ($delete) {
			echo "1";
		} else {
			echo "0";
		}
	}



	public function edit($id = '')
	{
		$login_user = $this->session->userdata('id');
		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '601' limit 1")->row_array();
		if ($role['edit'] != 1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'SaleLPG/index/');
		}
		if ($id) {
			$login_user = $this->session->userdata('id');
			$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			$date_array = array('issuenos' => $id);
			$get_rec_date =  $this->mod_common->select_single_records('tbl_issue_goods', $date_array);

			//$sale_date=$this->input->post('date');
			$date_array = array('post_date>=' => $get_rec_date['issuedate'], 'sale_point_id =' => $sale_point_id);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock', $date_array);

			if (!empty($last_date)) {
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'SaleLPG/');
			}
			$login_user = $this->session->userdata('id');
			$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			if ($sale_point_id == '0') {
				$this->session->set_flashdata('err_message', '- Admin Has No Rights To Add Sale!');
				redirect(SURL . 'SaleLPG');
				exit();
			}
			$general = $this->db->query("select customer_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['customer_code'];
			$bank = $this->db->query("select bank_code from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array()['bank_code'];
			//$data['vendor_list'] = $this->mod_vendor->getOnlyVendors_only();
			$data['customer_list'] = $this->db->query("select * from tblacode where general='$general'")->result_array();
			$data['banks_list'] = $this->db->query("select * from tblacode where general='$bank'")->result_array();
			// $data['customer_list'] = $this->mod_customer->getOnlyCustomers();
			$data['item_list'] = $this->db->query("select * from tblmaterial_coding order by materialcode")->result_array();
			$table = 'tbl_issue_goods';
			$where = "issuenos='$id'";
			$data['single_edit'] = $this->mod_common->select_single_records($table, $where);
			//pm($data['single_edit']);exit;
			$data['edit_list'] = $this->mod_salelpg->edit_salelpg($id);
			foreach ($data['edit_list'] as $key => $value) {
				$detail_data = array();
				$detail_data = array(
					'item_id' => $value['itemid'],
					'date' => $data['single_edit']['issuedate'],
				);
				$data['filledstock'][] =  $this->mod_salelpg->get_details($detail_data);
			}
			$data["filter"] = '';
			$table = 'tbl_company';
			$c_date = date('Y-m-d');
			$data['price'] = $this->db->query("select price from priceconfig where sale_point_id='$sale_point_id' and date>='$c_date'")->row_array()['price'];
			$data['pricing_centralized'] = $this->mod_common->get_all_records($table, "*");
			#----load view----------#
			$data["title"] = "Update Sale LPG";
			$this->load->view($this->session->userdata('language') . "/sale_lpg/add_sale_lpg", $data);
		}
	}

	public function makenew($id = '')
	{
		if ($id) {
			$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
			$table = 'tblmaterial_coding';
			$data['item_list'] = $this->mod_common->get_all_records($table, "*");
			$table = 'tbl_orderbooking';
			$where = "id='$id'";
			$data['single_edit'] = $this->mod_common->select_single_records($table, $where);

			$data['edit_list'] = $this->mod_salelpg->edit_makeneworder($id);

			foreach ($data['edit_list'] as $key => $value) {
				$data['filledstock'][] =  $this->mod_salelpg->get_details($value['itemid'], $data['single_edit']['issuedate']);
				//$itemids = $value['itemid'];
				//$wherem = "materialcode!='$itemids'";
				//$data['item_lists'] = $this->mod_common->select_array_records($tablem,'*',$wherem);
			}
			//echo '<pre>';print_r($data['edit_list']);exit;
			$data["filter"] = '';
			$data["id"] = $id;
			#----load view----------#
			$data["title"] = "Update Sale LPG";
			$this->load->view($this->session->userdata('language') . "/sale_lpg/add_sale_lpg", $data);
		}
	}

	public function update()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {

			$login_user = $this->session->userdata('id');
			$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			$sale_date = $this->input->post('date');

			$date_array = array('post_date>=' => $sale_date, 'sale_point_id =' => $sale_point_id);

			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock', $date_array);

			if (!empty($last_date)) {
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'SaleLPG');
			}



			$add_salelpg =  $this->mod_salelpg->update_sale_lpg($this->input->post());
			//$this->db->trans_complete();
			//echo "<pre>";print_r($add_salelpg);exit;
			if ($add_salelpg || $add_salelpg == 0) {
				$this->session->set_flashdata('ok_message', '- Updated Successfully!');
				redirect(SURL . 'SaleLPG/');
			} else {
				$this->session->set_flashdata('err_message', '- Error in updating please try again!');
				redirect(SURL . 'SaleLPG/');
			}
		}
		//$this->add_direct_girn();
	}

	// function record_delete()
	// {
	// 	$login_user=$this->session->userdata('id');
	//        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
	// 	$id = $_POST['parentid'];
	// 	$saleid=$sale_point_id."-Sale-".$id;



	// 	$this->db->where('trans_id',$id,'sale_point_id',$sale_point_id);
	// 	$count = $this->db->count_all_results('tbl_issue_goods_detail');

	//        $tablems = "tbltrans_master";
	//        $wherems = "vno = '".$saleid."'";
	//        $deletems = $this->mod_common->delete_record($tablems, $wherems);

	//        $tableds = "tbltrans_detail";
	//        $whereds = "vno = '".$saleid."'";
	//        $deleteds = $this->mod_common->delete_record($tableds, $whereds);



	//        $table = "tbl_issue_goods_detail";
	//        $deleteid=	$this->input->post('deleteid');
	//        $where = "srno = '" . $deleteid . "'";
	//        $delete_goods = $this->mod_common->delete_record($table, $where);



	// 	//$repost = $this->mod_salelpg->repost_sale($id);


	//        if ($delete_goods) {
	//            echo '1';
	// 	 	exit;
	// 	 }
	// 	 else {
	// 	 	echo '0';
	// 	 	exit;
	// 	 }
	// }
	function record_delete()
	{
		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$id = $_POST['parentid'];
		$saleid = $sale_point_id . "-Sale-" . $id;



		$this->db->where('trans_id', $id, 'sale_point_id', $sale_point_id);

		$count = $this->db->query("SELECT COUNT(ig_detail_id) as count FROM tbl_issue_goods_detail where trans_id='$id' and sale_point_id='$sale_point_id'")->row_array()['count'];


		$this->db->trans_start();


		$table = "tbl_issue_goods_detail";
		$deleteid =	$this->input->post('deleteid');
		$where = "srno = '" . $deleteid . "'";
		$delete_goods = $this->mod_common->delete_record($table, $where);
		// $lastqqf = $this->db->last_query();
		// $action_type = $this->db->query("SELECT max(action_type) as action_type FROM `tbl_user_log` where trans_reference='$id'")->row_array()['action_type'];
		// $action_type = $action_type + 1;
		// $this->mod_user_log->insert_into_log($id, 'Delete', 'SaleLPG.php', $lastqqf, $action_type);

		if ($count == 1) {
			$table = "tbl_issue_goods";
			$where = "trans_id = '" . $id . "' and sale_point_id = '" . $sale_point_id . "'";
			$delete_goods = $this->mod_common->delete_record($table, $where);
			$lastqqf = $this->db->last_query();
			// $this->mod_user_log->insert_into_log($id, 'Delete', 'SaleLPG.php', $lastqqf, $action_type);

			$tablems = "tbltrans_master";
			$wherems = "vno = '" . $saleid . "'";
			$deletems = $this->mod_common->delete_record($tablems, $wherems);
			$lastqqf = $this->db->last_query();
			// $this->mod_user_log->insert_into_log($id, 'Delete', 'SaleLPG.php', $lastqqf, $action_type);

			$tableds = "tbltrans_detail";
			$whereds = "vno = '" . $saleid . "'";
			$deleteds = $this->mod_common->delete_record($tableds, $whereds);
			$lastqqf = $this->db->last_query();
			// $this->mod_user_log->insert_into_log($id, 'Delete', 'SaleLPG.php', $lastqqf, $action_type);
		}

		$this->db->trans_complete();

		//$repost = $this->mod_salelpg->repost_sale($id);


		if ($delete_goods) {
			echo '1';
			exit;
		} else {
			echo '0';
			exit;
		}
	}


	public function detail($id = '')
	{
		if ($id) {
			$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
			$table = 'tblmaterial_coding';
			$data['item_list'] = $this->mod_common->get_all_records($table, "*");
			$table = 'tbl_issue_goods';
			$where = "issuenos='$id'";
			$data['single_edit'] = $this->mod_common->select_single_records($table, $where);

			$acode = $data['single_edit']['issuedto'];
			$issuedate = $data['single_edit']['issuedate'];
			$ftoday = '2018-01-01';
			$today =  $issuedate;
			$date_array2 = array('from_date' => $ftoday, 'to_date' => $today, 'filter' => 'party', 'acode' => $acode, 'id' =>  $id, 'hdate' => '', 'sort' => 'date', 'aname_hid' => '');
			$data['final_bal'] =  $this->mod_customerledger->get_report_small($date_array2);

			// pm(	$data['final_bal']);exit;

			foreach ($data['final_bal'] as $key => $value) {
				$data['report_new'] = $value['tbalance'];
			}
			if ($this->input->post('from_date') == '1947-01-01') {
				$data['from_date'] = '2018-01-01';
			} else {
				$data['from_date'] = $this->input->post('from_date');
			}
			$data['opening'] =  $this->mod_customerstockledger->get_opening($date_array2, 1);
			$data['itemname'] = $this->mod_common->select_array_records('tblmaterial_coding', "*", "catcode='1' ");
			$total_return = array();
			$total_sale = array();
			$total_return_sale = array();
			$data['return'] =  $this->mod_customerstockledger->getreturn($date_array2);
			foreach ($data['return'] as $key => $value) {
				if (count($value['return'] > 1)) {
					foreach ($value['return'] as $key => $value_sub) {
						$total_return[$value_sub['itemid']] = $total_return[$value_sub['itemid']] + $value_sub['qty'];
					}
				}
			}



			$data['sale'] =  $this->mod_customerstockledger->getsale($date_array2);
			foreach ($data['sale'] as $key => $value) {
				if (count($value['sale'] > 1)) {
					foreach ($value['sale'] as $key => $value_sub) {
						$total_sale[$value_sub['itemid']] = $total_sale[$value_sub['itemid']] + $value_sub['qty'];
					}
				}
			}

			for ($i = 0; $i < count($data['opening']); $i++) {
				$item_code = $data['opening'][$i]['itemid'];
				$opening_array[$item_code] = $data['opening'][$i]['opening'];
			}

			for ($i = 0; $i < count($data['itemname']); $i++) {
				$item_code = $data['itemname'][$i]['materialcode'];
				$total_return_sale[$item_code] = $total_sale[$item_code] - $total_return[$item_code] + $opening_array[$item_code];
			}

			$data['total_return_sale'] = $total_return_sale;
			$data['edit_list'] = $this->mod_salelpg->edit_salelpg($id);
			//echo '<pre>';print_r($data);
			$table = 'tbl_company';
			$data['company'] = $this->mod_common->get_all_records($table, "*");
			//exit;
			$data["filter"] = '';
			#----load view----------#
			$data["title"] = "Customer Invoice";
			$this->load->view($this->session->userdata('language') . "/sale_lpg/single", $data);
		}
	}

	public function detail_salestax($id = '')
	{
		if ($id) {
			$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
			$table = 'tblmaterial_coding';
			$data['item_list'] = $this->mod_common->get_all_records($table, "*");
			$table = 'tbl_issue_goods';
			$where = "issuenos='$id'";
			$data['single_edit'] = $this->mod_common->select_single_records($table, $where);

			$data['edit_list'] = $this->mod_salelpg->edit_salelpg($id);
			//echo '<pre>';print_r($data);
			$table = 'tbl_company';
			$data['company'] = $this->mod_common->get_all_records($table, "*");
			//exit;
			$data["filter"] = '';
			#----load view----------#
			$data["title"] = "Customer Invoice";
			$this->load->view($this->session->userdata('language') . "/sale_lpg/single_salestax", $data);
		}
	}

	public function detail_small($id = '')
	{
		if ($id) {
			$string = explode('-', $id);
			$id = $string[0];
			$trans_id = $string[1];
			$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
			$table = 'tblmaterial_coding';
			$data['item_list'] = $this->mod_common->get_all_records($table, "*");
			$table = 'tbl_issue_goods';
			$where = "issuenos='$id'";
			$data['single_edit'] = $this->mod_common->select_single_records($table, $where);
			//  pm($data['single_edit']);exit;
			$acode = $data['single_edit']['issuedto'];
			$scode = $data['single_edit']['scode'];

			//echo $scode;exit;

			$sale_point_id = $data['single_edit']['sale_point_id'];
			$issuedate = $data['single_edit']['issuedate'];
			$data['edit_list'] = $this->mod_salelpg->edit_salelpg($id);
			//	echo '<pre>';print_r($data['edit_list']);exit;
			$table = 'tbl_company';
			$data['company'] = $this->mod_common->get_all_records($table, "*");
			//exit;
			$data["filter"] = '';
			#----load view----------#
			$data["title"] = "Customer Invoice";


			$ftoday = '2018-01-01';
			$today =  $issuedate;
			// $date_array2 = array('from_date' => $ftoday, 'to_date' => $today, 'filter' => 'party', 'acode' => $acode, 'id' =>  $id, 'hdate' => '', 'sort' => 'date', 'aname_hid' => '', 'scode' => $scode, 'location' => $sale_point_id,);


			$d_customer = $data['single_edit']['direct_customer'];

			if ($d_customer > 0) {

				//echo $d_customer;exit;

				$date_array2 = array('from_date' => $ftoday, 'to_date' => $today, 'filter' => 'party', 'direct_customer' => $d_customer, 'id' =>  $id, 'hdate' => '', 'sort' => 'date', 'aname_hid' => '', 'scode' => $scode, 'location' => $sale_point_id, 'trans_id' => $trans_id);
				$data['final_bal'] =  $this->mod_customerledger->get_report_small_dcustomer($date_array2);


				//pm($data['final_bal']);exit;

			} else {

				$date_array2 = array('from_date' => $ftoday, 'to_date' => $today, 'filter' => 'party', 'acode' => $acode, 'id' =>  $id, 'hdate' => '', 'sort' => 'date', 'aname_hid' => '', 'scode' => $scode, 'location' => $sale_point_id, $trans_id);
				$data['final_bal'] =  $this->mod_customerledger->get_report_small($date_array2);
			}


			// pm(	$data['final_bal']);exit;

			foreach ($data['final_bal'] as $key => $value) {
				$data['report_new'] = $value['tbalance'];
				//$itemids = $value['itemid'];
				//$wherem = "materialcode!='$itemids'";
				//$data['item_lists'] = $this->mod_common->select_array_records($tablem,'*',$wherem);
			}

			//	$data['total_balance']=  $this->mod_customerstockledger->get_total_customer_stock_one($acode);

			//	 pm($data['total_balance']);
			if ($this->input->post('from_date') == '1947-01-01') {
				$data['from_date'] = '2018-01-01';
			} else {
				$data['from_date'] = $this->input->post('from_date');
			}
			$data['opening'] =  $this->mod_customerstockledger->get_opening($date_array2, 1);
			$data['itemname'] = $this->mod_common->select_array_records('tblmaterial_coding', "*", "catcode='1' ");
			$total_return = array();
			$total_sale = array();
			$total_return_sale = array();
			$data['return'] =  $this->mod_customerstockledger->getreturn($date_array2);

			foreach ($data['return'] as $key => $value) {
				if ($value['return'] > 1) {
					foreach ($value['return'] as $key => $value_sub) {
						$total_return[$value_sub['itemid']] = $total_return[$value_sub['itemid']] + $value_sub['qty'];
					}
				}
			}
			$data['sale'] =  $this->mod_customerstockledger->getsale($date_array2);
			// pm($data['sale']);exit;
			foreach ($data['sale'] as $key => $value) {
				if ($value['sale']) {
					foreach ($value['sale'] as $key => $value_sub) {
						$total_sale[$value_sub['itemid']] = $total_sale[$value_sub['itemid']] + $value_sub['qty'];
					}
				}
			}

			for ($i = 0; $i < count($data['opening']); $i++) {
				$item_code = $data['opening'][$i]['itemid'];
				$opening_array[$item_code] = $data['opening'][$i]['opening'];
			}
			for ($i = 0; $i < count($data['itemname']); $i++) {
				$item_code = $data['itemname'][$i]['materialcode'];
				$total_return_sale[$item_code] = $total_sale[$item_code] - $total_return[$item_code] + $opening_array[$item_code];
			}
			$customer = $this->input->post('customer');

			$balance = $this->db->query("select SUM(damount)-SUM(camount) as balance from tbltrans_detail  where acode='$customer' ")->row_array()['balance'];
			$opening_balance = $this->db->query("select * from tblacode  where acode='$customer' ")->row_array();
			$opening = $opening_balance['opngbl'];

			if ($opening_balance['optype'] == 'Credit') {
				$opening = -1 * $opening;
			}
			$acc_balance = $balance + $opening;
			$data['account_balance'] = $acc_balance;
			$data['total_return_sale'] = $total_return_sale;
			$data['itemname'] = $this->mod_common->select_array_records('tblmaterial_coding', "*", "catcode='1' ");
			// pm($data);exit;
			$data['res_record'] = $this->db->query("select  * from  tbl_company")->row_array();


			$this->load->view($this->session->userdata('language') . "/sale_lpg/invoice", $data);
		}
	}

	function get_filledstock()
	{
		$data['report'] =  $this->mod_salelpg->get_details($this->input->post());
		foreach ($data['report'] as $key => $value) {
			echo json_encode($value);
		}
	}
	function today_amount_recv()
	{
		$data['report'] =  $this->mod_salelpg->today_amount_recv($this->input->post());
		$total_recv = 0;
		foreach ($data['report'] as $key => $value) {

			$total_recv += $value['total_received'];
		}
		echo $total_recv;
	}
	function get_filledstockdate()
	{
		$data['report'] =  $this->mod_salelpg->get_details($this->input->post());
		//pm($data['report']);
		foreach ($data['report'] as $key => $value) {
			//pm($value);
			echo $value['empty'];
		}
	}

	public function get_Diret_Customer()
	{

		$sagment = $this->input->post('sagment');

		if ($sagment == "home") {
			$sagment = "Home";
		} elseif ($sagment == "walkin") {
			$sagment = "Walkin";
		}

		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		//echo $sagment;exit();
		if ($sale_point_id == '0') {
			$where_sale_point_id = "";
		} else {
			$where_sale_point_id = "and sale_point_id='$sale_point_id'";
		}

		$diect_customer_detail = $this->db->query("select id,name,cell_no from tbl_direct_customer where type='$sagment' $where_sale_point_id")->result_array();
		$direct_customer = $_SESSION["direct_customer"];

?>
		<?php
		foreach ($diect_customer_detail as $key => $data) {
		?>

			<option value="<?php echo $data['id']; ?>" <?php if ($data['id'] == $direct_customer) { ?> selected <?php } ?>><?php echo ucwords($data['cell_no'] . " " . $data['name']); ?></option>

		<?php }
	}




	function get_accbal()
	{

		$customer = $this->input->post('customer');

		$balance = $this->db->query("select SUM(damount)-SUM(camount) as balance from tbltrans_detail  where acode='$customer' ")->row_array()['balance'];
		$opening_balance = $this->db->query("select * from tblacode  where acode='$customer' ")->row_array();
		$opening = $opening_balance['opngbl'];

		if ($opening_balance['optype'] == 'Credit') {
			$opening = -1 * $opening;
		}
		$acc_balance = $balance + $opening;
		echo $acc_balance;
	}
	public function get_branch()
	{

		$customer = $this->input->post('customer');
		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		//echo $Customer;exit();
		if ($sale_point_id == '0') {
			$where_sale_point_id = "";
		} else {
			$where_sale_point_id = "and sale_point_id='$sale_point_id'";
		}

		$customer_code = $this->db->query("select * from tblsledger where acode='$customer' $where_sale_point_id")->result_array();
		$scode = $_SESSION["scode"];

		if ($customer_code[0]['scode'] > 0) {



		?>
			<?php
			foreach ($customer_code as $key => $data) {
			?>


				<option value="<?php echo $data['scode']; ?>" <?php if ($data['scode'] == $scode) { ?> selected <?php } ?>><?php echo ucwords($data['stitle']); ?></option>

<?php }
		} else {
			echo 0;
		}
	}
	public function get_type()
	{

		$customer = $this->input->post('customer');

		$record = $this->db->query("select * from tblacode where acode='$customer'")->row_array();
		echo json_encode($record);
	}
	public function get_cat_code()
	{

		$item = $this->input->post('item');

		$record = $this->db->query("select catcode from tblmaterial_coding where materialcode='$item'")->row_array()['catcode'];
		echo $record;
	}
}
