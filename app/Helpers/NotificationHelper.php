<?php

namespace App\Helpers;

use App\Models\{
    User,
    UserDevice,
    Notifications
};

class NotificationHelper {

    public static function sendNotification($token_array, $message, $notification_data) {

        //$API_ACCESS_KEY = 'AAAA_NmZ6Z4:APA91bHxjNhqiyguWUe2TmEZiEZDyO5VOndxgwOYp7sCKo-kBQIc3ECR-GZPbx4pHDcwoF1W9T6QtYc1oT2hlKC7lm8cC7JKZm-__p9lqS1u5lBKKCISoJS-G1gRDPPq9EufHuhQKQJm';
        $API_ACCESS_KEY = 'AAAAMhRb2OQ:APA91bHKvtGeERGDOs53BbkB2Z4Ra5TTwjPjN1QavSoQbb4Oov5NrLH9dQKCQo8kH__AHnxhhgKGF5OC7DcmEDq2LlpkUlRYSd1Mt1erp609WWtmn-o45pdzLACeWiPQaYLiyptJa-xv';
        $url = 'https://fcm.googleapis.com/fcm/send';

        $fields = array('registration_ids' => $token_array, 'data' => $message, 'notification' => $notification_data);
        $headers = array('Authorization: key=' . $API_ACCESS_KEY, 'Content-Type: application/json');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
    }

    public static function notification($noti_message, $type, $from_id, $to_id, $redirect_id = '') {
        $user_device = UserDevice::where('user_id', '=', $to_id)->orderBy('user_device_id', 'DESC')->first();
        $device_token = $user_device->device_token;
        $token_array = array($device_token);
        $user_data = User::where('user_id', $from_id)->first();
        $msg = $noti_message;
        $body = $msg;
        $unreadCount = Notifications::where('to_id', $to_id)->where('is_read', '0')->count();
        $badge = !empty($unreadCount) ? $unreadCount : '0';
        $message = array(
            'data' => array(
                'from_id' => $from_id,
                'to_id' => $to_id,
                'title' => $msg,
                'body' => $body,
                'redirect_url' => $type,
                'badge' => $badge,
                'redirect_id' => $redirect_id,
                'sound' => 'default',
                'from_image' => $user_data->getUserProfileImageAttribute(),
            ),
        );
        $notification_data = array('title' => $msg,
            'body' => $body, 'sound' => 'default', 'click_action' => 'FLUTTER_NOTIFICATION_CLICK','badge' => $badge);
        self::sendNotification($token_array, $message, $notification_data);
    }

}
