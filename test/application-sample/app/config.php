<?php

namespace Hatcher;

return [

    "modules" => [
        "front" => [
            "host" => "front.hatcher.test",
            "matcher" => moduleMatchesHost("front.hatcher.test", "127.0.0.1")
        ]
    ]

];
