<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewApplicationNotification extends Notification
{
    public function __construct(public JobApplication $application) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $job       = $this->application->jobListing;
        $applicant = $this->application->user;

        return (new MailMessage)
            ->subject('New Application: ' . $job->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($applicant->name . ' has applied for your job: ' . $job->title)
            ->action('View Application', route('applications.for-job', $job))
            ->line('Log in to your employer dashboard to review the application.');
    }
}
