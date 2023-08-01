<?php

namespace Bagoesz21\LaravelNotifWaWeb;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

use Throwable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Request
{
    /** @var array */
    protected array $config = [
        'url' => '',
        'uri' => '',
    ];

     /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var \Psr\Http\Message\ResponseInterface */
    protected $response;

    /** @var bool */
    protected bool $status;

    /** @var array */
    protected $body;

    /** @var array */
    protected array $payloads = [
        'params' => [],
        'request_body' => []
    ];

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    /**
     * Static
     *
     * @param array $config
     * @return static
     */
    public static function make($config = []){
        $class = get_called_class();
        return (new $class($config));
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

    protected function cleanUri($uri)
    {
        $endWithSlash = Str::endsWith($uri, '/');
        if(!$endWithSlash){
            $uri = '/';
        }
        return $uri;
    }

    public function buildUrl($uri)
    {
        $baseUri = Arr::get($this->config, 'uri');
        $url = Arr::get($this->config, 'url');

        $urlArr = [ $this->cleanUri($url), $this->cleanUri($baseUri), $this->cleanUri($uri) ];
        $fullUrl = implode('/', $urlArr);
        return $fullUrl;
    }

    public function setParams(array $params = [])
    {
        $this->payloads['params'] = $params;
        return $this;
    }

    protected function buildPayloads(array $payloads)
    {
        return array_merge($this->payloads['request_body'], $payloads);
    }

    /**
     * @param array $endpoints
     * @return string
     */
    protected function buildEndpoint($endpoints)
    {
        $endpoint = implode('/', $endpoints);
        return $endpoint;
    }

    /**
     * @return self
     */
    protected function setClient()
    {
        $baseUri = $this->getConfig('url');

        $url = $baseUri;

        $this->client = new Client(
            [
                'base_uri' => $url,
            ]
        );
        return $this;
    }

    /**
     * @param string $uri
     * @param array $payloads
     * @param string $method
     * @param array $files
     * @return array
     */
    public function makeRequest($uri, array $payloads = [], $method = 'GET', array $files = [])
    {
        $this->setClient();

        $params = [];
        if(!empty($this->payloads['params'])){
            $params = $this->payloads['params'];
        }

        $payloads = $this->buildPayloads($payloads);

        $endpoints[] = $uri;
        $endpoint = $this->buildEndpoint($endpoints);

        switch (strtoupper($method)) {
            case 'GET':
                $params = $payloads;
                break;
            case 'POST':
                $payloads = [
                    'form_params' => $payloads
                ];
                break;

            default:
                $payloads = [
                    'json' => $payloads
                ];
                break;
        }

        if(!empty($params)){
            $payloads['query'] = $params;
        }

        $this->body = [];
        try {
            $this->response = $this->client->request($method, $endpoint, $payloads);

            $this->setResponse(json_decode($this->response->getBody(), true), true);
            ;
        } catch (ClientException $e) {
            $this->responseError($e->getMessage(), 400, json_decode($e->getResponse()->getBody(), true));
        } catch (Throwable $e) {
            $this->responseError($e->getMessage());
            $this->logError('Error Make Request', $e);
        }

        // Log::info('request wa', [
        //     'endpoint' => $endpoint,
        //     'method' => $method,
        //     'payloads' => $payloads,
        //     'res' => $this->body
        // ]);
        return $this->body;
    }

    /**
     * Set response
     *
     * @param bool $status. status response
     * @param array $body. custom body response
     * @return self
     */
    protected function setResponse(array $body = [], $status = true)
    {
        $this->status = $status;
        $this->body = array_merge($this->defaultResponse(), $body);
        return $this;
    }

    /**
     * Default response
     *
     * @return array
     */
    protected function defaultResponse()
    {
        return [
            'success' => false,
            'message' => '',
            'data' => []
        ];
    }

    /**
     * Handle response error
     *
     * @param string $description. error description
     * @param int|string $code. error status code
     * @param array $others
     * @return self
     */
    protected function responseError($description = 'Error', $code = 400, array $others = [])
    {
        $data = array_merge([
            'success' => false,
            'message' => $description,
        ], $others);
        $this->setResponse($data, false);
        return $this;
    }

    /**
     * Log Error
     *
     * @param string $title
     * @param \Throwable $th
     *
     * @return void
     **/
    protected function logError($title, Throwable $th)
    {
        Log::error($title, [
            'error' => $th->getMessage(),
            'trace' => $th->getTraceAsString()
        ]);
    }
}
