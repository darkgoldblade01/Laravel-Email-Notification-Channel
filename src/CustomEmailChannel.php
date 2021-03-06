<?php

namespace NotificationChannels\CustomEmail;

use Illuminate\Support\Facades\Mail;
use NotificationChannels\CustomEmail\Exceptions\CouldNotSendNotification;
use NotificationChannels\CustomEmail\Events\MessageWasSent;
use NotificationChannels\CustomEmail\Events\SendingMessage;
use Illuminate\Notifications\Notification;

class CustomEmailChannel
{
    protected $config;

    public function __construct()
    {
        $this->config = config('services.custom_email');
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     *
     * @throws CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $to = $notifiable->routeNotificationFor('customemail')) {
            return;
        }

        $message = $notification->toCustomEmail($notifiable);

        if (is_string($message)) {
            $message = new CustomEmailMessage($message);
        }

        Mail::to($to)->send(new $this->config['MailClass']($message->getContent()));
    }
}
