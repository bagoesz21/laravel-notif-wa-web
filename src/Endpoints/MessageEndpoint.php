<?php

namespace Bagoesz21\LaravelNotifWaWeb\Endpoints;

use Bagoesz21\LaravelNotifWaWeb\Endpoints\BaseEndpoint;

class MessageEndpoint extends BaseEndpoint
{
    protected $config = [
        'uri' => 'message'
    ];

    public function sendText($phone, $message)
    {
        $this->setPhone($phone);

        $payloads = [
            'id' => $this->phoneLink,
            'message' => $message
        ];

        return $this->makeRequest('text', $payloads, 'POST');
    }

    protected function sendFile($uri, $phone, $file, $others = [])
    {
        $this->setPhone($phone);

        $payloads = [
            'id' => $this->phoneLink,
            'file' => $file,
        ];

        if(!empty($others)){
            $payloads = array_merge($payloads, $others);
        }

        $files = [
            'file' => $file,
        ];

        return $this->makeRequest($uri, $payloads, 'POST', $files);
    }

    public function sendImage($phone, $file, $caption)
    {
        $payloads = [
            'caption' => $caption
        ];
        return $this->sendFile('image', $phone, $file, $payloads);
    }

    public function sendVideo($phone, $file, $caption)
    {
        $payloads = [
            'caption' => $caption
        ];
        return $this->sendFile('video', $phone, $file, $payloads);
    }

    public function sendAudio($phone, $file)
    {
        return $this->sendFile('audio', $phone, $file);
    }

    public function sendDocument($phone, $file, $filename = '')
    {
        $payloads = [
            'filename' => $filename
        ];
        return $this->sendFile('doc', $phone, $file, $payloads);
    }
}
