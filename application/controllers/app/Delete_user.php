<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Delete_user extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array(
            "mod_common",
        ));
    }

    public function index()
    {
        $this->user_deletion();
        $data['title'] = "Delete Requests";
        $this->load->view("app/Delete_user/manage", $data);
    }
    public function your_ajax_endpoint()
    {
        $login_user = $this->session->userdata('id');
        $this->db->select('location');
        $this->db->from('tbl_admin');
        $this->db->where('id', $login_user);
        $sale_point_ids = $this->db->get()->row_array()['location'];

        $draw = $this->input->post('draw');
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $searchValue = $this->input->post('search')['value'];

        $orderColumnIndex = $this->input->post('order')[0]['column'];
        $orderDirection = $this->input->post('order')[0]['dir'];
        $columns = $this->input->post('columns');

        $orderColumn = $columns[$orderColumnIndex]['data'];

        if (isset($_POST['datepicker']) && isset($_POST['datepicker1'])) {
            $from_date = date("Y-m-d", strtotime($_POST['datepicker']));
            $to_date = date("Y-m-d", strtotime($_POST['datepicker1']));
        } else {
            $from_date = date('Y-m-d', strtotime('-60 day'));
            $to_date = date('Y-m-d');
        }
        $baseQuery = "SELECT COUNT(*) as count 
              FROM `delete_requests` 
              WHERE created_at BETWEEN '$from_date' AND '$to_date'";
        if (!empty($searchValue)) {
            $baseQuery .= " AND (";
            $baseQuery .= "id LIKE '%" . $this->db->escape_like_str($searchValue) . "%' OR ";
            $baseQuery .= "created_at LIKE '%" . $this->db->escape_like_str($searchValue) . "%' OR ";
            $baseQuery .= "user_id LIKE '%" . $this->db->escape_like_str($searchValue) . "%' OR ";
            $baseQuery .= "name LIKE '%" . $this->db->escape_like_str($searchValue) . "%' OR ";
            $baseQuery .= "email LIKE '%" . $this->db->escape_like_str($searchValue) . "%' OR ";
            $baseQuery .= "status LIKE '%" . $this->db->escape_like_str($searchValue) . "%'";
            $baseQuery .= ")";
        }

        $recordsTotal = $this->db->query($baseQuery)->row()->count;
        $query = "SELECT * 
			FROM `delete_requests` 
			WHERE created_at BETWEEN '$from_date' AND '$to_date'";
        if (!empty($searchValue)) {
            $query .= " AND (";
            $query .= "id LIKE '%" . $this->db->escape_like_str($searchValue) . "%' OR ";
            $query .= "created_at LIKE '%" . $this->db->escape_like_str($searchValue) . "%' OR ";
            $query .= "user_id LIKE '%" . $this->db->escape_like_str($searchValue) . "%' OR ";
            $query .= "name LIKE '%" . $this->db->escape_like_str($searchValue) . "%' OR ";
            $query .= "email LIKE '%" . $this->db->escape_like_str($searchValue) . "%' OR ";
            $query .= "status LIKE '%" . $this->db->escape_like_str($searchValue) . "%'";
            $query .= ")";
        }
        if (!empty($orderColumn) && !empty($orderDirection)) {
            $query .= " ORDER BY " . $this->db->escape_str($orderColumn) . " " . $this->db->escape_str($orderDirection);
        } else {
            $query .= " ORDER BY id DESC";
        }
        if (!empty($start) || !empty($length)) {
            $query .= " LIMIT " . intval($start) . ", " . intval($length);
        }
        $data = array();
        $results = $this->db->query($query)->result_array();
        $sno = 0;
        foreach ($results as $value) {
            $sno++;
            $id = $value['id'];
            $action_buttons = '';
            if ($value['status'] == 'Active') {
                $action_buttons = '<div class="action-buttons" style="display: flex; align-items: center;">';
                $action_buttons .= '<a class="btn btn-info btn-sm" href="' . SURL . 'app/Delete_user/manage_update/' . $id . '">Cancel Request</a>';
                $action_buttons .= '</div>';
            }

            $data[] = array(
                'count' => $sno,
                'user_id' => $value['user_id'],
                'name' => $value['name'],
                'email' => $value['email'],
                'phone_number' => $value['phone_number'],
                'status' => $value['status'],
                'created_at' => $value['created_at'],
                'actions' => $action_buttons
            );
        }

        echo json_encode([
            'draw' => intval($draw),
            'recordsTotal' => intval($recordsTotal),
            'recordsFiltered' => intval($recordsTotal),
            'data' => $data
        ]);
    }
    public function request()
    {
        $data['title'] = "Delete Requests";
        $this->load->view("app/Delete_user/request", $data);
    }

    public function add_request()
    {
        if ($this->input->method() === 'post') {

            $name = $this->input->post('name');
            $email = $this->input->post('email');
            $number = $this->input->post('number');
            $password = $this->input->post('password');
            $reasons = $this->input->post('reason');
            $otherReason = $this->input->post('otherReason');

            if (empty($name) || empty($email) || empty($number) || empty($password)) {
                echo json_encode(array('status' => 'error', 'message' => 'All fields are required.'));
                return;
            }

            if (!is_array($reasons)) {
                $reasons = array();
            }
            $reasonString = implode(', ', $reasons);
            $encoded_password = base64_encode($password);

            $check_user = $this->db->select('*')
                ->from('tbl_user')
                ->where('phone', $number)
                ->where('admin_pwd', $encoded_password)
                ->get()
                ->row_array();

            if (!$check_user) {
                $response = array('status' => 'error', 'message' => 'User does not exist with the provided details.');
            } else {
                $id = $check_user['id'];
                $check_request = $this->db->select('*')
                    ->from('delete_requests')
                    ->where('phone_number', $number)
                    ->where('password', $encoded_password)
                    ->where('status !=', 'Completed')
                    ->get()
                    ->row_array();

                if ($check_request) {
                    $response = array('status' => 'error', 'message' => 'User deletion request already submitted.');
                } else {
                    $this->db->set('status', 'InActive')
                        ->where('id', $id)
                        ->update('tbl_user');

                    $data = array(
                        'name' => $name,
                        'email' => $email,
                        'phone_number' => $number,
                        'password' => $encoded_password,
                        'reason' => $reasonString,
                        'other_reason' => in_array('Other', $reasons) ? $otherReason : null,
                        'created_at' => date('Y-m-d H:i:s'),
                        'user_id' => $id,
                        'status' => 'Active',
                    );

                    $this->db->insert('delete_requests', $data);
                    $insert_id = $this->db->insert_id();
                    if ($this->db->affected_rows() > 0) {
                        $response = array('status' => 'success', 'message' => 'User deletion request submitted successfully.');

                        $this->send_confirmation_email($email, $name, $insert_id);
                    } else {
                        $response = array('status' => 'error', 'message' => 'Failed to submit the request. Please try again.');
                    }
                }
            }
            echo json_encode($response);
        }
    }

    public function send_confirmation_email($email, $name, $insert_id)
    {
        // Retrieve deletion request details and user_id in one query
        $query = $this->db->query("SELECT user_id, created_at FROM delete_requests WHERE id = ?", array($insert_id));
        $delete_request_data = $query->row_array();

        if (empty($delete_request_data['user_id'])) {
            log_message('error', 'Deletion request not found or missing user ID for insert_id: ' . $insert_id);
            return false;  // Exit if no user_id is found
        }

        $user_id = $delete_request_data['user_id'];
        $deletion_request = $delete_request_data['created_at'];
        $deletion_date = date('Y-m-d', strtotime($deletion_request . ' +30 days'));

        // Retrieve user email
        $query = $this->db->query("SELECT email FROM tbl_user WHERE id = ?", array($user_id));
        $user_data = $query->row_array();
        $user_email = $user_data['email'] ?? null;

        $company_email = $this->db->query("select email from tbl_company where id = '1' ")->row_array()['email'];
        // Decide email recipient
        $to = !empty($user_email) ? $user_email : $email;
        $subject = "User Deletion Confirmation";

        // Construct the message
        $message = "
        <html>
            <head>
                <title>User Deletion Confirmation</title>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { background-color: #F5F5F5; padding: 20px; }
                    .heading { color: #1866C6; }
                    .right-align { text-align: right; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h1 class='heading'>User Deletion Confirmation</h1>
                    <p>Dear {$name},</p>
                    <p>Your account deletion request was submitted on {$deletion_request}. It will be processed within 30 days, by {$deletion_date}.</p>
                    <p>If you wish to cancel this request, click on this link: <a href='" . SURL . "app/Delete_user/update_request/{$insert_id}' target='_blank'>Cancel Request</a>.</p>
                    <br>
                    <p class='right-align'>Best regards,</p>
                    <p class='right-align'>Support Team</p>
                </div>
            </body>
        </html>";

        // Email headers
        if(!empty($company_email)){
            $from = $company_email;
        }else {
            $from = 'info@opigas.com';
        }
        $headers = "From: $from\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        // Send email and log any failures
        if (!mail($to, $subject, $message, $headers)) {
            log_message('error', "Failed to send confirmation email to $to.");
            return false;
        }

        return true;  // Indicate success if email is sent
    }
    public function update_request($id)
    {
        if ($id) {
            $query = $this->db->query("SELECT * FROM delete_requests WHERE id = '$id' AND status = 'Active'")->row_array();
            $user_id = $query['user_id'];
            if (!empty($query)) {
                $query = $this->db->set('status', 'InActive')
                    ->where('id', $id)
                    ->update('delete_requests');

                $query = $this->db->set('status', 'Active')
                    ->where('id', $user_id)
                    ->update('tbl_user');

                // $data['record'] = $query;
                $data['title'] = 'Request Canceled';
                $data['message'] = 'User deletion request has been canceled successfully.';
                $this->load->view('app/Delete_user/cancel', $data);
            } else {
                $data['title'] = 'Request Not Found';
                $data['message'] = 'No active deletion request found or the request is already canceled.';
                $this->load->view('app/Delete_user/cancel', $data);
            }
        } else {
            echo 'Invalid request ID.';
        }
    }
    public function user_deletion()
    {
        $current_date = date('Y-m-d H:i:s');

        $delete_requests = $this->db->query("SELECT * FROM delete_requests WHERE DATE_ADD(created_at, INTERVAL 30 DAY) < '$current_date' AND status = 'Active'")->result_array();

        if (!empty($delete_requests)) {
            foreach ($delete_requests as $delete_request) {
                $user_id = $delete_request['user_id'];

                $user_name = $this->db->query("SELECT name FROM tbl_user WHERE id = '$user_id'")->row_array()['name'];

                $this->db->set('userid', $user_name)
                    ->where('userid', $user_id)
                    ->update('tbl_place_order');

                $this->db->where('id', $user_id)->delete('tbl_user');
                $this->db->set('status', 'Completed')
                    ->where('id', $delete_request['id'])
                    ->update('delete_requests');
            }
        }
    }

    public function manage_update($id)
    {
        if ($id) {
            $query = $this->db->query("SELECT * FROM delete_requests WHERE id = $id AND status = 'Active'")->row_array();

            if (!empty($query)) {
                $user_id = $query['user_id'];

                $this->db->set('status', 'Active')
                    ->where('id', $user_id)
                    ->update('tbl_user');

                $this->db->set('status', 'Completed')
                    ->where('id', $id)
                    ->update('delete_requests');
                $this->session->set_flashdata('ok_message', 'You have succesfully Cancelled the request.');

                redirect(SURL . 'app/Delete_user/index/');
            } else {
                $this->session->set_flashdata('err_message', 'Request Cancellation failed.');
                redirect(SURL . 'app/Delete_user/index/');
            }
        }
    }
}
