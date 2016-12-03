<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Hatcher\Action;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

return new class extends Action{

    public function middlewares()
    {
        return [
            function (ServerRequestInterface $request, $next) {
                /** @var ResponseInterface $response */
                $response = $next($request);

                return $response->withHeader('foobar', 'baz');
            },

            function (ServerRequestInterface $request, $next) {
                $request = $request->withHeader('homer', 'simpsons');
                return $next($request);
            },

            function (ServerRequestInterface $request, $next) {
                /** @var ResponseInterface $response */
                $response = $next($request);

                return $response->withHeader('homerValue', $request->getHeaderLine('homer'));
            }
        ];
    }

    public function execute(ServerRequestInterface $request)
    {
        return 'Homer is: ' . $request->getHeaderLine('homer');
    }
};
