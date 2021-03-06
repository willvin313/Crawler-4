<?php

namespace LastCall\Crawler\Handler;

use LastCall\Crawler\CrawlerEvents;
use LastCall\Crawler\Event\CrawlerRequestEvent;
use LastCall\Crawler\Uri\MatcherInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Proxies calls to a handler, blocking events fired on pages that don't
 * match a given URI pattern.
 */
class MatchingProxy implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventSubscriberInterface
     */
    private $proxied;

    /**
     * @var \LastCall\Crawler\Uri\MatcherInterface
     */
    private $matcher;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    private $dispatcher;

    public function __construct(EventSubscriberInterface $subscriber, MatcherInterface $matcher)
    {
        $this->proxied = $subscriber;
        $this->matcher = $matcher;
        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addSubscriber($subscriber);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            CrawlerEvents::SETUP => 'proxyEvent',
            CrawlerEvents::TEARDOWN => 'proxyEvent',
            CrawlerEvents::FINISH => 'proxyEvent',
            CrawlerEvents::SENDING => 'checkAndProxyEvent',
            CrawlerEvents::SUCCESS => 'checkAndProxyEvent',
            CrawlerEvents::FAILURE => 'checkAndProxyEvent',
            CrawlerEvents::EXCEPTION => 'checkAndProxyEvent',
        ];
    }

    public function proxyEvent(Event $event, $name)
    {
        $this->dispatcher->dispatch($name, $event);
    }

    public function checkAndProxyEvent(CrawlerRequestEvent $event, $name)
    {
        $uri = $event->getRequest()->getUri();
        if ($this->matcher->matches($uri)) {
            $this->dispatcher->dispatch($name, $event);
        }
    }
}
