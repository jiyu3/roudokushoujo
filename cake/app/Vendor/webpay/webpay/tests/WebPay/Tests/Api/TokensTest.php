<?php

namespace WebPay\Tests\Api;

class TokensTest extends \WebPay\Tests\WebPayTestCase
{
    public function testCreate()
    {
        $this->mock('tokens/create');

        $params = array(
            'card' => array(
                'number' => "4242-4242-4242-4242",
                'exp_month' => 12,
                'exp_year' => 2015,
                'cvc' => 123,
                'name' => "YUUKO SHIONJI"
            )
        );
        $token = $this->webpay->tokens->create($params);

        $this->assertEquals('tok_3dw2T20rzekM1Kf', $token->id);
        $this->assertEquals(false, $token->used);
        $this->assertEquals('YUUKO SHIONJI', $token->card->name);

        $this->assertPost('/tokens', $params);
    }

    public function testCreateAcceptsParamsWithoutCardKey()
    {
        $this->mock('tokens/create');

        $params = array(
            'number' => "4242-4242-4242-4242",
            'exp_month' => 12,
            'exp_year' => 2015,
            'cvc' => 123,
            'name' => "YUUKO SHIONJI"
        );
        $token = $this->webpay->tokens->create($params);

        $this->assertPost('/tokens', array('card' => $params));
    }

    public function testRetrieve()
    {
        $this->mock('tokens/retrieve');
        $id = 'tok_3dw2T20rzekM1Kf';
        $token = $this->webpay->tokens->retrieve($id);

        $this->assertEquals($id, $token->id);

        $this->assertGet('/tokens/'.$id);
    }

    /**
     * @expectedException \WebPay\Exception\InvalidRequestException
     */
    public function testRetrieveWithEmptyString()
    {
        try {
            $token = $this->webpay->tokens->retrieve('');
        } catch (\WebPay\Exception\InvalidRequestException $e) {
            $this->assertEquals('id must not be empty', $e->getMessage());
            throw $e;
        }
    }
}
