<?php

namespace WebPay\Model;

class DeletedEntity extends Entity
{
    public function __construct($client, $data)
    {
        parent::__construct($client, $data);
    }

    /**
     * Classify this response object from DeletedEntity
     *
     * @return false
     */
    public function isDeleted()
    {
        return true;
    }
}
