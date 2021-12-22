<?php

namespace Amp\Http\Client\Interceptor;

use Amp\Cancellation;
use Amp\Http\Client\ApplicationInterceptor;
use Amp\Http\Client\Connection\Stream;
use Amp\Http\Client\DelegateHttpClient;
use Amp\Http\Client\Internal\ForbidCloning;
use Amp\Http\Client\Internal\ForbidSerialization;
use Amp\Http\Client\NetworkInterceptor;
use Amp\Http\Client\Request;
use Amp\Http\Client\Response;

class ModifyRequest implements NetworkInterceptor, ApplicationInterceptor
{
    use ForbidCloning;
    use ForbidSerialization;

    /** @var callable(Request):(\Generator<mixed, mixed, mixed, Promise<Request|null>|Request|null>|Promise<Request|null>|Request|null) */
    private $mapper;

    /**
     * @psalm-param callable(Request):(\Generator<mixed, mixed, mixed,
     *     Promise<Request|null>|Request|null>|Promise<Request|null>|Request|null) $mapper
     */
    public function __construct(callable $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @param Request $request
     * @param Cancellation $cancellation
     * @param Stream $stream
     *
     * @return Response
     */
    final public function requestViaNetwork(
        Request $request,
        Cancellation $cancellation,
        Stream $stream
    ): Response {
        $mappedRequest = ($this->mapper)($request);

        \assert($mappedRequest instanceof Request || $mappedRequest === null);

        return $stream->request($mappedRequest ?? $request, $cancellation);
    }

    public function request(
        Request $request,
        Cancellation $cancellation,
        DelegateHttpClient $httpClient
    ): Response {
        $mappedRequest = ($this->mapper)($request);

        \assert($mappedRequest instanceof Request || $mappedRequest === null);

        return $httpClient->request($mappedRequest ?? $request, $cancellation);
    }
}
