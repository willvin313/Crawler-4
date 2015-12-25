<?php

namespace LastCall\Crawler\Handler\Discovery;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use LastCall\Crawler\Common\HasResolvingNormalizer;
use LastCall\Crawler\Common\RedirectDetectionTrait;
use LastCall\Crawler\CrawlerEvents;
use LastCall\Crawler\Event\CrawlerResponseEvent;
use LastCall\Crawler\Uri\MatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Add in URLs that are redirected to, as long as they are matched.
 */
class RedirectDiscoverer implements EventSubscriberInterface
{
    use RedirectDetectionTrait;
    use HasResolvingNormalizer;

    /**
     * @var \LastCall\Crawler\Uri\MatcherInterface
     */
    private $matcher;
    /**
     * @var callable
     */
    private $normalizer;

    public static function getSubscribedEvents()
    {
        return [
            CrawlerEvents::SUCCESS => 'onResponse',
        ];
    }

    public function __construct(
        MatcherInterface $matcher,
        callable $normalizer
    ) {
        $this->matcher = $matcher;
        $this->normalizer = $normalizer;
    }

    public function onResponse(CrawlerResponseEvent $event)
    {
        $response = $event->getResponse();
        if ($this->isRedirectResponse($response)) {
            $request = $event->getRequest();
            $normalizer = $this->getResolvingNormalizer($request->getUri(), $this->normalizer);

            $location = new Uri($response->getHeaderLine('Location'));
            $location = $normalizer($location);

            if ($this->matcher->matches($location) && $this->matcher->matchesHtml($location)) {
                $request = new Request('GET', $location);
                $event->addAdditionalRequest($request);
            }
        }
    }
}
