<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class KycApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $firstname;
    public $site_url;
    public $site_name;
    public $logo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($firstname, $site_url, $site_name, $logo)
    {
        $this->firstname = $firstname;
        $this->site_url = $site_url;
        $this->site_name = $site_name;
        $this->logo = $logo;
    }

    public function build()
    {
        return $this->view('mail-templates.user.approve_kyc')->with(['firstname' => $this->firstname, 'site_url' => $this->site_url, 'site_name' => $this->site_name, 'logo' => $this->logo]);
    }
}
