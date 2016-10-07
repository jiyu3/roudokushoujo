<?php

namespace WebPay\Api;

use WebPay\Model\Token;

class Tokens extends Accessor
{
    /**
     * Create a token
     *
     * @param  array $params
     * @return Token
     */
    public function create(array $params)
    {
        if (! array_key_exists('card', $params) && array_key_exists('name', $params))
            $params = array('card' => $params);

        return new Token($this->client, $this->client->request('tokens.create', $params));
    }

    /**
     * Retrieve an existing token
     *
     * @param  string $id
     * @return Token
     */
    public function retrieve($id)
    {
        $this->assertId($id);

        return new Token($this->client, $this->client->request('tokens.retrieve', array('id' => $id)));
    }
}
