<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gas_price_configuration extends CI_Controller
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
		$data["filter"] = '';
		$table = 'tblcategory';
		$where = array('classcode' => 1);
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
		$data["title"] = "Gas Price Configuration";
		$this->load->view("app/Gas_price_configuration/add_price", $data);
	}
	public function add_price()
	{
		$table = 'tblcategory';
		$where = array('classcode' => 1);
		//		$data['category_list'] = $this->mod_common->select_array_records($table, "*", $where);

		$data["filter"] = 'add';
		$where = array('status' => 'Active');
		$data['brand'] = $this->mod_common->select_array_records('tbl_brand', "*", $where);
		$this->load->view("app/Gas_price_configuration/add_price", $data);
	}



	public function getDetails()
	{
		$location = $this->input->post('location', true);
		$category = $this->input->post('category', true);
		$date = $this->input->post('date', true);
		$price_11_8 = $this->input->post('price_11_8', true);
		$registered_11_8 = $this->input->post('registered_11_8', true);
		$un_registered_11_8 = $this->input->post('un_registered_11_8', true);

		$per_kg_price = round((float) $price_11_8 / 11.8, 2);
		$registered_per_kg_price = round((float) $registered_11_8 / 11.8, 2);
		$un_registered_per_kg_price = round((float) $un_registered_11_8 / 11.8, 2);

		$login_user = $this->session->userdata('id');

		$sale_point_id = $location;
		$this->db->select('materialcode, itemname, itemnameint');
		$this->db->from('tblmaterial_coding');
		$this->db->where('catcode', $category);
		$item_list = $this->db->get()->result_array();

		?>
		<div class="table-header my-4">
			Results For 'Gas Price Config'
		</div>
		<form id="price_form" method="post" action="<?php echo SURL . 'app/Gas_price_configuration/add'; ?>">

			<table id="dynamic-table" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th style="width: 11%;">Sr No</th>
						<th style="width: 11%;">Item Name</th>
						<!-- <th style="width: 11%;">Cylinder Price</th> -->
						<th style="width: 11%;">Registered Price (Incl.gst)</th>
						<th style="width: 11%;">Un-Registered Price (Incl.gst)</th>

						<?php if ($category == 1) { ?>
							<th style="width: 11%;">Security Charges</th>
						<?php } ?>
						<th style="width: 11%;">History</th>

					</tr>
				</thead>
				<tbody id="tbody">
					<?php
					$srno = 0;
					foreach ($item_list as $key => $value) {
						$materialcode = $value['materialcode'];
						$itemnameint = $value['itemnameint'];

						$this->db->select('*');
						$this->db->from('tbl_price_fluctuation');
						$this->db->where('edate <=', $date);
						$this->db->where('item_id', $materialcode);
						$this->db->where('sale_point_id', $sale_point_id);
						$this->db->order_by('edate', 'desc');
						$edit_detail = $this->db->get()->row_array();

						$srno++;
						if ($price_11_8 > 0) {
							$cyl_price = $per_kg_price * $itemnameint;
						} else {
							$cyl_price = isset($edit_detail['saleprice']) ? $edit_detail['saleprice'] : 0;
						}
						if ($registered_11_8 > 0) {
							$registered_cyl_price = $registered_per_kg_price * $itemnameint;
						} else {
							$registered_cyl_price = isset($edit_detail['registered_saleprice']) ? $edit_detail['registered_saleprice'] : 0;
						}
						if ($un_registered_11_8 > 0) {
							$un_registered_cyl_price = $un_registered_per_kg_price * $itemnameint;
						} else {
							$un_registered_cyl_price = isset($edit_detail['un_registered_saleprice']) ? $edit_detail['un_registered_saleprice'] : 0;
						}
						?>

						<tr id="<?php echo $srno ?>" class="master">
							<td align="left"><?php echo $srno ?></td>

							<td>
								<input type="hidden" name="edit[]" value="<?php echo isset($edit_detail['id']) ? $edit_detail['id'] : '' ?>" title="Only Numbers Allowed...">
								<input type="hidden" name="materialcode[]" value="<?php echo $materialcode ?>" title="Only Numbers Allowed...">
								<?php echo htmlspecialchars($value['itemname']) ?>
							</td>

							<td class="hidden">
								<input type="text" name="saleprice[]" required value="<?= round($cyl_price); ?>" maxlength="5" onkeypress="return /[0-9 .]/i.test(event.key)" title="Only Numbers Allowed...">
							</td>
							<td>
								<input type="text" name="registered_saleprice[]" required value="<?= round($registered_cyl_price); ?>" maxlength="5" onkeypress="return /[0-9 .]/i.test(event.key)" title="Only Numbers Allowed...">
							</td>
							<td>
								<input type="text" name="un_registered_saleprice[]" required value="<?= round($un_registered_cyl_price); ?>" maxlength="5" onkeypress="return /[0-9 .]/i.test(event.key)" title="Only Numbers Allowed...">
							</td>

							<?php if ($category == 1) { ?>
								<td>
									<input type="text" name="sec_charges[]" maxlength="5" required onchange="color_change('<?php echo $srno ?>');" id="sec_charges_<?php echo $srno ?>" value="<?php echo isset($edit_detail['security_charges']) ? $edit_detail['security_charges'] : '' ?>" placeholder="Security Charges" onkeypress='return /[0-9 .]/i.test(event.key)'>
								</td>
							<?php } ?>
							<td align="center">
								<input type="button" href="javascript:void(0)" class="btn btn-sm btn-info" value="History" data-toggle="collapse" data-target="#gas-details-<?= $srno; ?>" aria-expanded="false" aria-controls="gas-details-<?= $srno; ?>">
								<input type="hidden" value="<?= $edit_detail['id']; ?>" name="edit[]">
							</td>
						</tr>
						<tr id="gas-details-<?= $srno; ?>" class="collapse">
							<td colspan="6" class="p-0">
								<div class="card-body">
									<table class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>Sr No </th>
												<th>Date</th>
												<!-- <th>Cylinder Price</th> -->
												<th>Registered Price (Incl.gst)</th>
												<th>Un-Registered Price (Incl.gst)</th>

												<th>Security Charges</th>
											</tr>
										</thead>
										<tbody><?php
										$count = 0;
										$item_history = $this->db->query("Select * from tbl_price_fluctuation where sale_point_id = '$sale_point_id' and item_id = '$materialcode' order by edate desc")->result_array();
										foreach ($item_history as $key => $dat) {
											$count++; ?>
												<tr id="<?php echo $count; ?>">
													<td align="left"><?= $count; ?></td>
													<td><?= $dat['edate']; ?></td>
													<!-- <td><?php //$dat['saleprice']; ?></td> -->
													<td><?= $dat['registered_saleprice']; ?></td>
													<td><?= $dat['un_registered_saleprice']; ?></td>
													<td><?= $dat['security_charges']; ?></td>
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
			$date = $this->input->post('date');
			$location = $this->input->post('location');

			$adata['item_id'] = $row['item_id'];
			$adata['security_charges'] = $row['sec_charges'];
			$adata['saleprice'] = $row['saleprice'];
			$adata['registered_saleprice'] = $row['registered_saleprice'];
			$adata['un_registered_saleprice'] = $row['un_registered_saleprice'];
			$adata['rate_11_8'] = $this->input->post('price_11_8');
			$adata['registered_11_8'] = $this->input->post('registered_11_8');
			$adata['un_registered_11_8'] = $this->input->post('un_registered_11_8');
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
	}
	// function get_accbal()
	// {
	// 	$t_id = $this->input->post('t_id');
	// 	$date = $this->input->post('date');
	// 	$location = $this->input->post('location');

	// 	$rete = $this->Mod_fluctuation->get_fluctuation($t_id, $date, $location);

	// 	if ($rete > 0) {
	// 		echo $rete;
	// 	} else {
	// 		echo 0;
	// 	}

	// 	exit();
	// }
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
