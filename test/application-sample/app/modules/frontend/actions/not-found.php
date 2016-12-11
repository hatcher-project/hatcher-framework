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
        return $this->response()
            ->statusCode(404)
            ->html('custom not found');
    }
};
