<?php

namespace App\Notifications;

use App\Models\Provider;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProviderRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(protected Provider $provider)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => __('notifications.provider.rejected'),
            'provider_id' => $this->provider->id,
            'status' => $this->provider->status?->value,
        ];
    }
}
