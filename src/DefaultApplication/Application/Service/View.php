<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\DefaultApplication\Application\Service;

use Hatcher\Application;
use Hatcher\View\Twig\TwigTemplateEngine;
use Hatcher\View\ViewManager;

class View
{

    public function __invoke(Application $application)
    {
        $viewManager = new ViewManager();
        $viewManager->addTemplateEngine('twig', new TwigTemplateEngine($application), ['twig']);
        return $viewManager;
    }
}
