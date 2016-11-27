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

$options = [
    'dev' => getenv('DEV') === 'true',
    'env' => getenv('ENV') ?? 'production'
];

return new Application(__DIR__ . '/app', $composer, $options);
