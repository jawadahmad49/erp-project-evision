<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Slider_config extends CI_Controller
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
		$data['shift_list'] = $this->db->query("select * from tbl_slider")->result_array();
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Slider";
		$this->load->view("app/Slider_config/manage", $data);
	}

	public function add_shifts()
	{
		$data["title"] = "Add Slider";
		$this->load->view("app/Slider_config/add", $data);
	}

	public function add()
	{
		$this->db->trans_start();

		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$status = $this->input->post('status');
			$user_id = $this->session->userdata('id');
			$today = date('Y-m-d');

			$old_image = $this->input->post("old_image");
			$filename = $old_image;

			if ($_FILES['image']['name'] != "") {
				$projects_folder_path = './assets/images/slider/';

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
					redirect(SURL . 'app/Slider_config/');
					return;
				}

				$filename = $new_filename;
			}

			// Prepare data for insertion or update
			$udata['image'] = $filename;
			$udata['status'] = $status;
			$udata['date'] = $today;

			if (!empty($this->input->post('id'))) {
				$edit = $this->input->post('id');
				$res = $this->mod_common->update_table("tbl_slider", array("id" => $edit), $udata);
			} else {
				$res = $this->mod_common->insert_into_table("tbl_slider", $udata);
			}

			$this->db->trans_complete();

			if ($res) {
				$this->session->set_flashdata('ok_message', 'Slider recorded/updated successfully!');
			} else {
				$this->session->set_flashdata('err_message', 'Recording operation failed.');
			}

			redirect(SURL . 'app/Slider_config/');
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

		$table = "tbl_slider";
		$where = "id = '" . $id . "'";
		$delete_area = $this->mod_common->delete_record($table, $where);
		if ($delete_area) {
			$this->session->set_flashdata('ok_message', 'You have successfully deleted.');
			redirect(SURL . 'app/Slider_config/');
		} else {
			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
			redirect(SURL . 'app/Slider_config/');
		}
	}

	public function edit($id)
	{

		$data['id'] = $id;
		if ($id) {
			$login_user = $this->session->userdata('id');
			// echo $id;exit();
			$data['shift_list'] = $this->db->query("select * from tbl_slider")->result_array();
			$data['record'] = $this->db->query("select * from tbl_slider where id='$id'")->row_array();
			$data["title"] = "Edit Slider";

			$this->load->view("app/Slider_config/add", $data);
		}
	}
}
