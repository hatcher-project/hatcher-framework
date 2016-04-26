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
    public function add($name, $path, $data): Route;
}
