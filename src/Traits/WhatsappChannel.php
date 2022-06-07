<?php

namespace Bagoesz21\LaravelNotifWaWeb\Traits;

trait WhatsappChannel
{
    /**
     * Build whatsapp message text before send
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function toWhatsapp($notifiable, $notification)
    {
        $result = $this->getTitle();
        $result .= '\n\n';
        $result .= $this->getMessageAsPlainText();
        return $result;
    }
}
