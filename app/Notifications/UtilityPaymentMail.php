<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UtilityPaymentMail extends Notification
{
    use Queueable;

    public $user;
    public $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $data)
    {
        $this->user = $user;
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $user = $this->user;
        $data = $this->data;
        $trx_id = $this->data->trx_id;
        $date = Carbon::now();
        $dateTime = $date->format('Y-m-d h:i:s A');

        return (new MailMessage)
            ->greeting(__("Hello") . " " . $user->fullname . " !")
            ->subject(__("Utility Payment For") . " " . $data->operator_name . ' (' . $data->account_number . ' )')
            ->line(__("Utility Payment request successful") . " " . $data->account_number . " ," . __("details of payment") . ":")
            ->line(__("web_trx_id") . ": " . $trx_id)
            ->line(__("request Amount") . ": " . $data->request_amount)
            ->line(__("Exchange Rate") . ": " . $data->exchange_rate)
            ->line(__("Fees & Charges") . ": " . $data->charges)
            ->line(__("Total Payable Amount") . ": " . $data->payable)
            ->line(__("Current Balance") . ": " . $data->current_balance)
            ->line(__("Status") . ": " . $data->status)
            ->line(__("Date And Time") . ": " . $dateTime)
            ->line(__("Token") . ": " . $data->token)
            ->line(__('Thank you for using our application!'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
