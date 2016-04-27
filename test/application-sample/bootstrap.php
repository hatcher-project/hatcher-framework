<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response\SapiEmitter;

$composer = include __DIR__ . '/../../vendor/autoload.php';
$GLOBALS['composer'] = $composer;

return new Application(__DIR__ . '/app', $composer, ['dev' => false]);
