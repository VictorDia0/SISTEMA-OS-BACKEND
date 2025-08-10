<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificacaoEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $url)
    {

    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage())
        ->subject('Ordem de Serviço: Verificação de email')
        ->view('mail.auth.verificacao_email', [
            'notifiable' => $notifiable,
            'url' => $this->url,
        ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
