<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

class DirectoryDi extends DI
{

    /**
     * @var string
     */
    protected $directory;

    public function __construct(string $directory, array $callParams = [])
    {
        parent::__construct($callParams);
        $this->directory = $directory;
    }

    /**
     * get a service from the di
     * @param $what
     * @return mixed the service object
     */
    public function get($what)
    {
        if (!$this->registered($what)) {
            $file = $this->directory . "/$what.php";
            if (!file_exists($file)) {
                throw new Exception('Service "' . $what . '" does not exist in ' . $this->directory);
            }
            $service = include $file;
            if (!is_callable($service)) {
                throw new Exception(sprintf('Bad service type. The file%s should return a callable', $file));
            }
            $this->set($what, $service);
        }
        return parent::get($what);
    }
}
