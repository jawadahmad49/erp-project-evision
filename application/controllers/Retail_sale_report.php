<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Retail_sale_report extends CI_Controller
{



	public function __construct()
	{
		parent::__construct();

		$this->load->model(array(
			"mod_common", "mod_salelpg"
		));
	}

	public function index()
	{

		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
		$data['sale_point_id'] = $sale_point_id = $fix_code['sale_point_id'];

		if ($sale_point_id != '') {
			$where_sale_point_id = "where sale_point_id='$sale_point_id'  ";
		} else {
			$where_sale_point_id = "";
		}
		$fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
		$customer_code = $fix_code['customer_code'];
		if ($customer_code != '') {
			$where_customer = " and tblacode.general='$customer_code'  ";
		} else {
			$where_customer = "and tblacode.general in('2004001000','2004002000')";
		}

	
		$data['supplier_list'] = $this->db->query("select * from tblacode where atype='Child' $where_customer")->result_array();
		//$data['supplier_list'] = $this->db->query("select * from tblacode where  atype='child'")->result_array();
		$data['location'] = $this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();
		$data['item'] = $this->db->query("select * from tblmaterial_coding where status='Active'")->result_array();
		//$data['plant_list'] = $this->db->query("select * from tbl_tank where type='Own'")->result_array();
		$table = 'tbl_city';
		$data['city_list'] = $this->mod_common->get_all_records($table, "*");
		#----load view----------#
		$data["title"] = "Retail Sale Register";
		$this->load->view($this->session->userdata('language') . "/Retail_sale_report/search", $data);
	}

	public function report()
	{
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$from_date = $data['from_date'] = $this->input->post("from_date");
			$to_date = $data['to_date'] = $this->input->post("to_date");
			$data['supplier'] = $supplier = $this->input->post("supplier");
			$data['itemid'] = $itemid = $this->input->post("item");
			$data['type'] = $type = $this->input->post("type");
			$data['sale_point_id'] = $sale_point_id = $this->input->post('location');

			$data['direct_customer'] = $direct_customer = $this->input->post('direct_supplier');
			$data['city'] = $city = $this->input->post("city");
			$data['area'] = $area = $this->input->post("area");
			$plant = $data['plant'] = $this->input->post("plant");


			if ($supplier != 'All') {
				$data['customer_name'] = $this->db->query("select aname from tblacode where acode='$supplier'")->row_array()['aname'];

				$where_supplier = " and tbl_issue_goods.issuedto='$supplier'  ";
			} else {
				$where_supplier = "";
			}

			$segment = $this->db->query("select segment from tblacode where acode='$supplier'")->row_array()['segment'];
			if ($segment == 'walkin' || $segment == 'home') {
				if ($city != 'All') {
					$where_city = "and tbl_direct_customer.city='$city'";
					$direct_customer_join = "INNER JOIN tbl_direct_customer ON tbl_direct_customer.id =tbl_issue_goods.direct_customer ";
				} else {
					$where_city = "";
				}
				if ($area != 'All') {
					$where_area = "and tbl_direct_customer.area='$area'";
				} else {
					$where_area = "";
				}
			} else {
				if ($city != 'All') {
					$where_city = "and tblacode.city_id='$city'";
				} else {
					$where_city = "";
				}
				if ($area != 'All') {
					$where_area = "and tblacode.area_id='$area'";
				} else {
					$where_area = "";
				}
			}

			if ($direct_customer != '') {
				$where_direct_customer = " and tbl_issue_goods.direct_customer='$direct_customer'  ";
			} else {
				$where_direct_customer = "";
			}
			if ($itemid != 'All') {
				$where_itemid = " and tbl_issue_goods_detail.itemid='$itemid'  ";
			} else {
				$where_itemid = "";
			}
			if ($type != 'All') {
				if ($type == 'Empty') {
					$where_type = " and tbl_issue_goods_detail.type IN('Empty','sale')";
				}
				if ($type == 'Filled') {
					$where_type = " and tbl_issue_goods_detail.type NOT IN('Empty','sale')";
				}
			} else {
				$where_type = "";
			}

			// if($plant !='All'){ $where_plant = " and tbl_issue_goods.tank_id='$plant' "; }else{ $where_plant =""; }
			$data['where_type'] = $where_type;
			$data['where_itemid'] = $where_itemid;

			//$data['direct_customer'] = $direct_customer;

			$data['where_direct_customer'] = $where_direct_customer;
			

			$data['report'] = $this->db->query("SELECT tbl_issue_goods.*,tblacode.aname as customer FROM `tbl_issue_goods` inner join tblacode on tblacode.acode=tbl_issue_goods.issuedto inner join tbl_issue_goods_detail on tbl_issue_goods_detail.ig_detail_id=tbl_issue_goods.issuenos $direct_customer_join where  tbl_issue_goods.issuedate Between '$from_date' and '$to_date'  and tbl_issue_goods.sale_point_id='$sale_point_id' $where_direct_customer $where_supplier $where_itemid $where_type $where_area $where_city group by tbl_issue_goods.issuenos order by tbl_issue_goods.issuenos asc")->result_array();


			// echo "SELECT tbl_issue_goods.*,tblacode.aname as customer FROM `tbl_issue_goods` inner join tblacode on tblacode.acode=tbl_issue_goods.issuedto inner join tbl_issue_goods_detail on tbl_issue_goods_detail.ig_detail_id=tbl_issue_goods.issuenos where  tbl_issue_goods.issuedate Between '$from_date' and '$to_date' and tbl_issue_goods.sale_point_id='$sale_point_id'  $where_supplier $where_itemid $where_type  order by tbl_issue_goods.issuenos asc";exit;
			$data['cyl_list'] = $this->db->query("select * from tblmaterial_coding where catcode='1'")->result_array();
			$data['apl_list'] = $this->db->query("select * from tblmaterial_coding where catcode!='1'")->result_array();
			//pm($data['cyl_list']);
			$table = 'tbl_company';
			$data['company'] = $this->mod_common->get_all_records($table, "*");

			if ($data['report']) {
				$data["title"] = "Retail&nbsp;Sale&nbsp;Register";
				$this->load->view($this->session->userdata('language') . "/Retail_sale_report/single", $data);
			} else {
				$this->session->set_flashdata('err_message', 'No Record Found.');
				redirect(SURL . 'Retail_sale_report/');
			}
		};
	}

	// for direct customers to show in select in report 

	public function get_type()                   // get type
	{

		$direct_customer = $this->input->post('direct_customer');

		$record = $this->db->query("select * from tblacode where acode='$direct_customer'")->row_array();
		echo json_encode($record);
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
										public function get_Direct_Customer()                // get_Direct_Customer from ajax
										{
											$customer = $this->input->post('customer');
											$customer = $this->input->post('customer');
											//$salepoint_id = $this->input->post('salepoint_id');
											$login_user = $this->session->userdata('id');
											$salepoint_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

											$segment = $this->db->query("select segment from tblacode where acode='$customer'")->row_array()['segment'];
											if ($segment == 'walkin' || $segment == 'home') {
		?>
			<option value="">All</option>
			<?php
												$direct_customer_detail = $this->db->query("select id,name,cell_no from tbl_direct_customer where sale_point_id='$salepoint_id' and type= '$segment' ")->result_array();
												foreach ($direct_customer_detail as $key => $data) {
			?>
				<option value="<?php echo $data['id']; ?>"><?php echo ucwords($data['cell_no'] . " " . $data['name']); ?></option>

<?php }
											}
										}
									}
