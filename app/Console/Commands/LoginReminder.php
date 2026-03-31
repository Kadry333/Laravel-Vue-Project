<?php

namespace App\Console\Commands;

use App\Notifications\LoginReminderNotification;
use App\Models\User;
use Illuminate\Console\Command;

class LoginReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:login-reminder';



    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a reminder email to clients who have not logged in for over a month';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Find approved clients who haven't logged in for more than 30 days
        $clients = User::role('client')
            ->where('is_approved', true)
            ->where(function ($query) {
                $query
                    ->where('last_login_at', '<', now()->subMonth())
                    ->orWhere(function ($q) {
                        $q->whereNull('last_login_at')
                            ->where('created_at', '<', now()->subMonth());
                    });
            })
            ->get();

        if ($clients->isEmpty()) {
            $this->info('No clients to remind.');
            return;
        }

        foreach ($clients as $client) {
            $client->notify(new LoginReminderNotification());
        }

        $this->info("Login reminder sent to {$clients->count()} client(s).");
    }
}
