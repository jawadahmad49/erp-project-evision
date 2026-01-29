<?php

class Mod_user_log extends CI_Model
{

  function __construct()
  {

    parent::__construct();
    error_reporting(0);
    $this->load->helper('file');
  }
  public function insert_into_log($add_goods = '', $trans_type = '', $form_name = '', $last_query = '', $action_type = '', $order_id = '')
  {
    $uid = $this->session->userdata('id');
    $today = date('Y-m-d h:i:sa');
    $query = "insert into tbl_user_log (user_id,trans_reference,dt,trans_type,form_name,query_exec,trans_dt,action_type,order_id )
       values
     ('$uid' , '$add_goods' , now() , '$trans_type' ,'$form_name' ,\"$last_query\",'$today','$action_type','$order_id')";
    $this->db->query($query);
    $log_message = 'XML Log:| Max Action Type: ' . json_encode($query);
    $this->custom_log_message($log_message);
    $trans_id = $this->db->query("SELECT trans_id FROM `tbl_user_log` ORDER BY `tbl_user_log`.`trans_id` DESC")->row_array()['trans_id'];
    $qdata['trans_id'] = $trans_id;
    $this->mod_common->update_table("tbl_user_log_detail", array("trans_id" => '0'), $qdata);
    $log_message = 'XML Log:| Max Action Type: ' . json_encode($qdata);
    $this->custom_log_message($log_message);
  }
  public function insert_into_log_po($add_goods = '', $trans_type = '', $form_name = '', $last_query = '', $action_type = '', $order_id = '')
  {
    $uid = $this->session->userdata('id');
    $today = date('Y-m-d h:i:sa');
    $query = "insert into tbl_user_log (user_id,trans_reference,dt,trans_type,form_name,query_exec,trans_dt,action_type,order_id )
       values
     ('$uid' , '$add_goods' , now() , '$trans_type' ,'$form_name' ,\"$last_query\",'$today','$action_type','$order_id')";
    $this->db->query($query);
  
  }
  public function custom_log_message($message)
  {
    $log_directory = FCPATH . 'application/logs/';
    $log_file = 'custom_log_' . date('Y-m-d') . '.txt';
    $log_file_path = $log_directory . $log_file;
    $decoded_message = json_decode($message, true);
    if ($decoded_message !== null) {
      $log_message = date('Y-m-d H:i:s') . ' - ' . $decoded_message . PHP_EOL;
    } else {
      $log_message = date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;
    }
    write_file($log_file_path, $log_message, 'a');
  }
  function user_activity_for_log($title = '', $sec_mode = '')
  {
    $trans_id = $this->db->query("SELECT trans_id FROM `tbl_user_log` ORDER BY `tbl_user_log`.`trans_id` DESC")->row_array()['trans_id'];

    $url = '';
    $url .= $_SERVER['HTTP_HOST'];
    $url .= $_SERVER['REQUEST_URI'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $arrayy = array(
      'user_id' => $this->session->userdata('id'),
      'ip_address' => $ip,
      'page_url' => "http://" . $url,
      'section_name' => $title,
      'action' => $sec_mode,
      'trans_id' => $trans_id,
      'date' => date('Y-m-d'),
      'time' => date('h:i:sa'),

    );
    $this->mod_common->insert_into_table("tbl_user_activity", $arrayy);
  }
  public function useractivity_of_accounts($insert = '', $form_name = '', $type = '')
  {
    $trans_id = $this->db->query("SELECT max(trans_id) as trans_id FROM `tbl_user_log_detail`")->row_array()['trans_id'];

    $trans_id = $trans_id + 1;

    $today = date('Y-m-d h:i:sa');
    if ($type == 'master') {
      $master = $this->db->query("SELECT damount,camount,convo_ratee,vno,reference,remarks,svtype,vtype,created_date from tbltrans_master where masterid='$insert'")->result_array();
      foreach ($master as $key => $value) {
        $vno = $value["vno"];
        $array = array(
          'user_id' => $this->session->userdata('id'),
          'trans_dt' => $today,
          'trans_id' => $trans_id,
          'trans_reference' => $insert,
          "form_name" => $form_name,
          "vno" => $vno,
          "damount" => $value["damount"],
          "camount" => $value["camount"],
          "convo_ratee" => $value["convo_ratee"],
          "remarks" => $value["remarks"],
          "charges" => $value["charges"],
          "svtype" => $value["svtype"],
          "type" => $type,
          "vtype" => $value["vtype"],
          "vdate" => $value["created_date"],
        );
        $this->mod_common->insert_into_table("tbl_user_log_detail", $array);
      }
    } elseif ($type == 'details') {
      $details = $this->db->query("SELECT acode,damount,vno,camount,damount_dollar,camount_dollar,convo_ratee,remarks,svtype,vtype,vdate from tbltrans_detail where ig_detail_id='$insert'")->result_array();
      foreach ($details as $key => $value) {
        $vno = $value["vno"];
        $id = $value["testid"];
        $array = array(
          'user_id' => $this->session->userdata('id'),
          'trans_dt' => $today,
          'trans_master_id' => $insert,
          'trans_id' => $trans_id - 1,
          'trans_reference' => $id,
          "form_name" => $form_name,
          "vno" => $vno,
          "acode" => $value["acode"],
          "damount" => $value["damount"],
          "camount" => $value["camount"],
          "damount_dollar" => $value["damount_dollar"],
          "camount_dollar" => $value["camount_dollar"],
          "convo_ratee" => $value["convo_ratee"],
          "reference" => $value["reference"],
          "remarks" => $value["remarks"],
          "charges" => $value["charges"],
          "svtype" => $value["svtype"],
          "type" => $type,
          "vtype" => $value["vtype"],
          "vdate" => $value["vdate"],
        );
        $this->mod_common->insert_into_table("tbl_user_log_detail", $array);
      }
    } elseif ($type == 'master delete') {

      $master = $this->db->query("SELECT damount,camount,convo_ratee,vno,reference,remarks,svtype,vtype,created_date from tbltrans_master where masterid='$insert'")->result_array();
      foreach ($master as $key => $value) {
        $vno = $value["vno"];
        $array = array(
          'user_id' => $this->session->userdata('id'),
          'trans_dt' => $today,
          'trans_id' => $trans_id,
          'trans_reference' => $insert,
          "form_name" => $form_name,
          "vno" => $vno,
          "svtype" => $value["svtype"],
          "type" => "delete",
          "vtype" => $value["vtype"],
          "vdate" => $value["created_date"],
        );
        $this->mod_common->insert_into_table("tbl_user_log_detail", $array);
      }
    }
  }
  public function user_activity($title = '', $action = '')
  {
    $url = '';
    $url .= $_SERVER['HTTP_HOST'];
    $url .= $_SERVER['REQUEST_URI'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $arrayy = array(
      'user_id' => $this->session->userdata('id'),
      'ip_address' => $ip,
      'page_url' => "http://" . $url,
      'section_name' => $title,
      'action' => $action,
      'date' => date('Y-m-d'),
      'time' => date('h:i:sa'),

    );
    $this->mod_common->insert_into_table("tbl_user_activity", $arrayy);
  }
  function invoice_log($masterid = '', $vno = '', $vtype = '', $svtype = '', $vdate = '', $bank = '', $customer = '', $payment_type = '', $reference = '', $allocated_amount = '', $bank_charges = '', $form_name = '')
  {
    $idata['masterid'] = $masterid;
    $idata['vno'] = $vno;
    $idata['vtype'] = $vtype;
    $idata['svtype'] = $svtype;
    $idata['vdate'] = $vdate;
    $idata['bank'] = $bank;
    $idata['customer'] = $customer;
    $idata['payment_type'] = $payment_type;
    $idata['reference'] = $reference;
    $idata['allocated_amount'] = $allocated_amount;
    $idata['bank_charges'] = $bank_charges;
    $idata['form_name'] = $form_name;
    $idata['created_by'] = $this->session->userdata('id');
    $idata['created_time'] = date('Y-m-d h:i:sa');

    return $this->mod_common->insert_into_table("tbl_invoice_log", $idata);
  }
  function invoice_log_detail($trans_id = '', $invoice_type = '', $invoice = '', $invoice_id = '', $invoice_amount = '', $paid_amount = '', $remaining_amount ='', $master_id ='', $date = '')
  {
    $idata['trans_id'] = $trans_id;
    $idata['invoice_type'] = $invoice_type;
    $idata['invoice'] = $invoice;
    $idata['invoice_id'] = $invoice_id;
    $idata['invoice_amount'] = $invoice_amount;
    $idata['paid_amount'] = $paid_amount;
    $idata['remaining_amount'] = $remaining_amount;
    $idata['master_id'] = $master_id;
    $idata['date'] = $date;

    $this->mod_common->insert_into_table("tbl_invoice_log_detail", $idata);
  }
}
