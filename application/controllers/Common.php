<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Common extends CI_Controller
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
		error_reporting(0);

		$this->load->model(array(
			"mod_common"
		));

	}
	public function export_to_xls()
	{
		$out = '';
		if (isset($_POST['csv_hdr'])) {
			$out .= $_POST['csv_hdr'] . "\n";
		}
		if (isset($_POST['csv_output'])) {
			$out .= $_POST['csv_output'];
		}
		$filename = "Dispatched_Orders_Report_" . date("Y-m-d_H-i", time()) . ".xls";
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=" . $filename);
		echo $out;
		exit;
	}

	public function damagestock()
	{
		echo 1;

	}

	public function stock()
	{ //pm($_POST);
		$id = $this->input->post('item_id');
		$date = $this->input->post('date');
		$date = date('Y-m-d', strtotime("+1 day", strtotime($date)));

		$cate_id = 5;

		$where_item = "materialcode = '" . $id . "'";

		$item_value = $this->mod_common->select_single_records('tblmaterial_coding', $where_item);

		if (!empty($item_value)) {
			$cate_id = $item_value['catcode'];
		}


		if (($_POST['condition'] == "Damage") && ($_POST['type'] == "Empty")) {
			$login_user = $this->session->userdata('id');
			$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
			////////////////////////////// Empty CYLINDER Fresh to damage /////////////////////////////////
			$query = "SELECT sum(qty) as damagecylinder_e from tbl_exchange_condition where from_itemcode='$id' and cyl_condition_to='Damage' and cyl_type='Empty'";
			$result = $this->db->query($query);
			$convert_to_f_row3 = $result->row_array();
			$damagecylinder_e = $convert_to_f_row3['damagecylinder_e'];

			////////////////////////////// Empty CYLINDER Damage to Fresh /////////////////////////////////
			$query = "SELECT sum(qty) as freshcylinder_e from tbl_exchange_condition where from_itemcode='$id' and cyl_condition_to='Fresh' and cyl_type='Empty'";
			$result = $this->db->query($query);
			$convert_to_f_row4 = $result->row_array();
			$freshcylinder_e = $convert_to_f_row4['freshcylinder_e'];



			////////////////////////////// sale damage cylinder  /////////////////////////////////
			$query = "select sum(qty) as saledamagecylinder from tbl_issue_goods_detail where itemid='$id' and salestatus='Damage' and sale_point_id='$sale_point_id'";
			$result = $this->db->query($query);
			$saledamagecylinderquery = $result->row_array();
			$damagecylindersale = $saledamagecylinderquery['saledamagecylinder'];


			echo $newvalue = "123_" . ($damagecylinder_e - $freshcylinder_e - $damagecylindersale);


		} else if (($_POST['condition'] == "Damage") && ($_POST['type'] == "Filled")) {

			////////////////////////////// Filled CYLINDER Fresh to damage /////////////////////////////////
			$query = "SELECT sum(qty) as damagecylinder_f from tbl_exchange_condition where from_itemcode='$id' and cyl_condition_to='Damage' and cyl_type='Filled'";
			$result = $this->db->query($query);
			$convert_to_f_row1 = $result->row_array();


			////////////////////////////// Filled CYLINDER Damage to Fresh /////////////////////////////////
			$query = "SELECT sum(qty) as freshcylinder_f from tbl_exchange_condition where from_itemcode='$id' and cyl_condition_to='Fresh' and cyl_type='Filled'";
			$result = $this->db->query($query);
			$convert_to_f_row2 = $result->row_array();


			echo $newvalue = ($convert_to_f_row1['damagecylinder_f'] - $convert_to_f_row2['freshcylinder_f']) . "_123";

			//echo $newvalue = ($damagecylinder_e  -  $freshcylinder_e)."_123";


		} else {
			if ($cate_id == '1') {
				echo $today_stock = $this->mod_common->stock($id, 'empty', $date, 1) . '_' . $item_value['itemnameint'] . '_' . $item_value['catcode'];
				//echo $today_stock;exit;
			} else if ($cate_id == '7') {
				echo $today_stock = $this->mod_common->other_cylinder_stock($id, 'empty', $date, 1) . '_' . $item_value['itemnameint'] . '_' . $item_value['catcode'];
			} else {
				echo $today_stock = $this->mod_common->other_stock($id, 'empty', $date, 1) . '_' . $item_value['itemnameint'] . '_' . $item_value['catcode'];
			}
		}
		exit;



	}
	public function similaritem()
	{
		$id = $this->input->post('item_id');
		$date = $this->input->post('date');
		$itemnameint = '';
		$catcode = '';

		$where_item = "materialcode = '" . $id . "'";

		$item_value = $this->mod_common->select_single_records('tblmaterial_coding', $where_item);

		if (!empty($item_value)) {
			$itemnameint = $item_value['itemnameint'];
			$catcode = $item_value['catcode'];
		}


		$where_cat_id = array('itemnameint' => $itemnameint);
		$data['item_list'] = $this->mod_common->select_array_records('tblmaterial_coding', "*", $where_cat_id);

		print $catcode . '_';

		foreach ($data['item_list'] as $key => $value) { ?>
			<option value="<?php echo $value['materialcode']; ?>" <?php if ($id == $value['materialcode']) { ?> selected <?php } ?>><?php echo $value['itemname']; ?></option>
		<?php }
		exit;


	}
	public function export()
	{

		$out = '';
		$file = "";
		//Next we'll check to see if our variables posted and if they did we'll simply append them to out.
		if (isset($_POST['csv_hdr'])) {
			$out .= $_POST['csv_hdr'];
			$out .= "\n";
		}

		if (isset($_POST['csv_output'])) {
			$out .= $_POST['csv_output'];
		}

		//Now we're ready to create a file. This method generates a filename based on the current date & time.
		$filename = $file . "_" . date("Y-m-d_H-i", time());

		//Generate the CSV file header
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header("Content-disposition: filename=" . $filename . ".csv");
		//Print the contents of out to the generated file.
		print $out;

		//Exit the script
		exit;


	}
}