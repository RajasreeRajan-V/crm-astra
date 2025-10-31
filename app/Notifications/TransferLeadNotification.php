<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransferLeadNotification extends Notification
{
    use Queueable;

    protected $leads;

    public function __construct($leads)
    {
        $this->leads = $leads;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $leadNames = implode(', ', $this->leads->pluck('name')->toArray()); // Join lead names

        return (new MailMessage)
        ->subject('Leads Transferred to You')
        ->line('The following leads have been transferred to you:')
        ->line($leadNames)
        ->action('View Leads', route('agent.leads')) // ✅ Using named route here
        ->line('Please review these new leads.');
    }
}
