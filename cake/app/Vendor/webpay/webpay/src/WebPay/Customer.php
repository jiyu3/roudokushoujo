<?php

namespace WebPay;

use WebPay\Data\CustomerRequestCreate;
use WebPay\Data\CustomerResponse;
use WebPay\Data\CustomerIdRequest;
use WebPay\Data\CustomerRequestUpdate;
use WebPay\Data\BasicListRequest;
use WebPay\Data\CustomerResponseList;

class Customer
{
    /** @var WebPay */
    private $client;

    public function __construct(\WebPay\WebPay $client)
    {
        $this->client = $client;
    }

    /**
     * Create a customer object with given parameters.
     *
     * @param  mixed                 $params a value convertible to CustomerRequestCreate
     * @return CustomerRequestCreate
     */
    public function create($params = array())
    {
        $req = CustomerRequestCreate::create($params);
        $rawResponse = $this->client->request('post', 'customers', $req);

        return new CustomerResponse($rawResponse);
    }

    /**
     * Retrieve a customer object by customer id.
     * If the customer is already deleted, "deleted" attribute becomes true.
     *
     * @param  mixed             $params a value convertible to CustomerIdRequest
     * @return CustomerIdRequest
     */
    public function retrieve($params = array())
    {
        $req = CustomerIdRequest::create($params);
        $rawResponse = $this->client->request('get', 'customers' . '/' . (string) $req->id, $req);

        return new CustomerResponse($rawResponse);
    }

    /**
     * Update an existing customer with specified parameters
     *
     * @param  mixed                 $params a value convertible to CustomerRequestUpdate
     * @return CustomerRequestUpdate
     */
    public function update($params = array())
    {
        $req = CustomerRequestUpdate::create($params);
        $rawResponse = $this->client->request('post', 'customers' . '/' . (string) $req->id, $req);

        return new CustomerResponse($rawResponse);
    }

    /**
     * Delete an existing customer
     *
     * @param  mixed             $params a value convertible to CustomerIdRequest
     * @return CustomerIdRequest
     */
    public function delete($params = array())
    {
        $req = CustomerIdRequest::create($params);
        $rawResponse = $this->client->request('delete', 'customers' . '/' . (string) $req->id, $req);

        return new CustomerResponse($rawResponse);
    }

    /**
     * List customers filtered by params
     *
     * @param  mixed            $params a value convertible to BasicListRequest
     * @return BasicListRequest
     */
    public function all($params = array())
    {
        $req = BasicListRequest::create($params);
        $rawResponse = $this->client->request('get', 'customers', $req);

        return new CustomerResponseList($rawResponse);
    }

    /**
     * Delete a card data of a customer
     *
     * @param  mixed             $params a value convertible to CustomerIdRequest
     * @return CustomerIdRequest
     */
    public function deleteActiveCard($params = array())
    {
        $req = CustomerIdRequest::create($params);
        $rawResponse = $this->client->request('delete', 'customers' . '/' . (string) $req->id . '/' . 'active_card', $req);

        return new CustomerResponse($rawResponse);
    }

}
