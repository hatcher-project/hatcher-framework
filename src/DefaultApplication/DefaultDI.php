<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\DefaultApplication;

use Hatcher\DirectoryDi;

class DefaultDI extends DirectoryDi
{
    public function __construct(string $directory, array $callParams = [])
    {
        parent::__construct($directory, $callParams);

        $this->set('router', function () {
            return new DefaultRouter();
        });

    }
}
