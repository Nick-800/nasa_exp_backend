<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

final class SendApodPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        private readonly User $user,
        private readonly string $apodTitle,
        private readonly string $apodImageUrl
    ) {}

    public function handle(): void
    {
        if (! $this->user->fcm_token) {
            return;
        }

        $fcmServerKey = config('services.fcm.key');
        if (! $fcmServerKey) {
            logger()->error('FCM server key is missing.');
            return;
        }

        $response = Http::withHeaders([
            'Authorization' => 'key=' . $fcmServerKey,
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', [
            'to' => $this->user->fcm_token,
            'notification' => [
                'title' => 'Astronomy Picture of the Day',
                'body' => "Check out today's APOD: {$this->apodTitle}",
                'image' => $this->apodImageUrl,
            ],
            'data' => [
                'type' => 'apod',
            ]
        ]);

        if ($response->failed()) {
            logger()->error("FCM Push failed for user {$this->user->id}", [
                'response' => $response->json(),
            ]);
            
            $response->throw();
        }
    }

    public function failed(\Throwable $e): void
    {
        logger()->error('SendApodPushNotification job failed permanently.', [
            'user_id' => $this->user->id,
            'error' => $e->getMessage()
        ]);
    }
}
