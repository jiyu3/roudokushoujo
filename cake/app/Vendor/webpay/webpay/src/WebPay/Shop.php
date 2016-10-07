<?php

namespace WebPay;

use WebPay\Data\ShopRequestCreate;
use WebPay\Data\ShopResponse;
use WebPay\Data\ShopIdRequest;
use WebPay\Data\ShopRequestUpdate;
use WebPay\Data\ShopRequestAlterStatus;
use WebPay\Data\BasicListRequest;
use WebPay\Data\ShopResponseList;

class Shop
{
    /** @var WebPay */
    private $client;

    public function __construct(\WebPay\WebPay $client)
    {
        $this->client = $client;
    }

    /**
     * Create a shop object with given parameters.
     *
     * @param  mixed             $params a value convertible to ShopRequestCreate
     * @return ShopRequestCreate
     */
    public function create($params = array())
    {
        $req = ShopRequestCreate::create($params);
        $rawResponse = $this->client->request('post', 'shops', $req);

        return new ShopResponse($rawResponse);
    }

    /**
     * Retrieve a shop object by shop id.
     *
     * @param  mixed         $params a value convertible to ShopIdRequest
     * @return ShopIdRequest
     */
    public function retrieve($params = array())
    {
        $req = ShopIdRequest::create($params);
        $rawResponse = $this->client->request('get', 'shops' . '/' . (string) $req->id, $req);

        return new ShopResponse($rawResponse);
    }

    /**
     * Update an existing shop with specified parameters
     *
     * @param  mixed             $params a value convertible to ShopRequestUpdate
     * @return ShopRequestUpdate
     */
    public function update($params = array())
    {
        $req = ShopRequestUpdate::create($params);
        $rawResponse = $this->client->request('post', 'shops' . '/' . (string) $req->id, $req);

        return new ShopResponse($rawResponse);
    }

    /**
     * Alter the test shop's status to the specified one
     *
     * @param  mixed                  $params a value convertible to ShopRequestAlterStatus
     * @return ShopRequestAlterStatus
     */
    public function alterStatus($params = array())
    {
        $req = ShopRequestAlterStatus::create($params);
        $rawResponse = $this->client->request('post', 'shops' . '/' . (string) $req->id . '/' . 'alter_status', $req);

        return new ShopResponse($rawResponse);
    }

    /**
     * List shops filtered by params
     *
     * @param  mixed            $params a value convertible to BasicListRequest
     * @return BasicListRequest
     */
    public function all($params = array())
    {
        $req = BasicListRequest::create($params);
        $rawResponse = $this->client->request('get', 'shops', $req);

        return new ShopResponseList($rawResponse);
    }

}
