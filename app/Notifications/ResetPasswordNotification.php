<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $frontendUrl = rtrim((string) config('app.frontend_url'), '/');
        $resetUrl = $frontendUrl . '/reset-password?' . http_build_query([
            'token' => $this->token,
            'email' => $notifiable->email,
        ]);

        return (new MailMessage)
            ->subject('Reimposta la password')
            ->greeting('Ciao ' . $notifiable->name . ',')
            ->line('Abbiamo ricevuto una richiesta per reimpostare la password del tuo account ANTWHEELS.')
            ->action('Reimposta password', $resetUrl)
            ->line('Il link resta valido per 60 minuti.')
            ->line('Se non hai richiesto tu il recupero password, puoi ignorare questa email.');
    }
}
