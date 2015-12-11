<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Pimple\Container;

class DI
{

    /**
     * @var Container
     */
    protected $callables = [];
    protected $processed = [];

    protected $callParams;

    public function __construct(array $callParams = [])
    {
        $this->callParams = $callParams;
    }

    /**
     * get a service from the di
     * @param $what
     * @return mixed the service object
     */
    public function get($what)
    {
        if (!isset($this->callables[$what])) {
            throw new \InvalidArgumentException(sprintf('Service "%s" does not exist.', $what));
        }

        if (!isset($this->processed[$what])) {
            $this->processed[$what] = call_user_func_array($this->callables[$what], $this->callParams);
        }

        return $this->processed[$what];
    }

    /**
     * add a service to the di
     * @param $what
     * @param callable $callable
     */
    public function set($what, callable $callable)
    {
        $this->callables[$what] = $callable;
    }

    /**
     * check if a service is registered
     * @param $what
     * @return bool
     */
    public function registered($what)
    {
        return isset($this->callables[$what]);
    }

    /**
     * Check if the service was processed
     * @param $what
     * @return bool
     */
    public function processed($what)
    {
        return isset($this->processed[$what]);
    }

    /**
     * List of services that were processed
     * @return array
     */
    public function getProcessed()
    {
        return array_keys($this->processed);
    }
}
