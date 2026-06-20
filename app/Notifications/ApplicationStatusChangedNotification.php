<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Domain\Enums\ApplicationStatus;
use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Application $application,
        public ApplicationStatus $previousStatus,
    ) {
        $this->application->loadMissing('jobListing');
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
        $status = $this->application->status->value;

        return (new MailMessage)
            ->subject('Application update: '.$job->title)
            ->line(sprintf('Your application to %s is now: %s.', $job->title, $status))
            ->line('Thank you for your interest.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'application_status_changed',
            'application_id' => $this->application->id,
            'job_listing_id' => $this->application->job_listing_id,
            'job_title' => $this->application->jobListing->title,
            'previous_status' => $this->previousStatus->value,
            'status' => $this->application->status->value,
        ];
    }
}
