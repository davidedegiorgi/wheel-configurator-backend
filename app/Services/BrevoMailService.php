<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class BrevoMailService
{
    public function sendAccountCreated(User $user): void
    {
        $this->send(
            $user->email,
            trim($user->name . ' ' . ($user->last_name ?? '')),
            'Account Antwheels creato',
            [
                'Ciao ' . $user->name . ',',
                'Il tuo account Antwheels è stato creato correttamente.',
                'Da ora puoi salvare configurazioni, generare preventivi e gestire il tuo profilo personale.',
                'Se non hai richiesto tu questa registrazione, contatta subito il supporto Antwheels.',
            ]
        );
    }

    public function sendAccountDeleted(string $email, string $name): void
    {
        $this->send(
            $email,
            $name,
            'Account Antwheels eliminato',
            [
                'Ciao ' . $name . ',',
                'Ti confermiamo che il tuo account Antwheels è stato eliminato.',
                'Le configurazioni e i preventivi collegati all\'account sono stati rimossi dai nostri sistemi applicativi.',
                'Se non hai richiesto tu questa operazione, contatta subito il supporto Antwheels.',
            ]
        );
    }

    public function sendPasswordReset(User $user, string $token): void
    {
        $frontendUrl = rtrim((string) config('app.frontend_url'), '/');
        $resetUrl = $frontendUrl . '/reset-password?' . http_build_query([
            'token' => $token,
            'email' => $user->email,
        ]);

        $this->send(
            $user->email,
            trim($user->name . ' ' . ($user->last_name ?? '')),
            'Reimposta la password',
            [
                'Ciao ' . $user->name . ',',
                'Abbiamo ricevuto una richiesta per reimpostare la password del tuo account Antwheels.',
                'Apri questo link per scegliere una nuova password:',
                $resetUrl,
                'Il link resta valido per 60 minuti.',
                'Se non hai richiesto tu il recupero password, puoi ignorare questa email.',
            ],
            '<div style="font-family: Arial, sans-serif; color: #111; line-height: 1.6;">'
                . '<p>Ciao ' . e($user->name) . ',</p>'
                . '<p>Abbiamo ricevuto una richiesta per reimpostare la password del tuo account Antwheels.</p>'
                . '<p style="margin: 28px 0;">'
                . '<a href="' . e($resetUrl) . '" style="display: inline-block; background: #050505; color: #ffffff; padding: 13px 22px; border-radius: 6px; text-decoration: none; font-weight: 700;">Reimposta password</a>'
                . '</p>'
                . '<p>Se il pulsante non funziona, copia e incolla questo link nel browser:</p>'
                . '<p style="word-break: break-all;"><a href="' . e($resetUrl) . '" style="color: #111;">' . e($resetUrl) . '</a></p>'
                . '<p>Il link resta valido per 60 minuti.</p>'
                . '<p>Se non hai richiesto tu il recupero password, puoi ignorare questa email.</p>'
                . '</div>'
        );
    }

    public function sendTest(string $email): void
    {
        $this->send($email, null, 'Test email Antwheels', [
            'Test invio email da Antwheels.',
        ]);
    }

    private function send(string $email, ?string $name, string $subject, array $lines, ?string $htmlContent = null): void
    {
        $apiKey = config('services.brevo.key');

        if (!$apiKey) {
            throw new RuntimeException('BREVO_API_KEY non configurata.');
        }

        $fromEmail = config('mail.from.address');
        $fromName = config('mail.from.name');
        $textContent = implode("\n\n", $lines);

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'api-key' => $apiKey,
            'content-type' => 'application/json',
        ])->timeout((int) env('BREVO_TIMEOUT', 10))->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'name' => $fromName,
                'email' => $fromEmail,
            ],
            'to' => [
                array_filter([
                    'email' => $email,
                    'name' => $name ?: null,
                ]),
            ],
            'subject' => $subject,
            'textContent' => $textContent,
            'htmlContent' => $htmlContent ?? '<p>' . implode('</p><p>', array_map('e', $lines)) . '</p>',
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Brevo API error ' . $response->status() . ': ' . $response->body());
        }
    }
}
