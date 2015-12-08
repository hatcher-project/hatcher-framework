<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class ModuleAdapter
{

    public function getOptions(): array
    {
        return [];
    }

    /**
     * Dispatch a request and returns a valid http response
     * @param ServerRequestInterface $request the input request
     * @return ResponseInterface the http response to send back
     */
    abstract public function dispatchRequest(ServerRequestInterface $request);
}
