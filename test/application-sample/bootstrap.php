<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Dotenv\Dotenv;

$composer = include __DIR__ . '/../../vendor/autoload.php';
$GLOBALS['composer'] = $composer;


if (file_exists(__DIR__ . '/.env')) {
    $env = new Dotenv(__DIR__);
    $env->load();
    $options = [
        'dev' => getenv('DEV') === 'true',
        'env' => getenv('ENV') ?? 'production'
    ];
} else {
    $options = [
        'dev' => false,
        'env' => 'production'
    ];
}

return new Application(__DIR__ . '/app', $composer, $options);
