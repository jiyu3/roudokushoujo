<?php

namespace WebPay;

use WebPay\Data\EventIdRequest;
use WebPay\Data\EventResponse;
use WebPay\Data\EventListRequest;
use WebPay\Data\EventResponseList;

class Event
{
    /** @var WebPay */
    private $client;

    public function __construct(\WebPay\WebPay $client)
    {
        $this->client = $client;
    }

    /**
     * Retrieve an event object by event id.
     *
     * @param  mixed          $params a value convertible to EventIdRequest
     * @return EventIdRequest
     */
    public function retrieve($params = array())
    {
        $req = EventIdRequest::create($params);
        $rawResponse = $this->client->request('get', 'events' . '/' . (string) $req->id, $req);

        return new EventResponse($rawResponse);
    }

    /**
     * List events filtered by params
     *
     * @param  mixed            $params a value convertible to EventListRequest
     * @return EventListRequest
     */
    public function all($params = array())
    {
        $req = EventListRequest::create($params);
        $rawResponse = $this->client->request('get', 'events', $req);

        return new EventResponseList($rawResponse);
    }

}
