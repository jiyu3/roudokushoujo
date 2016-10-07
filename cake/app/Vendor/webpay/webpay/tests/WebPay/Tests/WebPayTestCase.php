<?php

namespace WebPay\Tests;

use WebPay\WebPay;

class WebPayTestCase extends \Guzzle\Tests\GuzzleTestCase
{
    protected $webpay;
    protected $lastPlugin;

    public function setup()
    {
        $this->webpay = new WebPay('test_key', 'http://api.example.com');
    }

    protected function mock($file)
    {
        $this->lastPlugin = $plugin = new \Guzzle\Plugin\Mock\MockPlugin();
        $plugin->addResponse(__DIR__ . '/../../mock/' . $file . '.txt');
        $this->webpay->addSubscriber($plugin);
    }

    protected function assertPost($path, $params)
    {
        $requests = $this->lastPlugin->getReceivedRequests();
        $request = $requests[0];
        $this->assertRequest($request, $path);
        if ($params != null && is_array($params)) {
            $this->assertEquals($params, $request->getPostFields()->toArray());
        }
    }

    protected function assertGet($path, $params = null)
    {
        $requests = $this->lastPlugin->getReceivedRequests();
        $request = $requests[0];
        $this->assertRequest($request, $path);
        if ($params != null && is_array($params)) {
            $this->assertEquals($params, $request->getQuery()->toArray());
        }
    }

    protected function assertDelete($path)
    {
        $requests = $this->lastPlugin->getReceivedRequests();
        $request = $requests[0];
        $this->assertRequest($request, $path);
    }

    private function assertRequest($request, $path)
    {
        $this->assertEquals('api.example.com', $request->getHost());
        $this->assertEquals('/v1' . $path, $request->getPath());
        $this->assertEquals('test_key', $request->getUsername());
    }
}
