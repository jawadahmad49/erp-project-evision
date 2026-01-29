<?php

require 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class Mod_notification1 extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        error_reporting(0);
    }
    public function add_notification($data)
    {

        $targetMode = isset($data['target_mode']) ? $data['target_mode'] : 'zone';
        $zone_ids_str = null;
        $sale_point_ids_str = null;


        // ---------- LOCATION MODE ----------
        if ($targetMode === 'location') {
            $sale_point_ids = (is_array($data) && isset($data['location'])) ? $data['location'] : [];
            $sale_point_ids_str = !empty($sale_point_ids) ? implode(",", $sale_point_ids) : null;

            if (!empty($sale_point_ids_str)) {
                // derive zone_ids from selected sale points
                $sale_point_zones = $this->db->query("SELECT DISTINCT zone_id FROM tbl_sales_point WHERE sale_point_id IN ($sale_point_ids_str)")->result_array();
                $zone_ids = [];

                // Loop each row and explode the zone_id string
                foreach ($sale_point_zones as $row) {
                    $ids = explode(',', $row['zone_id']);   // "1,2,3,4" → [1,2,3,4]
                    $zone_ids = array_merge($zone_ids, $ids);
                }

                // Remove duplicates and cast to int
                $zone_ids = array_unique(array_map('intval', $zone_ids));

                $zone_ids_str = implode(',', $zone_ids);
            }
        }
        // ---------- ZONE MODE ----------
        if ($targetMode === 'zone') {
            $zone_ids = (is_array($data) && isset($data['zone_id'])) ? $data['zone_id'] : [];

            if (empty($zone_ids) || in_array('All', $zone_ids, true)) {
                $all_zone_ids = $this->db->select('id')->get('tbl_zone')->result_array();
                $zone_ids_str = implode(',', array_map('intval', array_column($all_zone_ids, 'id')));
            } else {
                $zone_ids_str = !empty($zone_ids) ? implode(',', array_map('intval', $zone_ids)) : null;
            }

            // no locations directly stored when targeting zones
            $sale_point_ids_str = null;
        }
        $filename = $this->input->post("old_image");
        if ($_FILES['company_image']['name'] != "") {
            $projects_folder_path = './assets/images/gallery';
            $config['upload_path'] = $projects_folder_path;
            $config['allowed_types'] = 'jpg|jpeg|gif|tiff|tif|png';
            $config['overwrite'] = false;
            $config['encrypt_name'] = TRUE;
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('company_image')) {
                $this->session->set_flashdata('err_message', 'Image upload failed: ' . $this->upload->display_errors());
                return false;
            } else {
                $data_image_upload = array('upload_image_data' => $this->upload->data());
                $filename = $data_image_upload['upload_image_data']['file_name'];
            }
        }

        $in_array_master = array(
            "target_mode" => $targetMode,
            "location_id" => $sale_point_ids_str,
            "title" => $data['title'],
            "from_date" => !empty($data['from_date']) ? $data['from_date'] : null,
            "to_date" => !empty($data['to_date']) ? $data['to_date'] : null,
            "start_date" => $data['start_date'],
            "expiry_date" => $data['end_date'],
            "sent" => 'No',
            "zone_id" => $zone_ids_str,
            "details" => $data['remarks'],
            "short_detail" => $data['short'],
            // "video_url" => $data['video_url'],
            "logo" => $filename,
            "created_dt" => date('Y-m-d'),
            "created_by" => $this->session->userdata('id')
        );
        // Check if date time are in the future, then schedule Notification as Pending
        $scheduledAt = strtotime($data['date_time'] . " " . $data['time']); // future/past timestamp
        $in_array_master['scheduled_at'] = $scheduledAt; // default status
        if ($scheduledAt > time()) {
            $in_array_master['sts'] = 'Pending';
        } else {
            $in_array_master['sts'] = 'Active';
        }
        $table = "tbl_notification";
        $add = $this->mod_common->insert_into_table($table, $in_array_master);
        if ($in_array_master['sts'] === "Pending") {
            return $add !== false;
        }
        $from_date = $data['from_date'];
        $to_date = $data['to_date'];
        if (
            !empty($from_date) && $from_date !== '0000-00-00' &&
            !empty($to_date) && $to_date !== '0000-00-00'
        ) {
            // Convert to proper format if necessary
            $from_date = date('Y-m-d', strtotime($from_date));
            $to_date = date('Y-m-d', strtotime($to_date));
            $userTokens = $this->db->query(
                "SELECT id, token, area_id FROM tbl_user 
         WHERE token IS NOT NULL 
           AND token != '' 
           AND joining_date >= ? 
           AND joining_date <= ?",
                [$from_date, $to_date]
            )->result_array();
        } else {
            $userTokens = $this->db->query("SELECT id, token, area_id FROM tbl_user WHERE token IS NOT NULL AND token != ''")->result_array();
        }

        if (empty($userTokens)) {
            $this->session->set_flashdata('no_tokens', true);
        } else {
            try {
                $factory = (new Factory)
                    ->withServiceAccount(__DIR__ . '/opi-gas-727d8-firebase-adminsdk-2xd00-ecada38560.json')
                    ->withDatabaseUri('https://opi-gas-727d8-default-rtdb.firebaseio.com/');
                $messaging = $factory->createMessaging();
                $successCount = 0;
                $invalidTokens = array();
                if ($targetMode === 'location' && !empty($zone_ids_str)) {
                    // Use derived zone_ids from sales points
                    $tbl_area = $this->db->query("SELECT id FROM tbl_zone_detail WHERE zone_id IN ($zone_ids_str)")->result_array();
                } else {
                    $tbl_area = $this->db->query(
                        "SELECT id FROM tbl_zone_detail WHERE zone_id IN ($zone_ids_str)"
                    )->result_array();
                }
                $allowed_area_ids = array_column($tbl_area, 'id');
                $userTokens = array_filter($userTokens, function ($user) use ($allowed_area_ids) {
                    return in_array($user['area_id'], $allowed_area_ids);
                });
                $userTokens = array_values($userTokens);
                foreach ($userTokens as $tokenRow) {
                    $userToken = $tokenRow['token'];
                    $tokenId = $tokenRow['id'];
                    if ($userToken) {
                        try {
                            $message = CloudMessage::withTarget('token', $userToken)
                                ->withNotification(Notification::create($data['title'], $data['short'])
                                    ->withImageUrl('https://lpginsight.com/nerian_sharif/cms/assets/images/gallery/' . $filename))
                                ->withAndroidConfig([
                                    'notification' => [
                                        'icon' => 'https://lpginsight.com/nerian_sharif/assets/images/logo',
                                        'color' => '#FF0000',
                                    ]
                                ]);
                            $messaging->send($message);
                            $successCount++;
                        } catch (\Kreait\Firebase\Exception\Messaging\InvalidToken $e) {
                            // Token is invalid, mark for deletion
                            $invalidTokens[] = $tokenId;
                            log_message('info', 'Invalid token cleared: ' . $userToken);
                        } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
                            // Token not found, mark for deletion
                            $invalidTokens[] = $tokenId;
                            log_message('info', 'Token not found, cleared: ' . $userToken);
                        } catch (\Exception $e) {
                            // Other Firebase errors, log but continue
                            log_message('error', 'Firebase Error for token ' . $userToken . ': ' . $e->getMessage());
                        }
                    }
                }
                // Delete invalid tokens from database
                if (!empty($invalidTokens)) {
                    $tokenIdsStr = implode(',', array_map('intval', $invalidTokens));
                    $this->db->query("UPDATE tbl_user SET token = NULL WHERE id IN ($tokenIdsStr)");
                    log_message('info', 'Cleared ' . count($invalidTokens) . ' invalid tokens from tbl_user');
                }
                if ($successCount === 0) {
                    $this->session->set_flashdata('firebase_error', 'No valid tokens found or all tokens were invalid');
                } else {
                    // Set success message with counts
                    $totalTokens = count($userTokens);
                    $deletedCount = count($invalidTokens);
                    $this->session->set_flashdata(
                        'firebase_success',
                        "Notifications sent successfully to $successCount users. $deletedCount invalid tokens were cleared."
                    );
                }
            } catch (\Exception $e) {
                $this->session->set_flashdata('firebase_error', $e->getMessage());
                log_message('error', 'General Firebase Error: ' . $e->getMessage());
            }
        }
        return $add !== false;
    }
    public function update_notification($data)
    {
        $targetMode = isset($data['target_mode']) ? $data['target_mode'] : 'zone';
        $zone_ids_str = null;
        $sale_point_ids_str = null;
        //---------------Location Mode ------------//
        if ($targetMode === 'location') {
            $sale_point_ids = (is_array($data) && isset($data['location'])) ? $data['location'] : [];
            $sale_point_ids_str = !empty($sale_point_ids) ? implode(",", $sale_point_ids) : null;
            if (!empty($sale_point_ids_str)) {
                //derive zone ids from selected sale points
                $sale_point_zones = $this->db->query("SELECT DISTINCT zone_id FROM tbl_sales_point WHERE sale_point_id IN ($sale_point_ids_str)")->result_array();
                $zone_ids = [];
                //Loop each row and explode the zone_id string
                foreach ($sale_point_zones as $row) {
                    $ids = explode(',', $row['zone_id']);
                    $zone_ids = array_merge($zone_ids, $ids);
                }
                // Remove duplicates and cast to int
                $zone_ids = array_unique(array_map('intval', $zone_ids));
                $zone_ids_str = implode(',', $zone_ids);
            }
        }
        //Zone Mode
        if ($targetMode === 'zone') {
            $zone_ids = (is_array($data) && isset($data['zone_id'])) ? $data['zone_id'] : [];

            if (empty($zone_ids) || in_array('All', $zone_ids, true)) {
                $all_zone_ids = $this->db->select('id')->get('tbl_zone')->result_array();
                $zone_ids_str = implode(',', array_map('intval', array_column($all_zone_ids, 'id')));
            } else {
                $zone_ids_str = !empty($zone_ids) ? implode(',', array_map('intval', $zone_ids)) : null;
            }

            // no locations directly stored when targeting zones
            $sale_point_ids_str = null;
        }


        $id = $data['id'];
        $filename = $data['old_image'];

        // --- Handle Zone IDs (same as add) ---

        // --- Handle Image Upload ---
        if ($_FILES['company_image']['name'] != "") {
            $projects_folder_path = './assets/images/gallery/';
            $config['upload_path']   = $projects_folder_path;
            $config['allowed_types'] = 'jpg|jpeg|gif|tiff|tif|png';
            $config['overwrite']     = false;
            $config['encrypt_name']  = TRUE;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('company_image')) {
                $this->session->set_flashdata('err_message', 'Image upload failed: ' . $this->upload->display_errors());
                return false;
            } else {
                $data_image_upload = array('upload_image_data' => $this->upload->data());
                $filename = $data_image_upload['upload_image_data']['file_name'];
            }
        }

        // --- Update DB record ---
        $in_array_master = array(
            "target_mode" => $targetMode,
            "location_id" => $sale_point_ids_str,
            "title"        => $data['title'],
            "from_date"    => !empty($data['from_date']) ? $data['from_date'] : null,
            "to_date"      => !empty($data['to_date']) ? $data['to_date'] : null,
            "start_date"   => $data['start_date'],
            "expiry_date"  => $data['end_date'],
            "sent" => 'No',
            "zone_id"      => $zone_ids_str,   // ✅ Store CSV of zones
            "details"      => $data['remarks'],
            "short_detail" => $data['short'],
            "logo"         => $filename,
            "modify_dt"    => date('Y-m-d'),
            "modify_by"    => $this->session->userdata('id'),
        );
        $scheduledAt = strtotime($data['date_time']." ".$data['time']);
        $in_array_master['scheduled_at'] = $scheduledAt;
        if($scheduledAt > time()){
            $in_array_master['sts'] = 'Pending';
        }else{
            $in_array_master['sts'] = 'Active';
        }
        $table = "tbl_notification";
        $where = "transid='$id'";
        $update = $this->mod_common->update_table($table, $where, $in_array_master);
          if ($in_array_master['sts'] === "Pending") {
            return $update !== false;
        }
        // --- Fetch Users (with Date + Zone Filtering) ---
        $from_date = $data['from_date'];
        $to_date   = $data['to_date'];

        if (
            !empty($from_date) && $from_date !== '0000-00-00' &&
            !empty($to_date) && $to_date !== '0000-00-00'
        ) {
            $from_date = date('Y-m-d', strtotime($from_date));
            $to_date   = date('Y-m-d', strtotime($to_date));
            $userTokens = $this->db->query(
                "SELECT id, token, area_id 
                FROM tbl_user 
                WHERE token IS NOT NULL 
                AND token != '' 
                AND joining_date >= ? 
                AND joining_date <= ?",
                [$from_date, $to_date]
            )->result_array();
        } else {
            $userTokens = $this->db->query(
                "SELECT id, token, area_id 
                FROM tbl_user 
                WHERE token IS NOT NULL AND token != ''"
            )->result_array();
        }

        // --- Zone Filtering (same as add) ---
        $tbl_area = $this->db->query("SELECT id FROM tbl_zone_detail WHERE zone_id IN ($zone_ids_str)")->result_array();
        $allowed_area_ids = array_column($tbl_area, 'id');

        $userTokens = array_filter($userTokens, function ($user) use ($allowed_area_ids) {
            return in_array($user['area_id'], $allowed_area_ids);
        });
        $userTokens = array_values($userTokens);

        // --- Firebase Send (same as add) ---
        if (empty($userTokens)) {
            $this->session->set_flashdata('no_tokens', true);
        } else {
            try {
                $factory = (new Factory)
                    ->withServiceAccount(__DIR__ . '/opi-gas-727d8-firebase-adminsdk-2xd00-ecada38560.json')
                    ->withDatabaseUri('https://opi-gas-727d8-default-rtdb.firebaseio.com/');
                $messaging = $factory->createMessaging();

                $successCount = 0;
                $invalidTokens = [];

                foreach ($userTokens as $tokenRow) {
                    $userToken = $tokenRow['token'];
                    $tokenId   = $tokenRow['id'];

                    if ($userToken) {
                        try {
                            $message = CloudMessage::withTarget('token', $userToken)
                                ->withNotification(Notification::create($data['title'], $data['short'])
                                    ->withImageUrl('https://lpginsight.com/nerian_sharif/cms/assets/images/gallery/' . $filename))
                                ->withAndroidConfig([
                                    'notification' => [
                                        'icon'  => 'https://lpginsight.com/nerian_sharif/assets/images/logo',
                                        'color' => '#FF0000',
                                    ]
                                ]);

                            $messaging->send($message);
                            $successCount++;
                        } catch (\Kreait\Firebase\Exception\Messaging\InvalidToken $e) {
                            $invalidTokens[] = $tokenId;
                            log_message('info', 'Invalid token removed: ' . $userToken);
                        } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
                            $invalidTokens[] = $tokenId;
                            log_message('info', 'Token not found, removed: ' . $userToken);
                        } catch (\Exception $e) {
                            log_message('error', 'Firebase Error for token ' . $userToken . ': ' . $e->getMessage());
                        }
                    }
                }

                // Delete invalid tokens
                if (!empty($invalidTokens)) {
                    $tokenIdsStr = implode(',', array_map('intval', $invalidTokens));
                    $this->db->query("UPDATE tbl_user SET token = NULL WHERE id IN ($tokenIdsStr)");
                }

                if ($successCount === 0) {
                    $this->session->set_flashdata('firebase_error', 'No valid tokens found or all tokens were invalid');
                } else {
                    $this->session->set_flashdata(
                        'firebase_success',
                        "Notifications sent successfully to $successCount users. " . count($invalidTokens) . " invalid tokens were cleared."
                    );
                }
            } catch (\Exception $e) {
                $this->session->set_flashdata('firebase_error', $e->getMessage());
                log_message('error', 'General Firebase Error: ' . $e->getMessage());
            }
        }

        return $update !== false;
    }

    public function get_by_title($title)
    {
        $query = $this->db->select('*')
            ->from('tbl_promo_code')
            ->where('promo_code', $title)
            ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        //return $query->result_array();
        return $query->num_rows();
    }
    public function manage_notification()
    {
        $this->db->select('*');
        $this->db->from('tbl_notification');
        $this->db->order_by("FIELD(sts, 'Active') DESC", false);
        $this->db->order_by("FIELD(sts, 'Ended') DESC", false);
        $this->db->order_by("expiry_date DESC");
        $query = $this->db->get();
        return $query->result_array();
    }
    public function edit_by_title($title, $id)
    {
        $query = $this->db->select('*')
            ->from('tbl_promo_code')
            ->where('promo_code', $title)
            ->where('transid!=', $id)
            ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }
    public function under_area($promo)
    {
        $query = $this->db->select('*')
            ->from('tbl_orderbooking')
            ->where('promo_code=', $promo)
            ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }
}
