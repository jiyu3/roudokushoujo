<?php

namespace WebPay\Tests\Api;

class EventsTest extends \WebPay\Tests\WebPayTestCase
{
    public function testRetrieve()
    {
        $this->mock('events/retrieve');
        $id = 'evt_39o9oUevb5NCeM1';
        $event = $this->webpay->events->retrieve($id);

        $this->assertEquals($id, $event->id);
        $this->assertEquals('customer@example.com', $event->data->object->email);

        $this->assertGet('/events/'.$id);
    }

    /**
     * @expectedException \WebPay\Exception\InvalidRequestException
     */
    public function testRetrieveWithEmptyString()
    {
        try {
            $event = $this->webpay->events->retrieve('');
        } catch (\WebPay\Exception\InvalidRequestException $e) {
            $this->assertEquals('id must not be empty', $e->getMessage());
            throw $e;
        }
    }

    public function testAll()
    {
        $this->mock('events/all_with_type');
        $params = array('type' => '*.created');
        $events = $this->webpay->events->all($params);

        $this->assertEquals('/v1/events', $events->url);
        $this->assertEquals('customer.created', $events->data[0]->type);
        $this->assertEquals('YUUKO SHIONJI', $events->data[0]->data->object->active_card->name);

        $this->assertGet('/events', $params);
    }
}
