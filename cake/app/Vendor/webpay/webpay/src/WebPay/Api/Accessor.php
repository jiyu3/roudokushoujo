<?php

namespace WebPay\Api;

/**
 * Manage WebPay $client and provide utility methods
 */
abstract class Accessor
{
    /** @var WebPay */
    protected $client;

    /**
     * @param \WebPay\WebPay $client
     */
    public function __construct(\WebPay\WebPay $client)
    {
        $this->client = $client;
    }

    protected function assertId($id)
    {
        if (!is_string($id) || empty($id)) {
            throw \WebPay\Exception\InvalidRequestException::emptyIdException();
        }
    }
}
