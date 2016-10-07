<?php

namespace WebPay\Api;

class Account extends Accessor
{
    /**
     * Retrieve the current account
     *
     * @return Account
     */
    public function retrieve()
    {
        return new \WebPay\Model\Account($this->client, $this->client->request('account.retrieve', array()));
    }

    /**
     * Delete all test data
     *
     * @return bool
     */
    public function deleteData()
    {
        $result = $this->client->request('account.delete_data', array());

        return $result['deleted'];
    }
}
