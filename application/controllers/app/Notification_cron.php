<?php
defined('BASEPATH') or exit('No direct script access allowed');
require 'vendor/autoload.php';
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class Notification_cron extends CI_Controller
{
    public function process_pending_notifications()
    {
        $now = time();
        $pending = $this->db->where('sts', 'Pending')
            ->where('scheduled_at <=', $now)
            ->get('tbl_notification')
            ->result_array();
        foreach ($pending as $notification) {
            // Call your existing sending logic
            $this->send_notification($notification);
        }
    }
    public function send_notification($notification)
    {
        try {
            $factory = (new Factory)
                ->withServiceAccount(__DIR__ . '/opi-gas-727d8-firebase-adminsdk-2xd00-ecada38560.json')
                ->withDatabaseUri('https://opi-gas-727d8-default-rtdb.firebaseio.com/');
            $messaging = $factory->createMessaging();

            $successCount   = 0;
            $invalidTokens  = [];

            $zone_ids_str   = $notification['zone_id'];
            $data           = $notification;
            $filename       = $notification['logo'];

            // 1️⃣ Get eligible users
            $userTokens = $this->db->query("SELECT id, token, area_id FROM tbl_user 
            WHERE token IS NOT NULL AND token != ''")->result_array();

            if ($zone_ids_str) {
                $tbl_area = $this->db->query(
                    "SELECT id FROM tbl_zone_detail WHERE zone_id IN ($zone_ids_str)"
                )->result_array();

                $allowed_area_ids = array_column($tbl_area, 'id');

                $userTokens = array_filter($userTokens, function ($user) use ($allowed_area_ids) {
                    return in_array($user['area_id'], $allowed_area_ids);
                });
                $userTokens = array_values($userTokens);
            }

            // 2️⃣ Send notification to each token
            foreach ($userTokens as $tokenRow) {
                $userToken = $tokenRow['token'];
                $tokenId   = $tokenRow['id'];

                if ($userToken) {
                    try {
                        $message = CloudMessage::withTarget('token', $userToken)
                            ->withNotification(
                                Notification::create($data['title'], $data['short_detail'])
                                    ->withImageUrl('https://lpginsight.com/nerian_sharif/cms/assets/images/gallery/' . $filename)
                            )
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
                        log_message('info', 'Invalid token cleared: ' . $userToken);
                    } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
                        $invalidTokens[] = $tokenId;
                        log_message('info', 'Token not found, cleared: ' . $userToken);
                    } catch (\Exception $e) {
                        log_message('error', 'Firebase Error for token ' . $userToken . ': ' . $e->getMessage());
                    }
                }
            }

            // 3️⃣ Cleanup invalid tokens
            if (!empty($invalidTokens)) {
                $tokenIdsStr = implode(',', array_map('intval', $invalidTokens));
                $this->db->query("UPDATE tbl_user SET token = NULL WHERE id IN ($tokenIdsStr)");
                log_message('info', 'Cleared ' . count($invalidTokens) . ' invalid tokens from tbl_user');
            }

            // 4️⃣ Update notification status
            if ($successCount > 0) {
                $this->db->where('transid', $notification['transid'])
                    ->update('tbl_notification', ['sts' => 'Active','sent'=>'Yes']);
            } else {
                $this->db->where('transid', $notification['transid'])
                    ->update('tbl_notification', ['sts' => 'Inactive', 'sent'=>'No']);
            }

            return $successCount;
        } catch (\Exception $e) {
            log_message('error', 'General Firebase Error: ' . $e->getMessage());
            return false;
        }
    }
}
