<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use GuzzleHttp\Psr7\Response;
use Hatcher\Action;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

return new class extends Action{

    public function execute(ServerRequestInterface $request)
    {
        $response = new Response();
        $response = $response->withStatus(500);
        $response->getBody()->write('error page');
        return $response;
    }
};
