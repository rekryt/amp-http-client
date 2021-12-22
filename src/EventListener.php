<?php

namespace Amp\Http\Client;

use Amp\Http\Client\Connection\ConnectionPool;
use Amp\Http\Client\Connection\Stream;

/**
 * Allows listening to more fine granular events than interceptors are able to achieve.
 *
 * All event listener methods might be called multiple times for a single request. The implementing listener is
 * responsible to detect another call, e.g. via attributes in the request.
 */
interface EventListener
{
    /**
     * Called at the very beginning of {@see DelegateHttpClient::request()}.
     *
     * @param Request $request
     */
    public function startRequest(Request $request): void;

    /**
     * Optionally called by {@see ConnectionPool::getStream()} before DNS resolution is started.
     *
     * @param Request $request
     */
    public function startDnsResolution(Request $request): void;

    /**
     * Optionally called by {@see ConnectionPool::getStream()} after DNS resolution is completed.
     *
     * @param Request $request
     */
    public function completeDnsResolution(Request $request): void;

    /**
     * Called by {@see ConnectionPool::getStream()} before a new connection is initiated.
     *
     * @param Request $request
     */
    public function startConnectionCreation(Request $request): void;

    /**
     * Called by {@see ConnectionPool::getStream()} after a new connection is established and TLS negotiated.
     *
     * @param Request $request
     */
    public function completeConnectionCreation(Request $request): void;

    /**
     * Called by {@see ConnectionPool::getStream()} before TLS negotiation is started (only if HTTPS is used).
     *
     * @param Request $request
     */
    public function startTlsNegotiation(Request $request): void;

    /**
     * Called by {@see ConnectionPool::getStream()} after TLS negotiation is successful (only if HTTPS is used).
     *
     * @param Request $request
     */
    public function completeTlsNegotiation(Request $request): void;

    /**
     * Called by {@see Stream::request()} before the request is sent.
     *
     * @param Request $request
     * @param Stream  $stream
     */
    public function startSendingRequest(Request $request, Stream $stream): void;

    /**
     * Called by {@see Stream::request()} after the request is sent.
     *
     * @param Request $request
     * @param Stream  $stream
     */
    public function completeSendingRequest(Request $request, Stream $stream): void;

    /**
     * Called by {@see Stream::request()} after the first response byte is received.
     *
     * @param Request $request
     * @param Stream  $stream
     */
    public function startReceivingResponse(Request $request, Stream $stream): void;

    /**
     * Called by {@see Stream::request()} after the request is complete.
     *
     * @param Request $request
     * @param Stream  $stream
     */
    public function completeReceivingResponse(Request $request, Stream $stream): void;

    /**
     * Called if the request is aborted.
     *
     * @param Request    $request
     * @param \Throwable $cause
     */
    public function abort(Request $request, \Throwable $cause): void;
}
