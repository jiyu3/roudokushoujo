<?php

namespace WebPay\Tests\Api;

class AccountTest extends \WebPay\Tests\WebPayTestCase
{
    public function testRetrieve()
    {
        $this->mock('account/retrieve');
        $account = $this->webpay->account->retrieve();

        $this->assertEquals('acct_2Cmdexb7J2r78rz', $account->id);
        $this->assertEquals('test-me@example.com', $account->email);
        $this->assertEquals(array('jpy'), $account->currenciesSupported);

        $this->assertGet('/account');
    }

    public function testDeleteData()
    {
        $this->mock('account/delete');
        $this->assertEquals(true, $this->webpay->account->deleteData());
        $this->assertDelete('/account/data');
    }
}
