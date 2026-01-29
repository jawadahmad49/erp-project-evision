<?php

defined('BASEPATH') or exit('No direct script access allowed');



class Trip_coding extends CI_Controller
{
	public function __construct()
	{

		parent::__construct();



		$this->load->model(array(

			"mod_common"

		));
	}

	public function index()
	{
		$login_user = $this->session->userdata('id');

		$this->db->select('location');
		$this->db->from('tbl_admin');
		$this->db->where('id', $login_user);
		$sale_point_ids = $this->db->get()->row_array()['location'];

		if ($sale_point_ids) {
			$sale_point_id_array = explode(',', $sale_point_ids);

			$this->db->select('*');
			$this->db->from('tbl_trip_coding');
			$this->db->where_in('sale_point_id', $sale_point_id_array);
			$this->db->order_by('id', 'DESC'); // Add this line to order by id DESC
			$data['trip_detail'] = $this->db->get()->result_array();
		} else {
			$data['trip_detail'] = [];
		}
		$data["filter"] = '';
		$data["title"] = "Manage Trip Coding";
		$this->load->view("app/Trip_coding/manage_customer", $data);
	}
	public function add_rider()
	{
		$login_user = $this->session->userdata('id');
		$this->db->select('location');
		$this->db->from('tbl_admin');
		$this->db->where('id', $login_user);
		$sale_point_ids = $this->db->get()->row_array()['location'];

		if ($sale_point_ids) {
			$sale_point_id_array = explode(',', $sale_point_ids);
			$this->db->select('*');
			$this->db->from('tbl_sales_point');
			$this->db->where_in('sale_point_id', $sale_point_id_array);
			$data['salepoint'] = $this->db->get()->result_array();
		} else {
			$data['salepoint'] = [];
		}
		$data["filter"] = 'add';
		$this->load->view("app/Trip_coding/add_charges", $data);
	}


	public function add()
	{
		$login_user = $this->session->userdata('id');
		$edit = $this->input->post("edit");

		$adata['sale_point_id'] = $_POST["location"];
		$adata['created_by'] = $login_user;
		$adata['created_date'] = date('Y-m-d');
		$adata['rider_id'] = $rider_id = trim($_POST["rider_id"]);
		$adata['vehicle_id'] = $vehicle_id = trim($_POST["vehicle_id"]);

		$orderArray = $this->input->post('order_id');

		// $adata['order_id'] = is_array($orderArray) ? implode(',', $orderArray) : '';
		$adata['order_id'] = $this->input->post('order_sequence');

		$adata['route_link'] = trim($_POST["route_link"]);

		$this->db->where('order_id', is_array($orderArray) ? implode(',', $orderArray) : '');
		$this->db->where('sale_point_id', $_POST['location']);

		if (!empty($edit)) {
			$this->db->where('id !=', $edit);
		}

		$existing_record = $this->db->get('tbl_trip_coding')->row_array();

		if (!empty($existing_record)) {
			$this->session->set_flashdata('err_message', 'Trip with these orders and location already exists.');
			redirect(SURL . 'app/Trip_coding/');
			return;
		}
		if (empty($edit)) {
			$adata['status'] = "Pending";
			$res = $this->mod_common->insert_into_table("tbl_trip_coding", $adata);
		} else {
			$adata['modified_by'] = $login_user;
			$adata['modified_date'] = date('Y-m-d');
			$this->mod_common->update_table("tbl_trip_coding", array("id" => $edit), $adata);
			$res = $edit;
		}
		foreach ($orderArray as $key => $value) {
			$odata['trip_id'] = $res;
			$this->mod_common->update_table("tbl_place_order", array("id" => $value), $odata);
		}
		$this->db->query("UPDATE tbl_vehicle_coding set status='Allocated' where id='$vehicle_id' ");
		$this->db->query("UPDATE tbl_rider_coding set status='Allocated' where id ='$rider_id'");

		if ($res) {
			$this->session->set_flashdata('ok_message', 'Operation successful.');
			redirect(SURL . 'app/Trip_coding/');
		} else {
			$this->session->set_flashdata('err_message', 'Operation failed.');
			redirect(SURL . 'app/Trip_coding/');
		}
	}
	public function delete($id)
	{
		$login_user = $this->session->userdata('id');
		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '1061' limit 1")->row_array();
		if ($role['delete'] != 1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'app/Trip_coding/index/');
		}
		#-------------delete record--------------#
		$data['record'] = $record = $this->db->query("SELECT * from tbl_trip_coding where id='$id'")->row_array();
		$order_id = $record['order_id'];
		$vehicle_id = $record['vehicle_id'];
		$rider_id = $record['rider_id'];
		$this->db->query("UPDATE tbl_vehicle_coding set status='' where id='$vehicle_id' ");
		$this->db->query("UPDATE tbl_rider_coding set status='' where id ='$rider_id'");

		$this->db->query("UPDATE tbl_place_order set trip_id=0 where id IN ($order_id)");
		$table = "tbl_trip_coding";
		$where = "id = " . $id . "";
		$delete_area = $this->mod_common->delete_record($table, $where);
		if ($delete_area) {
			$this->session->set_flashdata('ok_message', 'You have successfully deleted.');
			redirect(SURL . 'app/Trip_coding/index/');
		} else {
			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
			redirect(SURL . 'app/Trip_coding/index/');
		}
	}


	public function edit($id)
	{
		$login_user = $this->session->userdata('id');
		$this->db->select('location');
		$this->db->from('tbl_admin');
		$this->db->where('id', $login_user);
		$sale_point_ids = $this->db->get()->row_array()['location'];

		if ($sale_point_ids) {
			$sale_point_id_array = explode(',', $sale_point_ids);

			$this->db->select('*');
			$this->db->from('tbl_sales_point');
			$this->db->where_in('sale_point_id', $sale_point_id_array);
			$data['salepoint'] = $this->db->get()->result_array();
		} else {
			$data['salepoint'] = [];
		}

		$data['record'] = $record = $this->db->query("SELECT * from tbl_trip_coding where id='$id'")->row_array();
		$sale_point_id = $record['sale_point_id'];
		$shop_location = $this->db->query("SELECT shop_location from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array()['shop_location'];
		$data['origin_lat'] = explode(',', $shop_location)[0];
		$data['origin_lng'] = explode(',', $shop_location)[1];

		$data["title"] = 'Edit Trip Coding';
		$data["filter"] = 'add';
		$this->load->view("app/Trip_coding/add_charges", $data);
	}
	public function get_vehicles()
	{
		$trip_id = $_POST['trip_id'];
		$vehicle_id = $this->db->query("SELECT vehicle_id from tbl_trip_coding where id='$trip_id'")->row_array()['vehicle_id'];

		$sale_point_id = $_POST['sale_point_id'];
		$vehicle_id = $_SESSION["vehicle_id"];
		$vehicles = $this->db->query("SELECT * FROM tbl_vehicle_coding WHERE sale_point_id = '$sale_point_id' and (status!='Allocated' || id='$vehicle_id')")->result_array();

		foreach ($vehicles as $value) {

			$vehicle_type = $value['vehicle_type'];
			if ($vehicle_type == 'motorcycle') {
				$vehicle_type = "Motor Cycle";
			} else if ($vehicle_type == 'motorcar') {
				$vehicle_type = "Motor Car";
			}
			echo '<option value="' . $value['id'] . '" ' . (($vehicle_id == $value['id']) ? 'selected' : '') . '>' . $value['vehicle_number'] . " - " . $vehicle_type . '</option>';
		}
	}
	public function get_riders()
	{
		$trip_id = $_POST['trip_id'];
		$sale_point_id = $_POST['sale_point_id'];
		$rider_id = $_SESSION["rider_id"];
		$trip_id = $_POST['trip_id'];
		$rider = $this->db->query("SELECT rider_id from tbl_trip_coding where id='$trip_id'")->row_array()['rider_id'];

		$riders = $this->db->query("Select * from tbl_rider_coding where sale_point_id = '$sale_point_id' and (status!='Allocated' || id='$rider')")->result_array();
		// echo "<pre>";print_r($riders);exit;
		foreach ($riders as $key => $value) { ?>
			<option value="<?php echo $value['id']; ?>" <?php if ($rider_id == $value['id']) {
				   echo 'selected';
			   } ?>><?php echo $value['rider_name']; ?></option>
		<?php }
	}

	public function get_orders()
	{
		$trip_id = $_POST['trip_id'];
		$sale_point_id = $_POST['sale_point_id'];
		$selected_orders = explode(',', $_SESSION['order_id']);
		if (!empty($trip_id)) {
			$where_trip_id = "trip_id = '$trip_id'";
		} else {
			$where_trip_id = "trip_id = '0' and deliveryStatus = 'Confirm'";
		}
		$riders = $this->db->query("Select * from tbl_place_order where sale_point_id = '$sale_point_id'  and type!='walkin' and $where_trip_id")->result_array();
		foreach ($riders as $key => $value) {
			$selected = in_array($value['id'], $selected_orders) ? 'selected' : '';
			?>
			<option value="<?php echo $value['id']; ?>" data-delivery_location='<?php echo $value['delivery_location']; ?>' <?php echo $selected; ?>>
				<?php echo "Order # " . $value['id']; ?>
			</option>
		<?php }
	}
	public function get_order_detail()
	{
		$order_id = $this->input->post('order');
		$sale_point_id = $this->input->post('sale_point_id');
		$delivery_charges = $this->input->post('delivery_charges');

		// Fetch order details
		$order_list_details = $this->db->query("SELECT * FROM tbl_place_order_detail WHERE order_id = '$order_id' and del_status!=1")->result_array();
		$order_details = $this->db->query("SELECT * FROM tbl_place_order WHERE id = '$order_id'")->row_array();
		$order_status = $order_details['deliveryStatus'];
		if ($order_status == 'Dispatch' || $order_status == 'Delivered') {
			$disabled = 'disabled';
		} else {
			$disabled = '';
		}

		$count = 0;
		$total_qty = 0;
		$lpg_amount = 0;
		$ttl_accessories = 0;
		$ttl_security_charges = 0;
		$ttl_delivery_charge = 0;
		$ttl_gst = 0;
		$rows = '';
		$ttl_swap_charges = 0;
		foreach ($order_list_details as $value) {
			$count++;
			$materialcode = $value['materialcode'];

			// Fetch item details
			$item_detail = $this->db->query("SELECT itemname, CONCAT('https://lpginsight.com/GasablePK/assets/images/items/', image_path) AS image_path, catcode, itemnameint, security_price FROM tblmaterial_coding WHERE materialcode = '$materialcode'")->row_array();
			$date = $order_details['date'];

			// Fetch sale price and security charges
			// $price = $this->db->query("SELECT saleprice, security_charges FROM tbl_price_fluctuation WHERE edate <= '$date' and item_id = '$materialcode' and sale_point_id = '$sale_point_id' order by id desc")->row_array();
			$saleprice = $value['price'];
			$security_charges = $value['security_charges'];



			if ($value['type'] != 'Swap') {
				$gst = round(($saleprice * $order_details['gst']) / 100);
				$ttl_gst += $value['quantity'] * $gst;
				$total_amount = ($saleprice + $gst) * $value['quantity'];
			}

			// Calculate total amount (with security charges if type is New)
			if ($value['type'] == 'New') {
				$total_amount += $security_charges * $value['quantity'];  // Add security charges if type is 'New'
				$ttl_security_charges += $security_charges * $value['quantity'];  // Add security charges if type is 'New'
			}

			$catcode = $item_detail['catcode'];
			$area_id = $order_details['area_id'];
			$zone_id = $this->db->query("SELECT zone_id FROM `tbl_zone_detail` where id='$area_id'")->row_array()['zone_id'];
			$delivery = $this->db->query("SELECT standard_range,express_range,night_range FROM `tbl_delivery_charges` where sale_point_id='$sale_point_id' and zone='$zone_id' and e_date<='$date'ORDER BY e_date DESC LIMIT 1")->row_array();

			if ($order_details['deliveryType'] == 'Standard') {
				$delivery_gst = ($delivery['standard_range'] * $order_details['gst']) / 100;
				$delivery_charges = $delivery['standard_range'] + $delivery_gst;
			}
			if ($order_details['deliveryType'] == 'Express') {
				$delivery_gst = ($delivery['express_range'] * $order_details['gst']) / 100;
				$delivery_charges = $delivery['express_range'] + $delivery_gst;
			}
			if ($order_details['deliveryType'] == 'Night') {
				$delivery_gst = ($delivery['night_range'] * $order_details['gst']) / 100;
				$delivery_charges = $delivery['night_range'] + $delivery_gst;
			}

			if ($catcode == 1) {
				if ($value['type'] != 'Swap') {
					$ttl_delivery_charge += (int) $value['quantity'] * (float) $delivery_charges;
					$lpg_amount += $value['quantity'] * $saleprice;
				}
			} else {
				$ttl_accessories += $value['quantity'] * $saleprice;
			}
			$total_qty += $value['quantity'];

			if ($value['type'] == 'Swap') {
				$ttl_swap_charges += $value['swap_charges'] * $value['quantity'];  // Add security charges if type is 'New'
				$total_amount = $value['swap_charges'] * $value['quantity'];
			}
			$brand_name = $this->db->query("SELECT brand_name FROM tbl_brand WHERE brand_id = '$value[cylinder_brand]'")->row_array()['brand_name'];

			// Generate HTML for table rows
			$rows .= '<tr id="row_' . $value['id'] . '">
                          
                    <td>' . $count . '<input type="hidden" value="' . $value['id'] . '" name="order_ids[]"></td>
                    <td>
                        <img src="' . $item_detail['image_path'] . '" alt="Item Image" width="50" height="50" />
                        ' . $item_detail['itemname'] . '
                    </td>
                    <td>' . $value['type'] . '</td>
                    <td>';
			if ($value['type'] == 'New') {
				$rows .= '<b>LPG Price:</b> <span class="saleprice">' . number_format($saleprice) . '</span>
                      <br>
                      <b>Security Charges:</b> <span class="securitycharges">' . number_format($security_charges) . '</span>';
			} elseif ($value['type'] == 'Swap') {
				$rows .= '<b>Cylinder Brand:</b> <span>' . $brand_name . '</span>
                      <br>
					  <b>Cylinder Condition:</b> <span>' . $value['cylinder_condition'] . '</span>
                      <br>
                      <b>Swap Credits:</b> <span class="swapcharges">' . number_format($value['swap_charges']) . '</span>';
			} elseif ($value['type'] == 'Refill') {
				$rows .= '<b>LPG Price:</b> <span class="saleprice">' . number_format($saleprice) . '</span>';
			} elseif ($value['type'] == 'Accessories') {
				$rows .= '<b>Accessories Price:</b> <span class="saleprice">' . number_format($saleprice) . '</span>';
			}
			if ($value['type'] != 'Swap') {
				$rows .= '<br><b>GST:</b> <span>' . number_format($gst) . '</span><input type="hidden" value="' . round($gst) . '" class="gst">';
			}
			$rows .= '</td>
          <td style="width: 150px;text-align-last: center;">
                  <input type="text" class="spinbox-input form-control text-center quantity-input" readonly tabindex="-1" value="' . $value['quantity'] . '" name="quantity[]" min="1" max="100" data-original="' . $value['quantity'] . '">
                 </td>
          <td class="amount">
              ' . number_format($total_amount) . '
          </td>';

			$rows .= '</tr>';
		}

		// Send the generated rows and totals as response
		echo json_encode([
			'rows' => $rows,
			'total_qty' => number_format($total_qty),
			'lpg_amount' => number_format($lpg_amount),
			'ttl_gst' => number_format($ttl_gst),
			'gst_perc' => $order_details['gst'] . "%",
			'ttl_accessories' => number_format($ttl_accessories),
			'ttl_security_charges' => number_format($ttl_security_charges),
			'ttl_swap_charges' => number_format($ttl_swap_charges),
			'ttl_delivery_charge' => $ttl_delivery_charge,
			'reject_reason' => $order_details['reject_reason'],
			'grand_total' => number_format($lpg_amount + $ttl_accessories + $ttl_security_charges + $ttl_delivery_charge + $ttl_gst + $ttl_swap_charges)
		]);
	}
}
