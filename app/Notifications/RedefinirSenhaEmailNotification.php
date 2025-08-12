<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RedefinirSenhaEmailNotification extends Notification implements ShouldQueue
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

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())->subject('Pharus: Redefinição de senha')->view('mail.auth.redefinir_senha_email', [
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
