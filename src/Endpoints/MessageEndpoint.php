<?php

namespace Bagoesz21\LaravelNotifWaWeb\Endpoints;

use Bagoesz21\LaravelNotifWaWeb\Endpoints\BaseEndpoint;
use Illuminate\Support\Arr;

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

    /**
     * @param string $type. replyButton, urlButton, callButton
     * @param string $title
     * @param string $payload
     * @return array
     */
    public function createButton($type = 'replyButton', $title, $payload)
    {
        return [
            'type' => $type,
            'title' => $title,
            'payload' => $payload
        ];
    }

    public function sendButtonMessage($uri, $phone, string $text, array $buttons, string $footerText = null, array $others = [])
    {
        $this->setPhone($phone);

        $payloads = [
            'id' => $this->phoneLink,
        ];

        $cleanButtons = [];
        foreach ($buttons as $key => $button) {
            $cleanButtons[] = $this->createButton(Arr::get($button, 'type'), Arr::get($button, 'title'), Arr::get($button, 'payload'));
        }

        $btndata = [
            'text' => $text,
            'buttons' => $cleanButtons,
            'footerText' => $footerText
        ];
        if(!empty($others)){
            $btndata = array_merge($btndata, $others);
        }
        $payloads['btndata'] = $btndata;

        return $this->makeRequest($uri, $payloads, 'POST');
    }

    public function sendButton($phone, string $text, array $buttons, string $footerText = null)
    {
        return $this->sendButtonMessage('button', $phone, $text, $buttons, $footerText);
    }

    /**
     * @param array $image
     * [
     *      'image' => 'http://localhost',
     *      'mediaType' => 'image'
     *      'mimeType' => 'image/jpeg'
     * ]
     */
    public function sendButtonWithMedia($phone, string $text, array $buttons, string $footerText = null, array $image = [])
    {
        return $this->sendButtonMessage('MediaButton', $phone, $text, $buttons, $footerText, $image);
    }

    public function sendContact($phone, string $contactPhone, string $fullName, string $displayName, string $organization = null)
    {
        $this->setPhone($phone);

        $payloads = [
            'id' => $this->phoneLink,
        ];

        $vcard = [
            'fullName' => $fullName,
            'displayName' => $displayName,
            'organization' => $organization,
            'phoneNumber' => $contactPhone,
        ];
        $payloads['vcard'] = $vcard;

        return $this->makeRequest('contact', $payloads, 'POST');
    }

    /**
     * @param string $title
     * @param string $rows
     * @return array
     */
    public function createSectionList($title, array $rows)
    {
        return [
            'title' => $title,
            'rows' => $this->createRowSection(Arr::get($rows, 'title'), Arr::get($rows, 'description'), Arr::get($rows, 'rowId')),
        ];
    }

    /**
     * @param string $title
     * @param string $description
     * @param string $rowId
     * @return array
     */
    public function createRowSection($title, $description, $rowId = 'string')
    {
        return [
            'title' => $title,
            'description' => $description,
            'rowId' => $rowId,
        ];
    }

    public function sendListMessage($phone, string $buttonText, string $text, string $title, string $description, array $sections)
    {
        $this->setPhone($phone);

        $payloads = [
            'id' => $this->phoneLink,
        ];

        $msgdata = [
            'buttonText' => $buttonText,
            'text' => $text,
            'title' => $title,
            'description' => $description,
            'listType' => 0
        ];
        $payloads['msgdata'] = $msgdata;

        $cleanSections = [];
        foreach ($sections as $key => $section) {
            $cleanSections[] = $this->createSectionList(Arr::get($section, 'title'), Arr::get($section, 'rows'));
        }

        $payloads['sections'] = $cleanSections;

        return $this->makeRequest('contact', $payloads, 'POST');
    }

    public function setStatus($status)
    {
        $payloads = [
            'status' => $status,
        ];

        return $this->makeRequest('setstatus', $payloads, 'PUT');
    }
}
