<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Order_log extends CI_Controller
{


  public function __construct()
  {
    parent::__construct();

    $this->load->model(array(
      "mod_common", "mod_customer"
    ));
  }

  public function index()
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



    $data["filter"] = '';
    #----load view----------#
    $data["title"] = "Log Report";

    $this->load->view("app/Order_log/search", $data);
  }

  public function orders_list()
  {
    $sale_point_id = $_POST['sale_point_id'];

    $order_list = $this->db->query("SELECT order_id FROM `tbl_order_log` INNER JOIN tbl_place_order on tbl_place_order.id =tbl_order_log.order_id where sale_point_id='$sale_point_id' group by order_id")->result_array();
    foreach ($order_list as $key) { ?>
      <option value="<?php echo $key['order_id'] ?>"><?php echo "Order # " . $key['order_id']; ?></option>
<?php }
  }




  public function details()
  {
    if ($this->input->server('REQUEST_METHOD') == 'POST') {




      $salepoint =  $this->input->post('salepoint');
      $data['order'] = $order =  $this->input->post('order');
      $data['old_result'] = $this->db->query("SELECT * FROM `tbl_order_log` where order_id='$order'")->result_array();

      $this->load->view("app/Order_log/detail_report_log", $data);


      $table = 'tbl_company';
      $data['company'] = $this->mod_common->get_all_records($table, "*");
    }
  }
}
