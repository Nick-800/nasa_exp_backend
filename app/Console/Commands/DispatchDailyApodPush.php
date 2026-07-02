<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SendApodPushNotification;
use App\Models\User;
use App\Services\NasaApiService;
use Illuminate\Console\Command;

final class DispatchDailyApodPush extends Command
{
    protected $signature = 'app:push-daily-apod';
    protected $description = 'Dispatch daily APOD push notifications to users based on their notification_time';

    public function handle(NasaApiService $nasaApi): void
    {
        try {
            $apod = $nasaApi->getApod();
        } catch (\Exception $e) {
            $this->error('Failed to fetch APOD: ' . $e->getMessage());
            return;
        }

        if (! isset($apod['title'])) {
            $this->error('APOD missing title.');
            return;
        }

        $currentHour = now()->format('H');
        
        // Use standard SQLite/MySQL compatible extraction for hour if possible
        // SQLite doesn't natively support HOUR(), but since this is small we can use whereLike
        // Or simply raw for MySQL, but for compatibility let's pull those matching the hour:
        $likePattern = "% {$currentHour}:%:__";
        
        $users = User::whereNotNull('fcm_token')
            ->where('notification_time', 'LIKE', "{$currentHour}:%:__")
            ->orWhere('notification_time', 'LIKE', "0{$currentHour}:%:__")
            ->get();

        // Let's filter in PHP to be absolutely safe across databases
        $usersToNotify = User::whereNotNull('fcm_token')->get()->filter(function ($user) use ($currentHour) {
            return substr($user->notification_time, 0, 2) === $currentHour;
        });

        $this->info("Found {$usersToNotify->count()} users to notify at hour {$currentHour}.");

        foreach ($usersToNotify as $user) {
            SendApodPushNotification::dispatch(
                $user, 
                $apod['title'], 
                $apod['url'] ?? ''
            );
        }

        $this->info('Push notification jobs dispatched successfully.');
    }
}
