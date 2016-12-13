<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

/**
 * Classes that extends that module are declared as belonging to this application
 */
interface ApplicationAwareInterface
{
    /**
     * @return Application
     */
    public function getApplication(): Application;
}
