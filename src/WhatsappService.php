<?php

namespace Bagoesz21\LaravelNotifWaWeb;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Throwable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Propaganistas\LaravelPhone\PhoneNumber;

class WhatsappService
{
    /** @var array */
    protected $config = [];

    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var string */
    protected $sessionID = '';

    /** @var \Psr\Http\Message\ResponseInterface */
    protected $response;

    /** @var array */
    protected $body;

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

    public function loadFromConfig()
    {
        $this->setConfig(config('whatsapp'));
        $this->setID(Arr::get($this->config, 'session_id'));
        return $this;
    }

    /**
     * @return self
     */
    protected function setClient()
    {
        $baseUri = Arr::get($this->config, 'host');
        $port = Arr::get($this->config, 'port');

        $url = $baseUri . ':' . $port;

        $this->client = new Client(
            [
                'base_uri' => $url,
            ]
        );
        return $this;
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
     * @param string $endpoint
     * @param array $payloads
     * @param string $method
     * @return array
     */
    public function makeRequest($endpoint, array $payloads = [], $method = 'GET'): array
    {
        $this->setClient();

        $endpoints = [];
        $uri = Arr::get($this->config, 'uri', '/');
        if($uri !== '/'){
            $endpoints[] = $uri;
        }
        $endpoints[] = $endpoint;

        $endpoint = $this->buildEndpoint($endpoints);

        $payloads = array_merge([
        ], $payloads);

        switch (strtoupper($method)) {
            case 'GET':
                $payloads = [
                    'query' => $payloads
                ];
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

        $this->body = [];
        try {
            $this->response = $this->client->request($method, $endpoint, $payloads);

            $this->setResponse(json_decode($this->response->getBody(), true), true);
            ;
        } catch (ClientException $e) {
            $this->responseError($e->getMessage(), 400, json_decode($e->getResponse()->getBody(), true));
        } catch (Throwable $e) {
            $this->responseError($e->getMessage());
            $this->logError($e);
        }

        // \Log::info('request wa', [
        //     'id' => $this->getID(),
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
     * @param \Throwable $th
     *
     * @return void
     **/
    protected function logError(\Throwable $th)
    {
        Log::error($th->getMessage(), ['trace' => $th->getTraceAsString()]);
    }

    /**
     * @param string $id
     * @return self
     */
    public function setID($id)
    {
        $this->sessionID = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getID()
    {
        return $this->sessionID;
    }

    /**
     * @param bool $isLegacy
     * @return array
     */
    public function newSession($isLegacy = false)
    {
        $id = $this->getID();
        $payloads = [
            'id' => $id,
            'isLegacy' => $isLegacy
        ];
        $response =  $this->makeRequest('sessions/add', $payloads, 'POST');

        return $response;
    }

    public function statusSession()
    {
        $id = $this->getID();
        return $this->makeRequest('sessions/status/' . $id);
    }

    public function deleteSession()
    {
        $id = $this->getID();
        $response = $this->makeRequest('sessions/delete/' . $id, [], "DELETE");
        if(Arr::get($response, 'status', false)){
        }
        return $response;
    }

    public function findSession()
    {
        $id = $this->getID();
        return $this->makeRequest('sessions/find/' . $id);
    }

    public function getChats()
    {
        $id = $this->getID();
        return $this->makeRequest('chats/', [
            'id' => $id
        ]);
    }

    /**
     * @param string $phoneNumber
     * @param int $limit
     * @return array
     */
    public function getChat($phoneNumber, $limit = 25)
    {
        $formattedPhone = $this->formatPhoneLink($phoneNumber);

        $id = $this->getID();

        return $this->makeRequest('chats/' . $formattedPhone, [
            'id' => $id,
            'limit' => $limit,
            'cursor_id' => 'REDACTED',
            'cursor_fromMe' => true
        ]);
    }

    /**
     * @param string $phoneNumber
     * @param string $message
     * @return array
     */
    public function sendMessage($phoneNumber, $message)
    {
        $payloads = $this->mapSendMessage([
            'phone' => $phoneNumber,
            'message' => $message,
        ]);

        return $this->makeRequest('chats/send?id=' . $this->getID(), $payloads, 'POST');
    }

    /**
     * @param array $datas
     * @return array
     */
    public function mapSendMessage($datas)
    {
        if(!is_array($datas) && empty($datas))return [];

        $phoneNumber = Arr::get($datas, 'phone');
        return array_merge([], [
            'receiver' => $this->formatPhone($phoneNumber),
            'message' => Arr::get($datas, 'message')
        ]);
    }

    /**
     * @param array $datas
     * @return array
     */
    public function mapSendMessages($datas)
    {
        if(!is_array($datas) && empty($datas))return [];

        if(!$this->isMultiArray($datas)){
            return $this->mapSendMessage($datas);
        }

        return array_map(function($data){
            return $this->mapSendMessage($data);
        }, $datas);
    }

    private function isMultiArray(array $array)
    {
        return count($array) !== count($array, COUNT_RECURSIVE);
    }

    /**
     * @param array $datas
     * @return array
     */
    public function sendBatchMessage($datas)
    {
        $payloads = $this->mapSendMessages($datas);

        return $this->makeRequest('chats/send-bulk?id=' . $this->getID(), $payloads, 'POST');
    }

    /**
     * @param string $phone
     * @return string
     */
    public function formatPhone($phone)
    {
        if(empty($phone))return '';

        try {
            $result = PhoneNumber::make($phone, 'ID')->formatE164();

            $result = str_replace('+', '', $result);
        } catch (\Throwable $th) {
            Log::error('Error format phone', [
                'error' => $th->getMessage()
            ]);
        }
        return $result;
    }

    /**
     * @param string $phone
     * @return string
     */
    public function formatPhoneLink($phone)
    {
        $formattedPhone = $this->formatPhone($phone);
        if($formattedPhone)return '';

        return $formattedPhone . '@s.whatsapp.net';
    }

    public function getGroups()
    {
        $id = $this->getID();
        return $this->makeRequest('groups/', [
            'id' => $id
        ]);
    }

    /**
     * @param string $groupID
     * @param int $limit
     * @return array
     */
    public function getGroupChat($groupID, $limit = 25)
    {
        $groupIDFull = $this->formatGroupLink($groupID);

        $id = $this->getID();

        return $this->makeRequest('groups/' . $groupIDFull, [
            'id' => $id,
            'limit' => $limit,
            'cursor_id' => 'REDACTED',
            'cursor_fromMe' => true
        ]);
    }

    /**
     * @param string $groupID
     * @param string $message
     * @return array
     */
    public function sendMessageToGroup($groupID, $message)
    {
        $payloads = $this->mapSendMessage([
            'receiver' => $groupID,
            'message' => $message,
        ]);

        return $this->makeRequest('groups/send?id=' . $this->getID(), $payloads, 'POST');
    }

    /**
     * @param string $string
     * @return string
     */
    public function formatGroupLink($string)
    {
        return $string . '@g.us';
    }
}
