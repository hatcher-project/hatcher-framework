<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Hatcher\DI;

/**
 * Represents a segment of the application
 *
 * It is located in the file system and offers a service locator
 *
 */
class ApplicationSegment
{

    /**
     * @var DI
     */
    protected $di;

    /**
     * @var string
     */
    protected $directory;



    public function __construct(string $directory, DI $di)
    {
        $this->directory = $directory;
        $this->di = $di;
    }

    /**
     * The application DI
     * @return DI
     */
    public function getDI()
    {
        return $this->di;
    }


    /**
     * Find a path from the application root
     * @param string|null $path
     * @return string
     */
    public function resolvePath($path = null)
    {
        if ($path) {
            return $this->directory . '/' . $path;
        } else {
            return $this->directory;
        }
    }


    /**
     * Provide shortcut to get config object or services
     */
    public function __get($name)
    {
        return  $this->getDI()->get($name);
    }
}
