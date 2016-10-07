<?php

namespace WebPay\Model;

class EntityList extends AbstractModel
{
    public function __construct(\WebPay\WebPay $client, array $data)
    {
        if (array_key_exists('data', $data) && !empty($data['data'])) {
            $converter = $this->dataToObjectConverter($client);
            $data['data'] = array_map($converter, $data['data']);
        }
        parent::__construct($data);
    }
}
