<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasicSettings extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'id'                        => 'integer',
        'site_name'                 => 'string',
        'site_title'                => 'string',
        'base_color'                => 'string',
        'timezone'                  => 'string',
        'site_logo_dark'            => 'string',
        'site_logo'                 => 'string',
        'site_fav_dark'             => 'string',
        'site_fav'                  => 'string',
        'preloader_image'           => 'string',
        'mail_activity'             => 'object',
        'push_notification_activity'=> 'object',
        'broadcast_activity'        => 'object',
        'sms_activity'              => 'object',
        'web_version'               => 'string',
        'admin_version'             => 'string',
        'otp_exp_seconds'           => 'integer',
        'user_registration'         => 'integer',
        'secure_password'           => 'integer',
        'agree_policy'              => 'integer',
        'force_ssl'                 => 'integer',
        'sms_verification'          => 'integer',
        'email_notification'        => 'integer',
        'push_notification'         => 'integer',
        'kyc_verification'          => 'integer',
        'mail_config'               => 'object',
        'sms_config'                => 'object',
        'push_notification_config'  => 'object',
        'broadcast_config'          => 'object',
    ];


    public function mailConfig() {

    }
}
