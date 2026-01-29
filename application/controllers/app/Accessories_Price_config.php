<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Accessories_Price_config extends CI_Controller
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	public function __construct()
	{
		parent::__construct();

		$this->load->model(array(
			"Mod_fluctuation",
			"mod_common"
		));
	}

	public function index()
	{
		//$data['price_list'] = $this->Mod_fluctuation->manage_item();
		$data["filter"] = '';

		$table = 'tblcategory';
		$where = array('catcode' => 2);
		$data['category_list'] = $this->mod_common->select_array_records($table, "*", $where);
		$login_user = $this->session->userdata('id');
		$sale_point_ids = $this->db->query("SELECT location FROM tbl_admin WHERE id = '$login_user'")->row_array()['location'];
		if ($sale_point_ids) {
			$sale_point_id_array = explode(',', $sale_point_ids);
			$this->db->where_in('sale_point_id', $sale_point_id_array);
			$data['name'] = $this->db->get('tbl_sales_point')->result_array();
		} else {
			$data['name'] = [];
		}
		#----load view----------#
		$data["title"] = "Price Configuration";

		$this->load->view("app/Accessories_Price_config/add_price", $data);
	}



	public function add_price()
	{
		$table = 'tblcategory';
		$where = array('catcode' => 2);

		$data["filter"] = 'add';
		$where = array('status' => 'Active');
		$data['brand'] = $this->mod_common->select_array_records('tbl_brand', "*", $where);
		$this->load->view("app/Accessories_Price_config/add_price", $data);
	}



	public function getDetails()
	{
		$category = $_POST['category'];
		$location = $_POST['location'];
		$date = $_POST['date'];
		$price_11_8 = $_POST['price_11_8'];
		$saleprice = $_POST['saleprice'];
		$id = $_POST['id'];
		$login_user = $this->session->userdata('id');
		$sale_point_id = $location; ?>


		<div class="table-header my-4">
			Resluts For 'Accessories Price Configuration'
		</div>
		<form id="price_form" method="post" action="<?php echo SURL . 'app/Accessories_Price_config/add'; ?>">

			<table id="dynamic-table" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th style="width: 11%;">Sr No</th>
						<th style="width: 11%;">Item Name</th>
						<!-- <th style="width: 11%;">Previous Rate</th> -->
						<!-- <th style="width: 11%;">Sale Price</th> -->
						<th style="width: 11%;">Registered Price (Incl.gst)</th>
						<th style="width: 11%;">Un-Registered Price (Incl.gst)</th>

						<?php if ($category == 1) { ?>
							<th style="width: 11%;"> Security Charges</th>
						<?php } ?>
						<th style="width: 11%;">History</th>
					</tr>
				</thead>
				<tbody id="tbody">
					<?php
					$srno = 0;
					$item_list = $this->db->query("SELECT materialcode, itemname from tblmaterial_coding where catcode='$category'")->result_array();
					foreach ($item_list as $key => $value) {
						$materialcode = $value['materialcode'];

						$previous_rate = $this->db->query("SELECT saleprice FROM tbl_price_fluctuation WHERE edate <= '$date' AND item_id = '$materialcode' and sale_point_id = '$sale_point_id' ORDER BY edate desc")->row_array()['saleprice'];

						$edit_detail = $this->db->query("SELECT * from tbl_price_fluctuation where edate <= '$date' AND item_id = '$materialcode' and sale_point_id = '$sale_point_id' order by edate desc")->row_array();

						$srno = $srno + 1; ?>

						<tr id="<?php echo $srno ?>" class="master">
							<td align="left"><?php echo $srno ?></td>

							<td>
								<input type="hidden" name="edit[]" value="<?php echo $edit_detail['id'] ?>">
								<input type="hidden" name="materialcode[]" value="<?php echo $materialcode ?>">
								<?php echo $value['itemname'] ?>
							</td>

							<!-- <td>
								<?php echo $previous_rate ?>
							</td> -->
							<td class="hidden">
								<input type="text" name="saleprice[]" onchange="color_change('<?php echo $srno ?>');" id="saleprice" value="<?php if ($edit_detail) {
									   echo $edit_detail['saleprice'];
								   } ?>" placeholder="Sale Price" onkeypress='return /[0-9 .]/i.test(event.key)'>
							</td>
							<td>
								<input type="text" name="registered_saleprice[]" onchange="registered_color_change('<?php echo $srno ?>');" id="registered_saleprice" value="<?php if ($edit_detail) {
									echo $edit_detail['registered_saleprice'];
								} ?>" maxlength="5" onkeypress="return /[0-9 .]/i.test(event.key)" title="Only Numbers Allowed...">
							</td>
							<td>
								<input type="text" name="un_registered_saleprice[]" onchange="un_registered_color_change('<?php echo $srno ?>');" id="un_registered_saleprice" value="<?php if ($edit_detail) {
									echo $edit_detail['un_registered_saleprice'];
								} ?>" maxlength="5" onkeypress="return /[0-9 .]/i.test(event.key)" title="Only Numbers Allowed...">
							</td>
							<td align="center">
								<input type="button" href="javascript:void(0)" class="btn btn-sm btn-info" value="History" data-toggle="collapse" data-target="#gas-details-<?= $srno; ?>" aria-expanded="false" aria-controls="gas-details-<?= $srno; ?>">
							</td>
						</tr>
						<tr id="gas-details-<?= $srno; ?>" class="collapse">
							<td colspan="5" class="p-0">
								<div class="card-body">
									<table class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>Sr No </th>
												<th>Date</th>
												<!-- <th>Sale Price</th> -->
												<th>Registered Price (Incl.gst)</th>
												<th>Un-Registered Price (Incl.gst)</th>

											</tr>
										</thead>
										<tbody><?php
										$count = 0;
										$item_history = $this->db->query("Select * from tbl_price_fluctuation where sale_point_id = '$sale_point_id' and item_id = '$materialcode' ORDER BY edate desc")->result_array();
										foreach ($item_history as $key => $dat) {
											$count++; ?>
												<tr id="<?php echo $count; ?>">
													<td align="left"><?= $count; ?></td>
													<td><?= $dat['edate']; ?></td>
													<!-- <td><?php //$dat['saleprice']; ?></td> -->
													<td><?= $dat['registered_saleprice']; ?></td>
													<td><?= $dat['un_registered_saleprice']; ?></td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>

			<div align="center">

				<input type="button" id="submit" class="btn btn-sm btn-info" value="Submit" onclick="add_details()">

			</div>
		</form>
		<?php
	}
	public function add()
	{
		$formData = $this->input->post('formData');
		foreach ($formData as $row) {
			$date = $_POST['date'];
			$location = $_POST['location'];
			$adata['item_id'] = $row['item_id'];
			$adata['saleprice'] = $row['saleprice'];
			$adata['registered_saleprice'] = $row['registered_saleprice'];
			$adata['un_registered_saleprice'] = $row['un_registered_saleprice'];
			$adata['edate'] = $date;
			$adata['sale_point_id'] = $location;
			$edit = $row['edit'];
			$table = 'tbl_price_fluctuation';

			$entries = $this->db->query("SELECT id FROM $table WHERE sale_point_id = '$location' AND edate = '$date' AND item_id = '$row[item_id]'")->result_array();
			if (!empty($entries)) {
				$first_entry_id = $entries[0]['id'];

				if (count($entries) > 1) {
					$ids_to_delete = array_column(array_slice($entries, 1), 'id');
					$this->db->where_in('id', $ids_to_delete)->delete($table);
				}
				$update_id = ($edit > 0) ? $edit : $first_entry_id;
				$this->mod_common->update_table($table, array("id" => $update_id), $adata);
			} else {
				$this->mod_common->insert_into_table($table, $adata);
			}
		}
		$this->session->set_flashdata('ok_message', 'You have successfully updated.');
		redirect(SURL . 'app/Accessories_Price_config/');
	}


	function get_accbal()
	{
		$t_id = $this->input->post('t_id');
		$date = $this->input->post('date');

		$rete = $this->Mod_fluctuation->get_fluctuation($t_id, $date);

		if ($rete > 0) {
			echo $rete;
		} else {
			echo 0;
		}


		exit();
	}

	function stock()
	{
		$t_id = $this->input->post('t_id');
		$date = $this->input->post('date');

		$stock = $this->Mod_fluctuation->get_stock($t_id, $date);

		if ($stock) {
			echo $stock;
		} else {
			echo 0;
		}

		exit();
	}

	public function delete($id)
	{
		$delete_id = $id . "-Stock";

		$where = "id = '" . $id . "'";

		$table = "tbltrans_detail";
		$where = "vno = '" . $delete_id . "'";
		$delete_aread = $this->mod_common->delete_record($table, $where);
		$table = "tbltrans_master";
		$where = "vno = '" . $delete_id . "'";
		$delete_areas = $this->mod_common->delete_record($table, $where);
	}

	public function edit($id)
	{

		$table = 'tblmaterial_coding';
		$data['item_list'] = $this->mod_common->get_all_records($table, "*");
		$where = "id='$id'";

		$data["filter"] = 'update';
		$this->load->view("app/price_fluctuation/add_price", $data);
	}
}
