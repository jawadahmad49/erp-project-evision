<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Direct_customer_ledger extends CI_Controller
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
			"mod_vendorledger", "mod_common", "mod_vendor", "mod_salelpg"
		));
	}

	public function index()
	{

		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];


		if ($sale_point_id == '1') {
			$where_codes = "where acode in ('2004001030','2004001086')";   // pattern=>>('walkin', 'home')
		} elseif ($sale_point_id == '2') {
			$where_codes = "where acode in ('2004002001','2004002008')";   //
		} elseif ($sale_point_id == '3') {
			$where_codes = "where acode in ('2004014001','2004014133')";   //
		} else {
			$where_codes = "";
		}

		if ($sale_point_id == '1') {
			if ($where_codes == '2004001030') {
				$type = 'walkin';
			}
			if ($where_codes == '2004001086') {
				$type = 'home';
			}
		} elseif ($sale_point_id == '2') {
			//$where_codes = "where acode in ('2004002001','2004002008')";

			if ($where_codes == '2004002001') {
				$type = 'walkin';
			}
			if ($where_codes == '2004002008') {
				$type = 'home';
			}
		} elseif ($sale_point_id == '3') {
			//	$where_codes = "where acode in ('2004014001','2004014133')";

			if ($where_codes == '2004014001') {
				$type = 'walkin';
			}
			if ($where_codes == '2004014133') {
				$type = 'home';
			}
		}

		$data['vendor_list'] = $this->db->query("select * from tblacode $where_codes $type")->result_array();
		#----load view----------#
		$data["title"] = "Direct Customer Ledger";
		$this->load->view($this->session->userdata('language') . "/direct_customer_ledger/search", $data);
	}

	public function report($id = '')
	{

		$data['myfrmdate'] = $this->input->post("from_date");
		$data['myto_date'] = $this->input->post("to_date");
		$data['myfilter'] = $this->input->post("filter");
		$data['myacode'] = $this->input->post("acode");
		$data['myid'] = $this->input->post("id");
		$data['myhdate'] = $this->input->post("hdate");
		$data['mysort'] = $this->input->post("sort");
		$data['myaname_hid'] = $this->input->post("aname_hid");
		$direct_customer= $this->input->post("direct_customer");      // get val of direct customer

		

        $dcustomer_name = $this->db->query("SELECT name FROM tbl_direct_customer WHERE id=$direct_customer")->row_array()['name'];   // get name of direct customer
		$data['dcustomer_name']=$dcustomer_name;
         //echo $dcustomer_name;exit;
		if ($this->input->server('REQUEST_METHOD') == 'POST' || $id != '') {

			$data['one'] = 2;
			$data['report'] =  $this->mod_vendorledger->get_dcustomer_report($this->input->post(), $id);
			//pm($data['report']);exit;
			if ($id != '') {
				$data['one'] = 1;
			}

			if ($this->input->post('t_id')) {
				$count = 1;
				foreach ($data['report'] as $key => $value) {

					if (!$value['voucherno']) {
						continue;
					}

					$total_opngbl = $value['balance'];

					$total_debit += $value['debit'];
					$total_credit += $value['credit'];


					$count++;
				}

				$total_opngbl = str_replace(",", "", $total_opngbl);

				if ($this->input->post('edit_amount')) {
					$total_opngbl = $total_opngbl + $this->input->post('edit_amount');
				}

				echo $total_opngbl;

				if (($total_opngbl) > 0) {
					echo  ' Dr';
				} else {
					echo ' Cr';
				}

				echo '|';
				echo $total_opngbl;
				echo '|';


				if (($total_opngbl) > 0) {
					echo  'Dr';
				} else {
					echo 'Cr';
				}

				exit();
			}

			$table = 'tbl_company';
			$data['company'] = $this->mod_common->get_all_records($table, "*");
			if ($data['report']) {

				$data["title"] = "Direct Customer Ledger Report";
				$this->load->view($this->session->userdata('language') . "/direct_customer_ledger/single", $data);
			} else {


				$data["title"] = "Direct Customer Ledger Report";
				$this->load->view($this->session->userdata('language') . "/direct_customer_ledger/single", $data);
			}
		} else {
			//$data["filter"] = 'add';
			$data["title"] = "Direct Customer Ledger Report";
			$this->load->view($this->session->userdata('language') . "/direct_customer_ledger/single", $data);
		}
	}
	
	public function newpdf()
	{

		if ($this->input->server('REQUEST_METHOD') == 'POST' || $id != '') {

			$data['one'] = 2;
			$data['report'] =  $this->mod_vendorledger->get_report($this->input->post(), $id);

			if ($id != '') {
				$data['one'] = 1;
			}

			if ($this->input->post('t_id')) {
				$count = 1;
				foreach ($data['report'] as $key => $value) {

					if (!$value['voucherno']) {
						continue;
					}

					$total_opngbl = $value['balance'];

					$total_debit += $value['debit'];
					$total_credit += $value['credit'];


					$count++;
				}

				$total_opngbl = str_replace(",", "", $total_opngbl);

				if ($this->input->post('edit_amount')) {
					$total_opngbl = $total_opngbl + $this->input->post('edit_amount');
				}

				echo $total_opngbl;

				if (($total_opngbl) > 0) {
					echo  ' Dr';
				} else {
					echo ' Cr';
				}

				echo '|';
				echo $total_opngbl;
				echo '|';


				if (($total_opngbl) > 0) {
					echo  'Dr';
				} else {
					echo 'Cr';
				}

				exit();
			}

			$from_date = $this->input->post('from_date');
			$to_date = $this->input->post('to_date');

			$table = 'tbl_company';
			$data['company'] = $this->mod_common->get_all_records($table, "*");
		}

		foreach ($data['report'] as $key => $value) {
			$profilename =  $value['accountname'] . ' From ' . $from_date . ' To ' . $to_date;
		}


		$this->load->view($this->session->userdata('language') . "/direct_customer_ledger/pdffile", $data);

		$this->load->library('pdf');
		$html = $this->output->get_output();
		$this->dompdf->loadHtml($html);
		$this->dompdf->setPaper('A4', 'landscape');
		$this->dompdf->render();

		$this->dompdf->stream($profilename . ".pdf", array("Attachment" => 0));
	}
	
	function get_Direct_Customer()
	{
		$customer = $this->input->post('customer');
		$login_user = $this->session->userdata('id');
		$sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

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
