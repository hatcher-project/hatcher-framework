<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Hatcher\Action;
use Zend\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

return new class extends Action{

    public function execute(ServerRequestInterface $request)
    {
        return 'Customer: ' . $this->data['id'];
    }

};
