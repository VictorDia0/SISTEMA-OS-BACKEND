<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificacaoEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function via(object $notifiable): array
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
