<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class SetPasswordNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = URL::temporarySignedRoute(
            'password.reset',
            now()->addDays(1),
            ['token' => \Illuminate\Support\Facades\Password::createToken($notifiable)]
        );

        return (new MailMessage)
            ->subject('Configura tu contraseña')
            ->line('Se ha creado una cuenta para ti.')
            ->line('Por favor, haz clic en el botón de abajo para establecer tu contraseña:')
            ->action('Establecer Contraseña', $url)
            ->line('Este enlace expirará en 24 horas.');
    }
}