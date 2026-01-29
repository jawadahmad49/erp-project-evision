<?php
defined('BASEPATH') or exit('No direct script access allowed');
//require_once APPPATH . 'vendor\PHPExcel\Classes\PHPExcel.php';

class Dispatched_orders_report extends CI_Controller
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
        $data['users'] = $this->db->query("SELECT * from tbl_user")->result_array();
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
        if ($sale_point_ids) {
            $where_master = "WHERE master_id IN ('$sale_point_id_list')";
        } else {
            $where_master = "";
        }
        $data["filter"] = '';
        #----load view----------#
        $data["title"] = "Dispatched Orders Report";
        $this->load->view("app/Dispatched_orders_report/search", $data);
    }
    public function details()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $data['fromdate'] = $fromdate = $this->input->post('from_date');
            $data['todate'] = $todate = $this->input->post('to_date');
            $data['sale_point_id'] = $sale_point_id = $this->input->post('salepoint');

            $where_sale_point_id = ($sale_point_id != 'All') ? "AND po.sale_point_id in ($sale_point_id)" : "";

            $query = "
    SELECT 
        osh.date AS dispatch_date,
        osh.time AS dispatch_time,
        po.userid,
       po.id as order_id,
        po.rider_id,
        po.area_id,
        po.city_id,
        po.deliveryType,
        po.date AS order_date,
        po.time AS order_time,
        po.trip_id,
        po.deliveryStatus,
        po.address,
        po.gst,
        po.delivery_charges,
        po.per_delivery_charges,
        po.delivery_gst,
        pod.materialcode,
        pod.quantity,
        pod.price,
        pod.swap_charges,
        pod.security_charges,
        pod.type AS item_type,
        po.type AS type
    FROM tbl_place_order po
    INNER JOIN tbl_orderstatushistory osh ON osh.order_id = po.id
    INNER JOIN tbl_place_order_detail pod ON pod.order_id = po.id
    WHERE osh.date BETWEEN '$fromdate' AND '$todate' 
        $where_sale_point_id
        AND osh.status LIKE 'Dispatch'
        
    UNION 

    SELECT 
        NULL AS dispatch_date,
        NULL AS dispatch_time,
        po.userid,
        po.id as order_id,
        po.rider_id,
        po.area_id,
        po.city_id,
        po.deliveryType,
        po.date AS order_date,
        po.time AS order_time,
        po.trip_id,
        po.deliveryStatus,
        po.address,
        po.gst,
        po.delivery_charges,
        po.per_delivery_charges,
        po.delivery_gst,
        pod.materialcode,
        pod.quantity,
        pod.price,
        pod.swap_charges,
        pod.security_charges,
        pod.type AS item_type,
        po.type AS type
    FROM tbl_place_order po
    INNER JOIN tbl_place_order_detail pod ON pod.order_id = po.id
    WHERE po.date BETWEEN '$fromdate' AND '$todate' 
        $where_sale_point_id
        AND po.type = 'walkin'
        
    UNION 

    SELECT 
        date AS dispatch_date,
        NULL AS dispatch_time,
        NULL AS userid,
        po.order_id as order_id,
        NULL AS rider_id,
        NULL AS area_id,
        NULL AS city_id,
        NULL AS deliveryType,
        NULL AS order_date,
        NULL AS order_time,
        NULL AS trip_id,
        NULL AS deliveryStatus,
        NULL AS address,
        NULL AS gst,
        NULL AS delivery_charges,
        NULL AS per_delivery_charges,
        NULL AS delivery_gst,
        pod.materialcode,
        pod.quantity,
        NULL AS price,
        NULL AS swap_charges,
        pod.security_charges,
        NULL AS item_type,
        NULL AS type
    FROM tbl_cylinder_return po
    INNER JOIN tbl_cylinder_return_detail pod ON pod.master_id = po.id
    WHERE po.date BETWEEN '$fromdate' AND '$todate' 
        $where_sale_point_id
    ORDER BY order_id ASC
";

            // Execute the query and get the result
            $data['report'] = $this->db->query($query)->result_array();

            // Sort the array based on 'order_id'
            usort($data['report'], function ($a, $b) {
                return $a['order_id'] <=> $b['order_id']; // Compare order_id for sorting
            });
            if (empty($data['report'])) {
                $this->session->set_flashdata('err_message', 'No Record Found.');
                redirect(SURL . 'app/Dispatched_orders_report/');
            }

            // Load view with data
            $this->load->view("app/Dispatched_orders_report/new", $data);
        }
    }

    // public function details()
    // {
    //     if ($this->input->server('REQUEST_METHOD') == 'POST') {
    //         $login_user = $this->session->userdata('id');
    //         $data['fromdate'] = $fromdate = $this->input->post('from_date');
    //         $data['todate'] = $todate = $this->input->post('to_date');
    //         $data['sale_point_id'] = $sale_point_id = $this->input->post('salepoint');
    //         $where_sale_point_id = ($sale_point_id != 'All') ? "AND po.sale_point_id = $sale_point_id" : "";
    //         $this->db->where('userid', $login_user);
    //         $this->db->delete('tbl_temp');
    //         echo "
    //     INSERT INTO tbl_temp (
    //         dispatch_date, dispatch_time, userid, order_id, rider_id, area_id, city_id, deliveryType, order_date,
    //         order_time, trip_id, deliveryStatus, address, gst, delivery_charges, per_delivery_charges, delivery_gst,
    //         materialcode, quantity, price, swap_charges, security_charges, item_type, type
    //     )
    //     SELECT 
    //         osh.date AS dispatch_date,
    //         osh.time AS dispatch_time,
    //         po.userid,
    //         po.id AS order_id,
    //         po.rider_id,
    //         po.area_id,
    //         po.city_id,
    //         po.deliveryType,
    //         po.date AS order_date,
    //         po.time AS order_time,
    //         po.trip_id,
    //         po.deliveryStatus,
    //         po.address,
    //         po.gst,
    //         po.delivery_charges,
    //         po.per_delivery_charges,
    //         po.delivery_gst,
    //         pod.materialcode,
    //         pod.quantity,
    //         pod.price,
    //         pod.swap_charges,
    //         pod.security_charges,
    //         pod.type AS item_type,
    //         po.type AS type
    //     FROM tbl_place_order po
    //     INNER JOIN tbl_orderstatushistory osh ON osh.order_id = po.id
    //     INNER JOIN tbl_place_order_detail pod ON pod.order_id = po.id
    //     WHERE osh.date BETWEEN '$fromdate' AND '$todate' 
    //         $where_sale_point_id
    //         AND osh.status LIKE 'Dispatch'
    //     ";
    //         exit;
    //         $query1 = "
    //     INSERT INTO tbl_temp (
    //         dispatch_date, dispatch_time, userid, order_id, rider_id, area_id, city_id, deliveryType, order_date,
    //         order_time, trip_id, deliveryStatus, address, gst, delivery_charges, per_delivery_charges, delivery_gst,
    //         materialcode, quantity, price, swap_charges, security_charges, item_type, type
    //     )
    //     SELECT 
    //         osh.date AS dispatch_date,
    //         osh.time AS dispatch_time,
    //         po.userid,
    //         po.id AS order_id,
    //         po.rider_id,
    //         po.area_id,
    //         po.city_id,
    //         po.deliveryType,
    //         po.date AS order_date,
    //         po.time AS order_time,
    //         po.trip_id,
    //         po.deliveryStatus,
    //         po.address,
    //         po.gst,
    //         po.delivery_charges,
    //         po.per_delivery_charges,
    //         po.delivery_gst,
    //         pod.materialcode,
    //         pod.quantity,
    //         pod.price,
    //         pod.swap_charges,
    //         pod.security_charges,
    //         pod.type AS item_type,
    //         po.type AS type
    //     FROM tbl_place_order po
    //     INNER JOIN tbl_orderstatushistory osh ON osh.order_id = po.id
    //     INNER JOIN tbl_place_order_detail pod ON pod.order_id = po.id
    //     WHERE osh.date BETWEEN '$fromdate' AND '$todate' 
    //         $where_sale_point_id
    //         AND osh.status LIKE 'Dispatch'
    //     ";
    //         $query2 = "
    //     INSERT INTO tbl_temp (
    //         dispatch_date, dispatch_time, userid, order_id, rider_id, area_id, city_id, deliveryType, order_date,
    //         order_time, trip_id, deliveryStatus, address, gst, delivery_charges, per_delivery_charges, delivery_gst,
    //         materialcode, quantity, price, swap_charges, security_charges, item_type, type
    //     )
    //     SELECT 
    //         NULL AS dispatch_date,
    //         NULL AS dispatch_time,
    //         po.userid,
    //         po.id AS order_id,
    //         po.rider_id,
    //         po.area_id,
    //         po.city_id,
    //         po.deliveryType,
    //         po.date AS order_date,
    //         po.time AS order_time,
    //         po.trip_id,
    //         po.deliveryStatus,
    //         po.address,
    //         po.gst,
    //         po.delivery_charges,
    //         po.per_delivery_charges,
    //         po.delivery_gst,
    //         pod.materialcode,
    //         pod.quantity,
    //         pod.price,
    //         pod.swap_charges,
    //         pod.security_charges,
    //         pod.type AS item_type,
    //         po.type AS type
    //     FROM tbl_place_order po
    //     INNER JOIN tbl_place_order_detail pod ON pod.order_id = po.id
    //     WHERE po.date BETWEEN '$fromdate' AND '$todate' 
    //         $where_sale_point_id
    //         AND po.type = 'walkin'
    //     ";
    //         $this->db->query($query1);
    //         $this->db->query($query2);
    //         $data['report'] = $this->db->get_where('tbl_temp', ['userid' => $login_user])->result_array();
    //         if (empty($data['report'])) {
    //             $this->session->set_flashdata('err_message', 'No Record Found.');
    //             redirect(SURL . 'app/Dispatched_orders_report/');
    //         }

    //         // Load view with data
    //         $this->load->view("app/Dispatched_orders_report/new", $data);
    //     }
    // }
}
