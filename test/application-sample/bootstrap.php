<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

$composer = include __DIR__ . '/../../vendor/autoload.php';
$GLOBALS['composer'] = $composer;

return new Application(__DIR__ . '/app', $composer, ['dev' => false]);
