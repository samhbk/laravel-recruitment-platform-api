<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class NewApplicationReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Application $application,
    ) {
        $this->application->loadMissing('jobListing', 'user');
    }

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $job = $this->application->jobListing;
        $candidate = $this->application->user;

        return (new MailMessage)
            ->subject('New application: '.$job->title)
            ->line(sprintf('%s applied to %s.', $candidate->name, $job->title))
            ->line('Sign in to review applications for this role.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_application',
            'application_id' => $this->application->id,
            'job_listing_id' => $this->application->job_listing_id,
            'job_title' => $this->application->jobListing->title,
            'candidate_name' => $this->application->user->name,
        ];
    }
}
