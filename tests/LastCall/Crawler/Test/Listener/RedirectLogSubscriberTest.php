<?php

namespace LastCall\Crawler\Test\Listener;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LastCall\Crawler\Configuration\Configuration;
use LastCall\Crawler\Crawler;
use LastCall\Crawler\Event\CrawlerResponseEvent;
use LastCall\Crawler\Listener\RedirectLogSubscriber;
use LastCall\Crawler\Url\URLHandler;
use Prophecy\Argument;
use LastCall\Crawler\Queue\QueueInterface;

class RedirectLogSubscriberTest extends \PHPUnit_Framework_TestCase
{


    public function testLogsRedirect()
    {
        $logger = $this->prophesize('Psr\Log\LoggerInterface');
        $queue = $this->prophesize(QueueInterface::class);
        $urlHandler = new URLHandler('http://google.com');

        $request = new Request('GET', 'http://google.com');
        $response = new Response(302, ['Location' => '/foo']);

        $event = new CrawlerResponseEvent($request, $response, $queue->reveal(), $urlHandler);
        $subscriber = new RedirectLogSubscriber($logger->reveal());
        $subscriber->onRequestSuccess($event);

        $logger->info('Redirect 302 detected from http://google.com to http://google.com/foo', Argument::type('array'))
          ->shouldHaveBeenCalled();
    }
}