<?php

namespace LastCall\Crawler\Event;

use LastCall\Crawler\Crawler;
use LastCall\Crawler\CrawlerSession;
use LastCall\Crawler\Queue\RequestQueueInterface;
use LastCall\Crawler\Url\URLHandler;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\EventDispatcher\Event;


class CrawlerEvent extends Event
{

    /**
     * @var \LastCall\Crawler\Crawler
     */
    private $crawler;

    /**
     * @var \Psr\Http\Message\RequestInterface
     */
    private $request;

    /**
     * @var \LastCall\Crawler\Url\URLHandler
     */
    private $urlHandler;

    /**
     * @var
     */
    private $queue;

    public function __construct(RequestInterface $request, RequestQueueInterface $queue, URLHandler $handler)
    {
        $this->request = $request;
        $this->urlHandler = $handler;
        $this->queue = $queue;
    }

    public function getQueue() {
        return $this->queue;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getUrlHandler()
    {
        return $this->urlHandler;
    }
}