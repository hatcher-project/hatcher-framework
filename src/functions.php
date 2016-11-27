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
 * @return array the handler to match the hostname
 */
function moduleMatchesHost(string ...$hosts)
{
    return [
        'function' => 'Hatcher\\requestMatchesHost',
        'data' => $hosts
    ];
}

/**
 * true if the given request matches one of the given hosts
 * @param ServerRequestInterface $request
 * @param string[] ...$hosts
 * @return bool
 */
function requestMatchesHost(ServerRequestInterface $request, string ...$hosts)
{
    $reqHost = $request->getServerParams()['SERVER_NAME'];
    return in_array($reqHost, $hosts);
}
