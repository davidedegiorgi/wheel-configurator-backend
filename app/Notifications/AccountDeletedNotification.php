<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountDeletedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $name) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Account ANTWHEELS eliminato')
            ->greeting('Ciao ' . $this->name . ',')
            ->line('Ti confermiamo che il tuo account ANTWHEELS è stato eliminato.')
            ->line('Le configurazioni e i preventivi collegati all\'account sono stati rimossi dai nostri sistemi applicativi.')
            ->line('Se non hai richiesto tu questa operazione, contatta subito il supporto ANTWHEELS.');
    }
}
