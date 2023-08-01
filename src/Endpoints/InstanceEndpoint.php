<?php

namespace Bagoesz21\LaravelNotifWaWeb\Endpoints;

use Bagoesz21\LaravelNotifWaWeb\Endpoints\BaseEndpoint;

class InstanceEndpoint extends BaseEndpoint
{
    protected $config = [
        'uri' => 'instance'
    ];

    public function init()
    {
        $payloads = [
            'token' => $this->getConfig('token')
        ];

        return $this->makeRequest('init', $payloads, 'GET');
    }

    public function scanQr()
    {
        return $this->makeRequest('qr', [], 'POST');
    }

    public function scanQrBase64()
    {
        return $this->makeRequest('qrbase64', [], 'GET');
    }

    public function info()
    {
        return $this->makeRequest('info', [], 'GET');
    }

    public function restore()
    {
        return $this->makeRequest('restore', [], 'GET');
    }

    public function delete()
    {
        return $this->makeRequest('delete', [], 'DELETE');
    }

    public function logout()
    {
        return $this->makeRequest('logout', [], 'DELETE');
    }

    public function list()
    {
        return $this->makeRequest('list', [], 'GET');
    }
}
