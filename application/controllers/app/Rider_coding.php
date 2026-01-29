<?php

defined('BASEPATH') or exit('No direct script access allowed');



class Rider_coding extends CI_Controller
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
			$this->db->from('tbl_rider_coding');
			$this->db->where_in('sale_point_id', $sale_point_id_array);
			$this->db->order_by('id', 'DESC');
			$data['tbl_data'] = $this->db->get()->result_array();
		} else {
			$data['tbl_data'] = [];
		}

		$data["filter"] = '';

		$data["title"] = "Manage Rider Coding";
		$this->load->view("app/Rider_coding/manage_customer", $data);
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
		$this->load->view("app/Rider_coding/add_charges", $data);
	}


	public function add()
	{
		$login_user = $this->session->userdata('id');
		$comp_id = $this->session->userdata('comp_id');
		$edit = $this->input->post("edit");
		$old_image = $this->input->post("old_image");
		$filename = $old_image;

		if ($_FILES['image']['name'] != "") {
			$projects_folder_path = './assets/images/rider/';

			// Delete the old image if it exists
			if (!empty($old_image) && file_exists($projects_folder_path . $old_image)) {
				unlink($projects_folder_path . $old_image);
			}

			// Get original file details
			$original_file_name = $_FILES['image']['name'];
			$file_tmp = $_FILES['image']['tmp_name'];
			$file_ext = ltrim(strtolower(strrchr($original_file_name, '.')), '.');

			// Generate a unique filename
			$new_filename = uniqid('img_', true) . '.' . $file_ext;
			$full_path = $projects_folder_path . $new_filename;

			// Compress the image and save it
			$compression_result = $this->compress_image($file_tmp, $full_path, 70); // 70% quality

			if (!$compression_result) {
				$this->session->set_flashdata('err_message', 'Image compression failed.');
				redirect(SURL . 'app/Rider_coding/');
				return;
			}

			$filename = $new_filename;
		}

		// Prepare data for insertion or update
		$adata['image'] = $filename;
		$adata['sale_point_id'] = $_POST["location"];
		$adata['created_by'] = $login_user;
		$adata['created_date'] = date('Y-m-d');
		$adata['comp_id'] = $comp_id;

		$adata['password'] = base64_encode($this->input->post('password'));
		$adata['loginid'] = $this->input->post('loginid');

		$adata['rider_name'] = trim($_POST["rider_name"]);
		$adata['phone_number'] = trim($_POST["phone_number"]);
		$adata['cnic'] = trim($_POST["cnic"]);
		$adata['license_type'] = trim($_POST["license_type"]);
		$adata['notes'] = trim($_POST["notes"]);
		$adata['date'] = trim($_POST["date"]);
		$this->db->group_start();
		// $this->db->where('rider_name', $adata['rider_name']);
		$this->db->where('cnic', $adata['cnic']);
		$this->db->or_where('phone_number', $adata['phone_number']);
		$this->db->or_where('loginid', $adata['loginid']);
		$this->db->group_end();

		if (!empty($edit)) {
			$this->db->where('id !=', $edit);
		}

		$existing_record = $this->db->get('tbl_rider_coding')->row_array();


		if (!empty($existing_record)) {
			$this->session->set_flashdata('err_message', 'Rider with this name, CNIC & Phone Number already exists.');
			redirect(SURL . 'app/Rider_coding/');
			exit;
		}
		if (empty($edit)) {
			$res = $this->mod_common->insert_into_table("tbl_rider_coding", $adata);
		} else {
			$adata['modified_by'] = $login_user;
			$adata['modified_date'] = date('Y-m-d');
			$this->mod_common->update_table("tbl_rider_coding", array("id" => $edit), $adata);
			$res = $edit;
		}

		if ($res) {
			$this->session->set_flashdata('ok_message', 'Operation successful.');
			redirect(SURL . 'app/Rider_coding/');
		} else {
			$this->session->set_flashdata('err_message', 'Operation failed.');
			redirect(SURL . 'app/Rider_coding/');
		}
	}
	/**
	 * Compress and save an image
	 *
	 * @param string $source_path Path to the original image
	 * @param string $destination_path Path where the compressed image will be saved
	 * @param int $quality Compression quality (0-100, higher means better quality)
	 * @return bool True on success, false on failure
	 */
	private function compress_image($source_path, $destination_path, $quality = 70)
	{
		$image_info = getimagesize($source_path);
		$mime = $image_info['mime'];

		switch ($mime) {
			case 'image/jpeg':
				$image = imagecreatefromjpeg($source_path);
				$result = imagejpeg($image, $destination_path, $quality);
				break;
			case 'image/png':
				$image = imagecreatefrompng($source_path);
				// Compression level for PNG (0-9), 9 is maximum compression
				$result = imagepng($image, $destination_path, 9);
				break;
			case 'image/gif':
				$image = imagecreatefromgif($source_path);
				$result = imagegif($image, $destination_path);
				break;
			default:
				return false;
		}

		// Free up memory
		imagedestroy($image);
		return $result;
	}


	public function delete($id)
	{
		$login_user = $this->session->userdata('id');
		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '1061' limit 1")->row_array();
		if ($role['delete'] != 1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'app/Rider_coding/index/');
		}
		#-------------delete record--------------#
		$table = "tbl_rider_coding";
		$where = "id = " . $id . "";
		$delete_area = $this->mod_common->delete_record($table, $where);
		if ($delete_area) {
			$this->session->set_flashdata('ok_message', 'You have successfully deleted.');
			redirect(SURL . 'app/Rider_coding/index/');
		} else {
			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
			redirect(SURL . 'app/Rider_coding/index/');
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

		$data['record'] = $this->db->query("SELECT * from tbl_rider_coding where id='$id'")->row_array();

		$data["filter"] = 'add';

		$this->load->view("app/Rider_coding/add_charges", $data);
	}
}
