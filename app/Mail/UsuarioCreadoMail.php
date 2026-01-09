<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UsuarioCreadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $password;

    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function build()
    {
        return $this
            ->subject('Acceso a la plataforma CIDE')
            ->view('emails.usuario_creado');
    }
}
