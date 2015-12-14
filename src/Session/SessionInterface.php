<?php

namespace LastCall\Crawler\Session;

use GuzzleHttp\ClientInterface;
use LastCall\Crawler\Queue\RequestQueueInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Contains data about the current crawler session and dispatches
 * events out to the subscribers.
 */
interface SessionInterface
{

    /**
     * Get the start URL for this session.
     *
     * @param string $startUrl
     *
     * @return string
     */
    public function getStartUrl($startUrl = null);

    /**
     * Add a request to the current session.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return void
     */
    public function addRequest(RequestInterface $request);

    /**
     * Get the request queue.
     *
     * @return RequestQueueInterface
     */
    public function getQueue();

    /**
     * Gets the Guzzle HTTP client configured for this session.
     *
     * @return ClientInterface
     */
    public function getClient();

    /**
     * Check whether the session has completed.
     *
     * @return bool
     */
    public function isFinished();

    /**
     * Dispatch a setup event.
     */
    public function onSetup();

    /**
     * Dispatch a teardown event.
     */
    public function onTeardown();

    /**
     * Dispatch a request sending event.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return void
     */
    public function onRequestSending(RequestInterface $request);

    /**
     * Dispatch a request success event.
     *
     * @param \Psr\Http\Message\RequestInterface  $request
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return void
     */
    public function onRequestSuccess(
        RequestInterface $request,
        ResponseInterface $response
    );

    /**
     * Dispatch a request failure event.
     *
     * @param \Psr\Http\Message\RequestInterface  $request
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return void
     */
    public function onRequestFailure(
        RequestInterface $request,
        ResponseInterface $response
    );

    /**
     * Dispatch a request exception event.
     *
     * @param \Psr\Http\Message\RequestInterface       $request
     * @param \Exception                               $exception
     * @param \Psr\Http\Message\ResponseInterface|null $response
     *
     * @return vod
     */
    public function onRequestException(
        RequestInterface $request,
        \Exception $exception,
        ResponseInterface $response = null
    );
}