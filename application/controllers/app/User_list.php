<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_list extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model(array(
			"mod_user",
			"mod_common"
		));
	}

	public function index()
	{
		$data['users'] = $this->db->query("select * from tbl_user  order by id desc ")->result_array();

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage User List";
		$this->load->view("app/User_list/manage", $data);
	}
	public function add_user()
	{
		$data['city_list'] = $this->db->query("SELECT * from tbl_city where status = 'Active'")->result_array();
		$data["filter"] = 'add';
		$this->load->view("app/User_list/add_user", $data);
	}


	public function add()
	{
		$filename = $_POST['old_image'];

		// Check if a new image is uploaded
		if (!empty($_FILES['image']['name'])) {

			$projects_folder_path = './assets/images/user/';

			$orignal_file_name = $_FILES['image']['name'];
			$file_ext = ltrim(strtolower(strrchr($orignal_file_name, '.')), '.');
			$rand_num = rand(1, 1000);
			$file_name = 'img_' . time() . '_' . $rand_num . '.' . $file_ext;

			$config['upload_path'] = $projects_folder_path;
			$config['allowed_types'] = 'jpg|jpeg|gif|tiff|tif|png';
			$config['overwrite'] = false;
			$config['encrypt_name'] = TRUE; // Encrypt the file name
			$config['file_name'] = $file_name;

			$this->load->library('upload', $config);
			$this->upload->initialize($config);

			if (!$this->upload->do_upload('image')) {
				$error_file_arr = array('error' => $this->upload->display_errors());
				$this->session->set_flashdata('err_message', 'Image upload failed: ' . implode(', ', $error_file_arr));
			} else {
				$data_image_upload = $this->upload->data();
				$filename = $data_image_upload['file_name']; // Set the filename to the uploaded image
			}
		}

		$edit = $this->input->post("edit");

		$adata['dp'] = $filename;
		$adata['name'] = $_POST["name"];
		$adata['phone'] = $_POST["phone"];
		$adata['admin_pwd'] = base64_encode(trim($_POST['admin_pwd']));
		$adata['city'] = $_POST["city"];
		$adata['area_id'] = $_POST["area_id"];
		$adata['joining_date'] = $_POST["joining_date"];
		$adata['address'] = $_POST["address"];
		$adata['status'] = $_POST["status"];
		$adata['location'] = $_POST["user_location"];
		$adata['email'] = $_POST["email"];

		$adata['tex_type'] = $this->input->post('tex_type');
		$adata['ntn'] = $this->input->post('ntn');
		$adata['nic'] = $this->input->post('nic');

		// $this->db->where('name', $_POST['name']);
		$this->db->where('phone', $_POST['phone']);

		if (!empty($edit)) {
			$this->db->where('id !=', $edit);
		}

		$existing_record = $this->db->get('tbl_user')->row_array();

		if (!empty($existing_record)) {
			$this->session->set_flashdata('err_message', 'User with this name and phone already exists.');
			redirect(SURL . 'app/User_list/');
			return;
		}

		if (empty($edit)) {
			$res = $this->mod_common->insert_into_table("tbl_user", $adata);
		} else {
			// echo "<pre>"; print_r($adata) ;exit;

			$this->mod_common->update_table("tbl_user", array("id" => $edit), $adata);
			$res = $edit;
		}

		if ($res) {
			$this->session->set_flashdata('ok_message', 'Operation successful.');
			redirect(SURL . 'app/User_list/');
		} else {
			$this->session->set_flashdata('err_message', 'Operation failed.');
			redirect(SURL . 'app/User_list/');
		}
	}
	public function delete($id)
	{

		$table = "tbl_user";
		$where = "id = '" . $id . "'";
		$delete = $this->mod_common->delete_record($table, $where);

		if ($delete) {
			$this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
			redirect(SURL . 'app/User_list/');
		} else {
			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
			redirect(SURL . 'app/User_list/');
		}
	}
	public function edit($id = '')
	{
		$data['record'] = $this->db->query("SELECT * from tbl_user where id='$id'")->row_array();
		$data['city_list'] = $this->db->query("SELECT * from tbl_city where status = 'Active'")->result_array();

		$data["title"] = 'Edit User Coding';
		$data["filter"] = 'add';
		$this->load->view("app/User_list/add_user", $data);
	}
	public function get_areas()
	{
		$city_id = $_POST['city_id'];
		$area_id = $_SESSION["area_id"];
		$assigned_sale_point_ids = $this->session->userdata('location');

		$assigned_zone_ids = $this->db->query("SELECT zone_id FROM tbl_sales_point WHERE sale_point_id IN ($assigned_sale_point_ids)")->result_array();

		$zone_ids = [];
		foreach ($assigned_zone_ids as $row) {
			$ids = explode(',', $row['zone_id']);
			$zone_ids = array_merge($zone_ids, $ids);
		}

		$zone_ids = array_unique(array_map('intval', $zone_ids));

		$zone_ids_str = implode(',', $zone_ids);

		$areas = $this->db->query("
				SELECT tbl_zone_detail.id as detail_id, tbl_zone_detail.area_name as area_name 
				FROM tbl_zone 
				INNER JOIN tbl_zone_detail ON tbl_zone.id = tbl_zone_detail.zone_id 
				WHERE status = 'Active' 
				AND city_id = '$city_id' 
				AND zone_id IN ($zone_ids_str)")->result_array();

		foreach ($areas as $key => $value) { ?>
			<option value="<?php echo $value['detail_id']; ?>" <?php if ($area_id == $value['detail_id'])
				   echo 'selected'; ?>>
				<?php echo $value['area_name']; ?>
			</option>
		<?php }
	}
}
