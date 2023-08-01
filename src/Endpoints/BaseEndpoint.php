<?php

namespace Bagoesz21\LaravelNotifWaWeb\Endpoints;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

use Bagoesz21\LaravelNotifWaWeb\Request;

use Propaganistas\LaravelPhone\PhoneNumber;

class BaseEndpoint
{
    /** @var string */
    protected $instanceKey = '';

    /** @var string */
    protected $phone = '';
    protected $phoneLink = '';

    protected $config = [
        'url' => '',
        'uri' => '',
        'country' => 'ID'
    ];

    /**
     * @param string|null $key
     * @param array $config
     */
    public function __construct($key = null, array $config = [])
    {
        $this->setInstanceKey($key);
        $this->setConfig($config);
    }

    /**
     * Static
     *
     * @param string|null $key
     * @param array $config
     * @return static
     */
    public static function make($key = null, $config = []){
        $class = get_called_class();
        return (new $class($key, $config));
    }

    public function setConfig(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }

    public function getConfig($key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    public function setInstanceKey($key)
    {
        if(empty($key))return $this;
        $this->instanceKey = $key;
        return $this;
    }

    public function getInstanceKey()
    {
        return $this->instanceKey;
    }

    /**
     * Get default param queries
     */
    protected function getParams()
    {
        return [
            'key' => $this->getInstanceKey(),
        ];
    }

    protected function buildPayloads(array $payloads)
    {
        return array_merge([
        ], $payloads);
    }

    /**
     * @param string $phone
     * @return self
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        $this->phoneLink = $this->phoneLink();
        return $this;
    }

    /**
     * @param string $phone
     * @return string
     */
    public function formatPhone($phone)
    {
        if(empty($phone))return '';

        try {
            $result = PhoneNumber::make($phone, $this->getConfig('country'))->formatE164();

            $result = str_replace('+', '', $result);
        } catch (\Throwable $th) {
            $this->logError('Error format phone', $th);
        }
        return $result;
    }

    /**
     * @return string
     */
    public function phoneLink()
    {
        $formattedPhone = $this->formatPhone($this->phone);
        if(empty($formattedPhone))return '';

        $domain = '@s.whatsapp.net';
        $port = ':24';
        return $formattedPhone . $port . $domain;
    }

    /**
     * @param string $string
     * @return string
     */
    public function formatGroupLink($string)
    {
        return $string . '@g.us';
    }

    /**
     * Log Error
     *
     * @param string $title
     * @param \Throwable $th
     *
     * @return void
     **/
    protected function logError($title, \Throwable $th)
    {
        Log::error($title, [
            'error' => $th->getMessage(),
            'trace' => $th->getTraceAsString()
        ]);
    }

    protected function cleanUri($uri)
    {
        if(empty($uri))return '';

        $startWithSlash = Str::startsWith($uri, '/');
        if($startWithSlash){
            $uri = Str::substr($uri, 1, strlen($uri));
        }

        $endWithSlash = Str::endsWith($uri, '/');
        if($endWithSlash){
            $uri = Str::substr($uri, 0, strlen($uri) - 1);
        }
        return $uri;
    }

    public function getBaseUrl()
    {
        return $this->cleanUri($this->getConfig('url'));
    }

    public function buildUrl($uri)
    {
        $baseUrl = $this->getBaseUrl();
        $endpoint = $this->getConfig('uri');

        $urlArr = [
            $baseUrl,
            $this->cleanUri($endpoint),
            $this->cleanUri($uri)
        ];
        $fullUrl = implode('/', $urlArr);
        return $fullUrl;
    }

    public function buildEndpoint($uri)
    {
        $endpoint = $this->getConfig('uri');

        $urlArr = [
            $this->cleanUri($endpoint),
            $this->cleanUri($uri)
        ];
        $fullUrl = implode('/', $urlArr);
        return $fullUrl;
    }

    /**
     * @param string $uri
     * @param array $payloads
     * @param string $method
     * @param array $files
     * @return array
     */
    public function makeRequest($uri, array $payloads = [], $method = 'GET', $files = [])
    {
        $payloads = $this->buildPayloads($payloads);
        $endpoint = $this->buildEndpoint($uri);

        $config = [
            'url' => $this->getBaseUrl(),
        ];
        return Request::make($config)
        ->setParams($this->getParams())
        ->makeRequest($endpoint, $payloads, $method, $files);
    }
}
