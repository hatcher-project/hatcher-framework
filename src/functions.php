<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Psr\Http\Message\ResponseInterface;
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

/**
 * Send http response
 *
 * from zend diactoros emitter
 *
 * @param ResponseInterface $response
 * @throws Exception
 */
function sendResponse(ResponseInterface $response)
{
    if (headers_sent()) {
        throw new \Hatcher\Exception('Unable to emit response; headers already sent');
    }

    // Status code
    http_response_code($response->getStatusCode());

    // Headers
    $headers = $response->getHeaders();
    foreach ($headers as $header => $values) {
        $first = true;
        foreach ($values as $value) {
            header($header . ':' . $value, $first);
            $first = false;
        }
    }

    // Flush ob
    $maxBufferLevel = ob_get_level();
    while (ob_get_level() > $maxBufferLevel) {
        ob_end_flush();
    }

    // Body
    echo $response->getBody();
}
