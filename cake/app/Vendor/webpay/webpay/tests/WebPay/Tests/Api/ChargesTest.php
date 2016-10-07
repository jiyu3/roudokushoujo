<?php

namespace WebPay\Tests\Api;

class ChargesTest extends \WebPay\Tests\WebPayTestCase
{
    public function testCreate()
    {
        $this->mock('charges/create');

        $params = array(
            'amount' => 1000,
            'currency' => "jpy",
            'card' => array(
                'number' => "4242-4242-4242-4242",
                'exp_month' => 12,
                'exp_year' => 2015,
                'cvc' => 123,
                'name' => "YUUKO SHIONJI",
            ),
            'description' => "Test Charge from Java",
        );
        $charge = $this->webpay->charges->create($params);

        $this->assertEquals('ch_2SS17Oh1r8d2djE', $charge->id);
        $this->assertEquals('Test Charge from Java', $charge->description);
        $this->assertEquals('YUUKO SHIONJI', $charge->card->name);

        $this->assertPost('/charges', $params);
    }

    public function testCreateWithToken()
    {
        $this->mock('charges/create');

        $params = array(
            'amount' => 1000,
            'currency' => "jpy",
            'card' => 'tok_3dw2T20rzekM1Kf',
            'description' => "Test Charge from Java",
        );
        $charge = $this->webpay->charges->create($params);

        $this->assertEquals('Test Charge from Java', $charge->description);

        $this->assertPost('/charges', $params);
    }

    public function testRetrieve()
    {
        $this->mock('charges/retrieve');
        $id = 'ch_bWp5EG9smcCYeEx';
        $charge = $this->webpay->charges->retrieve($id);

        $this->assertEquals($id, $charge->id);

        $this->assertGet('/charges/'.$id);
    }

    /**
     * @expectedException \WebPay\Exception\InvalidRequestException
     */
    public function testRetrieveWithEmptyString()
    {
        try {
            $charge = $this->webpay->charges->retrieve('');
        } catch (\WebPay\Exception\InvalidRequestException $e) {
            $this->assertEquals('id must not be empty', $e->getMessage());
            throw $e;
        }
    }

    public function testAll()
    {
        $this->mock('charges/all');
        $params = array(
            'count' => 3,
            'offset' => 0,
            'created' => array(
                'gt' => 1378000000,
            ),
        );
        $charges = $this->webpay->charges->all($params);

        $this->assertEquals('/v1/charges', $charges->url);
        $this->assertEquals('Test Charge from Java', $charges->data[0]->description);
        $this->assertEquals('KEI KUBO', $charges->data[0]->card->name);

        $this->assertGet('/charges', $params);
    }

    public function testRefund()
    {
        $this->mock('charges/retrieve');
        $id = 'ch_bWp5EG9smcCYeEx';
        $charge = $this->webpay->charges->retrieve($id);

        $this->mock('charges/refund');
        $charge->refund(400);

        $this->assertEquals(true, $charge->refunded);
        $this->assertEquals(400, $charge->amountRefunded);

        $this->assertPost('/charges/' . $id . '/refund', array('amount' => 400));
    }

    public function testRefundWithoutAmount()
    {
        $this->mock('charges/retrieve');
        $id = 'ch_bWp5EG9smcCYeEx';
        $charge = $this->webpay->charges->retrieve($id);

        $this->mock('charges/refund');
        $charge->refund();

        $this->assertEquals(true, $charge->refunded);
        $this->assertEquals(400, $charge->amount_refunded);

        $this->assertPost('/charges/' . $id . '/refund', array());
    }

    public function testCapture()
    {
        $this->mock('charges/retrieve_not_captured');
        $id = 'ch_2X01NDedxdrRcA3';
        $charge = $this->webpay->charges->retrieve($id);
        $this->assertEquals(false, $charge->captured);

        $this->mock('charges/capture');
        $charge->capture(1000);

        $this->assertEquals(true, $charge->captured);
        $this->assertEquals(true, $charge->paid);
        $this->assertEquals(1000, $charge->amount);

        $this->assertPost('/charges/' . $id . '/capture', array('amount' => 1000));
    }
}
