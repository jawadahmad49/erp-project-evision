<?php
defined('BASEPATH') or exit('No direct script access allowed');


require 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;


class Rider_location extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array(
			"mod_item",
			"mod_common"
		));
	}

	public function index($rider_id = '')
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

		$data['rider_id'] = $rider_id;
		$data['sale_point_id'] = $this->db->query("SELECT sale_point_id FROM tbl_place_order WHERE id = '$rider_id'")->row_array()['sale_point_id'];

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Rider Location";

		$this->load->view("app/Rider_location/manage", $data);
	}
	public function get_riders()
	{
		$sale_point_id = $_POST['sale_point_id'];
		$rider_id = $_POST['rider_id'];


		if (!empty($rider_id)) {
			$query = "SELECT * FROM tbl_rider_coding WHERE id = '$rider_id'";
		} else {
			$query = "SELECT * FROM tbl_rider_coding WHERE sale_point_id = '$sale_point_id' ORDER BY id DESC";
		}
		$order_list = $this->db->query($query)->result_array();

		foreach ($order_list as $key) { ?>
			<option value="<?php echo $key['id']; ?>" <?php if (!empty($rider_id)) { ?> selected <?php } ?>>
				<?php echo ucfirst($key['rider_name']); ?>
			</option>
		<?php }
	}
}
