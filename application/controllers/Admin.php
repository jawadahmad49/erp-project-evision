<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Admin extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		//error_reporting(E_ALL);
		$this->load->model(array(
			"mod_common", "mod_admin", "mod_customerstockledger", "mod_salelpg", "mod_user"
		));
	}
	public function reminder()
	{
		$reminder = $this->db->query("SELECT * from tbl_reminder")->result_array();
		$this->db->query("DELETE from tbl_reminder");
		// pm($reminder);
	}
	public function index($id='')
	{
		if ($this->session->userdata('loginid') == '') {
			redirect(SURL . 'login');
		}
		$check = $this->db->get_where('tbl_admin', array('id' => $this->session->userdata('id')))->row();
		if ($check->dashboard == "Show") {
			$user_id = $this->session->userdata('id');
			$where_right = array('uid' => $user_id, 'pageid' => '10');
			$data['bank_right'] = $this->mod_common->select_array_records('tbl_user_rights', "*", $where_right);
			if (!empty($data['bank_right'])) {
				$data['bank_flage'] = 'yes';
				$data['bank_position'] = $this->mod_admin->bank_position_ledger();
			} else {
				$data['bank_flage'] = 'no';
			}
			////////////////////////////////// *********** Receivables  **********************///////////////////////
			$data['new_balance_new'] =  $this->mod_customerstockledger->get_total_balance1();
			foreach ($data['new_balance_new'] as $key => $value) {
				if ($value['optype'] == 'Credit') continue;
				$net_balace = $net_balace + $value['new_balance'];
			}
			$data['new_balance'] = $net_balace;
			//////////////////////////////// *********** Payables  **********************///////////////////////
			$data['new_balance_new_pay'] =  $this->mod_customerstockledger->get_total_balance_pay1();
			foreach ($data['new_balance_new_pay'] as $key => $value) {
				if ($value['optype'] == 'Debit') continue;
				$net_balace_pay = $net_balace_pay + $value['new_balance_pay'];
			}
			$data['new_balance_pay'] = $net_balace_pay;
			/////////////////////////////// Expenses ///////////////////////////////////////////
			$login_user = $this->session->userdata('id');
			$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			$fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
			$cash_code = $fix_code['cash_code'];
			$expense_code = $fix_code['expense_code'];
			$acod = $expense_code[0] . $expense_code[1] . $expense_code[2] . $expense_code[3] . $expense_code[4] . $expense_code[5];
			$date = date('Y-m-d');
			$credit_debit = $this->db->query("SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE LEFT(acode,6)= '$acod' and LEFT(acode,7)!='4001011' and vdate='$date' and sale_point_id='$sale_point_id'")->result_array();;
			$data['new_balance_expenses'] = $credit_debit[0]['op_camount'] - $credit_debit[0]['op_damount'];
			/////////////////////////////// Monthly Expenses ///////////////////////////////////////////
			$month = date('Y-m');
			$fdate = $month . '-01';
			$tdate = $month . '-31';
			$credit_debit_tot = $this->db->query("SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE LEFT(acode,6)= '$acod' and LEFT(acode,7)!='4001011' AND `vdate` BETWEEN '$fdate' AND '$tdate' and sale_point_id='$sale_point_id'")->result_array();;
			$data['new_balance_expenses_tot'] = $credit_debit_tot[0]['op_camount'] - $credit_debit_tot[0]['op_damount'];
			/////////////////////////////// TODAY SALE AND CASH POSITION ///////////////////////////////////////////
			$this->db->select('tbl_issue_goods.*,tblacode.*,SUM(tbl_issue_goods_detail.total_amount) as amounttotal');
			$this->db->from('tbl_issue_goods');
			$this->db->join('tblacode', 'tbl_issue_goods.issuedto = tblacode.acode');
			$this->db->join('tbl_issue_goods_detail', ' tbl_issue_goods_detail.ig_detail_id= tbl_issue_goods.issuenos');
			$this->db->where('issuedate=', date("Y-m-d"));
			$this->db->where('tbl_issue_goods.sale_point_id=', $sale_point_id);
			$this->db->group_by('ig_detail_id');
			$this->db->order_by("issuenos", "desc");
			$query = $this->db->get();
			//return $query->result_array();
			$data['salelpg_list'] = $query->result_array();
			///////////////////////////////// * *** ***** cash position ************ //////////////////////////////////
			$cash_position = $this->db->query("select (sum(damount)-sum(camount)) as cash from tbltrans_detail
			where acode='$cash_code'")->result_array()[0]['cash'];
			$cash_position_acode = $this->db->query("select opngbl,optype from tblacode
			where acode='$cash_code'")->result_array();
			foreach ($cash_position_acode as $key => $value) {
				if ($value['optype'] == "Credit") {
					$cash_position -= $value['opngbl'];
				} else {
					$cash_position += $value['opngbl'];
				}
			}
			$data['cash_position'] = $cash_position;
			$data['sale_point_id'] = $sale_point_id;
			///////////////////////////////// * *** ***** TODAY CASH ************ //////////////////////////////////
			$data['cash_today'] = $this->db->query("select (sum(damount)-sum(camount)) as cash from tbltrans_detail
			where acode='$cash_code' and vdate='" . date("Y-m-d") . "'")->result_array()[0]['cash'];
		}
		//echo "<pre>";var_dump($data);
		$data["title"] = " Admin ";
		if ($this->session->userdata('language') != '') {
			$this->load->view($this->session->userdata('language') . "/admin/home", $data);
		} else {
			$this->load->view("en/admin/home", $data);
		}
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
	public function get_stock_dashboard()
	{
		// ini_set('max_execution_time', '-1');
		// error_reporting(E_ALL);
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
				$today_stock = $this->mod_common->stock($id, 'empty', $date, 1);
				$empty_filled = explode('_', $today_stock); //echo "<pre>";pm($empty_filled);
				$filled = $empty_filled[0];
				$empty = $empty_filled[1];
				$stock_in_market = $this->mod_admin->getcurrent_stock_new_access($id, 'All', date('Y-m-d'), 'Market');
				$security_cylinder = $this->mod_admin->getcurrent_security_cylinder($id, 'All', date('Y-m-d'), 'Market');
				// echo "<pre>";var_dump($security_cylinder);
				$security_total = 0;
				foreach ($security_cylinder as $key => $values) {
					$security_total += $values['opening'];
				}
				//echo "<pre>";var_dump($security_total);
				//echo $security_total;  echo "<br>";
				$market_total = 0;
				foreach ($stock_in_market as $key => $values) {
					$market_total += $values['opening'];
				}
				$access_cylinder = $this->mod_admin->getcurrent_stock_new_access($id, 'All', date('Y-m-d'), 'Access');
				$acces_total = 0;
				foreach ($access_cylinder as $key => $values) {
					$acces_total += $values['opening'];
				}
				$all_brand[$brand_count]['item'][$item_count]['filled'] = $filled;
				$all_brand[$brand_count]['item'][$item_count]['item_market'] = $market_total;
				$all_brand[$brand_count]['item'][$item_count]['security_cylin'] = $security_total;
				$all_brand[$brand_count]['item'][$item_count]['access_cylinder'] = $acces_total;
				$all_brand[$brand_count]['item'][$item_count++]['empty'] = $empty;
			}
			$brand_count++;
		}
		$total_tonnage = 0;
		$month = $_POST["month"];
		$year = $_POST["year"];
		if ($month != "" && $year != "") {
			//echo $month = "0".$month;
			if ($year == date('Y')) {
				$date = "$year-$month-01";
				$last_date = date('Y-m-t', strtotime("$date"));
			} else {
				$date = "$year-$month-01";
				$last_date = date('Y-m-t', strtotime("$date"));
			}
			//echo $brand_id;exit;
			//echo "<pre>";var_dump($brand_id);exit;
			$where_item = array('catcode' => 1, 'brandname' => $brand_id);
			$all_brand[$brand_count]['item'] = $this->mod_common->select_array_records('tblmaterial_coding', "*", $where_item);
			$new_i = 0;
			$item_count = 0;
			foreach ($all_brand[$brand_count]['item'] as $key => $value) {
				$id = $value['materialcode'];
				$itemnameint = $value['itemnameint'];
				$today_stock = $this->mod_common->stock($id, 'empty', $last_date, 1);
				$empty_filled = explode('_', $today_stock);
				$filled = $empty_filled[0];
				$total_tonnage += ($itemnameint * $filled) / 1000;
			}
			//$res = array("result"=>$total_tonnage);
			//echo ($total_tonnage);
			//exit;
		}
		$data['new_stock_brand'] = $all_brand;
		$new_stock_brand = $all_brand;
		//echo "<pre>";var_dump($new_stock_brand);exit;
?>
		<div class="widget-main no-padding">
			<div class="dialogs">
				<table class="table table-bordered table-striped">
					<thead class="thin-border-bottom">
						<tr>
							<th class="hidden-480">
								<i class="ace-icon fa fa-caret-right blue"></i>Brand
							</th>
							<th>
								<i class="ace-icon fa fa-caret-right blue"></i>Item Name
							</th>
							<th>
								<i class="ace-icon fa fa-caret-right blue"></i>Filled Stock
							</th>
							<th>
								<i class="ace-icon fa fa-caret-right blue"></i>Empty Stock
							</th>
							<th hidden>
								<i class="ace-icon fa fa-caret-right blue"></i>Damage Stock Filled
							</th>
							<th hidden>
								<i class="ace-icon fa fa-caret-right blue"></i>Damage Stock Empty
							</th>
							<th class="hidden-480">
								<i class="ace-icon fa fa-caret-right blue"></i>
								<a target="_blank" href="Customerstockledger/customerSale" style="text-decoration:underline;">Stock in Market</a>
							</th>
							<th class="hidden-480">
								<i class="ace-icon fa fa-caret-right blue"></i>
								<a target="_blank" href="Accesscylinders/details" style="text-decoration:underline;">Access Cylinder</a>
							</th>
							<th class="hidden-480">
								<i class="ace-icon fa fa-caret-right blue"></i>
								<a target="_blank" href="SecurityReceipt/details" style="text-decoration:underline;">Issue On Security</a>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php $count = 0;
						foreach ($new_stock_brand as $key => $value) {
						?>
							<td class="hidden-480"><b class="green">
									<?php echo $value['brand_name']; ?>
								</b>
							</td>
							<?php foreach ($value['item'] as $key => $value_sub) {
								$security_cylin = $value_sub['security_cylin'];
							?>
								<tr>
									<td class="hidden-480"></td>
									<td>
										<?php
										echo $itemname_final = $value_sub['itemname'];
										?>
									</td>
									<td>
										<span class="label label-info arrowed-right arrowed-in"><?php echo $cbb = $value_sub['filled']; ?></span>
									</td>
									<td><span class="label label-danger arrowed-right arrowed-in">
											<?php
											echo $cb = $value_sub['empty'];
											?>
										</span>
									</td>
									<td hidden>
										<?php
										////////////////////////////// Filled CYLINDER Fresh to damage /////////////////////////////////
										$query = "SELECT sum(qty) as damagecylinder_f from tbl_exchange_condition where from_itemcode='" . $value_sub['materialcode'] . "' and cyl_condition_to='Damage' and cyl_type='Filled'";
										$result = $this->db->query($query);
										$convert_to_f_row1 = $result->row_array();
										////////////////////////////// Filled CYLINDER Damage to Fresh /////////////////////////////////
										$query = "SELECT sum(qty) as freshcylinder_f from tbl_exchange_condition where from_itemcode='" . $value_sub['materialcode'] . "' and cyl_condition_to='Fresh' and cyl_type='Filled'";
										$result = $this->db->query($query);
										$convert_to_f_row2 = $result->row_array();
										?>
										<span class="label label-success arrowed arrowed-right">
											<?php
											echo intval($convert_to_f_row1['damagecylinder_f'] - $convert_to_f_row2['freshcylinder_f']);
											?>
										</span>
									</td>
									<td hidden>
										<?php
										////////////////////////////// Empty CYLINDER Fresh to damage /////////////////////////////////
										$query = "SELECT sum(qty) as damagecylinder_e from tbl_exchange_condition where from_itemcode='" . $value_sub['materialcode'] . "' and cyl_condition_to='Damage' and cyl_type='Empty'";
										$result = $this->db->query($query);
										$convert_to_f_row2 = $result->row_array();
										////////////////////////////// Empty CYLINDER Damage to Fresh /////////////////////////////////
										$query = "SELECT sum(qty) as freshcylinder_e from tbl_exchange_condition where from_itemcode='" . $value_sub['materialcode'] . "' and cyl_condition_to='Fresh' and cyl_type='Empty'";
										$result = $this->db->query($query);
										$convert_to_f_row4 = $result->row_array();
										////////////////////////////// sale damage cylinder  /////////////////////////////////
										$query = "select sum(qty) as saledamagecylinder from tbl_issue_goods_detail where itemid='" . $value_sub['materialcode'] . "' and salestatus='Damage'";
										//$query = "select sum(qty) as saledamagecylinder from tbl_issue_goods_detail where itemid='".$value_sub['materialcode']."' ";
										// echo $query;exit;
										$result = $this->db->query($query);
										$saledamagecylinderquery = $result->row_array();
										$damagecylindersale = $saledamagecylinderquery['saledamagecylinder'];
										?>
										<span class="label label-success arrowed arrowed-right">
											<?php
											echo intval($convert_to_f_row2['damagecylinder_e'] - $convert_to_f_row4['freshcylinder_e'] -  $damagecylindersale);
											?>
										</span>
									</td>
									<td class="hidden-480"> <span class="label label-warning arrowed arrowed-right">
											<?php
											echo $cb = $value_sub['item_market'];
											?>
										</span>
									</td>
									<td class="hidden-480"> <span class="label label-success arrowed arrowed-right">
											<?php
											echo $cb = $value_sub['access_cylinder'];
											?>
										</span>
									</td>
									<td class="hidden-480"> <span class="label label-primary arrowed arrowed-right">
											<?php
											echo $security_cylin;
											?>
										</span>
									</td>
								</tr>
							<?php }
						}
						if (!$new_stock_brand) { ?>
							<tr>
								<td colspan="3" class="red" style="text-align: center;">No Record Found!</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div><!-- /.widget-main -->
		</div>
	<?php
	}
	public function get_chart()
	{
		$data['monthly_stock'] =  $this->mod_admin->getmonthly_stock($this->input->post());
		$month = $this->input->post('chart_month');
		$year = $this->input->post('chart_year');
		$timestamp    = strtotime("$month" . "$year");
		$start_date = date('Y-m-01', $timestamp);
		$end_date  = date('Y-m-t', $timestamp);
		$total_date = count($data['monthly_stock']) + 1;
		while (strtotime($start_date) <= strtotime($end_date)) {
			if (array_search($start_date, array_column($data['monthly_stock'], 'issuedate')) !== False) {
			} else {
				$data['monthly_stock'][$total_date]['issuedate'] = $start_date;
				$data['monthly_stock'][$total_date++]['totala'] = 0;
			}
			$start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
		}
	?>
		<div id="chartContainer" style="height: 300px; width: 100%;"></div>
		<div class="over" style="height: 20px;margin-top: -14px;width: 60px;background-color: white;position: absolute;"></div>
		<?php
		array_multisort(array_column($data['monthly_stock'], "issuedate"), SORT_ASC, $data['monthly_stock']);
		?>
		<script type="text/javascript">
			var chart = new CanvasJS.Chart("chartContainer", {
				axisY: {
					viewportMinimum: 0,
					viewportMaximum: 5,
					title: "Tonnage",
					interval: .25
				},
				axisX: {
					title: "Days",
					interval: 1
				},
				data: [
					{
						// Change type to "doughnut", "line", "splineArea", etc.
						type: "line",
						dataPointWidth: 20,
						click: onClick,
						dataPoints: [
							<?php foreach ($data['monthly_stock'] as $key => $value) {
								$timestamp = strtotime($value['issuedate'])
							?>
								{
									label: '<?php echo date("d", $timestamp);  ?>',
									y: <?php echo $value['totala']; ?>
								},
							<?php } ?>
						]
					}
				]
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
			if (array_search($start_date, array_column($data['monthly_stock'], 'issuedate')) !== False) {
			} else {
				$data['monthly_stock'][$total_date]['issuedate'] = $start_date;
				$data['monthly_stock'][$total_date++]['totala'] = 0;
			}
			$start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
		}
	?>
		<div id="chartContainer" style="height: 300px; width: 100%;"></div>
		<div class="over" style="height: 20px;margin-top: -14px;width: 60px;background-color: white;position: absolute;"></div>
		<?php
		array_multisort(array_column($data['monthly_stock'], "issuedate"), SORT_ASC, $data['monthly_stock']);
		?>
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
				data: [
					{
						// Change type to "doughnut", "line", "splineArea", etc.
						type: "line",
						dataPointWidth: 20,
						click: onClick,
						dataPoints: [
							<?php foreach ($data['monthly_stock'] as $key => $value) {
								$timestamp = strtotime($value['issuedate'])
							?>
								{
									label: '<?php echo date("d", $timestamp);  ?>',
									y: <?php echo $value['totala']; ?>
								},
							<?php } ?>
						]
					}
				]
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
			redirect(SURL . 'restaurant');
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
			redirect(SURL . 'company');
		}
		$this->load->view('company/detail', $data);
	}
	public function update($rid)
	{
		#------------- if post--------------#
		if ($this->input->post("update_restaurant_submit")) {
			#---------- update restaurant record---------------#
			$update_restaurant =  $this->mod_restaurant->update_restaurant($this->input->post());
			if ($update_restaurant) {
				$this->session->set_flashdata('ok_message', '- restaurant updated successfully!');
				redirect(SURL . 'company');
			} else {
				$this->session->set_flashdata('err_message', '- Error in adding restaurant please try again!');
				redirect(SURL . 'company/edit/' . $rid);
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
		redirect(SURL . 'company/');
	}
	public function delete($id)
	{
		#-------------delete record--------------#
		$table = "tbl_resturant_reg";
		$where = "restaurant_id = '" . $id . "'";
		$delete_restaurant = $this->mod_common->delete_record($table, $where);
		if ($delete_restaurant) {
			$this->session->set_flashdata('ok_message', '- Restaurant deleted successfully!');
			redirect(SURL . 'company/');
		} else {
			$this->session->set_flashdata('err_message', '- Error in deleteting Restaurant please try again!');
			redirect(SURL . 'company/');
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
			redirect(SURL . 'admin/');
		} else {
			$this->session->set_flashdata('err_message', 'Operation Failed!');
			redirect(SURL . 'admin/');
		}
	}
	public function location_information()
	{
		$sale_point_id = $this->input->post('sale_point_id');
		$fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
		$cash_code = $fix_code['cash_code'];
		/////////////////////////////// TODAY SALE AND CASH POSITION ///////////////////////////////////////////
		$this->db->select('tbl_issue_goods.*,tblacode.*,SUM(tbl_issue_goods_detail.total_amount) as amounttotal');
		$this->db->from('tbl_issue_goods');
		$this->db->join('tblacode', 'tbl_issue_goods.issuedto = tblacode.acode');
		$this->db->join('tbl_issue_goods_detail', ' tbl_issue_goods_detail.ig_detail_id= tbl_issue_goods.issuenos');
		$this->db->where('issuedate=', date("Y-m-d"));
		$this->db->where('tbl_issue_goods.sale_point_id=', $sale_point_id);
		$this->db->group_by('ig_detail_id');
		$this->db->order_by("issuenos", "desc");
		$query = $this->db->get();
		$salelpg_list = $query->result_array();
		foreach ($salelpg_list as $key => $value) {
			$today_sale += $value['amounttotal'];
		}
		///////////////////////////////// * *** ***** TODAY CASH ************ //////////////////////////////////
		$cash_today = $this->db->query("select (sum(damount)-sum(camount)) as cash from tbltrans_detail
	where acode='$cash_code' and vdate='" . date("Y-m-d") . "'")->result_array()[0]['cash'];
		///////////////////////////////// * *** ***** cash position ************ //////////////////////////////////
		$cash_position = $this->db->query("select (sum(damount)-sum(camount)) as cash from tbltrans_detail
			where acode='$cash_code'")->result_array()[0]['cash'];
		$cash_position_acode = $this->db->query("select opngbl,optype from tblacode
			where acode='$cash_code'")->result_array();
		foreach ($cash_position_acode as $key => $value) {
			if ($value['optype'] == "Credit") {
				$cash_position -= $value['opngbl'];
			} else {
				$cash_position += $value['opngbl'];
			}
		}
		//////////////////////////////// *********** Payables  **********************///////////////////////
		$data['sale_point_id'] = $sale_point_id;
		$data['new_balance_new_pay'] =  $this->mod_customerstockledger->get_total_balance_pay1($sale_point_id);
		foreach ($data['new_balance_new_pay'] as $key => $value) {
			if ($value['optype'] == 'Debit') continue;
			$net_balace_pay = $net_balace_pay + $value['new_balance_pay'];
		}
		$payables = $net_balace_pay;
		if ($payables < 0) {
			$payables = -$net_balace_pay;
		} else {
			$payables = $net_balace_pay;
		}
		////////////////////////////////// *********** Receivables  **********************///////////////////////
		$data['new_balance_new'] =  $this->mod_customerstockledger->get_total_balance1($sale_point_id);
		foreach ($data['new_balance_new'] as $key => $value) {
			if ($value['optype'] == 'Credit') continue;
			$net_balace = $net_balace + $value['new_balance'];
		}
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
		$response_return = array('cash_today' => number_format($cash_today), 'today_sale' => number_format($today_sale), 'cash_position' => number_format($cash_position), 'payables' => number_format($payables), 'receivables' => number_format($receivables), 'm_expenses' => number_format(-$m_expenses), 'items' => $items);
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
					<th style="font-size:13px;text-align: center;">
						Bank Name
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
							<a target="_blank" href="<?php echo SURL . 'VendorLedger/report/' . $value['accountcode']; ?>">
								<b class="green">
									<?php
									if ($value['tbalance'] < 0) {
										echo  number_format(-$value['tbalance']);
										print ' Cr';
									} else {
										echo  number_format($value['tbalance']);
										print ' Dr';
									}
									?>
								</b>
							</a>
						</td>
					</tr>
				<?php }
				if (!$bank_position) {
				?>
					<tr>
						<td colspan="2" class="red" style="text-align: center;">No Record Found!</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
<?php
	}
}
