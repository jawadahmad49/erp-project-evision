<?php
defined('BASEPATH') or exit('No direct script access allowed');


require 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;


class Cylinder_replacement extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array(
            "mod_item",
            "mod_common"
        ));
    }

    public function index($order_id = '')
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

        $data['order_id'] = $order_id;
        $data['sale_point_id'] = $this->db->query("SELECT sale_point_id FROM tbl_place_order WHERE id = '$order_id'")->row_array()['sale_point_id'];

        $this->db->query("UPDATE tbl_place_order_detail set del_status=0");
        $this->db->query("DELETE from tbl_place_order_detail where temp=1");

        $data["filter"] = '';
        #----load view----------#
        $data["title"] = "Replacement Order";

        $this->load->view("app/Cylinder_replacement/manage_replacement", $data);
    }
    public function get_orders()
    {
        $sale_point_id = $_POST['sale_point_id'];
        $order_id = $_POST['order_id'];

        $trip_orders = $this->db->query("SELECT GROUP_CONCAT(order_id) as order_ids FROM tbl_trip_coding")->row_array();

        $excluded_order_ids = [];
        if (!empty($trip_orders['order_ids'])) {
            $excluded_order_ids = explode(',', $trip_orders['order_ids']);
        }

        $excluded_ids_str = implode("','", $excluded_order_ids);
        if (!empty($order_id)) {
            $query = "SELECT * 
				FROM tbl_place_order 
				WHERE id = '$order_id'";
        } else {
            $query = "SELECT * 
				FROM tbl_place_order 
				WHERE sale_point_id = '$sale_point_id' 
				AND type != 'walkin'
				AND deliveryStatus = 'Delivered'
				AND id NOT IN ('$excluded_ids_str')
				ORDER BY id DESC";
        }


        $order_list = $this->db->query($query)->result_array();

        foreach ($order_list as $key) { ?>
<option value="<?php echo $key['id']; ?>" <?php if (!empty($order_id)) { ?> selected <?php } ?>>
    <?php echo "Order # " . $key['id'] . " - " . $key['deliveryStatus']; ?>
</option>
<?php }
    }

    public function get_customer()
    {
        $order_id = $_POST['order'];
        $order_detail = $this->db->query("SELECT userid,address,area_id,area_name,city_id,deliveryType,date,sale_point_id,deliveryStatus,gst,delivery_location,delivery_gst,per_delivery_charges FROM `tbl_place_order` where id='$order_id'")->row_array();
        $userid = $order_detail['userid'];
        $address = $order_detail['address'];
        $area_name = $order_detail['area_name'];
        $area_id = $order_detail['area_id'];
        $city_id = $order_detail['city_id'];
        $date = $order_detail['date'];
        $sale_point_id = $order_detail['sale_point_id'];
        $zone_id = $this->db->query("SELECT zone_id FROM `tbl_zone_detail` where id='$area_id'")->row_array()['zone_id'];
        $zone_name = $this->db->query("SELECT zone_name FROM `tbl_zone` where id='$zone_id'")->row_array()['zone_name'];

        $user_detail = $this->db->query("SELECT * FROM `tbl_user` where id='$userid'")->row_array();
        $city_name = $this->db->query("SELECT city_name FROM `tbl_city` where city_id='$city_id'")->row_array()['city_name'];
        if ($user_detail['dp']) {
            $dp = $user_detail['dp'];
        } else {
            $dp = "default.jpeg";
        }
        // $delivery = $this->db->query("SELECT standard_range,express_range,night_range FROM `tbl_delivery_charges` where sale_point_id='$sale_point_id' and zone='$zone_id' and e_date<='$date'ORDER BY e_date DESC LIMIT 1")->row_array();
        // $gst = $order_detail['delivery_gst'];
        // if ($order_detail['deliveryType'] == 'Standard') {
        // 	$delivery_gst = ($delivery['standard_range'] * $gst) / 100;
        // 	// $delivery_charges = $delivery['standard_range'] + $delivery_gst;
        // 	$delivery_charges = $delivery['standard_range'] + $delivery_gst;
        // }
        // if ($order_detail['deliveryType'] == 'Express') {
        // 	$delivery_gst = ($delivery['express_range'] * $gst) / 100;
        // 	// $delivery_charges = $delivery['express_range'] + $delivery_gst;
        // 	$delivery_charges = $delivery['express_range'] + $delivery_gst;
        // }
        // if ($order_detail['deliveryType'] == 'Night') {
        // 	$delivery_gst = ($delivery['night_range'] * $gst) / 100;
        // 	// $delivery_charges = $delivery['night_range'] + $delivery_gst;
        // 	$delivery_charges = $delivery['night_range'] + $delivery_gst;
        // }
        $delivery_gst = $order_detail['delivery_gst'];
        $per_delivery_charges = $order_detail['per_delivery_charges'];
        $delivery = ($per_delivery_charges * $delivery_gst) / 100;
        $delivery_charges = round($per_delivery_charges + $delivery, 0);

        echo $user_detail['name'] . "|" . $user_detail['phone'] . "|" . $city_name . "|" . $area_name . "|" . $address . "|" . $dp . "|" . $order_detail['deliveryType'] . "|" . $delivery_charges . "|" . $order_detail['deliveryStatus'] . "|" . $order_detail['delivery_location'];
    }
    public function fetch_items_by_order()
    {
        $order_id = $this->input->post('order_id');  

        if (!$order_id) {
            echo json_encode([]);  
            return;
        }

        $this->db->select('md.materialcode, md.itemname');
        $this->db->from('tbl_place_order_detail as pod');
        $this->db->join('tblmaterial_coding as md', 'md.materialcode = pod.materialcode');
        $this->db->where('pod.order_id', $order_id);
        $this->db->where('md.status', 'Active');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $items = $query->result_array();
            echo json_encode($items);  
        } else {
            echo json_encode([]);  
        }
    }

    public function get_item_detail()
    {
        $item_type = $_POST['item_type'];
        $materialcode = $_POST['materialcode'];
        $order_id = $this->input->post('order');
        $sale_point_id = $this->input->post('sale_point_id');

        $order_details = $this->db->query("SELECT * FROM tbl_place_order WHERE id = '$order_id'")->row_array();
        $date = $order_details['date'];

        $price = $this->db->query("SELECT saleprice, security_charges FROM tbl_price_fluctuation WHERE edate <= '$date' and item_id = '$materialcode' and sale_point_id = '$sale_point_id' order by id desc")->row_array();
        $saleprice = $price['saleprice'];

        $item_detail = $this->db->query("SELECT itemname, catcode, security_price FROM tblmaterial_coding WHERE materialcode = '$materialcode'")->row_array();
        $catcode = $item_detail['catcode'];

        $catname = $this->db->query("SELECT catname FROM tblcategory WHERE id = '$catcode'")->row_array()['catname'];



        $options = '';
        if ($materialcode > 0) {
            if ($catcode == 1) {
                if ($item_type && $item_type == 'New') {
                    $options .= '<option value="New">New</option>';
                    $options .= '<option value="Refill">Refill</option>';
                 
                } else {
                    $options .= '<option value="Refill">Refill</option>';
                    $options .= '<option value="New">New</option>';
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
        echo $catname . "|" . $saleprice . "|" . $security_charges . "|" . $options;
    }
    public function get_swap_charges()
    {
        $materialcode = $_POST['materialcode'];
        $brand_id = $_POST['brand_id'];


        $order_id = $this->input->post('order');
        $sale_point_id = $this->input->post('sale_point_id');
        $order_details = $this->db->query("SELECT * FROM tbl_place_order WHERE id = '$order_id'")->row_array();
        $date = $order_details['date'];

        $price = $this->db->query("SELECT security_charges FROM tbl_price_fluctuation WHERE edate <= '$date' and item_id = '$materialcode' and sale_point_id = '$sale_point_id' order by id desc")->row_array();

        $brand = $this->db->query("SELECT swap_good,swap_average FROM tbl_brand WHERE brand_id = '$brand_id'")->row_array();

        // Get swap charges based on condition
        $swap_charges = 0; // Default swap charges
        // Assume conditions determine the swap charges
        $condition = $this->input->post('cylinder_condition'); // Get condition from request
        if ($condition == 'New/Good Condition') {
            $swap_charges = round(($price['security_charges'] * $brand['swap_good']) / 100);
        } else if ($condition == 'Average Condition') {
            $swap_charges = round(($price['security_charges'] * $brand['swap_average']) / 100);
        }

        // Return the response
        echo -$swap_charges;
    }
    public function temp_product()
    {
        $today = date('Y-m-d');
        $sale_point_id = $this->input->post('sale_point_id');
        $udata['order_id'] = $order_id = $this->input->post('order');
        $udata['materialcode'] = $materialcode = $this->input->post('materialcode');
        $udata['quantity'] = $quantity = $this->input->post('qty');
        $udata['type'] = $type = $this->input->post('item_type');
        $udata['swap_charges'] = $swap_charges = $this->input->post('swap_charges');
        $udata['cylinder_condition'] = $cylinder_condition = $this->input->post('cylinder_condition');
        $udata['cylinder_brand'] = $cylinder_brand = $this->input->post('cylinder_brand');


        $price_detail = $this->db->query("SELECT saleprice,security_charges FROM tbl_price_fluctuation WHERE saleprice > 0 AND edate <= '$today' AND sale_point_id = '$sale_point_id' and item_id = '$materialcode' order BY id DESC")->row_array();
        $price = $price_detail['saleprice'];
        $security_charges = $price_detail['security_charges'];
        $udata["price"] = $price;
        if ($type == 'New') {
            $udata["security_charges"] = $security_charges;
        }

        $udata['temp'] = 1;
        $check = $this->db->query("SELECT * from tbl_place_order_detail where order_id='$order_id' and materialcode='$materialcode' and type='$type' and del_status!=1")->row_array();
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
        $temp = $this->db->query("SELECT * from tbl_place_order_detail where id='$id'")->row_array()['temp'];
        if ($temp == 1) {
            $this->db->query("DELETE from tbl_place_order_detail where id='$id'");
        } else {
            $this->db->query("UPDATE tbl_place_order_detail set del_status=1 where id='$id'");
        }
    }
    public function submit()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->input->post('salepoint');
            $order = $this->input->post('order');
            $order_status = $this->input->post('order_status');
            $check_order = $this->db->query("SELECT * from tbl_place_order_detail where order_id='$order'")->row_array();
            if (empty($check_order)) {
                $this->session->set_flashdata('err_message', 'Please Add Some Item In Order.');
                redirect(SURL . 'app/Order_confirmation/');
            }
            if ($order_status == 'Reject' && empty($this->input->post('reject_reason'))) {
                $this->session->set_flashdata('err_message', 'Please Enter Reject Reason.');
                redirect(SURL . 'app/Order_confirmation/');
            }
            $rider_id = $this->input->post('rider_id');
            $udata['address'] = $address = $this->input->post('delivery_address');
            if ($order_status == 'Reject') {
                $udata['reject_reason'] = $this->input->post('reject_reason');
            } else {
                $udata['reject_reason'] = '';
            }
            $udata['delivery_charges'] = $this->input->post('ttl_delivery_charge');
            $udata['deliveryStatus'] = $order_status;
            // $udata['rider_id'] = $rider_id;
            $udata['delivery_location'] = $this->input->post('delivery_location');
            $udata['status_dt'] = date('Y-m-d');
            $order_change = false;
            $previous_address = $this->db->query("Select address from tbl_place_order where id = '$order'")->row_array()['address'];
            if ($previous_address !== $address) {
                $order_change = true;
            }
            if ($order_status == 'Delivered') {
                $udata['delivery_date'] = date('Y-m-d');
                $udata['delivery_time'] = date('H:i:s');
            }
            $this->mod_common->update_table("tbl_place_order", array("id" => $order), $udata);

            // Log update
            $log_data['order_id'] = $order;
            $log_data['address'] = $this->input->post('delivery_address');
            $log_data['delivery_charges'] = $this->input->post('ttl_delivery_charge');
            $log_data['created_date'] = date('Y-m-d');
            $log_data['created_time'] = date('H:i:s');
            $log_data['created_by'] = $this->session->userdata('id');

            $log_id = $this->mod_common->insert_into_table("tbl_order_log", $log_data);

            $order_detail = $this->db->query("SELECT * from tbl_place_order_detail where order_id='$order' and temp=0")->result_array();
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


            $check_temp = $this->db->query("SELECT * from tbl_place_order_detail where (temp=1 OR del_status=1) and order_id='$order'")->row_array();
            if ($check_temp) {
                $order_change = true;
            }
            $this->db->query("UPDATE tbl_place_order_detail set temp=0 where order_id='$order'");
            $this->db->query("DELETE from tbl_place_order_detail where del_status=1 and order_id='$order'");
            foreach ($this->input->post('order_ids') as $key => $value) {
                $ddata['quantity'] = $quantity = $this->input->post('quantity')[$key];
                $check_quantity = $this->db->query("SELECT quantity from tbl_place_order_detail where id='$value'")->row_array()['quantity'];
                if ($check_quantity != $quantity) {
                    $order_change = true;
                }
                $this->mod_common->update_table("tbl_place_order_detail", array("id" => $value), $ddata);
            }

            // Fetch user token for notification
            $userid = $this->db->query("SELECT userid FROM tbl_place_order WHERE id='$order' ")->row_array()['userid'];
            $userToken = $this->db->query("SELECT token FROM tbl_user WHERE id='$userid' ")->row_array()['token'];

            // Set notification message based on order status
            if ($order_status == 'Confirm') {
                if ($order_change == true) {
                    $notificationMessage = 'Your order #' . $order . ' has been confirmed and updated according to our discussion on the call. Please review the changes.';
                } else {
                    $notificationMessage = 'Your order #' . $order . ' has been confirmed';
                }
            } elseif ($order_status == 'Reject') {
                $rejectReason = $this->input->post('reject_reason');
                $notificationMessage = 'Your order #' . $order . ' has been rejected. Reason: ' . $rejectReason;
            } elseif ($order_status == 'Delivered') {
                $notificationMessage = 'Your order #' . $order . ' has been Delivered';
            } elseif ($order_status == 'Dispatch') {
                $notificationMessage = 'Your order #' . $order . ' has been Dispatch';
            }

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
            if ($order_status == 'Confirm' || $order_status == 'Reject') {
                $this->db->query("DELETE FROM tbl_orderstatushistory WHERE order_id='$order'");
            }
            $check = $this->db->query("SELECT * FROM tbl_orderstatushistory WHERE order_id='$order' AND status='$order_status'")->row_array();
            $idata['order_id'] = $order;
            $idata['status'] = $order_status;
            $idata['date'] = date('Y-m-d');
            $idata['time'] = date('H:i:s');
            if (empty($check)) {
                $this->mod_common->insert_into_table("tbl_orderstatushistory", $idata);
            } else {
                $id = $check['id'];
                $con['conditions'] = array("id" => $id);
                $this->mod_common->update_table("tbl_orderstatushistory", array("id" => $id), $idata);
            }
            if ($order_status !== 'Confirm') {
                redirect(SURL . "app/Order_confirmation");
            } else {
                echo "<script>
						window.location.href = '" . SURL . "app/Order_confirmation/';
						window.open('" . SURL . "app/Order_confirmation/small_invoice/" . $order . "', '_blank');
					</script>";
                exit; // Ensure 
            }
        }
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
        if ($order_status == 'Delivered') {
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
            // $delivery = $this->db->query("SELECT standard_range,express_range,night_range FROM `tbl_delivery_charges` where sale_point_id='$sale_point_id' and zone='$zone_id' and e_date<='$date'ORDER BY e_date DESC LIMIT 1")->row_array();

            // if ($order_details['deliveryType'] == 'Standard') {
            // 	$delivery_gst = ($delivery['standard_range'] * $order_details['delivery_gst']) / 100;
            // 	$delivery_charges = $delivery['standard_range'] + $delivery_gst;
            // }
            // if ($order_details['deliveryType'] == 'Express') {
            // 	$delivery_gst = ($delivery['express_range'] * $order_details['delivery_gst']) / 100;
            // 	$delivery_charges = $delivery['express_range'] + $delivery_gst;
            // }
            // if ($order_details['deliveryType'] == 'Night') {
            // 	$delivery_gst = ($delivery['night_range'] * $order_details['delivery_gst']) / 100;
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
                $ttl_swap_charges += $value['swap_charges'] * $value['quantity'];  // Add security charges if type is 'New'
                $total_amount = $value['swap_charges'] * $value['quantity'];
            }
            $brand_name = $this->db->query("SELECT brand_name FROM tbl_brand WHERE brand_id = '$value[cylinder_brand]'")->row_array()['brand_name'];

            // Generate HTML for table rows
            $rows .= '<tr id="row_' . $value['id'] . '">
                          
                    <td>' . $count . '<input type="hidden" value="' . $value['id'] . '" name="order_ids[]"></td>
                    <td>
                        <img src="' . $item_detail['image_path'] . '" alt="Item Image" height="50" />
                        ' . $item_detail['itemname'] . '
                    </td>
                    <td>' . $value['type'] . '</td>
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
          
          $rows .= '</td>
         
        <td>';
          if ($disabled != "disabled") {
              $rows .= '  <button type="button" class="btn btn-danger btn-sm btn-reset" onclick="del_row(\'' . $value['id'] . '\')">Delete <i class="fa fa-trash-o" aria-hidden="true"></i></button>';
          }
            // if ($value['type'] == 'New') {
            //     $rows .= '<b>LPG Price:</b> <span class="saleprice">' . number_format($saleprice) . '</span>
            //           <br>
            //           <b>Security Charges:</b> <span class="securitycharges">' . number_format($security_charges) . '</span>';
            // } elseif ($value['type'] == 'Swap') {
            //     $rows .= '<b>Cylinder Brand:</b> <span>' . $brand_name . '</span>
            //           <br>
			// 		  <b>Cylinder Condition:</b> <span>' . $value['cylinder_condition'] . '</span>
            //           <br>
            //           <b>Swap Credits:</b> <span class="swapcharges">' . number_format($value['swap_charges']) . '</span>';
            // } elseif ($value['type'] == 'Refill') {
            //     $rows .= '<b>LPG Price:</b> <span class="saleprice">' . number_format($saleprice) . '</span>';
            // } elseif ($value['type'] == 'Accessories') {
            //     $rows .= '<b>Accessories Price:</b> <span class="saleprice">' . number_format($saleprice) . '</span>';
            // }
            // if ($value['type'] != 'Swap') {
            //     $rows .= '<br><b>GST:</b> <span class="gst">' . number_format($gst) . '</span><input type="hidden" value="' . round($gst) . '" class="gst">';
            // }
            $rows .= '</td>';
         
            $rows .= '</td>';

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
    public function small_invoice($id = '')
    {
        $data['id'] = $id;
        $data["title"] = "Customer Invoice";
        $this->load->view("app/Order_confirmation/invoice", $data);

        if (isset($_POST['pdf']) && isset($_POST['name'])) {
            $pdf = $_POST['pdf'];
            $name = $_POST['name'];
            $id = $_POST['id'];

            $pdf = substr($pdf, strpos($pdf, ",") + 1);
            $pdf = base64_decode($pdf);

            file_put_contents('uploads/' . $name, $pdf);

            $name = $id . '.pdf';
            $url = SURL . "uploads/" . $name;

            $this->db->query("UPDATE `tbl_place_order` SET invoice_url='$url' WHERE id = '$id'");

            if ($this->db->affected_rows() == 0) {
                $this->session->set_flashdata('err_message', 'Something went wrong. Please try again.');
            }
        }
    }
}
