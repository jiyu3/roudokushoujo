<?php

namespace WebPay;

use WebPay\Data\TokenRequestCreate;
use WebPay\Data\TokenResponse;
use WebPay\Data\TokenIdRequest;

class Token
{
    /** @var WebPay */
    private $client;

    public function __construct(\WebPay\WebPay $client)
    {
        $this->client = $client;
    }

    /**
     * Create a token object with given parameters.
     *
     * @param  mixed              $params a value convertible to TokenRequestCreate
     * @return TokenRequestCreate
     */
    public function create($params = array())
    {
        $req = TokenRequestCreate::create($params);
        $rawResponse = $this->client->request('post', 'tokens', $req);

        return new TokenResponse($rawResponse);
    }

    /**
     * Retrieve a token object by token id.
     *
     * @param  mixed          $params a value convertible to TokenIdRequest
     * @return TokenIdRequest
     */
    public function retrieve($params = array())
    {
        $req = TokenIdRequest::create($params);
        $rawResponse = $this->client->request('get', 'tokens' . '/' . (string) $req->id, $req);

        return new TokenResponse($rawResponse);
    }

}
