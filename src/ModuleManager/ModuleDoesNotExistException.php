<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\ModuleManager;

use Exception;
use Hatcher\HatcherException;

class ModuleDoesNotExistException extends HatcherException
{

    public $moduleName;

    public function __construct($moduleName, $code = 0, Exception $previous = null)
    {
        parent::__construct("The module $moduleName was not found", $code, $previous);
        $this->moduleName = $moduleName;
    }
}
