<?php

namespace WebPay;

use WebPay\Data\EmptyRequest;
use WebPay\Data\AccountResponse;
use WebPay\Data\DeletedResponse;

class Account
{
    /** @var WebPay */
    private $client;

    public function __construct(\WebPay\WebPay $client)
    {
        $this->client = $client;
    }

    /**
     * Retrieve information of the current user
     *
     * @param  mixed        $params a value convertible to EmptyRequest
     * @return EmptyRequest
     */
    public function retrieve($params = array())
    {
        $req = EmptyRequest::create($params);
        $rawResponse = $this->client->request('get', 'account', $req);

        return new AccountResponse($rawResponse);
    }

    /**
     * Delete all test data of this account
     *
     * @param  mixed        $params a value convertible to EmptyRequest
     * @return EmptyRequest
     */
    public function deleteData($params = array())
    {
        $req = EmptyRequest::create($params);
        $rawResponse = $this->client->request('delete', 'account' . '/' . 'data', $req);

        return new DeletedResponse($rawResponse);
    }

}
