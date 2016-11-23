<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Hatcher\Action;
use Zend\Diactoros\Response\HtmlResponse;

return new class extends Action{

    public function execute()
    {

        $value = $this->application->config->get('something', 'not found');

        return new HtmlResponse($value);
    }

};
