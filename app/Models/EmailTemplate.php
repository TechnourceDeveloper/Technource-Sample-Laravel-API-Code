<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{

    protected $table = 'email_template';

    const ACCOUNT_VERIFICATION = 'account-verification ';
    const FORGET_PASSWORD = 'forget-password';
    const RESEND_OTP = 'resend-otp';
    const EMAIL_VERIFICATION = 'email-verification';
}
