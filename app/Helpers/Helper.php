<?php

namespace App\Helpers;
use App\Mail\CommonMail;
use Mail;

use App\Models\Cms;

class Helper {

    public static function createOtp() {
        return mt_rand(100000, 999999);
    }

    public static function storeMediaFile($model, $media, $folderName, $collectionName, $user_image) {
        try {
            $model->clearMediaCollection($collectionName);
            $model->addMedia($media)
                    ->usingFileName($user_image)
                    ->withCustomProperties(['folder_name' => $folderName])
                    // ->withResponsiveImages()
                    ->toMediaCollection($collectionName);
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public static function commonSendEmail($email,$subject,$message_content) {
        try {
            Mail::to($email)->send(new CommonMail($subject, $message_content));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /*
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */
}
