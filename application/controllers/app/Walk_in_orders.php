<?php
defined('BASEPATH') or exit('No direct script access allowed');

require 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;



class Walk_in_orders extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array(
			"mod_item",
			"mod_common"
		));
	}

	public function index()
	{


		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Walk In Orders";

		$this->load->view("app/Walk_in_orders/manage_orders", $data);
	}
	public function add_orders()
	{
		$login_user = $this->session->userdata('id');
		$sale_point_ids = $this->db->query("SELECT location FROM tbl_admin WHERE id = '$login_user'")->row_array()['location'];
		if ($sale_point_ids) {
			$sale_point_id_array = explode(',', $sale_point_ids);
			$sale_point_id_list = implode("','", $sale_point_id_array);
			$where_location = "WHERE sale_point_id IN ('$sale_point_id_list')";
		} else {
			$where_location = "";
		}
		$data['salepoint'] = $this->db->query("SELECT * from tbl_sales_point $where_location")->result_array();
		$data['user_list'] = $this->db->query("SELECT * from tbl_user")->result_array();
		$this->db->query("UPDATE tbl_place_order_detail set del_status_walkin=0");
		$this->db->query("DELETE from tbl_place_order_detail where temp_walkin=1");

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Walk In Orders";

		$this->load->view("app/Walk_in_orders/add_orders", $data);
	}
	public function edit($id)
	{
		$login_user = $this->session->userdata('id');
		$sale_point_ids = $this->db->query("SELECT location FROM tbl_admin WHERE id = '$login_user'")->row_array()['location'];
		if ($sale_point_ids) {
			$sale_point_id_array = explode(',', $sale_point_ids);
			$sale_point_id_list = implode("','", $sale_point_id_array);
			$where_location = "WHERE sale_point_id IN ('$sale_point_id_list')";
		} else {
			$where_location = "";
		}
		$data['record'] = $this->db->query("SELECT * from tbl_place_order where id ='$id'")->row_array();
		$data['salepoint'] = $this->db->query("SELECT * from tbl_sales_point $where_location")->result_array();
		$data['user_list'] = $this->db->query("SELECT * from tbl_user")->result_array();
		$this->db->query("UPDATE tbl_place_order_detail set del_status_walkin=0");
		$this->db->query("DELETE from tbl_place_order_detail where temp_walkin=1");

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Walk In Orders";
		$this->load->view("app/Walk_in_orders/add_orders", $data);
	}
	public function get_orders()
	{
		$sale_point_id = $_POST['sale_point_id'];

		$order_list = $this->db->query("SELECT * FROM tbl_place_order WHERE sale_point_id = '$sale_point_id' ORDER BY id DESC")->result_array();

		foreach ($order_list as $key) { ?>
			<option value="<?php echo $key['id'] ?>"><?php echo "Order # " . $key['id']; ?></option>
		<?php }
	}
	public function get_customer()
	{
		$customer_id = $_POST['customer_id'];

		$user_detail = $this->db->query("SELECT * FROM `tbl_user` where id='$customer_id'")->row_array();
		if ($user_detail['dp']) {
			$dp = $user_detail['dp'];
		} else {
			$dp = "default.JPG";
		}
		echo $dp;
		exit;
	}
	public function get_item_detail()
	{
		$item_type = $_POST['item_type'];
		$materialcode = $_POST['materialcode'];
		$date = $_POST['date'];
		$customer_id = $_POST['customer_id'];

		$sale_point_id = $this->input->post('sale_point_id'); // Assuming you need sale_point_id
		$tex_type = $this->db->query("SELECT tex_type FROM `tbl_user` where id='$customer_id'")->row_array()['tex_type'];
		$company_detail = $this->db->query("SELECT * FROM `tbl_company`")->row_array();
		$filer = $company_detail['filer'];
		$non_filer = $company_detail['non_filer'];
		// Fetch sale price and security charges
		$price = $this->db->query("SELECT security_charges FROM tbl_price_fluctuation WHERE edate <= '$date' and item_id = '$materialcode' and sale_point_id = '$sale_point_id' order by id desc")->row_array();
		$Refill_registered_cyl_price = $this->db->query("SELECT registered_saleprice FROM tbl_price_fluctuation WHERE registered_saleprice > 0 AND edate <= '$date' AND sale_point_id = '$sale_point_id' and item_id = '$materialcode' order BY id DESC")->row_array()['registered_saleprice'];
		$Refill_un_registered_cyl_price = $this->db->query("SELECT un_registered_saleprice FROM tbl_price_fluctuation WHERE un_registered_saleprice > 0 AND edate <= '$date' AND sale_point_id = '$sale_point_id' and item_id = '$materialcode' order BY id DESC")->row_array()['un_registered_saleprice'];
		if ($tex_type == 'filer' && !empty($tex_type)) {
			$gst_perc = $filer;
			$saleprice = $Refill_registered_cyl_price / (1 + ($gst_perc / 100));

		} else if ($tex_type == 'non_filer' || empty($tex_type)) {
			$gst_perc = $non_filer;
			$saleprice = $Refill_un_registered_cyl_price / (1 + ($gst_perc / 100));
		}

		// $saleprice = $price['saleprice'];

		// Fetch item details
		$item_detail = $this->db->query("SELECT itemname, CONCAT('https://lpginsight.com/GasablePK/assets/images/items/', image_path) AS image_path, catcode, itemnameint, security_price FROM tblmaterial_coding WHERE materialcode = '$materialcode'")->row_array();
		$catcode = $item_detail['catcode'];

		// Fetch category name
		$catname = $this->db->query("SELECT catname FROM tblcategory WHERE id = '$catcode'")->row_array()['catname'];

		// Build options
		$options = '';
		if ($materialcode > 0) {
			if ($catcode == 1) {
				if ($item_type && $item_type == 'New') {
					$options .= '<option value="New">New</option>';
					$options .= '<option value="Swap">Swap</option>';
					$options .= '<option value="Refill">Refill</option>';
				} else if ($item_type && $item_type == 'Swap') {
					$options .= '<option value="Swap">Swap</option>';
					$options .= '<option value="New">New</option>';
					$options .= '<option value="Refill">Refill</option>';
				} else {
					$options .= '<option value="Refill">Refill</option>';
					$options .= '<option value="New">New</option>';
					$options .= '<option value="Swap">Swap</option>';
				}
			} else {
				$options .= '<option value="Accessories">Accessories</option>';
			}
		}
		if ($item_type && $item_type == 'New') {
			$security_charges = $price['security_charges'];
		} else {
			$security_charges = 0;
		}
		// Return the response
		echo $catname . "|" . round($saleprice) . "|" . $security_charges . "|" . $options;
	}
	public function get_swap_charges()
	{
		$materialcode = $_POST['materialcode'];
		$brand_id = $_POST['brand_id'];
		$date = $_POST['date'];
		$sale_point_id = $_POST['sale_point_id'];

		$price = $this->db->query("SELECT security_charges FROM tbl_price_fluctuation WHERE edate <= '$date' and item_id = '$materialcode' and sale_point_id = '$sale_point_id' order by id desc")->row_array();

		$brand = $this->db->query("SELECT swap_good,swap_average FROM tbl_brand WHERE brand_id = '$brand_id'")->row_array();

		$swap_charges = 0;
		$condition = $this->input->post('cylinder_condition');
		if ($condition == 'New/Good Condition') {
			$swap_charges = round(($price['security_charges'] * $brand['swap_good']) / 100);
		} else if ($condition == 'Average Condition') {
			$swap_charges = round(($price['security_charges'] * $brand['swap_average']) / 100);
		}
		echo -$swap_charges;
	}
	public function temp_product()
	{
		if (empty($this->input->post('edit'))) {
			$udata['order_id'] = 0;
		} else {
			$udata['order_id'] = $this->input->post('edit');
		}
		$sale_point_id = $_POST['sale_point_id'];
		$customer_id = $_POST['customer_id'];
		$today = date('Y-m-d');
		$udata['materialcode'] = $materialcode = $this->input->post('materialcode');
		$udata['quantity'] = $quantity = $this->input->post('qty');
		$udata['type'] = $type = $this->input->post('item_type');
		$edit = $this->input->post('edit');
		$udata['temp_walkin'] = 1;
		$udata['swap_charges'] = $swap_charges = $this->input->post('swap_charges');
		$udata['cylinder_condition'] = $cylinder_condition = $this->input->post('cylinder_condition');
		$udata['cylinder_brand'] = $cylinder_brand = $this->input->post('cylinder_brand');
		//////////////////////////////////////////////////////////////////////////
		$tex_type = $this->db->query("SELECT tex_type FROM `tbl_user` where id='$customer_id'")->row_array()['tex_type'];
		$company_detail = $this->db->query("SELECT * FROM `tbl_company`")->row_array();
		$filer = $company_detail['filer'];
		$non_filer = $company_detail['non_filer'];

		$Refill_registered_cyl_price = $this->db->query("SELECT registered_saleprice FROM tbl_price_fluctuation WHERE registered_saleprice > 0 AND edate <= '$today' AND sale_point_id = '$sale_point_id' and item_id = '$materialcode' order BY id DESC")->row_array()['registered_saleprice'];
		$Refill_un_registered_cyl_price = $this->db->query("SELECT un_registered_saleprice FROM tbl_price_fluctuation WHERE un_registered_saleprice > 0 AND edate <= '$today' AND sale_point_id = '$sale_point_id' and item_id = '$materialcode' order BY id DESC")->row_array()['un_registered_saleprice'];
		if ($tex_type == 'filer' && !empty($tex_type)) {
			$gst_perc = $filer;
			$price = $Refill_registered_cyl_price / (1 + ($gst_perc / 100));

		} else if ($tex_type == 'non_filer' || empty($tex_type)) {
			$gst_perc = $non_filer;
			$price = $Refill_un_registered_cyl_price / (1 + ($gst_perc / 100));
		}
		//////////////////////////////////////////////////////////////////////////
		$price_detail = $this->db->query("SELECT security_charges FROM tbl_price_fluctuation WHERE edate <= '$today' AND sale_point_id = '$sale_point_id' and item_id = '$materialcode' order BY id DESC")->row_array();
		// $price = $price_detail['saleprice'];
		$security_charges = $price_detail['security_charges'];
		$udata["price"] = round($price);
		if ($type == 'New') {
			$udata["security_charges"] = $security_charges;
		}

		$check = $this->db->query("SELECT * from tbl_place_order_detail where order_id='$edit' and materialcode='$materialcode' and type='$type' and del_status_walkin!=1")->row_array();
		if ($check) {
			echo "Item Already Added Against This Order !";
			exit;
		}

		$this->mod_common->insert_into_table("tbl_place_order_detail", $udata);
		echo "success";
		exit;
	}
	public function del_row()
	{
		$udata['id'] = $id = $this->input->post('id');
		$temp_walkin = $this->db->query("SELECT * from tbl_place_order_detail where id='$id'")->row_array()['temp_walkin'];
		if ($temp_walkin == 1) {
			$this->db->query("DELETE from tbl_place_order_detail where id='$id'");
		} else {
			$this->db->query("UPDATE tbl_place_order_detail set del_status_walkin=1 where id='$id'");
		}
	}
	public function submit()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$udata['deliveryStatus'] = 'Delivered';
			$udata['date'] = $this->input->post('date');
			$udata['userid'] = $this->input->post('customer_id');
			$edit = $this->input->post('edit');
			$udata['sale_point_id'] = $sale_point_id = $this->input->post('salepoint');
			$udata['delivery_charges'] = $this->input->post('delivery_charges');
			$udata['gst'] = explode('%', $this->input->post('gst_perc'))[0];
			$udata['type'] = 'walkin';
			if (empty($edit)) {
				$order_id = $this->mod_common->insert_into_table("tbl_place_order", $udata);
			} else {
				$this->mod_common->update_table("tbl_place_order", array("id" => $edit), $udata);
				$order_id = $edit;
			}
			// Log update
			$log_data['order_id'] = $order_id;
			$log_data['created_date'] = date('Y-m-d');
			$log_data['created_time'] = date('H:i:s');
			$log_data['created_by'] = $this->session->userdata('id');

			$log_id = $this->mod_common->insert_into_table("tbl_order_log", $log_data);

			$order_detail = $this->db->query("SELECT * from tbl_place_order_detail where order_id='$order_id' and temp_walkin=0")->result_array();
			foreach ($order_detail as $key => $value) {
				$log_detail['log_id'] = $log_id;
				$log_detail['order_id'] = $value['order_id'];
				$log_detail['materialcode'] = $value['materialcode'];
				$log_detail['quantity'] = $value['quantity'];
				$log_detail['type'] = $value['type'];
				$log_detail['security_charges'] = $value['security_charges'];
				$log_detail['price'] = $value['price'];

				$this->mod_common->insert_into_table("tbl_order_log_detail", $log_detail);
			}
			if (empty($edit)) {
				$this->db->query("
					UPDATE tbl_place_order_detail 
					SET temp_walkin = 0, order_id = ? 
					WHERE order_id = 0 AND temp_walkin = 1",
					array($order_id)
				);
			} else {
				$this->db->query("
					UPDATE tbl_place_order_detail 
					SET temp_walkin = 0 
					WHERE order_id = ?",
					array($order_id)
				);
			}
			$check = $this->db->query("SELECT * FROM tbl_orderstatushistory WHERE order_id='$order_id' AND status='Delivered'")->row_array();
			$idata['order_id'] = $order_id;
			$idata['status'] = $udata['deliveryStatus'];
			$idata['date'] = date('Y-m-d');
			$idata['time'] = date('H:i:s');
			if (empty($check)) {
				$this->mod_common->insert_into_table("tbl_orderstatushistory", $idata);
			} else {
				$id = $check['id'];
				$this->mod_common->update_table("tbl_orderstatushistory", array("id" => $id), $idata);
			}
			// Fetch user token for notification
			$userid = $this->db->query("SELECT userid FROM tbl_place_order WHERE id='$order_id' ")->row_array()['userid'];
			$userToken = $this->db->query("SELECT token FROM tbl_user WHERE id='$userid' ")->row_array()['token'];


			$notificationMessage = 'Your order #' . $order_id . ' has been Delivered';


			if ($userToken) {
				// Send notification
				try {
					$factory = (new Factory)
						->withServiceAccount(__DIR__ . '/opi-gas-727d8-firebase-adminsdk-2xd00-ecada38560.json')
						->withDatabaseUri('https://opi-gas-727d8.firebaseio.com');
					$messaging = $factory->createMessaging();
					$message = CloudMessage::withTarget('token', $userToken)
						->withNotification(Notification::create('Order Status Update', $notificationMessage))
						->withAndroidConfig([
							'notification' => [
								'icon' => 'https://lpginsight.com/GasablePK/assets/images/logo',
								'color' => '#FF0000', // Optional: Notification color
							]
						]);
					$response = $messaging->send($message);
					$notificationStatus = true;
				} catch (\Exception $e) {
					$notificationStatus = false;
					// Handle notification sending error
				}
			}
			$order_url = SURL . "app/Order_confirmation/";
			$invoice_url = SURL . "app/Order_confirmation/small_invoice/" . $order_id;
			$walk_in_orders_url = SURL . "app/Walk_in_orders";

			echo "<script>
				window.location.href = '$walk_in_orders_url';  // Redirect to Walk_in_orders
				window.open('$invoice_url', '_blank');          // Open small invoice in new tab
			</script>";
		}
	}

	public function get_order_detail()
	{
		$order_id = $this->input->post('order');
		$userid = $this->input->post('userid');
		$edit = $this->input->post('edit');
		$sale_point_id = $this->input->post('sale_point_id');
		$delivery_charges = $this->input->post('delivery_charges');
		$customer_id = $this->input->post('customer_id');

		// Fetch order details
		if ($edit > 0) {
			$order_list_details = $this->db->query("SELECT * FROM tbl_place_order_detail WHERE order_id = '$order_id'")->result_array();
		} else {
			$order_list_details = $this->db->query("SELECT * FROM tbl_place_order_detail WHERE order_id = '$order_id' and del_status_walkin !=1")->result_array();
		}

		$order_details = $this->db->query("SELECT * FROM tbl_place_order WHERE id = '$edit'")->row_array();
		if (empty($edit)) {
			$tex_type = $this->db->query("SELECT tex_type FROM `tbl_user` where id = '$customer_id'")->row_array()['tex_type'];

			$gst = $this->db->query("SELECT filer, non_filer FROM `tbl_company` where id = '1'")->row_array();
			if ($tex_type == 'filer') {
				$order_details['gst'] = $gst['filer'];
			} else if ($tex_type == 'non_filer') {
				$order_details['gst'] = $gst['non_filer'];
			}
		}
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
                          
                    <td>' . $count . '<input type="hidden" value="' . $value['id'] . '" name="order_ids[]">
					<input type="hidden" value="' . $value['materialcode'] . '" name="materialcode[]">
					<input type="hidden" value="' . $value['type'] . '" name="type[]">
					</td>
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
				$rows .= '<br><b>GST:</b> <span class="gst">' . number_format($gst) . '</span><input type="hidden" value="' . round($gst) . '" class="gst">';
			}
			$rows .= '</td>
          <td style="width: 150px;text-align-last: center;">
              <div class="input-group form-group">
                  <div class="spinbox-buttons input-group-btn">';
			if ($disabled != "disabled") {
				$rows .= '      <button type="button" class="btn spinbox-down btn-sm btn-danger">
                      <i class="icon-only ace-icon fa fa-minus bigger-110"></i>
                  </button>';
			}
			$rows .= '  </div>
                  <input type="text" class="spinbox-input form-control text-center quantity-input" readonly tabindex="-1" value="' . $value['quantity'] . '" name="quantity[]" min="1" max="100" data-original="' . $value['quantity'] . '">
                  <div class="spinbox-buttons input-group-btn">';
			if ($disabled != "disabled") {
				$rows .= '      <button type="button" class="btn spinbox-up btn-sm btn-success">
                      <i class="icon-only ace-icon fa fa-plus bigger-110"></i>
                  </button>';
			}
			$rows .= '  </div></div>';
			if ($disabled != "disabled") {
				$rows .= '  <button type="button" class="btn btn-warning btn-sm btn-reset">Reset <i class="fa fa-refresh" aria-hidden="true"></i></button>';
			}
			$rows .= '</td>
          <td class="amount">
              ' . number_format($total_amount) . '
          </td>
          <td>';
			if ($disabled != "disabled") {
				$rows .= '  <button type="button" class="btn btn-danger btn-sm btn-reset" onclick="del_row(\'' . $value['id'] . '\')">Delete <i class="fa fa-trash-o" aria-hidden="true"></i></button>';
			}
			$rows .= '</td>';

			$rows .= '</tr>';
		}

		echo json_encode([
			'rows' => $rows,
			'total_qty' => $total_qty,
			'lpg_amount' => $lpg_amount,
			'ttl_gst' => $ttl_gst,
			'gst_perc' => $order_details['gst'] . "%",
			'ttl_accessories' => $ttl_accessories,
			'ttl_security_charges' => $ttl_security_charges,
			'ttl_swap_charges' => $ttl_swap_charges,
			'ttl_delivery_charge' => $ttl_delivery_charge,
			'reject_reason' => $order_details['reject_reason'],
			'grand_total' => $lpg_amount + $ttl_accessories + $ttl_security_charges + $ttl_delivery_charge + $ttl_gst + $ttl_swap_charges
		]);
	}
	public function your_ajax_endpoint()
	{
		$login_user = $this->session->userdata('id');
		$this->db->select('location');
		$this->db->from('tbl_admin');
		$this->db->where('id', $login_user);
		$sale_point_ids = $this->db->get()->row_array()['location'];

		$draw = $this->input->post('draw');
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$searchValue = $this->input->post('search')['value'];

		$orderColumnIndex = $this->input->post('order')[0]['column'];
		$orderDirection = $this->input->post('order')[0]['dir'];
		$columns = $this->input->post('columns');

		$orderColumn = $columns[$orderColumnIndex]['data'];

		if (isset($_POST['datepicker'])) {
			$from_date = date("Y-m-d", strtotime($_POST['datepicker']));
			$to_date = date("Y-m-d", strtotime($_POST['datepicker1']));
		} else {
			$from_date = date('Y-m-d', strtotime('-60 day'));
			$to_date = date('Y-m-d');
		}

		$this->db->select('COUNT(*) as count');
		$this->db->from('tbl_place_order');
		$this->db->where('date >=', $from_date);
		$this->db->where('date <=', $to_date);
		$this->db->where('type', 'walkin');

		if (!empty($searchValue)) {
			$this->db->group_start();
			$this->db->like('id', $searchValue);
			$this->db->or_like('date', $searchValue);
			$this->db->or_like('deliveryType', $searchValue);
			$this->db->or_like('deliveryStatus', $searchValue);
			$this->db->group_end();
		}

		if ($sale_point_ids) {
			$sale_point_id_array = explode(',', $sale_point_ids);
			$this->db->where_in('sale_point_id', $sale_point_id_array);
		}

		$recordsTotal = $this->db->get()->row()->count;

		$this->db->select('*');
		$this->db->from('tbl_place_order');
		$this->db->where('date >=', $from_date);
		$this->db->where('date <=', $to_date);
		$this->db->where('type', 'walkin');

		if (!empty($searchValue)) {
			$this->db->group_start();
			$this->db->like('id', $searchValue);
			$this->db->or_like('date', $searchValue);
			$this->db->or_like('deliveryType', $searchValue);
			$this->db->or_like('deliveryStatus', $searchValue);
			$this->db->group_end();
		}

		if ($sale_point_ids) {
			$sale_point_id_array = explode(',', $sale_point_ids);
			$this->db->where_in('sale_point_id', $sale_point_id_array);
		}

		$this->db->order_by($orderColumn, $orderDirection);
		$this->db->limit($length, $start);

		$results = $this->db->get()->result_array();

		$data = [];
		$sno = 0;
		foreach ($results as $value) {
			$sno++;
			$id = $value['id'];

			$delivery_status = '';
			switch ($value["deliveryStatus"]) {
				case "Delivered":
					$delivery_status = '<strong style="color: blue;">' . $value["deliveryStatus"] . '</strong>';
					break;
				case "Booked":
					$delivery_status = '<strong style="color: green;">' . $value["deliveryStatus"] . '</strong>';
					break;
				case "Reject":
					$delivery_status = '<strong style="color: red;">' . $value["deliveryStatus"] . '</strong>';
					break;
				case "Confirm":
					$delivery_status = '<strong style="color: green;">' . $value["deliveryStatus"] . '</strong>';
					break;
				case "Dispatch":
					$delivery_status = '<strong style="color: blue;">' . $value["deliveryStatus"] . '</strong>';
					break;
			}



			$action_buttons = '<a class="green" href="' . SURL . 'app/Walk_in_orders/edit/' . $id . '">
                      <i class="ace-icon fa fa-pencil bigger-130"></i>
                   </a>
                   <a id="bootbox-confirm" href="javascript:void(0)" class="red" onClick="confirmDelete(\'' . SURL . 'app/Walk_in_orders/delete/' . $value['id'] . '\');">
                      <i class="ace-icon fa fa-trash-o bigger-130"></i>
                   </a>';


			$data[] = [
				'count' => $sno,
				'id' => $value['id'],
				'date' => $value['date'],
				'deliveryType' => $value['type'],
				'deliveryStatus' => $delivery_status,
				'actions' => $action_buttons
			];
		}
		echo json_encode([
			'draw' => intval($draw),
			'recordsTotal' => intval($recordsTotal),
			'recordsFiltered' => intval($recordsTotal),
			'data' => $data
		]);
	}
	public function delete($id)
	{
		$login_user = $this->session->userdata('id');
		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '1061' limit 1")->row_array();
		if ($role['delete'] != 1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'app/Walk_in_orders/index/');
		}
		#-------------delete record--------------#
		$table = "tbl_place_order";
		$where = "id = " . $id . "";
		$delete_area = $this->mod_common->delete_record($table, $where);
		$table = "tbl_place_order_detail";
		$where = "order_id = " . $id . "";
		$delete_area = $this->mod_common->delete_record($table, $where);
		$table = "tbl_orderstatushistory";
		$where = "order_id = " . $id . "";
		$delete_area = $this->mod_common->delete_record($table, $where);
		if ($delete_area) {
			$this->session->set_flashdata('ok_message', 'You have successfully deleted.');
			redirect(SURL . 'app/Walk_in_orders/index/');
		} else {
			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
			redirect(SURL . 'app/Walk_in_orders/index/');
		}
	}
}
