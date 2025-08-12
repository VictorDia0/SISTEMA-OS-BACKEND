<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlertaAlteracaoSenhaEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Pharus: Alteração de senha')
            ->view('mail.auth.alerta_alteracao_senha_email', [
                'notifiable' => $notifiable,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
                //
            ];
    }
}
