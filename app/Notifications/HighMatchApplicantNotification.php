<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HighMatchApplicantNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public JobApplication $application) {}

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
        $job = $this->application->jobListing;
        $applicant = $this->application->user;

        return (new MailMessage)
            ->subject("Strong Match: {$applicant->name} for {$job->title}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Our AI screening found a strong match ({$this->application->match_score}% fit) for your \"{$job->title}\" posting.")
            ->line($this->application->ai_summary ?? '')
            ->action('Review Application', route('applications.for-job', $job))
            ->line('This candidate may be worth prioritizing.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
