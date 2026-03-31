<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('We miss you at Grand Luxury!')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('We noticed you haven\'t visited us in a while.')
            ->line('We miss you! Come back and explore our available rooms.')
            ->action('Log in now', url('/login'))
            ->line('We look forward to seeing you again.')
            ->salutation('The Grand Luxury Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => 'We miss you! You haven\'t logged in for over a month.',
            'sent_at' => now()->toDateTimeString(),
        ];
    }
}
