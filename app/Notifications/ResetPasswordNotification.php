<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
{
    $url = url(route('password.reset', [
        'token' => $this->token,
        'email' => $notifiable->getEmailForPasswordReset(),
    ], false));

    return (new \Illuminate\Notifications\Messages\MailMessage)
        ->subject('Restablecer contraseña | CIDE SENA')
        ->markdown('emails.auth.reset-password', [
            'url'  => $url,
            'user' => $notifiable,
        ]);
}
}
