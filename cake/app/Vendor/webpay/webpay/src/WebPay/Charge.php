<?php

namespace WebPay;

use WebPay\Data\ChargeRequestCreate;
use WebPay\Data\ChargeResponse;
use WebPay\Data\ChargeIdRequest;
use WebPay\Data\ChargeRequestRefund;
use WebPay\Data\ChargeRequestWithAmount;
use WebPay\Data\ChargeListRequest;
use WebPay\Data\ChargeResponseList;

class Charge
{
    /** @var WebPay */
    private $client;

    public function __construct(\WebPay\WebPay $client)
    {
        $this->client = $client;
    }

    /**
     * Create a charge object with given parameters.
     * In live mode, this issues a transaction.
     *
     * @param  mixed               $params a value convertible to ChargeRequestCreate
     * @return ChargeRequestCreate
     */
    public function create($params = array())
    {
        $req = ChargeRequestCreate::create($params);
        $rawResponse = $this->client->request('post', 'charges', $req);

        return new ChargeResponse($rawResponse);
    }

    /**
     * Retrieve a existing charge object by charge id
     *
     * @param  mixed           $params a value convertible to ChargeIdRequest
     * @return ChargeIdRequest
     */
    public function retrieve($params = array())
    {
        $req = ChargeIdRequest::create($params);
        $rawResponse = $this->client->request('get', 'charges' . '/' . (string) $req->id, $req);

        return new ChargeResponse($rawResponse);
    }

    /**
     * Refund a paid charge specified by charge id.
     * Optional argument amount is to refund partially.
     *
     * @param  mixed               $params a value convertible to ChargeRequestRefund
     * @return ChargeRequestRefund
     */
    public function refund($params = array())
    {
        $req = ChargeRequestRefund::create($params);
        $rawResponse = $this->client->request('post', 'charges' . '/' . (string) $req->id . '/' . 'refund', $req);

        return new ChargeResponse($rawResponse);
    }

    /**
     * Capture a not captured charge specified by charge id
     *
     * @param  mixed                   $params a value convertible to ChargeRequestWithAmount
     * @return ChargeRequestWithAmount
     */
    public function capture($params = array())
    {
        $req = ChargeRequestWithAmount::create($params);
        $rawResponse = $this->client->request('post', 'charges' . '/' . (string) $req->id . '/' . 'capture', $req);

        return new ChargeResponse($rawResponse);
    }

    /**
     * List charges filtered by params
     *
     * @param  mixed             $params a value convertible to ChargeListRequest
     * @return ChargeListRequest
     */
    public function all($params = array())
    {
        $req = ChargeListRequest::create($params);
        $rawResponse = $this->client->request('get', 'charges', $req);

        return new ChargeResponseList($rawResponse);
    }

}
