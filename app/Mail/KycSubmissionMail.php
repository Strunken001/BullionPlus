<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class KycSubmissionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $username;
    public $site_url;
    public $site_name;
    public $logo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($username, $site_url, $site_name, $logo)
    {
        $this->username = $username;
        $this->site_url = $site_url;
        $this->site_name = $site_name;
        $this->logo = $logo;
    }

    public function build()
    {
        return $this->view('mail-templates.user.submit_kyc')->with(['username' => $this->username, 'site_url' => $this->site_url, 'site_name' => $this->site_name, 'logo' => $this->logo]);
    }
}
