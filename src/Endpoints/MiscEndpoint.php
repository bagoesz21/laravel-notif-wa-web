<?php

namespace Bagoesz21\LaravelNotifWaWeb\Endpoints;

use Bagoesz21\LaravelNotifWaWeb\Endpoints\BaseEndpoint;

class MiscEndpoint extends BaseEndpoint
{
    protected $config = [
        'uri' => 'misc'
    ];

    public function onwhatsapp($phone)
    {
        $this->setPhone($phone);

        $payloads = [
            'id' => $this->phoneLink,
        ];

        return $this->makeRequest('onwhatsapp', $payloads, 'GET');
    }

    public function downProfile($phone)
    {
        $this->setPhone($phone);

        $payloads = [
            'id' => $this->phoneLink,
        ];

        return $this->makeRequest('downProfile', $payloads, 'GET');
    }

    public function getUserStatus($phone)
    {
        $this->setPhone($phone);

        $payloads = [
            'id' => $this->phoneLink,
        ];

        return $this->makeRequest('getStatus', $payloads, 'GET');
    }

    public function blockUser($phone)
    {
        $this->setPhone($phone);

        $payloads = [
            'id' => $this->phoneLink,
        ];

        return $this->makeRequest('blockUser', $payloads, 'GET');
    }

    public function updateProfilePicture($phone, $url)
    {
        $this->setPhone($phone);

        $payloads = [
            'id' => $this->phoneLink,
            'url' => $url,
        ];

        return $this->makeRequest('updateProfilePicture', $payloads, 'POST');
    }
}
