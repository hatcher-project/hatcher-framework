<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

return [

    'cache-directory' => 'cache',

    'modules' => [
        'frontend' => [
            'matcher' => moduleMatchesHost('front.hatcher.test', '127.0.0.1', 'localhost')
        ]
    ]

];

// Same as :

//return function (Application $application) {
//    $moduleManager = $application->getModuleManager();
//    $moduleManager->registerModule('frontend', moduleMatchesHost('front.hatcher.test', '127.0.0.1'));
//};
//
