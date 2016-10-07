<?php

namespace WebPay\Model;

class EventData extends AbstractModel
{
    public function __construct(\WebPay\WebPay $client, array $data)
    {
        if (array_key_exists('object', $data)) {
            $converter = $this->dataToObjectConverter($client);
            $data['object'] = $converter($data['object']);
        }
        parent::__construct($data);
    }
}
