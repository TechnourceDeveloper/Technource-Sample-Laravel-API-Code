<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Carbon\Carbon;

class EmailTemplateSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('email_template')->truncate();
        DB::table('email_template')->insert([
            [
                'title' => 'account-verification ',
                'parameter' => 'USERNAME,OTP',
                'subject' => 'Account Verification',
                'content' => '<p>Hello {{USERNAME}},</p><p>We just need to verify your email address before you can access.</p><p><strong>Your Verification code is {{OTP}}</strong></p><p>Thanks!</p><p>&nbsp;</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'title' => 'resend-otp',
                'parameter' => 'USERNAME,OTP',
                'subject' => 'Resend OTP',
                'content' => '<p>Hello {{USERNAME}},</p><p>You have recently requested to resend OTP please find your OTP below,<br /><strong>Your OTP: {{OTP}}</strong></p><p>Please ignore if you have not requested.<br />&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;</p><p>Thanks!</p><p>&nbsp;</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'title' => 'forget-password',
                'parameter' => 'USERNAME,OTP',
                'subject' => 'Forget Password?',
                'content' => '<p>Hello {{USERNAME}},</p><p>You have recently requested to reset your password please find your OTP below,<br /><strong>Your OTP: {{OTP}}</strong></p><p>If you did not request a password reset, please ignore this email or contact us if you have any questions.</p><p>Thanks!</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);
    }

}
