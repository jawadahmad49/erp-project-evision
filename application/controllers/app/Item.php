<?php

defined('BASEPATH') or exit('No direct script access allowed');



class Item extends CI_Controller
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
					* So any other public methods not prefixed with an underscore will
					* map to /index.php/welcome/<method_name>
					* @see https://codeigniter.com/user_guide/general/urls.html
					*/

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
		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("SELECT location from tbl_admin where id = '$login_user'")->row_array()['location'];

		if ($sale_point_id) {
			$where_location = "where sale_point_id = '$sale_point_id'";
		} else {
			$where_location = "";
		}
		$salepoint = $this->db->query("SELECT * from tbl_sales_point $where_location")->row_array()['sale_point_id'];


		$data['item_list'] = $this->db->query("SELECT tblmaterial_coding.*, tblcategory.catname, tblclass.classname
											FROM tblmaterial_coding
											JOIN tblcategory ON tblmaterial_coding.catcode = tblcategory.id
											JOIN tblclass ON tblmaterial_coding.classcode = tblclass.classcode
											-- Where tblmaterial_coding.sale_point_id = '$salepoint'
											ORDER BY tblmaterial_coding.materialcode DESC")->result_array();

		$data["filter"] = '';

		#----load view----------#

		$data["title"] = "Manage Item";

		$this->load->view("app/item_coding/manage_item", $data);
	}


	public function add_item()
	{

		$login_user = $this->session->userdata('id');
		$role = $this->db->query("SELECT * from tbl_user_rights where uid = '$login_user' and pageid = '1053' limit 1")->row_array();
		if ($role['add'] != 1) {

			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');

			redirect(SURL . 'app/Item/index/');
		}

		$table = 'tblclass';
		$data['class_list'] = $this->mod_common->get_all_records($table, "*");


		$table = 'tblcategory';
		$where = array(
			'classcode' => 1,
			'status' => 'Active'
		);
		$data['category_list'] = $this->mod_common->select_array_records($table, "*", $where);


		$table = 'tblacode';
		$where = array('status' => 'Active');
		$data['brand'] = $this->mod_common->select_array_records('tbl_brand', "*", $where);

		$sale_point_id = $this->db->query("SELECT location from tbl_admin where id = '$login_user'")->row_array()['location'];

		if ($sale_point_id) {
			$where_location = "where sale_point_id = '$sale_point_id'";
		} else {
			$where_location = "";
		}
		$data['salepoint'] = $this->db->query("SELECT * from tbl_sales_point $where_location")->result_array();

		$this->load->view("app/item_coding/add_item", $data);
	}



	public function define_prices($id)
	{

		$table = 'tblmaterial_coding';
		$where = "materialcode='$id'";
		$data['item'] = $this->mod_common->select_single_records($table, $where);


		$table = 'tbl_city';
		$data['cities'] = $this->mod_common->get_all_records($table, "*");
		$data["itemid"] = $id;


		if (isset($_POST['submit'])) {

			$itemname = $_POST["itemid"];
			$location = $_POST["location"];
			$date = $_POST["date"];
			$udata["sale_point_id"] = $_POST["sale_point_id"];
			$city = $_POST["city"];
			$area = $_POST["area"];
			$udata['itemcode'] = $_POST["itemid"];
			$udata['sale_price'] = $_POST["price"];
			$udata['effective_date'] = $_POST['date'];
			$udata['created_by'] = $this->session->userdata('id');
			$udata['created_dt'] = date('Y-m-d');


			if ($_POST['city'] == "All") {

				$getCitiesofRegion = $this->db->get("tbl_city")->result_array();

				foreach ($getCitiesofRegion as $key => $value) {

					$udata['city'] = $value['city_id'];

					$getAreasofCity = $this->db->get_where("tbl_area", array('city_id' => $value['city_id']))->result_array();

					$total_areas = count($getAreasofCity);

					if ($total_areas != 0) {

						foreach ($getAreasofCity as $k => $val) {

							$udata['area'] = $val['area_id'];
							$res = $this->db->insert("tbl_price", $udata);
						}
					} else {

						$udata['area'] = "";

						$res = $this->db->insert("tbl_price", $udata);
					}
				}
			} elseif ($_POST['city'] !== "All") {



				if ($_POST['area'] !== "All") {

					$udata['city'] = $city;

					$udata['area'] = $area;

					$res = $this->db->insert("tbl_price", $udata);
				} else {

					$udata['city'] = $city;

					$getAreasofCity = $this->db->get_where("tbl_area", array('city_id' => $city))->result_array();



					foreach ($getAreasofCity as $k => $val) {

						$udata['area'] = $val['area_id'];


						$res = $this->db->insert("tbl_price", $udata);
					}
				}
			}

			if ($res) {

				$this->session->set_flashdata('ok_message', 'You have successfully added.');

				redirect(SURL . 'app/item/define_prices/' . $itemname);
			} else {

				$this->session->set_flashdata('err_message', 'Adding Operation Failed.');

				redirect(SURL . 'app/item/define_prices/' . $itemname);
			}
		}



		$login_user = $this->session->userdata('id');

		$sale_point_id = $this->db->query("SELECT location from tbl_admin where id='$login_user'")->row_array()['location'];

		$fix_code = $this->db->query("SELECT * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();

		$data['sale_point_id'] = $sale_point_id = $fix_code['sale_point_id'];



		if ($sale_point_id != '') {

			$where_sale_point_id = "and sale_point_id='$sale_point_id'  ";
		} else {

			$where_sale_point_id = "";
		}

		$data['location'] = $this->db->query("SELECT * from tbl_sales_point where sale_point_id in (SELECT sale_point_id from tbl_code_mapping) $where_sale_point_id")->result_array();

		$this->load->view("app/item_coding/add_prices", $data);
	}



	public function edit_price($id)
	{



		$table = 'tbl_price';

		$where = "trans_id='$id'";

		$data["item_p"] = $this->mod_common->select_single_records($table, $where);



		$itemId = $data["item_p"]['itemcode'];

		$table = 'tblmaterial_coding';

		$where = "materialcode='$itemId'";

		$data['item'] = $this->mod_common->select_single_records($table, $where);



		$table = 'tbl_city';

		$data['cities'] = $this->mod_common->get_all_records($table, "*");



		if (isset($_POST['submit'])) {

			$id = $id;

			$itemname = $itemId;

			$location = $_POST["location"];

			$date = $_POST["date"];

			$udata["sale_point_id"] = $_POST["sale_point_id"];

			$udata["city"] = $_POST["city"];

			$udata['area'] = $_POST['area'];

			$udata["itemcode"] = $_POST["sp_id"];

			$udata["effective_date"] = $_POST["date"];

			$udata["sale_price"] = $_POST["price"];

			$udata["modify_dt"] = date("Y-m-d");

			$udata["modify_by"] = $this->session->userdata("id");



			$where = array("trans_id" => $id, "itemcode" => $itemId);



			$table = 'tbl_price';

			$add = $this->mod_common->update_table($table, $where, $udata);



			if ($add) {

				$this->session->set_flashdata('ok_message', 'You have successfully Update the record.');

				redirect(SURL . 'app/item/define_prices/' . $udata["itemcode"]);
			} else {

				$this->session->set_flashdata('err_message', 'Adding Operation Failed.');

				redirect(SURL . 'app/item/define_prices/' . $udata["itemcode"]);
			}
		}


		$this->load->view("app/item_coding/edit_price", $data);
	}


	public function price_list($id, $itemid)
	{



		$table = 'tblmaterial_coding';

		$where = "materialcode='$itemid'";

		$data['item'] = $this->mod_common->select_single_records($table, $where);



		$table = 'tbl_city';

		$where = "city_id='$id'";

		$data['city'] = $this->mod_common->select_single_records($table, $where);



		$data['price_list'] = $this->mod_item->get_prices_item($id, $itemid);



		$this->load->view("app/item_coding/manage_prices", $data);
	}


	public function delete_price($id)
	{


		$getItem = $this->db->get_where("tbl_price", array('trans_id' => $id))->row();



		$table = "tbl_price";

		$where = "trans_id = '" . $id . "'";

		$delete_price = $this->mod_common->delete_record($table, $where);



		if ($delete_price) {

			$this->session->set_flashdata('ok_message', 'You have succesfully deleted.');

			redirect(SURL . 'app/item/define_prices/' . $getItem->itemcode);
		} else {

			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');

			redirect(SURL . 'app/item/define_prices/' . $getItem->itemcode);
		}
	}



	function get_category()
	{

		$table = 'tblcategory';

		$classcode = $this->input->post('class_id');

		$where = array('classcode' => $classcode);

		$data['category_list'] = $this->mod_common->select_array_records($table, "*", $where);



		foreach ($data['category_list'] as $key => $value) { ?>

			<option value="<?php echo $value['id']; ?>"><?php echo $value['catname']; ?></option>


			<?php
		}
	}



	// public function add()
	// {
	// 	$udata['classcode'] = $this->input->post('clas');
	// 	$udata['catcode'] = $this->input->post('category');
	// 	$udata['itemname'] = trim($this->input->post('itemname'));
	// 	$udata['sale_point_id'] = trim($this->input->post('salepoint'));
	// 	$itemname_new = trim($this->input->post('itemname'));
	// 	$udata['status'] = $this->input->post('status');
	// 	if ($this->input->post('category') == 1 || $this->input->post('category') == 7) {
	// 		$udata['brandname'] = $this->input->post('brandname');
	// 		// $udata['security_price'] = $this->input->post('sprice');
	// 		$table = 'tbl_brand';
	// 		$where = array('brand_id' => $udata['brandname']);
	// 		$data['brand_new_name'] = $this->mod_common->select_array_records($table, "brand_name", $where);
	// 		$udata['itemname'] = $this->input->post('itemname') . '-' . $data['brand_new_name'][0]['brand_name'];
	// 		$where = array('catcode' => $this->input->post('category'), 'brandname' => $this->input->post('brandname'), 'itemname' => $udata['itemname']);
	// 		$udata['itemnameint'] = $this->input->post('itemnameint');
	// 	} else {
	// 		$udata['brandname'] = '';
	// 		$udata['security_price'] = '';
	// 		$where = array('catcode' => $this->input->post('category'), 'itemname' => $udata['itemname']);
	// 	}
	// 	$data['item_name'] = $this->mod_common->select_array_records('tblmaterial_coding', "*", $where);
	// 	if (!empty($data['item_name'])) {
	// 		$this->session->set_flashdata('err_message', 'Name Already Exist.');
	// 		redirect(SURL . 'app/Item/add_item');
	// 		exit();
	// 	}
	// 	$filename = "";
	// 	if ($_FILES['company_image']['name'] != "") {
	// 		$projects_folder_path = './assets/images/items/';
	// 		$orignal_file_name = $_FILES['company_image']['name'];
	// 		$file_ext = ltrim(strtolower(strrchr($_FILES['company_image']['name'], '.')), '.');
	// 		$rand_num = rand(1, 1000);
	// 		$config['upload_path'] = $projects_folder_path;
	// 		$config['allowed_types'] = 'jpg|jpeg|gif|tiff|tif|png';
	// 		$config['overwrite'] = false;
	// 		$config['encrypt_name'] = TRUE;
	// 		//$config['file_name'] = $file_name;
	// 		$this->load->library('upload', $config);
	// 		$this->upload->initialize($config);
	// 		if (!$this->upload->do_upload('company_image')) {
	// 			$error_file_arr = array('error' => $this->upload->display_errors());
	// 			//print_r($error_file_arr); exit;
	// 			//return $error_file_arr;
	// 		} else {
	// 			$data_image_upload = array('upload_image_data' => $this->upload->data());
	// 			$filename = $data_image_upload['upload_image_data']['file_name'];
	// 			$full_path = $data_image_upload['upload_image_data']['full_path'];
	// 		}
	// 	}
	// 	$udata['image_path'] = $filename;
	// 	$table = 'tblmaterial_coding';
	// 	$res = $this->mod_common->insert_into_table($table, $udata);
	// 	$materialcode = $this->db->insert_id();
	// 	$price_data['change_by'] = $this->session->userdata('id');
	// 	$price_data['change_date'] = date('Y-m-d H:i:s');
	// 	//$price_data['saleprice'] = $this->input->post('saleprice');
	// 	$price_data['materialcode'] = $materialcode;
	// 	$table = 'tbl_pricing_log';
	// 	$res = $this->mod_common->insert_into_table($table, $price_data);
	// 	if ($res) {
	// 		$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	// 		redirect(SURL . 'app/Item/');
	// 	} else {
	// 		$this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	// 		redirect(SURL . 'app/Item/');
	// 	}
	// }
	public function add()
	{
		$this->db->trans_start();
		$udata['classcode'] = $this->input->post('clas');
		$udata['catcode'] = $this->input->post('category');
		$udata['itemname'] = trim($this->input->post('itemname'));
		$udata['sale_point_id'] = trim($this->input->post('salepoint'));
		$udata['status'] = $this->input->post('status');

		if ($this->input->post('category') == 1 || $this->input->post('category') == 7) {
			$udata['brandname'] = $this->input->post('brandname');

			$table = 'tbl_brand';
			$where = array('brand_id' => $udata['brandname']);
			$data['brand_new_name'] = $this->mod_common->select_array_records($table, "brand_name", $where);

			$udata['itemname'] = $this->input->post('itemname') . '-' . $data['brand_new_name'][0]['brand_name'];
			$where = array('catcode' => $this->input->post('category'), 'brandname' => $this->input->post('brandname'), 'itemname' => $udata['itemname']);
			$udata['itemnameint'] = $this->input->post('itemnameint');
		} else {
			$udata['brandname'] = '';
			$udata['security_price'] = '';
			$where = array('catcode' => $this->input->post('category'), 'itemname' => $udata['itemname']);
		}

		$data['item_name'] = $this->mod_common->select_array_records('tblmaterial_coding', "*", $where);
		if (!empty($data['item_name'])) {
			$this->session->set_flashdata('err_message', 'Name Already Exist.');
			redirect(SURL . 'app/Item/add_item');
			exit();
		}

		$filename = "";
		$old_image = $this->input->post('old_image'); // Assume old image name is posted
		$projects_folder_path = './assets/images/items/';

		if ($_FILES['company_image']['name'] != "") {
			if (!empty($old_image) && file_exists($projects_folder_path . $old_image)) {
				unlink($projects_folder_path . $old_image);
			}

			$original_file_name = $_FILES['company_image']['name'];
			$file_tmp = $_FILES['company_image']['tmp_name'];
			$file_ext = ltrim(strtolower(strrchr($original_file_name, '.')), '.');

			$new_filename = uniqid('img_', true) . '.' . $file_ext;
			$full_path = $projects_folder_path . $new_filename;

			$compression_result = $this->compress_image($file_tmp, $full_path, 70); // 70% quality

			if (!$compression_result) {
				$this->session->set_flashdata('err_message', 'Image compression failed.');
				redirect(SURL . 'app/Item/add_item');
				return;
			}

			$filename = $new_filename;
		}

		$udata['image_path'] = $filename;

		$table = 'tblmaterial_coding';
		$res = $this->mod_common->insert_into_table($table, $udata);
		$materialcode = $this->db->insert_id();

		$price_data['change_by'] = $this->session->userdata('id');
		$price_data['change_date'] = date('Y-m-d H:i:s');
		$price_data['materialcode'] = $materialcode;

		$table = 'tbl_pricing_log';
		$res = $this->mod_common->insert_into_table($table, $price_data);

		$this->db->trans_complete();

		if ($res) {
			$this->session->set_flashdata('ok_message', 'You have successfully added.');
		} else {
			$this->session->set_flashdata('err_message', 'Adding operation failed.');
		}

		redirect(SURL . 'app/Item/');
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

		$role = $this->db->query("SELECT * from tbl_user_rights where uid = '$login_user' and pageid = '1053' limit 1")->row_array();

		if ($role['delete'] != 1) {

			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');

			redirect(SURL . 'app/Item/index/');
		}





		if ($this->mod_item->get_issue($id)) {

			$this->session->set_flashdata('err_message', 'You can not delete it.');

			redirect(SURL . 'app/Item/');

			exit();
		} elseif ($this->mod_item->get_sold($id)) {

			$this->session->set_flashdata('err_message', 'You can not delete it.');

			redirect(SURL . 'app/Item/');

			exit();
		} elseif ($this->mod_item->get_shopopening($id)) {

			$this->session->set_flashdata('err_message', 'You can not delete it.');

			redirect(SURL . 'app/Item/');

			exit();
		}

		#-------------delete record--------------#

		$table = "tblmaterial_coding";

		$where = "materialcode = '" . $id . "'";

		$delete_area = $this->mod_common->delete_record($table, $where);



		if ($delete_area) {

			$this->session->set_flashdata('ok_message', 'You have succesfully deleted.');

			redirect(SURL . 'app/Item/');
		} else {

			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');

			redirect(SURL . 'app/Item/');
		}
	}



	public function edit($id)
	{

		$login_user = $this->session->userdata('id');

		$role = $this->db->query("SELECT * from tbl_user_rights where uid = '$login_user' and pageid = '1053' limit 1")->row_array();

		if ($role['edit'] != 1) {

			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');

			redirect(SURL . 'app/Item/index/');
		}

		$table = 'tblmaterial_coding';

		$where = "materialcode='$id'";

		$data['item'] = $this->mod_common->select_single_records($table, $where);

		$table = 'tblclass';

		$data['class_list'] = $this->mod_common->get_all_records($table, "*");

		$table = 'tblcategory';
		$where = array(
			'classcode' => 1,
			'status' => 'Active'
		);
		$data['category_list'] = $this->mod_common->select_array_records($table, "*", $where);

		//pme($data['country']);

		$table = 'tblacode';

		//$classcode=	$this->input->post('class_id');

		$where = array('status' => 'Active');

		$data['brand'] = $this->mod_common->select_array_records('tbl_brand', "*", $where);



		$this->load->view("app/item_coding/edit", $data);
	}



	// public function update()
	// {
	// 	$udata['classcode'] = $this->input->post('clas');
	// 	$udata['catcode'] = $this->input->post('category');
	// 	$udata['itemname'] = trim($this->input->post('itemname'));
	// 	$udata['status'] = $this->input->post('status');
	// 	//$udata['status'] = $this->input->post('status');
	// 	if ($this->input->post('category') == 1 || $this->input->post('category') == 7) {
	// 		$udata['brandname'] = $this->input->post('brandname');
	// 		// $udata['security_price'] = $this->input->post('sprice');
	// 		$table = 'tbl_brand';
	// 		$where = array('brand_id' => $udata['brandname']);
	// 		$data['brand_new_name'] = $this->mod_common->select_array_records($table, "brand_name", $where);
	// 		$udata['itemname'] = $this->input->post('itemname') . '-' . $data['brand_new_name'][0]['brand_name'];
	// 		$where = array('catcode' => $this->input->post('category'), 'brandname' => $this->input->post('brandname'), 'itemname' => $udata['itemname']);
	// 		$udata['itemnameint'] = $this->input->post('itemnameint');
	// 	} else {
	// 		$udata['brandname'] = '';
	// 		$udata['security_price'] = '';
	// 		$where = array('catcode' => $this->input->post('category'), 'itemname' => $udata['itemname']);
	// 	}
	// 	#----check name already exist---------#
	// 	if ($this->mod_item->edit_by_title($udata['itemname'], $_POST['id'])) {
	// 		$this->session->set_flashdata('err_message', 'Name Already Exist.');
	// 		redirect(SURL . 'app/Item/edit/' . $_POST['id']);
	// 		exit();
	// 	}
	// 	$filename = "";
	// 	if ($_FILES['company_image']['name'] != "") {
	// 		$projects_folder_path = './assets/images/items/';
	// 		$orignal_file_name = $_FILES['company_image']['name'];
	// 		$file_ext = ltrim(strtolower(strrchr($_FILES['company_image']['name'], '.')), '.');
	// 		$rand_num = rand(1, 1000);
	// 		$config['upload_path'] = $projects_folder_path;
	// 		$config['allowed_types'] = 'jpg|jpeg|gif|tiff|tif|png';
	// 		$config['overwrite'] = false;
	// 		$config['encrypt_name'] = TRUE;
	// 		//$config['file_name'] = $file_name;
	// 		$this->load->library('upload', $config);
	// 		$this->upload->initialize($config);
	// 		if (!$this->upload->do_upload('company_image')) {
	// 			$error_file_arr = array('error' => $this->upload->display_errors());
	// 		} else {
	// 			$data_image_upload = array('upload_image_data' => $this->upload->data());
	// 			$filename = $data_image_upload['upload_image_data']['file_name'];
	// 			$full_path = $data_image_upload['upload_image_data']['full_path'];
	// 			$udata['image_path'] = $filename;
	// 			unlink('./assets/images/items/' . $_POST['old_image']);
	// 		}
	// 	} else {
	// 		$udata['image_path'] = $_POST['old_image'];
	// 	}
	// 	$id = $_POST['id'];
	// 	$where = "materialcode='$id'";
	// 	$table = 'tblmaterial_coding';
	// 	$res = $this->mod_common->update_table($table, $where, $udata);
	// 	////////////////////////////////// FOR LOG ///////////////////////////////
	// 	$price_data['change_by'] = $this->session->userdata('id');
	// 	$price_data['change_date'] = date('Y-m-d H:i:s');
	// 	//$price_data['saleprice'] = $this->input->post('saleprice');
	// 	$price_data['materialcode'] = $id;
	// 	$table = 'tbl_pricing_log';
	// 	$res = $this->mod_common->insert_into_table($table, $price_data);
	// 	///////////////////////////////////////////////////////////////////////////
	// 	if ($res) {
	// 		$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
	// 		redirect(SURL . 'app/Item/');
	// 	} else {
	// 		$this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	// 		redirect(SURL . 'app/Item/');
	// 	}
	// }
	public function update()
	{
		$udata['classcode'] = $this->input->post('clas');
		$udata['catcode'] = $this->input->post('category');
		$udata['itemname'] = trim($this->input->post('itemname'));
		$udata['status'] = $this->input->post('status');

		if ($this->input->post('category') == 1 || $this->input->post('category') == 7) {
			$udata['brandname'] = $this->input->post('brandname');
			$table = 'tbl_brand';
			$where = array('brand_id' => $udata['brandname']);
			$data['brand_new_name'] = $this->mod_common->select_array_records($table, "brand_name", $where);
			$udata['itemname'] = $this->input->post('itemname') . '-' . $data['brand_new_name'][0]['brand_name'];
			$where = array('catcode' => $this->input->post('category'), 'brandname' => $this->input->post('brandname'), 'itemname' => $udata['itemname']);
			$udata['itemnameint'] = $this->input->post('itemnameint');
		} else {
			$udata['brandname'] = '';
			$udata['security_price'] = '';
			$where = array('catcode' => $this->input->post('category'), 'itemname' => $udata['itemname']);
		}

		if ($this->mod_item->edit_by_title($udata['itemname'], $_POST['id'])) {
			$this->session->set_flashdata('err_message', 'Name Already Exist.');
			redirect(SURL . 'app/Item/edit/' . $_POST['id']);
			exit();
		}

		$filename = "";
		if (!empty($_FILES['company_image']['name'])) {
			$projects_folder_path = './assets/images/items/';
			$config['upload_path'] = $projects_folder_path;
			$config['allowed_types'] = 'jpg|jpeg|gif|tiff|tif|png';
			$config['overwrite'] = false;
			$config['encrypt_name'] = TRUE;

			$this->load->library('upload', $config);
			$this->upload->initialize($config);

			if (!$this->upload->do_upload('company_image')) {
				$this->session->set_flashdata('err_message', $this->upload->display_errors());
				redirect(SURL . 'app/Item/edit/' . $_POST['id']);
				exit();
			} else {
				$data_image_upload = $this->upload->data();
				$filename = $data_image_upload['file_name'];
				$udata['image_path'] = $filename;

				if (!empty($_POST['old_image'])) {
					@unlink('./assets/images/items/' . $_POST['old_image']);
				}
			}
		} else {
			$udata['image_path'] = $_POST['old_image'];
		}

		$id = $_POST['id'];
		$where = "materialcode='$id'";
		$table = 'tblmaterial_coding';
		$res = $this->mod_common->update_table($table, $where, $udata);

		$price_data['change_by'] = $this->session->userdata('id');
		$price_data['change_date'] = date('Y-m-d H:i:s');
		$price_data['materialcode'] = $id;
		$table = 'tbl_pricing_log';
		$this->mod_common->insert_into_table($table, $price_data);

		if ($res) {
			$this->session->set_flashdata('ok_message', 'You have successfully updated.');
			redirect(SURL . 'app/Item/');
		} else {
			$this->session->set_flashdata('err_message', 'Update Operation Failed.');
			redirect(SURL . 'app/Item/');
		}
	}

}
