<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class StaffNotification extends Notification
{
    public function __construct(
        protected string $title,
        protected string $message,
        protected array $payload = [],
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->payload['type'] ?? 'general',
            'reference' => $this->payload['reference'] ?? md5($this->title . $this->message . microtime(true)),
            'url' => $this->payload['url'] ?? null,
            'related_id' => $this->payload['related_id'] ?? null,
            'related_type' => $this->payload['related_type'] ?? null,
            'icon' => $this->payload['icon'] ?? 'fas fa-bell',
            'action_label' => $this->payload['action_label'] ?? 'View',
        ];
    }
}
