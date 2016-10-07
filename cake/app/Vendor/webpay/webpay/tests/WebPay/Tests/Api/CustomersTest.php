<?php

namespace WebPay\Tests\Api;

class CustomersTest extends \WebPay\Tests\WebPayTestCase
{
    public function testCreate()
    {
        $this->mock('customers/create');

        $params = array(
            'description' => "Test Customer from Java",
            'email' => "customer@example.com",
            'card' => array(
                'number' => "4242-4242-4242-4242",
                'exp_month' => 12,
                'exp_year' => 2015,
                'cvc' => 123,
                'name' => "YUUKO SHIONJI"
            )
        );
        $customer = $this->webpay->customers->create($params);

        $this->assertEquals('cus_39o4Fv82E1et5Xb', $customer->id);
        $this->assertEquals('Test Customer from Java', $customer->description);
        $this->assertEquals('YUUKO SHIONJI', $customer->activeCard->name);

        $this->assertPost('/customers', $params);
    }

    public function testCreateWithToken()
    {
        $this->mock('customers/create');

        $params = array(
            'description' => "Test Customer from Java",
            'email' => "customer@example.com",
            'card' => 'tok_3dw2T20rzekM1Kf'
        );
        $customer = $this->webpay->customers->create($params);

        $this->assertEquals('Test Customer from Java', $customer->description);

        $this->assertPost('/customers', $params);
    }

    public function testRetrieve()
    {
        $this->mock('customers/retrieve');
        $id = 'cus_39o4Fv82E1et5Xb';
        $customer = $this->webpay->customers->retrieve($id);

        $this->assertEquals($id, $customer->id);
        $this->assertEquals(false, $customer->isDeleted());

        $this->assertGet('/customers/'.$id);
    }

    /**
     * @expectedException \WebPay\Exception\InvalidRequestException
     */
    public function testRetrieveWithEmptyString()
    {
        try {
            $customer = $this->webpay->customers->retrieve('');
        } catch (\WebPay\Exception\InvalidRequestException $e) {
            $this->assertEquals('id must not be empty', $e->getMessage());
            throw $e;
        }
    }

    public function testRetrieveDeletedEntity()
    {
        $this->mock('customers/retrieve_deleted');
        $id = 'cus_7GafGMbML8R28Io';
        $customer = $this->webpay->customers->retrieve($id);

        $this->assertEquals($id, $customer->id);
        $this->assertEquals(true, $customer->deleted);
        $this->assertEquals(true, $customer->isDeleted());

        $this->assertGet('/customers/'.$id);
    }

    public function testAll()
    {
        $this->mock('customers/all');
        $params = array(
            'count' => 3,
            'offset' => 0,
            'created' => array(
                'gt' => 1378000000,
            ),
        );
        $customers = $this->webpay->customers->all($params);

        $this->assertEquals('/v1/customers', $customers->url);
        $this->assertEquals('Test Customer from Java', $customers->data[0]->description);
        $this->assertEquals('YUUKO SHIONJI', $customers->data[0]->active_card->name);

        $this->assertGet('/customers', $params);
    }

    public function testSave()
    {
        $this->mock('customers/retrieve');
        $id = 'cus_39o4Fv82E1et5Xb';
        $customer = $this->webpay->customers->retrieve($id);

        $newParams = array(
            'email' => "newmail@example.com",
            'description' => "New description",
            'card' => array(
                'number' => "4242-4242-4242-4242",
                'exp_month' => 12,
                'exp_year' => 2016,
                'cvc' => 123,
                'name' => "YUUKO SHIONJI",
            ),
        );
        $this->mock('customers/update');
        $customer->setEmail($newParams['email']);
        $customer->setDescription($newParams['description']);
        $customer->setNewCard($newParams['card']);

        $this->assertEquals('newmail@example.com', $customer->email);
        $this->assertEquals(2015, $customer->activeCard->expYear);

        $customer->save();

        $this->assertEquals('newmail@example.com', $customer->email);
        $this->assertEquals(2016, $customer->activeCard->expYear);

        $this->assertPost('/customers/' . $id , $newParams);
    }

    public function testSaveWithToken()
    {
        $this->mock('customers/retrieve');
        $id = 'cus_39o4Fv82E1et5Xb';
        $customer = $this->webpay->customers->retrieve($id);

        $newParams = array(
            'card' => 'tok_3dw2T20rzekM1Kf',
        );
        $this->mock('customers/update');
        $customer->setNewCard($newParams['card']);
        $customer->save();
        $this->assertPost('/customers/' . $id , $newParams);
    }

    public function testSaveSendsOnlySetFields()
    {
        $this->mock('customers/retrieve');
        $id = 'cus_39o4Fv82E1et5Xb';
        $customer = $this->webpay->customers->retrieve($id);

        $this->mock('customers/update');
        $customer->setEmail('newmail@example.com');
        $customer->save();

        $this->assertPost('/customers/' . $id , array('email' => 'newmail@example.com'));
    }

    public function testCallingSaveTwiceSendsNothing()
    {
        $this->mock('customers/retrieve');
        $id = 'cus_39o4Fv82E1et5Xb';
        $customer = $this->webpay->customers->retrieve($id);

        $this->mock('customers/update');
        $customer->setEmail('newmail@example.com');
        $customer->save();
        $this->assertPost('/customers/' . $id , array('email' => 'newmail@example.com'));

        $this->mock('customers/update');
        $customer->save();
        $this->assertPost('/customers/' . $id , array());
    }

    public function testDelete()
    {
        $this->mock('customers/retrieve');
        $id = 'cus_39o4Fv82E1et5Xb';
        $customer = $this->webpay->customers->retrieve($id);

        $this->mock('customers/delete');
        $this->assertEquals(true, $customer->delete());
        $this->assertDelete('/customers/' . $id);
    }
}
