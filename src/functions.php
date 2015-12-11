<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Helper to get a module hostname/domain matcher for a request
 *
 * @param string[] ...$hosts list of host names that can match
 * @return callable the handler to match the hostname
 */
function moduleMatchesHost(string ...$hosts): callable
{
    return function (ServerRequestInterface $request) use ($hosts) {
        $reqHost = $addr = $request->getServerParams()["REMOTE_ADDR"];
        return in_array($reqHost, $hosts);
    };
}
