<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sales_forecast_report extends CI_Controller
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
			"mod_cashbookreport", "mod_common", "mod_customer", "mod_salelpg"
		));
	}

	public function index()
	{
		$table = 'tblacode';
		$where = "general='2001001000'";
		$data['customers'] = $this->mod_common->select_array_records($table, '*', $where);
		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
		$cash_code = $fix_code['cash_code'];

		if ($cash_code != '') {
			$where_code = " and tblacode.acode='$cash_code' ";
		} else {
			$where_code = " and tblacode.general='2003013000' ";
		}
		// $data['result1'] = $this->db->query("select * from tblacode where general='2003013000' and atype='Child'")->result_array();
		$data['result1'] = $this->db->query("select * from tblacode where atype='Child' $where_code")->result_array();
		$table = 'tblmaterial_coding';
		$data['items'] = $this->mod_common->get_all_records($table, "*");
		$data["filter"] = '';


		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];


		if ($sale_point_id == '1') {
			$where_codes = "where acode in ('2004001086')";   // pattern=>>(, ' delivery')
		} elseif ($sale_point_id == '2') {
			$where_codes = "where acode in ('2004002008')";   //
		} elseif ($sale_point_id == '3') {
			$where_codes = "where acode in ('2004014133')";   //
		}
		$data['sale_point '] = $sale_point = $this->db->query("select * from tbl_sales_point where sts='Active'")->row_array()['sp_name'];
		//echo $sale_point;exit;
		$data['vendor_list'] = $this->db->query("select * from tblacode $where_codes")->result_array();
		$data['city_list'] = $this->db->query("select city_name,city_id from tbl_city where status='Active'")->result_array();
		$data['area_list'] = $this->db->query("select aname,area_id from tbl_area where status='Active'")->result_array();


		#----load view----------#
		$data["title"] = "Sales Forcast Report";
		$this->load->view($this->session->userdata('language') . "/sales_forecast_report/search", $data);
	}

	public function report()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {

			$data['area'] = $area = $this->input->post('area');
			$data['city'] = $city = $this->input->post('city');
			$data['to_date'] = $to_date = $this->input->post('to_date');
			$data['from_date'] = $from_date = $this->input->post('from_date');
			$data['acode'] = $acode = $this->input->post('acode');
			$data['itemid'] = $itemid = $this->input->post('itemid');
			$data['Date_Posted'] = $Date_Posted = $this->input->post('Date_Posted');
			$data['direct_customer'] = $direct_customer = $this->input->post('direct_customer');
			$data['d_customer'] = $d_customer = $this->input->post('d_customer');
			$data['salepoint'] = $sale_point_id = $this->input->post('salepoint');



			$data['login_user']=$login_user = $this->session->userdata('id');
			// $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

			if ($area != 'All') {
				$where_area = "and area='$area'";
			} else {
				$where_area = "";
			}
			if ($city != 'All') {
				$where_city = "and city='$city'";
			} else {
				$where_city = "";
			}
			if ($sale_point_id != 'All') {
				$where_location = "and sale_point_id='$sale_point_id'";
			} else {
				$where_location = "";
			}
			
			

		
			$delete = $this->db->query("delete from tbl_sales_forecast where userid='$login_user'");
		
			$customers = $this->db->query("select * from tbl_direct_customer where id!='' $where_location $where_city $where_area")->result_array(); // loop in sigle file to get values from direct customer table

			foreach ($customers as $key => $value) {
				$name = $value['name'];
				$d_customer = $value['id'];
				$d_customercell = $value['cell_no'];
				$d_customercity = $value['city'];
				$d_customerarea = $value['area'];
				$estimated_consumption_days = $value['consumption_days'];
				$Sales_Reminder_days = $value['reminder'];


				$issue_data = $this->db->query("SELECT itemid,DATE(Posted_Date) AS posted_date FROM tbl_issue_goods_detail WHERE direct_customer = '$d_customer' order by ig_detail_id DESC")->row_array();

				if (empty($issue_data)) {
					continue;
				}

				$itemid = $issue_data['itemid'];
				$itemname = $this->db->query("SELECT itemname FROM tblmaterial_coding WHERE materialcode = '$itemid'")->row_array()['itemname'];
				$area = $this->db->query("SELECT aname FROM tbl_area WHERE area_id = '$d_customerarea'")->row_array()['aname'];
				$city = $this->db->query("SELECT city_name FROM tbl_city WHERE city_id = '$d_customercity'")->row_array()['city_name'];
				$next_refill_date = date("Y-m-d", strtotime($issue_data['posted_date'] . " + $estimated_consumption_days days"));
				$remider_date = date("Y-m-d", strtotime($next_refill_date . " - $Sales_Reminder_days days"));

				$adata['name'] = $name;
				$adata['cell_no'] = $d_customercell;
				$adata['city'] = $city;
				$adata['area'] = $area;
				$adata['cylinder'] = $itemname;
				$adata['sale_date'] = $issue_data['posted_date'];
				$adata['consumption_days'] = $estimated_consumption_days;
				$adata['next_refil_date'] = $next_refill_date;
				$adata['reminder_days'] = $Sales_Reminder_days;
				$adata['reminder_date'] = $remider_date;
				$adata['userid'] = $login_user;


				$result = $this->mod_common->insert_into_table('tbl_sales_forecast', $adata);
			}


			$table = 'tbl_company';
			$data['company'] = $this->mod_common->get_all_records($table, "*");
			if ($data['report']) {
				$data["title"] = "Sales&nbsp;Forecast";

				$this->load->view($this->session->userdata('language') . "/sales_forecast_report/single", $data);
			} else {
				//$this->session->set_flashdata('err_message', 'No Record Found.');
				//redirect(SURL . 'Customer_outstanding/');
				$data["title"] = "Sales&nbsp;Forecast";
				$this->load->view($this->session->userdata('language') . "/sales_forecast_report/single", $data);
			}
		} else {
			//$data["filter"] = 'add';
			$data["title"] = "Sales&nbsp;Forecast";
			$this->load->view($this->session->userdata('language') . "/sales_forecast_report/single", $data);
		}
	}

	function get_area()
	{

		$edit_area = $this->input->post('edit_area');
		$table = 'tbl_area';
		$city_id =	$this->input->post('city_id');
		$where = array('city_id' => $city_id);

?> <option value="All">All</option><?php
									$data['area_list'] = $this->mod_common->select_array_records($table, "*", $where);

									foreach ($data['area_list'] as $key => $value) {
									?>
			<option value="<?php echo  $value['area_id']; ?>" <?php if ($edit_area == $value['area_id']) {
																	echo "selected";
																} ?>><?php echo  $value['aname']; ?></option>

		<?php }
								}


								function get_sale_point()
								{
									$login_user = $this->session->userdata('id');
									$sale_Point = $this->input->post('sale_Point');
									$table = 'tbl_sales_point';
									
									if ($login_user == 1) {
										$where = '';
										$all='<option value="All">All</option>';
										
									} else {
										$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
										$where = "sale_point_id='$sale_point_id'";
										$all='';
									}

											$sale_point_detail = $this->mod_common->select_array_records($table, "*", $where);
											echo $all;
											foreach ($sale_point_detail as $key => $value) {
											?>
			<option value="<?php echo  $value['sale_point_id']; ?>" <?php if ($sale_Point == $value['sale_point_id']) {
																		echo "selected";
																	} ?>><?php echo  $value['sp_name']; ?></option>

		<?php }
										}







										function get_Direct_Customer()
										{
											$customer = $this->input->post('customer');
											$login_user = $this->session->userdata('id');
											$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		?><option value="All">All</option><?php
											if ($sale_point_id == '1') {
												if ($customer == '2004001030') {
													$type = 'walkin';
												}
												if ($customer == '2004001086') {
													$type = 'home';
												}
											} elseif ($sale_point_id == '2') {
												//$customer = "where acode in ('2004002001','2004002008')"; 

												if ($customer == '2004002001') {
													$type = 'walkin';
												}
												if ($customer == '2004002008') {
													$type = 'home';
												}
											} elseif ($sale_point_id == '3') {
												//	$customer = "where acode in ('2004014001','2004014133')";

												if ($customer == '2004014001') {
													$type = 'walkin';
												}
												if ($customer == '2004014133') {
													$type = 'home';
												}
											}

											$direct_customer_detail = $this->db->query("select id,name,cell_no from tbl_direct_customer where sale_point_id='$sale_point_id' and type= '$type' ")->result_array();

											foreach ($direct_customer_detail as $key => $data) {

											?>
			<option value="<?php echo $data['id']; ?>"><?php echo ucwords($data['cell_no'] . " " . $data['name']); ?></option>

<?php }
										}
									}
