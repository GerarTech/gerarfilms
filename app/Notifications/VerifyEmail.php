<?php
namespace AppNotifications;
use IlluminateBusQueueable;
use IlluminateNotificationsNotification;
use IlluminateContractsQueueShouldQueue;
use IlluminateNotificationsMessagesMailMessage;
use IlluminateSupportCarbon;
use IlluminateSupportFacadesURL;
use IlluminateSupportFacadesLang;
use IlluminateAuthNotificationsVerifyEmail as VerifyEmailBase;

class VerifyEmail extends VerifyEmailBase
{
//    use Queueable;

    // change as you want
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable);
        }
        return (new MailMessage)
            ->subject(Lang::getFromJson('Verify Email Address'))
            ->line(Lang::getFromJson('Please click the button below to verify your email address.'))
            ->action(
                Lang::getFromJson('Verify Email Address'),
                $this->verificationUrl($notifiable)
            )
            ->line(Lang::getFromJson('If you did not create an account, no further action is required.'));
    }
}