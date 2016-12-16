<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\Router;


class MatchedRoute extends \ArrayObject
{

    protected $routeName;
    protected $data;

    /**
     * MatchedRoute constructor.
     * @param string $routeName
     * @param string[] $data
     */
    public function __construct(string $routeName, array $data)
    {
        $this->routeName = $routeName;
        parent::__construct($data);
    }

    /**
     * @return mixed
     */
    public function getRouteName() : string
    {
        return $this->routeName;
    }

    /**
     * @return mixed
     */
    public function getData(string $name, string $default = null) : string
    {
        return $this->data[$name] ?? $default;
    }

}
