<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Aura\Router\Route;
use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{

    public function match(ServerRequestInterface $request): Route;
    public function add($name, $path, $data = null): Route;

    /**
     * Action matched when an error happens (http 500)
     */
    public function error($data);

    /**
     * Action matched when not route matched or the not found exception was thrown
     */
    public function notFound($data);
}
