<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Trip_detail_coding extends CI_Controller
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

		redirect(SURL . 'app/Trip_detail_coding/add_rider');
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
		$data["title"] = 'Trip Status';
		$data["filter"] = 'add';
		$this->load->view("app/Trip_detail_coding/add_charges", $data);
	}
	public function add()
	{
		// pm($_POST);exit;
		$login_user = $this->session->userdata('id');
		$edit = $_POST["trip"];
		$vehicle_id = $_POST["vehicle_id"];
		$rider_id = $_POST["rider_id"];

		$adata['total_receivable'] = trim($_POST["total_receivable"]);
		$adata['total_received'] = trim($_POST["total_received"]);
		$adata['status'] = $trip_status = trim($_POST["trip_status"]);

		$adata['modified_by'] = $login_user;
		$adata['modified_date'] = date('Y-m-d');
		$res_trip = $this->mod_common->update_table("tbl_trip_coding", array("id" => $edit), $adata);
		if ($trip_status == 'Completed') {
			$this->db->set('status', 'Unallocated');
			$this->db->where('id', $rider_id);
			$this->db->update('tbl_rider_coding');

			$this->db->set('status', 'Unallocated');
			$this->db->where('id', $vehicle_id);
			$this->db->update('tbl_vehicle_coding');
		}

		if ($res_trip) {
			$this->session->set_flashdata('ok_message', 'Operation successful.');
			redirect(SURL . 'app/Trip_detail_coding/');
		} else {
			$this->session->set_flashdata('err_message', 'Operation failed.');
			redirect(SURL . 'app/Trip_detail_coding/');
		}
	}
	public function get_trips()
	{
		$sale_point_id = $_POST['sale_point_id'];
		$trips = $this->db->query("SELECT * FROM tbl_trip_coding WHERE sale_point_id = '$sale_point_id' and status = 'Started'")->result_array();

		foreach ($trips as $value) {
			echo '<option value="' . $value['id'] . '" >Trip #' . $value['id'] . '</option>';
		}
	}
	public function get_trip_detail()
	{
		$rows = '';
		$rider_name = '';
		$vehicle_name = '';
		$order_names = '';
		$route_link = '';
		$status = '';
		$status_options = '';
		$trip_id = $_POST['trip_id'];
		$detail = $this->db->query("SELECT * from tbl_trip_coding where id='$trip_id'")->row_array();
		$rider_id = $detail['rider_id'];
		$sale_id = $detail['sale_point_id'];
		$rider_name = $this->db->query("Select rider_name from tbl_rider_coding where id = '$rider_id'")->row_array()['rider_name'];

		$vehicle_id = $detail['vehicle_id'];

		$vehicle_detail = $this->db->query("SELECT * FROM tbl_vehicle_coding WHERE id = '$vehicle_id'")->row_array();
		$pickup_location = $this->db->query("SELECT address FROM tbl_sales_point WHERE sale_point_id = '$sale_id'")->row_array()['address'];

		$vehicle_type = $vehicle_detail['vehicle_type'];
		if ($vehicle_type == 'motorcycle') {
			$vehicle_type = "Motor Cycle";
		} else if ($vehicle_type == 'motorcar') {
			$vehicle_type = "Motor Car";
		}
		$vehicle_name = $vehicle_detail['vehicle_number'] . " - " . $vehicle_type;

		$order_id = $detail['order_id'];
		$total_received = $detail['total_received'];
		$selected_orders = explode(',', $order_id);
		$orders = [];

		foreach ($selected_orders as $id) {
			$orders[] = "Order # " . trim($id);
		}

		$order_names = implode(',', $orders);

		$route_link = $detail['route_link'];
		$status = $detail['status'];
		if ($status == 'Completed') {
			$status_options .= '<option value="Completed" selected>Completed</option>
							<option value="Started">Started</option>';
		} else if ($status == 'Started') {
			$status_options .= '<option value="Completed">Completed</option>
					<option value="Started" selected>Started</option>';
		}

		$order_details = $this->db->query("SELECT * FROM tbl_place_order WHERE id in ($order_id) and trip_id = '$trip_id'")->result_array();

		// pm($order_details);
		// exit;
		foreach ($order_details as $key) {
			$order_id = $key['id'];
			$userid = $key['userid'];
			$address = $key['address'];
			$area_name = $key['area_name'];
			$area_id = $key['area_id'];
			$city_id = $key['city_id'];
			$date = $key['date'];
			$order_status = $key['deliveryStatus'];
			$sale_point_id = $key['sale_point_id'];
			$delivery_charges = $key['delivery_charges'];
			$delivery_gst = $key['delivery_gst'];

			$delivery_location = json_decode($key['delivery_location'], true);

			$zone_id = $this->db->query("SELECT zone_id FROM `tbl_zone_detail` where id='$area_id'")->row_array()['zone_id'];

			$user_detail = $this->db->query("SELECT * FROM `tbl_user` where id='$userid'")->row_array();
			$city_name = $this->db->query("SELECT city_name FROM `tbl_city` where city_id='$city_id'")->row_array()['city_name'];
			if ($user_detail['dp']) {
				$dp = $user_detail['dp'];
			} else {
				$dp = "default.jpeg";
			}
			$per_delivery_charges = $key['per_delivery_charges'];
			$delivery = ($per_delivery_charges * $delivery_gst) / 100;
			$delivery_charges = round($per_delivery_charges + $delivery, 0);
			// $delivery = $this->db->query("SELECT standard_range,express_range,night_range FROM `tbl_delivery_charges` where sale_point_id='$sale_point_id' and zone='$zone_id' and e_date<='$date'ORDER BY e_date DESC LIMIT 1")->row_array();
			// $gst = $key['gst'];
			// if ($key['deliveryType'] == 'Standard') {
			// 	$delivery_gst = ($delivery['standard_range'] * $gst) / 100;
			// 	$delivery_charges = $delivery['standard_range'] + $delivery_gst;
			// }
			// if ($key['deliveryType'] == 'Express') {
			// 	$delivery_gst = ($delivery['express_range'] * $gst) / 100;
			// 	$delivery_charges = $delivery['express_range'] + $delivery_gst;
			// }
			// if ($key['deliveryType'] == 'Night') {
			// 	$delivery_gst = ($delivery['night_range'] * $gst) / 100;
			// 	$delivery_charges = $delivery['night_range'] + $delivery_gst;
			// }
			$rows .= '<tr class="order-row" data-toggle="collapse" data-target="#order-details-' . $order_id . '" aria-expanded="false" aria-controls="order-details-' . $order_id . '">
				<td><a href="javascript:void(0)" class="order-number">Order # ' . $order_id . '</a></td>
				<td>' . $user_detail['name'] . '</td>
				<td><img src="' . IMG . 'profile/' . $dp . '" alt="user Image" width="50" height="50"></td>
				<td>' . $user_detail['phone'] . '</td>
				<td>' . $city_name . '</td>
				<td>' . $area_name . '</td>
				<td>' . $key['deliveryStatus'] . '</td>
				<td>' . $key['deliveryType'] . '</td>
				<td><input type="hidden" name="delivery_location" data-order-id = "' . $key['id'] . '" data-delivery_location="{&quot;latitude&quot;:' . $delivery_location['latitude'] . ',&quot;longitude&quot;:' . $delivery_location['longitude'] . ',&quot;latitudeDelta&quot;:' . $delivery_location['latitudeDelta'] . ',&quot;longitudeDelta&quot;:' . $delivery_location['longitudeDelta'] . '}">' . $address . '</td>';
			$rows .= '<td>';
			if ($key['deliveryStatus'] == "Delivered") {
				$rows .= '<a href="' . SURL . 'app/Rider_location/index/' . $rider_id . '" target="_blank" class="btn btn-info">Show</a>';
			} elseif ($key['deliveryStatus'] == "Dispatch") {
				$rows .= '<a href="' . SURL . 'app/Order_confirmation/index/' . $order_id . '" target="_blank" class="btn btn-primary">Order Confirmation</a>';
				$rows .= '<a href="' . SURL . 'app/Rider_location/index/' . $rider_id . '" target="_blank" class="btn btn-info">Show</a>';
			} else {
				$rows .= '<a href="' . SURL . 'app/Order_confirmation/index/' . $order_id . '" target="_blank" class="btn btn-primary">Order Confirmation</a>';
			}
			$rows .= '</td>';

			$rows .= '</tr>
			<tr id="order-details-' . $order_id . '" class="collapse">
				<td colspan="10" class="p-0">
					<div class="card-body">
						<table class="table table-striped table-bordered">
							<thead>
								<tr>
									<th>Sr No </th>
									<th>Item </th>
									<th>Type</th>
									<th>Unit Price </th>
									<th>Quantity</th>
									<th>Amount</th>
								</tr>
							</thead>
							<tbody>';

			$order_list_details = $this->db->query("SELECT * FROM tbl_place_order_detail WHERE order_id = '$order_id' and del_status!=1")->result_array();
			$count = 0;
			$total_qty = 0;
			$lpg_amount = 0;
			$ttl_accessories = 0;
			$ttl_security_charges = 0;
			$ttl_delivery_charge = 0;
			$ttl_gst = 0;
			$ttl_swap_charges = 0;
			foreach ($order_list_details as $value) {
				$count++;
				$materialcode = $value['materialcode'];
				$item_detail = $this->db->query("SELECT itemname, CONCAT('https://lpginsight.com/GasablePK/assets/images/items/', image_path) AS image_path, catcode, itemnameint, security_price FROM tblmaterial_coding WHERE materialcode = '$materialcode'")->row_array();
				$date = $key['date'];
				$saleprice = $value['price'];
				$security_charges = $value['security_charges'];
				if ($value['type'] != 'Swap') {
					$gst = round(($saleprice * $key['gst']) / 100);
					$ttl_gst += $value['quantity'] * $gst;
					$total_amount = ($saleprice + $gst) * $value['quantity'];
				}
				if ($value['type'] == 'New') {
					$total_amount += $security_charges * $value['quantity'];
					$ttl_security_charges += $security_charges * $value['quantity'];
				}
				$catcode = $item_detail['catcode'];
				$area_id = $key['area_id'];
				// $zone_id = $this->db->query("SELECT zone_id FROM `tbl_zone_detail` where id='$area_id'")->row_array()['zone_id'];
				// $delivery = $this->db->query("SELECT standard_range,express_range,night_range FROM `tbl_delivery_charges` where sale_point_id='$sale_point_id' and zone='$zone_id' and e_date<='$date'ORDER BY e_date DESC LIMIT 1")->row_array();
				// if ($key['deliveryType'] == 'Standard') {
				// 	$delivery_gst = ($delivery['standard_range'] * $key['gst']) / 100;
				// 	$delivery_charges = $delivery['standard_range'] + $delivery_gst;
				// }
				// if ($key['deliveryType'] == 'Express') {
				// 	$delivery_gst = ($delivery['express_range'] * $key['gst']) / 100;
				// 	$delivery_charges = $delivery['express_range'] + $delivery_gst;
				// }
				// if ($key['deliveryType'] == 'Night') {
				// 	$delivery_gst = ($delivery['night_range'] * $key['gst']) / 100;
				// 	$delivery_charges = $delivery['night_range'] + $delivery_gst;
				// }
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
					$ttl_swap_charges += $value['swap_charges'] * $value['quantity'];
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
										<td class="amount">' . number_format($total_amount) . '</td>';
				$rows .= '</tr>';
			}
			$rows .= '</tbody>
											</table>
											<table style="width:35%; float: right;" id="simple-table" class="table  table-bordered table-hover fc_currency">
												<thead>
													<tr>
														<th colspan="2">Bill Details <span class="currency"></span> </th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td style="background:#848484; color:#fff">Total Quantity</td>
														<td><input class="form-control" type="text" tabindex="-1" readonly="" id="total_qty" name="total_qty" value="' . number_format($total_qty) . '"></td>
													</tr>
													<tr>
														<td style="background:#848484; color:#fff">LPG Amount</td>
														<td><input class="form-control" type="text" tabindex="-1" readonly="" id="lpg_amount" name="lpg_amount" value="' . number_format($lpg_amount) . '"></td>
													</tr>
													<tr>
														<td style="background:#848484; color:#fff">GST Percentage</td>
														<td><input class="form-control" type="text" tabindex="-1" readonly="" id="gst_perc" name="gst_perc" value="' . $key['gst'] . "%" . '"></td>
													</tr>
													<tr>
														<td style="background:#848484; color:#fff">GST Amount</td>
														<td><input class="form-control" type="text" tabindex="-1" readonly="" id="ttl_gst" name="ttl_gst" value="' . number_format($ttl_gst) . '"></td>
													</tr>
													<tr>
														<td style="background:#848484; color:#fff">Total Security Charges</td>
														<td><input class="form-control" type="text" tabindex="-1" readonly="" id="ttl_security_charges" name="ttl_security_charges" value="' . number_format($ttl_security_charges) . '"></td>
													</tr>
													<tr>
														<td style="background:#848484; color:#fff">Accessories Amount</td>
														<td><input class="form-control" type="text" tabindex="-1" readonly="" id="ttl_accessories" name="ttl_accessories" value="' . number_format($ttl_accessories) . '"></td>
													</tr>
													<tr>
														<td style="background:#848484; color:#fff">Total Delivery Charges</td>
														<td><input class="form-control" type="text" tabindex="-1" readonly="" id="ttl_delivery_charge" name="ttl_delivery_charge" value="' . number_format($ttl_delivery_charge) . '"></td>
													</tr>

													<tr>
														<td style="background:#848484; color:#fff">Total Swap Credits</td>
														<td><input class="form-control" type="text" tabindex="-1" readonly="" id="ttl_swap_charges" name="ttl_swap_charges" value="' . number_format($ttl_swap_charges) . '"></td>
													</tr>
													<tr>
														<td style="background:#848484; color:#fff">Grand Total</td>
														<td><input class="form-control" type="text" tabindex="-1" readonly="" data-status="' . $order_status . '"  id="grand_total" name="grand_total" value="' . number_format($lpg_amount + $ttl_accessories + $ttl_security_charges + $ttl_delivery_charge + $ttl_gst + $ttl_swap_charges) . '"></td>
													</tr>
												</tbody>
											</table>
										</div>
									</td>
								</tr>';
			$count++;
		}
		header('Content-Type: application/json');
		echo json_encode([
			'rows' => $rows,
			'rider_name' => $rider_name,
			'vehicle_name' => $vehicle_name,
			'rider_id' => $rider_id,
			'vehicle_id' => $vehicle_id,
			'order_names' => $order_names,
			'route_link' => $route_link,
			'status' => $status,
			'status_options' => $status_options,
			'pickup_location' => $pickup_location,
			'total_received' => $total_received,
		]);
	}
}
