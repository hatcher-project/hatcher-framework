<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Aura\Router\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface RouteHandlerInterface
{

    /**
     * Handles arbitrary data returned by a matched route
     * @param $data
     * @return ResponseInterface
     */
    public function handle(Route $route, ServerRequestInterface $request): ResponseInterface;
}
