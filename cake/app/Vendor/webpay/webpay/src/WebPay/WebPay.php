<?php

namespace WebPay;

use Guzzle\Common\Event as GuzzleEvent;
use Guzzle\Service\Client as GuzzleClient;

use WebPay\Data\EventResponse;
use WebPay\ErrorResponse\InvalidRequestException as ERInvalidRequestException;
use WebPay\ErrorResponse\AuthenticationException as ERAuthenticationException;
use WebPay\ErrorResponse\CardException as ERCardException;
use WebPay\ErrorResponse\ApiException as ERApiException;

class WebPay
{
    /** @var GuzzleClient */
    private $client;

    /** @var Charge */
    private $charge;
    /** @var Customer */
    private $customer;
    /** @var Token */
    private $token;
    /** @var Event */
    private $event;
    /** @var Shop */
    private $shop;
    /** @var Recursion */
    private $recursion;
    /** @var Account */
    private $account;

    /**
     * @param array $options API options
     */
    public function __construct($authToken, $options = array())
    {
        $apiBase = isset($options['api_base']) ? $options['api_base'] : 'https://api.webpay.jp/v1';
        $this->client = new GuzzleClient($apiBase);

        $this->client->setDefaultOption('headers/Authorization', 'Bearer ' . $authToken);

        $this->client->setDefaultOption('headers/Content-Type', "application/json");

        $this->client->setDefaultOption('headers/Accept', "application/json");

        $this->client->setDefaultOption('headers/User-Agent', "Apipa-webpay/2.2.2 php");

        $this->client->setDefaultOption('headers/Accept-Language', "en");
        $this->client->getEventDispatcher()->addListener('request.error', array($this, 'onRequestError'));
        $this->client->getEventDispatcher()->addListener('request.exception', array($this, 'onRequestException'));

        $this->charge = new Charge($this);
        $this->customer = new Customer($this);
        $this->token = new Token($this);
        $this->event = new Event($this);
        $this->shop = new Shop($this);
        $this->recursion = new Recursion($this);
        $this->account = new Account($this);
    }

    public function setAcceptLanguage($value)
    {
        $this->client->setDefaultOption('headers/Accept-Language', $value);
    }

    /**
     * Decode request body sent as Webhook
     *
     * @param  string        $requestBody JSON string
     * @return EventResponse object that represents webhook data or null
     */
    public function receiveWebhook($requestBody)
    {
        $decodedJson = json_decode($requestBody, true);
        if (!$decodedJson) {
            throw new ApiConnectionException('Webhook request body is invalid JSON string', null);
        }

        return new EventResponse($decodedJson);
    }

    public function __get($key)
    {
        $accessors = array('charge', 'customer', 'token', 'event', 'shop', 'recursion', 'account');
        if (in_array($key, $accessors) && property_exists($this, $key)) {
            return $this->{$key};
        } else {
            throw new \Exception('Unknown accessor ' . $key);
        }
    }

    public function __set($key, $value)
    {
        throw new \Exception($key . ' is not able to override');
    }

    /**
     * Dispatch API request
     *
     * @param string $method    HTTP method
     * @param string $path      Target path relative to base_url option value
     * @param object $paramData Request data
     *
     * @return mixed Response object
     */
    public function request($method, $path, $paramData)
    {
        $req = $this->client->createRequest($method, $path, array());
        $query = $req->getQuery();
        foreach ($paramData->queryParams() as $k => $v) {
            if ($v === null) continue;
            $query->add($k, (is_bool($v)) ? ($v ? 'true' : 'false') : $v);
        }
        if ($method !== 'get' && $method !== 'delete') {
            $body = $paramData->requestBody();
            $json = empty($body) ? '{}' : json_encode($body);
            $req->setBody($json, 'application/json');
        }
        try {
            $res = $req->send();

            return $res->json();
        } catch (\Guzzle\Common\Exception\RuntimeException $e) {
            throw ApiConnectionException::inRequest($e);
        }
    }

    /**
     * Add a guzzle plugin to the client.
     * This is mainly for testing, but also useful for logging, validation, etc.
     *
     * @param mixed $plugin A guzzle plugin
     */
    public function addSubscriber($plugin)
    {
        $this->client->addSubscriber($plugin);
    }

    /**
     * @param  GuzzleEvent     $event
     * @throws WebPayException
     */
    public function onRequestError(GuzzleEvent $event)
    {
        $this->throwErrorResponseException($event['response']);
    }

    /**
     * @param  GuzzleEvent     $event
     * @throws WebPayException
     */
    public function onRequestException(GuzzleEvent $event)
    {
        $cause = $event['exception'];

        if (isset($event['response'])) {
            $this->throwErrorResponseException($event['response']);
        } else {
            throw ApiConnectionException::inRequest($cause);
        }
    }

    private function throwErrorResponseException($response)
    {
        $data = null;
        try {
            $data = $response->json();
        } catch (\Exception $e) {
            throw ApiConnectionException::invalidJson($e);
        }
        $status = $response->getStatusCode();
        if ($status == 400) {
            throw new ERInvalidRequestException($status, $data);
        }
        if ($status == 401) {
            throw new ERAuthenticationException($status, $data);
        }
        if ($status == 402) {
            throw new ERCardException($status, $data);
        }
        if ($status == 404) {
            throw new ERInvalidRequestException($status, $data);
        }
        if (true) {
            throw new ERApiException($status, $data);
        }
        throw new \Exception("Unknown error is returned");
    }
}
