<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\View\Twig;

use Hatcher\Application;

class TwigApplicationLoader extends \Twig_Loader_Filesystem
{

    /**
     * @var Application
     */
    protected $application;

    /**
     * TwigLoader constructor.
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
        parent::__construct([], $application->resolvePath());
    }


    protected function parseName($name, $default = self::MAIN_NAMESPACE)
    {
        $parsed = parent::parseName($name, $default);

        $namespace = $parsed[0];


        if (!isset($this->paths[$namespace])) {
            if (false !== $pos = strpos($namespace, ':')) {
                $segmentType = substr($namespace, 0, $pos);
                $segmentName = substr($namespace, $pos + 1);
                switch ($segmentType) {
                    case 'Module':
                        if ($this->application->getModuleManager()->hasModule($segmentName)) {
                            $this->addPath("modules/$segmentName/views", $namespace);
                        }
                        // else we let twig managing the unexisting namespace error by itself
                        break;
                    // TODO: extension
                }
            } elseif ($namespace == 'App' || $namespace == self::MAIN_NAMESPACE) {
                $this->addPath('views', 'App');
                $this->addPath('views');
            }
        }

        return $parsed;
    }
}
