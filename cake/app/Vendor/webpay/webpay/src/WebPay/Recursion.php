<?php

namespace WebPay;

use WebPay\Data\RecursionRequestCreate;
use WebPay\Data\RecursionResponse;
use WebPay\Data\RecursionIdRequest;
use WebPay\Data\RecursionRequestResume;
use WebPay\Data\RecursionListRequest;
use WebPay\Data\RecursionResponseList;

class Recursion
{
    /** @var WebPay */
    private $client;

    public function __construct(\WebPay\WebPay $client)
    {
        $this->client = $client;
    }

    /**
     * Create a recursion object with given parameters.
     *
     * @param  mixed                  $params a value convertible to RecursionRequestCreate
     * @return RecursionRequestCreate
     */
    public function create($params = array())
    {
        $req = RecursionRequestCreate::create($params);
        $rawResponse = $this->client->request('post', 'recursions', $req);

        return new RecursionResponse($rawResponse);
    }

    /**
     * Retrieve a recursion object by recursion id.
     * If the recursion is already deleted, "deleted" attribute becomes true.
     *
     * @param  mixed              $params a value convertible to RecursionIdRequest
     * @return RecursionIdRequest
     */
    public function retrieve($params = array())
    {
        $req = RecursionIdRequest::create($params);
        $rawResponse = $this->client->request('get', 'recursions' . '/' . (string) $req->id, $req);

        return new RecursionResponse($rawResponse);
    }

    /**
     * Resume a suspended recursion
     *
     * @param  mixed                  $params a value convertible to RecursionRequestResume
     * @return RecursionRequestResume
     */
    public function resume($params = array())
    {
        $req = RecursionRequestResume::create($params);
        $rawResponse = $this->client->request('post', 'recursions' . '/' . (string) $req->id . '/' . 'resume', $req);

        return new RecursionResponse($rawResponse);
    }

    /**
     * Delete an existing recursion
     *
     * @param  mixed              $params a value convertible to RecursionIdRequest
     * @return RecursionIdRequest
     */
    public function delete($params = array())
    {
        $req = RecursionIdRequest::create($params);
        $rawResponse = $this->client->request('delete', 'recursions' . '/' . (string) $req->id, $req);

        return new RecursionResponse($rawResponse);
    }

    /**
     * List recursions filtered by params
     *
     * @param  mixed                $params a value convertible to RecursionListRequest
     * @return RecursionListRequest
     */
    public function all($params = array())
    {
        $req = RecursionListRequest::create($params);
        $rawResponse = $this->client->request('get', 'recursions', $req);

        return new RecursionResponseList($rawResponse);
    }

}
