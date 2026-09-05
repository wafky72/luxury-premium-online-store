<?php

namespace App\Jobs;

use App\Models\NotificationsLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 30; // seconds between retries

    public function __construct(
        protected string $phone,
        protected string $message,
        protected int $logId
    ) {}

    public function handle(): void
    {
        $response = Http::withoutVerifying()->timeout(10)->post('https://bulksmsbd.net/api/smsapi', [
            'api_key'  => config('services.bulksmsbd.api_key'),
            'type'     => 'text',
            'number'   => $this->phone,
            'senderid' => config('services.bulksmsbd.sender_id', 'TORYCROWN'),
            'message'  => $this->message,
        ]);

        $success = $response->successful() && ($response->json('response_code') === 202);

        NotificationsLog::find($this->logId)?->update([
            'status'  => $success ? 'sent' : 'failed',
            'sent_at' => $success ? now() : null,
            'error'   => $success ? null : $response->body(),
        ]);

        if (! $success) {
            Log::error('SMS send failed', ['phone' => $this->phone, 'response' => $response->body()]);
            $this->fail('SMS delivery failed');
        }
    }
}
