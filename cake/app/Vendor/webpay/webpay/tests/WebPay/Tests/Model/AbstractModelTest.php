<?php

namespace WebPay\Tests\Model;

class AbstractModelTest extends \PHPUnit_Framework_TestCase
{
    public function testIsset()
    {
        $model = $this
                    ->getMockBuilder('WebPay\Model\AbstractModel')
                    ->setConstructorArgs(array(array('key1' => 'value1', 'under_score' => 'value2')))
                    ->getMockForAbstractClass();

        $this->assertTrue(isset($model->key1));
        $this->assertFalse(isset($model->key2));

        $this->assertTrue(isset($model->underScore));
        $this->assertTrue(isset($model->under_score));
    }
}
