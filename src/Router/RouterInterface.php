<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\Router;

use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{

    /**
     * Match the given request against the router rules
     * @param ServerRequestInterface $request
     * @return array
     */
    public function match(ServerRequestInterface $request) : MatchedRoute;
}
