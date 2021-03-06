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
    
    public function execute(ServerRequestInterface $request)
    {
        return 'hello world';
    }

};
