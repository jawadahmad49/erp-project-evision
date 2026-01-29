<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array(
			"mod_common", "mod_admin", "mod_customerstockledger", "mod_salelpg", "mod_user"
		));
	}
	public function index($id = '')
	{
		$login_user = $this->session->userdata('id');

		$sale_point_id = $this->db->query("SELECT location from tbl_admin where id='$login_user'")->row_array()['location'];
		$fix_code = $this->db->query("SELECT * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();

		$bank_code = $fix_code['bank_code'];

		$data['bank_position'] = $this->mod_admin->one_bank_position_ledger($bank_code);
		if ($this->session->userdata('email') == '') {
			redirect(SURL . 'app/login');
		}

		$data["title"] = " Admin ";
		if ($this->session->userdata('language') != '') {
			$this->load->view($this->session->userdata('language') . "/admin/home", $data);
		} else {
			$this->load->view("app/home", $data);
		}
	}
	public function notification_list()
	{

		$login_user = $this->session->userdata('id');
		$sale_point_ids = $this->db->query("SELECT location FROM tbl_admin WHERE id = '$login_user'")->row_array()['location'];
		if ($sale_point_ids) {
			$sale_point_id_array = explode(',', $sale_point_ids);
			$sale_point_id_list = implode("','", $sale_point_id_array);
			$where_location = "and sale_point_id IN ('$sale_point_id_list')";
		} else {
			$where_location = "";
		}

		$data['booked_orders'] = $this->db->query("SELECT * FROM `tbl_place_order` where deliveryStatus='Booked' $where_location")->result_array();
		
		$data["title"] = " Booked Orders List";

		$this->load->view("app/include/notification", $data);
	}
	public function home()
	{

		$table = 'tbl_resturant_reg';
		$data['restaurant_list'] = $this->mod_restaurant->get_all_restaurants($table, "*");

		//pm($data['restaurant_list']);
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Admin";
		$this->load->view($this->session->userdata('language') . "/admin/manage_company", $data);
	}

	public function complete()
	{
		$login_user = $this->session->userdata('id');

		$data['item'] = $this->db->query("SELECT * FROM `tbl_issue_goods` where  status ='Complete' ")->result_array();
		$this->load->view($this->session->userdata('language') . "/order_details", $data);
	}

	public function get_urchart()
	{

		$data['monthly_stock'] =  $this->mod_admin->getmonthly_stock($this->input->post());


		$month = $this->input->post('chart_month');
		$year = $this->input->post('chart_year');

		$timestamp    = strtotime("$month" . "$year");


		$start_date = date('Y-m-01', $timestamp);
		$end_date  = date('Y-m-t', $timestamp);



		$total_date = count($data['monthly_stock']) + 1;

		while (strtotime($start_date) <= strtotime($end_date)) {


			if (array_search($start_date, array_column($data['monthly_stock'], 'created_date')) !== False) {
			} else {

				$data['monthly_stock'][$total_date]['created_date'] = $start_date;
				$data['monthly_stock'][$total_date++]['total_amount'] = 0;
			}


			$start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
		}

?>
		<div id="chartContainer" style="height: 300px; width: 100%;"></div>
		<div class="over" style="height: 20px;margin-top: -14px;width: 60px;background-color: white;position: absolute;"></div>
		<?php

		array_multisort(array_column($data['monthly_stock'], "created_date"), SORT_ASC, $data['monthly_stock']); ?>
		<script type="text/javascript">
			var chart = new CanvasJS.Chart("chartContainer", {
				axisY: {
					viewportMinimum: 0,
					viewportMaximum: 20,
					title: "ٹننیج",
					interval: 2

				},
				axisX: {
					title: "دن",
					interval: 1

				},
				data: [{
					// Change type to "doughnut", "line", "splineArea", etc.
					type: "line",
					dataPointWidth: 20,
					click: onClick,
					dataPoints: [
						<?php foreach ($data['monthly_stock'] as $key => $value) {
							$timestamp = strtotime($value['created_date']) ?>

							, {
								label: '<?php echo date("d", $timestamp);  ?>',
								y: <?php echo $value['total_amount']; ?>
							},
						<?php } ?>

					]
				}]
			});
			chart.render();

			function onClick(e) {

				var month = $('#chart_month').val();
				var year = $('#chart_year').val();

				var url = "<?php echo SURL ?>SaleDateReport/item_report_detail";
				var form = $('<form target="_blank" action="' + url + '" method="post">' +

					'<input type="hidden" name="day" value="' + e.dataPoint.x + '" />' +
					'<input type="hidden" name="month" value="' + month + '" />' +
					'<input type="hidden" name="year" value="' + year + '" />' +

					'</form>');
				$('body').append(form);
				form.submit();
			}
		</script>

		<?php
	}

	public function edit($rid)
	{
		$data['restaurant'] = $this->mod_restaurant->edit_record($rid);
		if (empty($data['restaurant'])) {
			$this->session->set_flashdata('err_message', '-recode Restaurant not exist!');
			redirect(SURL . 'app/restaurant');
		}
		//pm($data['restaurant']);
		$table = 'tbl_country';
		$data['country_list'] = $this->mod_common->get_all_records($table, "*");

		$where_id = array('country_id' => $data['restaurant']['restaurant_country']);

		$table = 'tbl_city';
		$data['city_list'] = $this->mod_common->select_array_records($table, "*", $where_id);


		$data['form_data'] = 'sss';
		$this->load->view($this->session->userdata('language') . "/role/edit", $data);
	}
	public function detail($rid)
	{
		#---------- detail restaurant record---------------#
		$where = array('restaurant_id' => $rid);
		$data['restaurant'] =  $this->mod_restaurant->select_single_restaurant($where);
		if (empty($data['restaurant'])) {
			$this->session->set_flashdata('err_message', '-recode Restaurant not exist!');
			redirect(SURL . 'app/company');
		}
		$this->load->view('company/detail', $data);
	}
	public function update($rid)
	{
		#------------- if post --------------#
		if ($this->input->post("update_restaurant_submit")) {
			#---------- update restaurant record---------------#
			$update_restaurant =  $this->mod_restaurant->update_restaurant($this->input->post());

			if ($update_restaurant) {
				$this->session->set_flashdata('ok_message', '- restaurant updated successfully!');
				redirect(SURL . 'app/company');
			} else {
				$this->session->set_flashdata('err_message', '- Error in adding restaurant please try again!');
				redirect(SURL . 'app/company/edit/' . $rid);
			}
		}
	}
	public function changeStatus($id)
	{
		$status = 1 - $this->input->post('status');
		$update_data = array("status" => $status, "approve_date" => date('Y-m-d'), "approve_by" => $this->session->userdata('id'), "approve_time" => date('h:i:s'));
		$where = array("restaurant_id" => $id);

		$update = $this->mod_common->update_table('tbl_resturant_reg', $where, $update_data);

		$this->session->set_flashdata('ok_message', 'Status changed successfully!');
		redirect(SURL . 'app/company/');
	}
	public function delete($id)
	{
		#-------------delete record--------------#
		$table = "tbl_resturant_reg";
		$where = "restaurant_id = '" . $id . "'";
		$delete_restaurant = $this->mod_common->delete_record($table, $where);

		if ($delete_restaurant) {
			$this->session->set_flashdata('ok_message', '- Restaurant deleted successfully!');
			redirect(SURL . 'app/company/');
		} else {
			$this->session->set_flashdata('err_message', '- Error in deleteting Restaurant please try again!');
			redirect(SURL . 'app/company/');
		}
	}
	function email_exist()
	{
		$table = 'tbl_resturant_reg';
		$email =	$this->input->post('email');
		$where = array('restaurant_email' => $email);
		$data['restaurant_list'] = $this->mod_common->select_array_records($table, "restaurant_email", $where);
		if (!empty($data['restaurant_list'])) {
			echo '1';
			exit;
		} else {
			echo '0';
			exit;
		}
	}
	function website_exist()
	{
		$table = 'tbl_resturant_reg';
		$website_name =	$this->input->post('website_name');
		$where = array('restaurant_website_name' => $website_name);
		$data['restaurant_list'] = $this->mod_common->select_array_records($table, "restaurant_website_name", $where);
		if (!empty($data['restaurant_list'])) {
			echo '1';
			exit;
		} else {
			echo '0';
			exit;
		}
	}

	function get_city()
	{
		$table = 'tbl_city';
		$country_id =	$this->input->post('country_id');
		$where = array('country_id' => $country_id);
		$data['city_list'] = $this->mod_common->select_array_records($table, "*", $where);

		foreach ($data['city_list'] as $key => $value) {
		?>
			<option value="<?php echo  $value['city_id']; ?>"><?php echo  $value['city_name']; ?></option>

		<?php }
	}


	function get_stock()
	{
		$data['report'] =  $this->mod_admin->getcurrent_stock($this->input->post());
		//pm($data['report']);
		foreach ($data['report'] as $key => $value) {
			//pm($value);
			//echo $value['filled'];
			//print $value;
			echo json_encode($value);
		}
	}

	public function cancel($id)
	{
		#-------------cancel order--------------#
		$ins_array = array(
			"status" => "cancelled",
		);
		$table = "tbl_orderbooking";
		$where = "id = '" . $id . "'";
		$delete = $this->mod_common->update_table($table, $where, $ins_array);

		if ($delete) {
			$this->session->set_flashdata('ok_message', 'You have succesfully cancel the order.');
			redirect(SURL . 'app/admin/');
		} else {
			$this->session->set_flashdata('err_message', 'Operation Failed!');
			redirect(SURL . 'app/admin/');
		}
	}
	public function location_information()
	{
		$sale_point_id = $this->input->post('sale_point_id');
		$date = date('Y-m-d');

		$fix_code = $this->db->query("SELECT * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
		$cash_code = $fix_code['cash_code'];

		/////////////////////////////// TODAY SALE AND CASH POSITION ///////////////////////////////////////////
		$today_sale = 0;
		$salelpg_list = $this->db->query("SELECT *,SUM(total_amount) as total_amount FROM `tbl_issue_goods_detail` where sale_point_id='$sale_point_id' and created_date='$date'")->result_array();


		foreach ($salelpg_list as $key => $value) {
			$today_sale += $value['total_amount'];
		}

		///////////////////////////////// * *** ***** TODAY CASH ************ //////////////////////////////////
		$cash_today = $this->db->query("SELECT (sum(damount)-sum(camount)) as cash from tbltrans_detail 
	where acode='$cash_code' and vdate='" . date("Y-m-d") . "'")->result_array()[0]['cash'];

		///////////////////////////////// * *** ***** cash position ************ //////////////////////////////////
		$cash_position = $this->db->query("SELECT (sum(damount)-sum(camount)) as cash from tbltrans_detail 
			where acode='$cash_code' and sale_point_id='$sale_point_id'")->result_array()[0]['cash'];


		$cash_position_acode = $this->db->query("SELECT opngbl,optype from tblacode 
			where acode='$cash_code'")->result_array();

		foreach ($cash_position_acode as $key => $value) {
			if ($value['optype'] == "Credit") {
				$cash_position -= $value['opngbl'];
			} else {
				$cash_position += $value['opngbl'];
			}
		}
		//////////////////////////////// *********** Payables  **********************///////////////////////

		$fix_code = $this->db->query("SELECT * from tbl_code_mapping")->row_array();
		if ($sale_point_id == '1') {
			$vendor_code = '1001001000';
			$customer_code = '2004001000';
		} else if ($sale_point_id == '2') {
			$vendor_code = '1001002000';
			$customer_code = '2004002000';
		}

		$query1 = $this->db->query("SELECT opngbl,optype,cell,phone_no,address,aname,acode,general FROM `tblacode` WHERE general in ('$customer_code','$vendor_code')")->result_array();

		$line = $query1;

		for ($i = 0; $i < count($line); $i++) {


			$acode = $line[$i]['acode'];
			$query2 = $this->db->query("SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' ")->result_array();


			$credit_debit = $query2;
			//pm($credit_debit);exit();

			$change_difference = $credit_debit[0]['op_camount'] - $credit_debit[0]['op_damount'];



			$opngbl_new = $line[$i]['opngbl'];
			if ($line[$i]['optype'] == 'Credit') {
				$opngbl_new = -$line[$i]['opngbl'];
				$line[$i]['new_balance_pay'] = $line[$i]['opngbl'] + $change_difference;
			} else {

				$line[$i]['new_balance_pay'] = -$line[$i]['opngbl'] + $change_difference;
			}




			if ($line[$i]['new_balance_pay'] <= 0) {
				$line[$i]['optype'] = 'Debit';
			} else {
				$line[$i]['optype'] = 'Credit';
			}


			$acode = $line[$i]['general'];
		}

		$net_balace_pay = 0;
		foreach ($line as $key => $value) {
			if ($value['optype'] == 'Debit') continue;
			$net_balace_pay = $net_balace_pay + $value['new_balance_pay'];
		}
		$data['new_balance_pay'] = $net_balace_pay;
		$payables = $net_balace_pay;


		if ($payables < 0) {
			$payables = -$net_balace_pay;
		} else {
			$payables = $net_balace_pay;
		}
		////////////////////////////////// *********** Receivables  **********************///////////////////////






		$fix_code = $this->db->query("SELECT * from tbl_code_mapping")->row_array();

		if ($sale_point_id == '1') {
			$vendor_code = '1001001000';
			$customer_code = '2004001000';
		} else if ($sale_point_id == '2') {
			$vendor_code = '1001002000';
			$customer_code = '2004002000';
		}


		$query1 = $this->db->query("SELECT opngbl,optype,cell,phone_no,address,aname,reg_date,acode,general FROM `tblacode` 
			WHERE  general in ('$customer_code','$vendor_code')")->result_array();





		$line = $query1;

		for ($i = 0; $i < count($line); $i++) {
			$acode = $line[$i]['acode'];
			$query2 = $this->db->query("SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode'")->result_array();

			$credit_debit = $query2;
			$change_difference = $credit_debit[0]['op_damount'] - $credit_debit[0]['op_camount'];

			$opngbl_new = $line[$i]['opngbl'];
			if ($line[$i]['optype'] == 'Credit') {
				$opngbl_new = -$line[$i]['opngbl'];
				$line[$i]['new_balance'] = -$line[$i]['opngbl'] + $change_difference;
			} else {

				$line[$i]['new_balance'] = $line[$i]['opngbl'] + $change_difference;
			}




			if ($line[$i]['new_balance'] <= 0) {
				$line[$i]['optype'] = 'Credit';
			} else {
				$line[$i]['optype'] = 'Debit';
			}

			$acode = $line[$i]['general'];
		}
		$net_balace = 0;
		foreach ($line as $key => $value) {
			if ($value['optype'] == 'Credit') continue;
			$net_balace = $net_balace + $value['new_balance'];
		}
		$data['new_balance'] = $net_balace;

		$receivables = $net_balace;



		/////////////////////////////// Monthly Expenses ///////////////////////////////////////////
		$month = date('Y-m');
		$fdate = $month . '-01';
		$tdate = $month . '-31';
		$expense_code = $fix_code['expense_code'];
		$acod = $expense_code[0] . $expense_code[1] . $expense_code[2] . $expense_code[3] . $expense_code[4] . $expense_code[5];
		$credit_debit_tot = $this->db->query("SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE LEFT(acode,6)= '$acod' and LEFT(acode,7)!='4001011' AND `vdate` BETWEEN '$fdate' AND '$tdate' and sale_point_id='$sale_point_id'")->result_array();
		$m_expenses = $credit_debit_tot[0]['op_camount'] - $credit_debit_tot[0]['op_damount'];
		///////////////////////////////// FOR STOCK////////////////////////////////////////////////////////////////
		$all_brand = $this->mod_admin->get_all_brand();
		$brand_count = 0;
		foreach ($all_brand as $key => $value) {
			$brand_id = $value['brand_id'];
			$date = date('Y-m-d', strtotime("+1 day"));
			$where_item = array('catcode' => 1, 'brandname' => $brand_id);
			$all_brand[$brand_count]['item'] = $this->mod_common->select_array_records('tblmaterial_coding', "*", $where_item);

			$new_i = 0;
			$item_count = 0;

			foreach ($all_brand[$brand_count]['item'] as $key => $value) {
				$id = $value['materialcode'];
				$today_stock = $this->mod_common->stock($id, 'empty', $date, 1, $sale_point_id);
				$empty_filled = explode('_', $today_stock);
				$filled = $empty_filled[0];
				$empty = $empty_filled[1];
				$stock_in_market = $this->mod_admin->getcurrent_stock_new_access($id, 'All', date('Y-m-d'), 'Market', $sale_point_id);

				$security_cylinder = $this->mod_admin->getcurrent_security_cylinder($id, 'All', date('Y-m-d'), 'Market', $sale_point_id);
				$security_total = 0;
				foreach ($security_cylinder as $key => $values) {

					$security_total += $values['opening'];
				}
				$market_total = 0;

				foreach ($stock_in_market as $key => $values) {
					$market_total += $values['opening'];
				}
				$access_cylinder = $this->mod_admin->getcurrent_stock_new_access($id, 'All', date('Y-m-d'), 'Access', $sale_point_id);
				$acces_total = 0;
				foreach ($access_cylinder as $key => $values) {
					$acces_total += $values['opening'];
				}
				$all_brand[$brand_count]['item'][$item_count]['filled'] = $filled;
				$all_brand[$brand_count]['item'][$item_count]['item_market'] = $market_total;
				$all_brand[$brand_count]['item'][$item_count]['security_cylin'] = $security_total;

				$all_brand[$brand_count]['item'][$item_count]['access_cylinder'] = $acces_total;
				$all_brand[$brand_count]['item'][$item_count++]['empty'] = $empty;
				$brandid = $value['brandname'];
				$brand_name = $this->db->query("SELECT brand_name from tbl_brand where brand_id='$brandid'")->row_array()['brand_name'];
				$items[] = '<tr><td  class="hidden-480"><b class="green">' . $brand_name . '</b></td><td>' . $value['itemname'] . '</td><td><span class="label label-info arrowed-right arrowed-in">' . $filled . '</span></td><td><span class="label label-danger arrowed-right arrowed-in">' . $empty . '</span></td><td  class="hidden-480"><span class="label label-warning arrowed arrowed-right">' . $market_total . '</span></td><td  class="hidden-480"><span class="label label-success arrowed arrowed-right">' . $acces_total . '</span></td><td  class="hidden-480"><span class="label label-primary arrowed arrowed-right">' . $security_total . '</span></td></tr>';
			}
			$brand_count++;
		}
		$sale_point_name = $this->db->query("SELECT sp_name from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array()['sp_name'];
		$response_return = array('cash_today' => number_format($cash_today, 3), 'today_sale' => number_format($today_sale, 3), 'cash_position' => number_format($cash_position, 3), 'payables' => number_format($payables, 3), 'receivables' => number_format($receivables, 3), 'm_expenses' => number_format('0', 3), 'items' => $items, 'sale_point_id' => $sale_point_name);
		echo json_encode($response_return);
	}
	public function bank_position()
	{
		$sale_point_id = $this->input->post('sale_point_id');
		/////////////////////////////// BANKS POSITION ///////////////////////////////////////////
		$data['sale_point_id'] = $sale_point_id;
		$bank_position = $this->mod_admin->bank_position_ledger($sale_point_id);


		?>
		<table class="table table-bordered table-striped">
			<thead class="thin-border-bottom">
				<tr>
					<th>
						<i class="ace-icon fa fa-caret-right blue"></i>Bank Name
					</th>

					<th style="font-size:13px;">
						Balance
					</th>

				</tr>
			</thead>

			<tbody>
				<?php $count = 0;
				$bank_total = 0;
				foreach ($bank_position as $key => $value) {

				?>
					<tr>

						<td style="font-size:11px;">
							<?php


							echo $bank_name = $value['accountname'];
							?>
						</td>

						<td style="font-size:11px;">

							<b class="green">
								<?php
								if ($value['tbalance'] < 0) {

									echo  number_format(-$value['tbalance'], 3);
									print ' Cr';
								} else {
									echo  number_format($value['tbalance'], 3);
									print ' Dr';
								}

								?>
							</b>



						</td>
					</tr>

				<?php }
				if (!$bank_position) { ?>
					<tr>

						<td colspan="3" class="red" style="text-align: center;">No Record Found!</td>

					</tr>

				<?php } ?>

			</tbody>
		</table>
<?php

	}
}
