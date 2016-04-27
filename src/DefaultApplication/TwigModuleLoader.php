<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\DefaultApplication;

use Hatcher\Application;
use Hatcher\ApplicationSegment;

class TwigModuleLoader implements \Twig_LoaderInterface
{
    protected $cache = [];
    protected $errorCache = [];

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var ApplicationSegment
     */
    protected $currentSegment;

    /**
     * @param Application $application
     * @param ApplicationSegment $currentSegment
     */
    public function __construct(Application $application, ApplicationSegment $currentSegment)
    {
        $this->application = $application;
        $this->currentSegment = $currentSegment;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource($name)
    {
        return file_get_contents($this->findTemplate($name));
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name)
    {
        return $this->findTemplate($name);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        $name = $this->normalizeName($name);

        if (isset($this->cache[$name])) {
            return true;
        }

        try {
            return false !== $this->findTemplate($name, false);
        } catch (\Twig_Error_Loader $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($name, $time)
    {
        return filemtime($this->findTemplate($name)) <= $time;
    }

    protected function findTemplate($name)
    {
        $throw = func_num_args() > 1 ? func_get_arg(1) : true;
        $name = $this->normalizeName($name);

        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        if (isset($this->errorCache[$name])) {
            if (!$throw) {
                return false;
            }

            throw new \Twig_Error_Loader($this->errorCache[$name]);
        }

        $this->validateName($name);

        list($module, $shortname) = $this->parseName($name);

        if (null == $module) {
            // currentSegment
            $viewPath = $this->currentSegment->resolvePath('views/' . $shortname);
        } elseif (true === $module) {
            // Root
            $viewPath = $this->application->resolvePath('views/' . $shortname);
        } else {
            // Module
            $viewPath = $this->application
                ->getModuleManager()
                ->getModule($module)
                ->resolvePath('views/' . $shortname);
        }

        if (is_file($viewPath)) {
            return $this->cache[$name] = $viewPath;
        }

        $this->errorCache[$name] = sprintf(
            'Unable to find template "%s"  (File does not exist: %s).',
            $name,
            $viewPath
        );

        if (!$throw) {
            return false;
        }

        throw new \Twig_Error_Loader($this->errorCache[$name]);
    }

    private function parseName($name)
    {
        if (isset($name[0]) && '~' == $name[0]) {
            if (isset($name[1]) && '/' == $name[1]) {
                $shortname = substr($name, 2);
                return [true, $shortname];
            }

            if (false === $pos = strpos($name, ':')) {
                throw new \Twig_Error_Loader(sprintf('Malformed namespaced template name "%s" (expecting "~module:template_name").', $name));
            }

            $module = substr($name, 1, $pos - 1);
            $shortname = substr($name, $pos + 1);

            return [$module, $shortname];
        }

        return [null, $name];
    }

    protected function normalizeName($name)
    {
        return preg_replace('#/{2,}#', '/', str_replace('\\', '/', (string) $name));
    }

    protected function validateName($name)
    {
        if (false !== strpos($name, "\0")) {
            throw new \Twig_Error_Loader('A template name cannot contain NUL bytes.');
        }

        $name = ltrim($name, '/');
        $parts = explode('/', $name);
        $level = 0;
        foreach ($parts as $part) {
            if ('..' === $part) {
                --$level;
            } elseif ('.' !== $part) {
                ++$level;
            }

            if ($level < 0) {
                throw new \Twig_Error_Loader(sprintf('Looks like you try to load a template outside configured directories (%s).', $name));
            }
        }
    }
}
