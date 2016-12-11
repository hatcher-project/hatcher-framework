<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\View\Twig;

use Hatcher\View\Twig\TokenParser\HatcherTokenParser;

/**
 * Hatcher extension for twig
 */
class TwigExtensionHatcher extends \Twig_Extension
{

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return [
            new HatcherTokenParser()
        ];
    }
}
