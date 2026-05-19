<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    /**
     * Build the mail representation using the branded ResQFlow email template.
     */
    public function toMail($notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject('Reset Your ResQFlow Access Credentials')
            ->view('emails.reset-password', [
                'url'        => $url,
                'notifiable' => $notifiable,
            ]);
    }
}
