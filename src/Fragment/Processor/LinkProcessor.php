<?php

namespace LastCall\Crawler\Fragment\Processor;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use LastCall\Crawler\Event\CrawlerResponseEvent;
use LastCall\Crawler\Fragment\FragmentSubscription;
use LastCall\Crawler\Uri\MatcherInterface;
use LastCall\Crawler\Uri\Normalizations;
use LastCall\Crawler\Uri\Normalizer;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class LinkProcessor implements FragmentProcessorInterface
{
    public function getSubscribedMethods()
    {
        return [
            new FragmentSubscription($this, 'xpath',
                'descendant-or-self::a[@href]', 'processLinks'),
        ];
    }

    private $matcher;
    private $normalizer;

    public function __construct(
        MatcherInterface $matcher,
        callable $normalizer
    ) {
        $this->matcher = $matcher;
        $this->normalizer = $normalizer;
    }

    public function processLinks(
        CrawlerResponseEvent $event,
        DomCrawler $crawler
    ) {
        $urls = array_unique($crawler->extract(['href']));

        $request = $event->getRequest();
        $resolve = Normalizations::resolve($request->getUri());
        $normalizer = $this->normalizer;

        foreach ($urls as $url) {
            $uri = new Uri($url);
            $uri = $resolve($uri);
            $uri = $normalizer($uri);

            if ($this->matcher->matches($uri)) {
                $newRequest = new Request('GET', $uri);
                $event->addAdditionalRequest($newRequest);
            }
        }
    }
}
