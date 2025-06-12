<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait LogsUserAction
{
    public function logUserAction(string $channel, string $message, array $context = []): void
    {
        $user = auth()->check() ? auth()->user()->email : 'system';
        $context = array_merge(['performed_by' => $user], $context);

        Log::channel($channel)->info($message, $context);
    }
}
