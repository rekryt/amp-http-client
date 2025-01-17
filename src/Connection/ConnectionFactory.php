<?php declare(strict_types=1);

namespace Amp\Http\Client\Connection;

use Amp\Cancellation;
use Amp\Http\Client\Request;

interface ConnectionFactory
{
    /**
     * During connection establishment, the factory must call the {@see EventListener::startConnectionCreation()},
     * {@see EventListener::startTlsNegotiation()}, {@see EventListener::completeTlsNegotiation()}, and
     * {@see EventListener::completeConnectionCreation()} on all event listeners registered on the given request in the
     * order defined by {@see Request::getEventListeners()} as appropriate (TLS events are only invoked if TLS is
     * used). Before calling the next listener, the promise returned from the previous one must resolve successfully.
     *
     * Additionally, the factory may invoke {@see EventListener::startDnsResolution()} and
     * {@see EventListener::completeDnsResolution()}, but is not required to implement such granular events.
     */
    public function create(Request $request, Cancellation $cancellation): Connection;
}
