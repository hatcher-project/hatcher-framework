<?php

namespace Hatcher\Config;

use Hatcher\Config;

/**
 * @license see LICENSE
 */
class ConfigFactory
{

    protected $file;
    protected $format;
    protected $cache;

    /**
     * ConfigFactory constructor.
     * @param $file
     * @param $format
     * @param $cache
     */
    public function __construct(string $file, string $format, $cache = null)
    {
        $this->file = $file;
        $this->format = $format;
        $this->cache = $cache;
    }


    public function read()
    {
        // TODO cache

        $config = null;
        switch ($this->format) {
            case 'php':
                $config = include $this->file;
                break;
        }

        if (null==$config) {
            throw new \Exception("Config reader type $this->format does not exist");
        } else {
            return new Config($config);
        }

    }
}
