<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = URL::temporarySignedRoute(
            'auth.email.verify',
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->email),
            ]
        );

        return (new MailMessage)
            ->subject('Conferma il tuo account ANTWHEELS')
            ->greeting('Ciao ' . $notifiable->name . ',')
            ->line('Grazie per esserti registrato ad ANTWHEELS Configurator.')
            ->line('Conferma il tuo indirizzo email per attivare l\'account e salvare configurazioni e preventivi.')
            ->action('Conferma email', $verificationUrl)
            ->line('Il link resta valido per 60 minuti. Se non hai richiesto tu la registrazione, puoi ignorare questa email.');
    }
}
