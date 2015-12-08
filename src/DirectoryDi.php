<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

class DirectoryDi extends DI
{

    protected $directory;

    public function __construct($directory)
    {
        $this->directory = $directory;
        parent::__construct();
    }

    /**
     * get a service from the di
     * @param $what
     * @return mixed the service object
     */
    public function get($what)
    {
        if (!isset($this->container[$what])) {
            $service = include $this->directory . "/$what.php";
            if (! is_callable($service)) {
                throw new Exception("Bad service type. The file $this->directory/$what.php should return a callable");
            }
            $this->set($what, $service);
        }
        return $this->container[$what];
    }

    public function has($what)
    {
        return isset($this->container[$what]) ?? file_exists($this->directory . "/$what.php");
    }
}
