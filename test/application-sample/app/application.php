<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

return function (Application $application) {
    return [
        'modules' => [
            'frontend' => [
                'matcher' => moduleMatchesHost('front.hatcher.test', '127.0.0.1', 'localhost')
            ]
        ]
    ];
};
