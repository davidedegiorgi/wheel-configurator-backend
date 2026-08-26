<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountCreatedNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Account ANTWHEELS creato')
            ->greeting('Ciao ' . $notifiable->name . ',')
            ->line('Il tuo account ANTWHEELS è stato creato correttamente.')
            ->line('Da ora puoi salvare configurazioni, generare preventivi e gestire il tuo profilo personale.')
            ->line('Se non hai richiesto tu questa registrazione, contatta subito il supporto ANTWHEELS.');
    }
}
