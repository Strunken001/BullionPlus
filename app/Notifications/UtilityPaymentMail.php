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
        $date = $data->date;
        $dateTime = $date->format('Y-m-d h:i:s A');

        return (new MailMessage)
            ->greeting(__("Hello") . " " . $user->fullname . " !")
            ->subject(__("UTILITY PAYMENT SUCCESSFUL"))
            ->line(__("Eko Electricity Prepaid - Meter") . ": " . $data->account_number)
            ->line(__("Transaction ID") . ": " . $trx_id)
            ->line(__("CHARGES") . ": ")
            ->line(__("Bill Amount") . ": " . $data->request_amount . "(" . $data->exchange_rate . ")")
            ->line(__("Admin Fee") . ": " . $data->charges)
            ->line(__("Total") . ": " . $data->payable)
            ->line(__("BALANCE") . ": " . ($data->previous_balance) . " -> " . $data->current_balance)
            ->line(__("Token") . ": " . $data->token)
            ->line(__("Date") . ": " . $dateTime)
            ->line(__('Thank you for using BullionPlus!'));
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
