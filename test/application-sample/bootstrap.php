<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Dotenv\Dotenv;

$composer = include __DIR__ . '/../../vendor/autoload.php';
$GLOBALS['composer'] = $composer;

$env = new Dotenv(__DIR__);
$env->load();

$dev = getenv('DEV');
$dev = $dev === true || $dev === 'true';


return new Application(__DIR__ . '/app', $composer, $dev);
