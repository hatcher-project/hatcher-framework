<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\View\Twig;

use Hatcher\Application;
use Hatcher\Exception;
use Hatcher\View\TemplateEngineInterface;

class TwigTemplateEngine implements TemplateEngineInterface
{

    /**
     * @var \Twig_Environment
     */
    protected $te;

    /**
     * @var Application
     */
    protected $application;

    /**
     * TwigTemplateEngine constructor.
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;

        $loader = new TwigApplicationLoader($application);

        $this->te = new \Twig_Environment($loader, [
            'cache' => $application->getCacheDirectory() . '/view/twig',
            'debug' => $application->isDev()
        ]);

        $this->te->addExtension(new TwigExtensionHatcher());
    }


    /**
     * @inheritdoc
     */
    public function render(string $name, array $data = [])
    {
        return $this->te->render($name, $data);
    }

    /**
     * an helper function to resolve template names during template rendering
     */
    public static function resolveTemplateName($name, $from)
    {
        if (is_string($name)) {
            return self::resolveTemplateNameString($name, $from);
        } elseif (is_array($name)) {
            foreach ($name as $k => $v) {
                $name[$k] = self::resolveTemplateNameString($v, $from);
            }
            return $name;
        } else {
            throw new Exception('invalid type for template name');
        }
    }

    private static function resolveTemplateNameString($name, $from)
    {
        if (isset($name{0}) && $name{0} != '@') {
            if (is_string($from)) {
                return $from . '/' . $name;
            } elseif (is_array($from)) {
                return '@' . $from[0] . ($from[1] ? ':' . $from[1] : '') . '/' . $name;
            }
        } else {
            return $name;
        }
    }
}
