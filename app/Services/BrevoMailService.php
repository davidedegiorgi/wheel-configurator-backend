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
            '<p>Ciao ' . e($user->name) . ',</p>'
                . '<p>Abbiamo ricevuto una richiesta per reimpostare la password del tuo account Antwheels.</p>'
                . '<p><a href="' . e($resetUrl) . '">Reimposta password</a></p>'
                . '<p>Il link resta valido per 60 minuti.</p>'
                . '<p>Se non hai richiesto tu il recupero password, puoi ignorare questa email.</p>'
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
        $footerLines = [
            'Messaggio automatico inviato da Antwheels. Ti chiediamo di non rispondere a questa email.',
            'Contatti: +39 3283162784 - paolascorrano972@gmail.com',
        ];
        $textContent = implode("\n\n", [...$lines, ...$footerLines]);
        $htmlContent = $this->withFooter($htmlContent ?? '<p>' . implode('</p><p>', array_map('e', $lines)) . '</p>');

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
            'htmlContent' => $htmlContent,
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Brevo API error ' . $response->status() . ': ' . $response->body());
        }
    }

    private function withFooter(string $htmlContent): string
    {
        return $htmlContent
            . '<hr style="border: 0; border-top: 1px solid #e5e5e5; margin: 28px 0 16px;">'
            . '<p style="font-size: 13px; color: #666; line-height: 1.5;">'
            . 'Messaggio automatico inviato da Antwheels. Ti chiediamo di non rispondere a questa email.'
            . '</p>'
            . '<p style="font-size: 13px; color: #666; line-height: 1.5;">'
            . 'Contatti: <a href="tel:+393283162784" style="color: #111;">+39 3283162784</a> · '
            . '<a href="mailto:paolascorrano972@gmail.com" style="color: #111;">paolascorrano972@gmail.com</a>'
            . '</p>';
    }
}
