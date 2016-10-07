<?php

namespace WebPay\Api;

use WebPay\Model\Customer;
use WebPay\Model\DeletedEntity;
use WebPay\Model\EntityList;

class Customers extends Accessor
{
    /**
     * Create a customer
     *
     * @param  array    $params
     * @return Customer
     */
    public function create(array $params)
    {
        return new Customer($this->client, $this->client->request('customers.create', $params));
    }

    /**
     * Retrieve an existing customer
     *
     * @param  string $id
     * @return mixed  Customer or DeletedEntity
     */
    public function retrieve($id)
    {
        $this->assertId($id);

        $response = $this->client->request('customers.retrieve', array('id' => $id));
        if (array_key_exists('deleted', $response)) {
            return new DeletedEntity($this->client, $response);
        } else {
            return new Customer($this->client, $response);
        }
    }

    /**
     * Get a list of existing customers
     *
     * @param  array      $params
     * @return EntityList
     */
    public function all(array $params = array())
    {
        return new EntityList($this->client, $this->client->request('customers.all', $params));
    }

    /**
     * Update parameters of the customer specified by id
     *
     * @param  string   $id     The customer to update
     * @param  integer  $params New parameters
     * @return Customer
     */
    public function save($id, $params)
    {
        $this->assertId($id);
        $params['id'] = $id;

        return new Customer($this->client, $this->client->request('customers.save', $params));
    }

    /**
     * Delete the customer specified by id
     *
     * @param  string $id The customer to delete
     * @return bool   true if deletion succeeded
     */
    public function delete($id)
    {
        $this->assertId($id);
        $response = $this->client->request('customers.delete', array('id' => $id));

        return $response['deleted'];
    }
}
