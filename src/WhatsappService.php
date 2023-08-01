<?php

namespace Bagoesz21\LaravelNotifWaWeb;

use Illuminate\Support\Arr;

use Bagoesz21\LaravelNotifWaWeb\Endpoints\InstanceEndpoint;
use Bagoesz21\LaravelNotifWaWeb\Endpoints\GroupEndpoint;
use Bagoesz21\LaravelNotifWaWeb\Endpoints\MessageEndpoint;
use Bagoesz21\LaravelNotifWaWeb\Endpoints\MiscEndpoint;

class WhatsappService
{
    /** @var array */
    protected $config = [];

    /** @var string */
    protected $sessionID = '';

    public function __construct()
    {
        $this->loadFromConfig();
    }

    /**
     * Static
     *
     * @return static
     */
    public static function make(){
        $class = get_called_class();
        return (new $class());
    }

    /**
     * @param array $config
     * @return self
     */
    public function setConfig($config = [])
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @param string $key
     * @param string|null $default
     * @return string
     */
    public function getConfig($key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    public function loadFromConfig()
    {
        $this->setConfig(config('whatsapp'));
        $this->sessionID = $this->getConfig('session_id');
        return $this;
    }

    private function getConfigEndpoint()
    {
        return [
            'token' => $this->getConfig('token'),
            'url' => $this->getConfig('url'),
            'country' => $this->getConfig('country.default'),
        ];
    }

    public function instance()
    {
        return InstanceEndpoint::make($this->sessionID, $this->getConfigEndpoint());
    }

    public function group()
    {
        return GroupEndpoint::make($this->sessionID, $this->getConfigEndpoint());
    }

    public function message()
    {
        return MessageEndpoint::make($this->sessionID, $this->getConfigEndpoint());
    }

    public function misc()
    {
        return MiscEndpoint::make($this->sessionID, $this->getConfigEndpoint());
    }
}
