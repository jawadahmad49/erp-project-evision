
<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Promocodes extends CI_Controller
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
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	public function __construct()
	{
		parent::__construct();

		$this->load->model(array(
			"mod_promocode", "mod_common"
		));
	}

	public function index()
	{
		$data['promo_list'] = $this->mod_promocode->manage_cities();
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Promo codes";
		$this->load->view("app/Promocodes/manage_promo", $data);
	}
	public function add_promo()
	{
		// $table='tbl_country';       
		// $data['country_list'] = $this->mod_common->get_all_records($table,"*");    			
		$this->load->view("app/Promocodes/add_promo");
	}

	public function add()
	{

		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$udata['promo_code'] = $this->input->post('pcode');
			$udata['discount_type'] = trim($this->input->post('category'));
			$udata['start_date'] = trim($this->input->post('start_date'));
			$udata['expiry_date'] = trim($this->input->post('end_date'));
			$udata['sts'] = trim($this->input->post('status'));
			$udata['discount_percentage'] = $this->input->post('brandname');
			$udata['discount_amount'] = $this->input->post('amount');

			#----check name already exist---------#
			if ($this->mod_promocode->get_by_title($udata['promo_code'])) {
				$this->session->set_flashdata('err_message', 'Promocodes Already Exist.');
				redirect(SURL . 'app/Promocodes/add_promo');
				exit();
			}


			$table = 'tbl_promo_code';
			$res = $this->mod_common->insert_into_table($table, $udata);

			if ($res) {
				$this->session->set_flashdata('ok_message', 'You have succesfully added.');
				redirect(SURL . 'app/Promocodes/');
			} else {
				$this->session->set_flashdata('err_message', 'Adding Operation Failed.');
				redirect(SURL . 'app/Promocodes/');
			}
		}
	}

	public function edit($id)
	{
		if ($id) {
			$table = 'tbl_promo_code';
			$where = "transid='$id'";
			$data['promo'] = $this->mod_common->select_single_records($table, $where);

			//pme($data['country']);

			$this->load->view("app/Promocodes/edit", $data);
		}
	}

	public function update()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$udata['promo_code'] = $this->input->post('pcode');
			$udata['discount_type'] = trim($this->input->post('category'));
			$udata['start_date'] = trim($this->input->post('start_date'));
			$udata['expiry_date'] = trim($this->input->post('end_date'));
			$udata['sts'] = trim($this->input->post('status'));
			$udata['discount_percentage'] = $this->input->post('brandname');
			$udata['discount_amount'] = $this->input->post('amount');
			$id = $_POST['id'];

			#----check name already exist---------#
			if ($this->mod_promocode->edit_by_title($udata['promo_code'], $id)) {
				$this->session->set_flashdata('err_message', 'promo code Already Exist.');
				redirect(SURL . 'app/Promocodes/edit/' . $id);
				exit();
			}


			$where = "transid='$id'";
			$table = 'tbl_promo_code';
			$res = $this->mod_common->update_table($table, $where, $udata);

			if ($res) {
				$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
				redirect(SURL . 'app/Promocodes/');
			} else {
				$this->session->set_flashdata('err_message', 'Adding Operation Failed.');
				redirect(SURL . 'app/Promocodes/');
			}
		}
	}

	public function delete($promo)
	{

		// if ($this->mod_promocode->under_area($promo)) {
		// 	$this->session->set_flashdata('err_message', 'This Promocodes is Used by order you can not delete it.');
		// 	redirect(SURL . 'app/Promocodes/');
		// 	exit();
		// }


		#-------------delete record--------------#
		$table = "tbl_promo_code";
		$where = "promo_code = '" . $promo . "'";
		$delete_country = $this->mod_common->delete_record($table, $where);


		if ($delete_country) {
			$this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
			redirect(SURL . 'app/Promocodes/');
		} else {
			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
			redirect(SURL . 'app/Promocodes/');
		}
	}
}
