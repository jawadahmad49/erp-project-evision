<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sale_point_coding extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model(array(
			"mod_user",
			"mod_common",
			"mod_city"
		));
	}

	public function index()
	{

		$data['result'] = $this->db->query("select * from tbl_sales_point  order by sale_point_id desc ")->result_array();

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage ";
		$this->load->view("app/Sale_point_coding/manage", $data);
	}


	public function add_sale_point()
	{
		// // Initialize cURL session
		// $curl = curl_init();

		// // GeoNames API endpoint URL
		// $api_url = 'http://api.geonames.org/searchJSON';

		// // API username (replace with your own)
		// $username = 'fawadahmad';

		// // Query parameters
		// $country = urlencode('PK'); // Country code for Pakistan
		// $url = "{$api_url}?country={$country}&username={$username}";

		// // Set cURL options
		// curl_setopt($curl, CURLOPT_URL, $url);
		// curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		// // Execute cURL request
		// $response = curl_exec($curl);

		// // Check for errors
		// if ($response === false) {
		// 	// Handle cURL error
		// 	$error = curl_error($curl);
		// 	echo "cURL error: " . $error;
		// } else {
		// 	// Decode JSON response
		// 	$data = json_decode($response, true);

		// 	// Check if decoding was successful
		// 	if ($data !== null && isset($data['geonames'])) {
		// 		// Process the results
		// 		$data['city_list'] = $data['geonames'];
		// 	} else {
		// 		// Error handling if decoding failed or results are not present
		// 		echo "Failed to retrieve valid data from the GeoNames API.";
		// 	}
		// }

		// // Close cURL session
		// curl_close($curl);
		$data['city_list'] = $this->db->query("SELECT * from tbl_city where status = 'Active'")->result_array();

		$data['city_config'] = $this->db->query("SELECT city from tbl_city_config")->row_array()['city'];

		$data["title"] = "Add Sale Points";
		$this->load->view("app/Sale_point_coding/add", $data);
	}

	public function add()
	{


		if ($this->input->server('REQUEST_METHOD') == 'POST') {


			$udata['password'] = base64_encode($this->input->post('password'));
			$udata['sp_name'] = $this->input->post('name');
			$udata['email_id'] = $this->input->post('email');
			$udata['phone_num'] = $this->input->post('phone_no');
			$udata['incharge_name'] = $this->input->post('name');
			$udata['loginid'] = $this->input->post('loginid');

			$udata['assistant_name'] = $this->input->post('name');
			$udata['sp_type'] = 'Salepoint';

			$filename = $this->input->post("old_image");
			if ($_FILES['image']['name'] != "") {

				$projects_folder_path = './assets/images/shop_logo/';

				$orignal_file_name = $_FILES['image']['name'];

				$file_ext = ltrim(strtolower(strrchr($_FILES['image']['name'], '.')), '.');

				$rand_num = rand(1, 1000);

				$config['upload_path'] = $projects_folder_path;
				$config['allowed_types'] = 'jpg|jpeg|gif|tiff|tif|png';
				$config['overwrite'] = false;
				$config['encrypt_name'] = TRUE;

				$this->load->library('upload', $config);

				if (!$this->upload->do_upload('image')) {

					$error_file_arr = array('error' => $this->upload->display_errors());
					print_r($error_file_arr);
					exit;
				} else {
					$data_image_upload = array('upload_image_data' => $this->upload->data());
					$filename = $data_image_upload['upload_image_data']['file_name'];
					$full_path = $data_image_upload['upload_image_data']['full_path'];
				}
				$udata['sp_logo'] = $filename;
			}


			$udata['city_id'] = $this->input->post('city_id');
			// $udata['zone_id'] = $this->input->post('zone_id');
			$udata['address'] = $this->input->post('address');

			$orderArray = $this->input->post('zone_id');
			$udata['zone_id'] = is_array($orderArray) ? implode(',', $orderArray) : '';

			$udata['shop_location'] = $this->input->post('shop_location');
			if (empty($this->input->post("id"))) {
				$udata['created_by'] = $this->session->userdata('id');
				$udata['created_dt'] = date('Y-m-d');
				$udata['sts'] = 'Active';
				$res = $this->mod_common->insert_into_table("tbl_sales_point", $udata);
				$this->db->query("INSERT INTO `tbl_code_mapping` (`trans_id`, `sale_point_id`, `cash_code`, `tax_pay`, `tax_receive`, `customer_code`, `vendor_code`, `sales_code`, `stock_code`, `bank_code`, `expense_code`, `cost_of_goods_code`, `frieght_code`, `bulk_sales_code`, `transporter_code`, `security_code`, `gas_return_code`, `created_by`, `created_dt`, `empty_stock_code`, `empty_sale_code`, `appliances_code`, `sale_cylinder_code`, `cost_of_goods_cylinder_code`, `cylinder_wo_sec_code`, `gain_loss_code`, `cylinder_sec_code`, `delivery_charges_code`, `discount_code`, `other_cylinder_stock`, `cost_of_goods_appliances_code`) VALUES (NULL, '$res', '2007001004', '1001012003', '2005003003', '2004014000', '1001013000', '3001001003', '2003001007', '2007004000', '4006000000', '4001011007', '', '', '', '1002002003', '0', '1', '2021-10-14', '2003001008', '2003019000', '2003018003', '3001002003', '4001011008', '2005002003', '4004001003', '2005001003', '4004002003', '4001013004', '2003001009', '4001011009')");
			} else {
				$udata['modify_by'] = $this->session->userdata('id');
				$udata['modify_dt'] = date('Y-m-d');
				$last_id = $this->input->post("id");
				$res = $this->mod_common->update_table("tbl_sales_point", array("sale_point_id" => $last_id), $udata);
			}

			if ($res) {
				$this->session->set_flashdata('ok_message', 'You have succesfully added.');
				redirect(SURL . 'app/Sale_point_coding/');
			} else {
				$this->session->set_flashdata('err_message', 'Adding Operation Failed.');
				redirect(SURL . 'app/Sale_point_coding/');
			}
		}
	}

	public function edit($id)
	{
		if ($id) {
			$data['city_list'] = $this->db->query("SELECT * from tbl_city where status = 'Active'")->result_array();
			$data['record'] = $this->db->query("select * from tbl_sales_point where sale_point_id='$id'")->row_array();
			$data['city_config'] = $this->db->query("SELECT city from tbl_city_config")->row_array()['city'];

			$this->load->view("app/Sale_point_coding/add", $data);
		}
	}
	public function get_zones()
	{
		$selected_orders = explode(',', $_SESSION['zone_id']);
		$city_id = $_POST['city_id'];
		$zones = $this->db->query("SELECT * FROM `tbl_zone` where city_id='$city_id' and status='Active'")->result_array();
		foreach ($zones as $key => $value) { 
			$selected = in_array($value['id'], $selected_orders) ? 'selected' : '';
			?>
			<option value="<?php echo $value['id'] ?>" <?php echo $selected; ?>><?php echo $value['zone_name'] ?></option>
<?php }
	}
	public function delete($id)
	{


		$table = "tbl_sales_point";
		$where = "id = '" . $id . "'";
		$delete_country = $this->mod_common->delete_record($table, $where);

	 
		if ($delete_country) {
			$this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
			redirect(SURL . 'app/Sale_point_coding/');
		} else {
			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
			redirect(SURL . 'app/Sale_point_coding/');
		}
	}




	function get_area()
	{
		$city_id = $_POST['city_id'];

		$edit_area = $_POST['edit_area'];
		$selected_areas = explode("|", $edit_area);

		// Extract latitude and longitude from city_id
		list($lat, $lng) = explode(",", $city_id);

		// Google API key
		$api_key = 'AIzaSyCcFOE6o37oTX5ptY5MupQMWhKtJ_jRlFw';

		// Function to get areas within a city
		function get_places_in_city($lat, $lng, $api_key, $selected_areas)
		{
			$curl = curl_init();
			$places_url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location={$lat},{$lng}&radius=50000&type=areas&key={$api_key}";
			curl_setopt($curl, CURLOPT_URL, $places_url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$places_response = curl_exec($curl);
			curl_close($curl);

			if ($places_response === false) {
				return "Places cURL error: " . curl_error($curl);
			}

			$places_data = json_decode($places_response, true);
			if ($places_data === null || !isset($places_data['results'])) {
				return "Failed to retrieve valid data from the Places API.";
			}

			$results = $places_data['results'];
			$options = '';
			foreach ($results as $result) {
				$name = $result['name'];
				$location = $result['geometry']['location'];
				$value = $location['lat'] . "," . $location['lng'];
				$options .= '<option value="' . htmlspecialchars($value) . '" ' . (in_array($value, $selected_areas) ? 'selected' : '') . '>' . htmlspecialchars($name) . '</option>';
			}
			return $options;
		}

		// Fetch areas within the city
		$areas = get_places_in_city($lat, $lng, $api_key, $selected_areas);
		if ($areas !== null) {
			echo $areas;
		} else {
			echo "Failed to retrieve valid data from the Places API.";
		}
	}
}
