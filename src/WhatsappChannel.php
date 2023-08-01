<?php

namespace Bagoesz21\LaravelNotifWaWeb;

use Illuminate\Notifications\Notification;

use Bagoesz21\LaravelNotifWaWeb\WhatsappService;

class WhatsappChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $phone = $notifiable->routeNotificationFor('whatsapp', $notification)) {
            return;
        }
        $message = $notification->toWhatsapp($phone, $notification);

        $wa = WhatsappService::make();

        $wa->message()->sendText($phone, $message);
    }
}
