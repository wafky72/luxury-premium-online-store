<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        protected string $recipient,
        protected string $subject,
        protected string $content
    ) {}

    public function handle(): void
    {
        try {
            Mail::raw($this->content, function ($message) {
                $message->to($this->recipient)
                        ->subject($this->subject);
            });
            
            Log::info("Email sent to {$this->recipient}");
        } catch (\Exception $e) {
            Log::error("Email failed to {$this->recipient}: " . $e->getMessage());
            $this->fail($e);
        }
    }
}
