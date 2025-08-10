<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserForgotPasswordCode extends Mailable
{
    use Queueable, SerializesModels;

    public $username;
    public $pwdCode;
    public $site_url;
    public $site_name;
    public $logo;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($username, $pwdCode, $site_url, $site_name, $logo)
    {
        $this->username = $username;
        $this->pwdCode = $pwdCode;
        $this->site_url = $site_url;
        $this->site_name = $site_name;
        $this->logo = $logo;
    }

    public function build()
    {
        return $this->subject('Password Reset Request')->view('mail-templates.user.forgot_password')->with(['username' =>  $this->username, 'code' => $this->pwdCode, 'site_url' => $this->site_url, 'site_name' => $this->site_name, 'logo' => $this->logo]);
    }
}
