<?php

namespace WebPay\Model;

class Charge extends Entity
{
    public function __construct(\WebPay\WebPay $client, array $data)
    {
        if (array_key_exists('card', $data) && !empty($data['card'])) {
            $data['card'] = new Card($data['card']);
        }
        parent::__construct($client, $data);
    }

    /**
     * Refund this charge
     *
     * @param  integer $amount Amount to refund. Default is all.
     * @return self
     */
    public function refund($amount = null)
    {
        $this->data = $this->client->charges->refund($this->id, $amount)->data;

        return $this;
    }

    /**
     * Capture this charge
     *
     * @param  integer $amount Amount to capture. Default is all.
     * @return self
     */
    public function capture($amount = null)
    {
        $this->data = $this->client->charges->capture($this->id, $amount)->data;

        return $this;
    }
}
