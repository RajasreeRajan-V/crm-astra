<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Lead;
use Carbon\Carbon;

class FollowUpNotification extends Notification
{
    use Queueable;

    protected $leads;

    /**
     * Create a new notification instance.
     */
    public function __construct($leads)
    {
        $this->leads = $leads;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $followUpDate = Carbon::today()->toDateString(); // Today's date

        $mailMessage = (new MailMessage)
            ->subject('Todays followup reminder')
            ->line('You have the following follow-ups scheduled for today (' . $followUpDate . '):')
            ->line('--------------------------------------------');

        // Loop through leads that need follow-ups today
        foreach ($this->leads as $lead) {
            $mailMessage->line('Lead: ' . $lead->name . ' - Contact: ' . $lead->contact)
                        ->line('Scheduled Follow-Up Time: ' . Carbon::parse($lead->follow_up_date)->format('g:i A'));
        }

        return $mailMessage->action('View Follow-Up Details', url('/leads/follow-ups'));
    }
}
