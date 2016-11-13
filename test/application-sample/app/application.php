<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

return [

    'modules' => [
        'frontend' => moduleMatchesHost('front.hatcher.test', '127.0.0.1')
    ]

];

// Same as :

//return function (Application $application) {
//    $moduleManager = $application->getModuleManager();
//    $moduleManager->registerModule('frontend', moduleMatchesHost('front.hatcher.test', '127.0.0.1'));
//};
//
