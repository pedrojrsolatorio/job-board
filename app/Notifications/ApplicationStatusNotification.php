<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ApplicationStatusNotification extends Notification
{
    public function __construct(public JobApplication $application) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $job    = $this->application->jobListing;
        $status = ucfirst($this->application->status);

        return (new MailMessage)
            ->subject('Application Update: ' . $job->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your application for "' . $job->title . '" has been updated.')
            ->line('New Status: ' . $status)
            ->action('View My Applications', route('my-applications'))
            ->line('Thank you for using Job Board!');
    }
}
