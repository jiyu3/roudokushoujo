<?php

namespace WebPay\Model;

abstract class Entity extends AbstractModel
{
    /** @var WebPay */
    protected $client;

    public function __construct(\WebPay\WebPay $client, array $data)
    {
        parent::__construct($data);
        $this->client = $client;
    }
}
