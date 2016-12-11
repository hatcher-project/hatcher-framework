<?php

/**
 * @license see LICENSE
 */

namespace Hatcher\View;

use Hatcher\Exception;

class ViewManager implements TemplateEngineInterface
{

    private $extensionTemplateEngineMatch;

    /**
     * @var TemplateEngineInterface[]
     */
    private $templateEngines;

    /**
     * Find the template matching the template name and renders this template
     * @param string $name
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    public function render(string $name, array $data = [])
    {
        $extension = substr($name, strrpos($name, '.') + 1);

        if (!isset($this->extensionTemplateEngineMatch[$extension])) {
            throw new Exception(
                "No template engine was registered for extension '.$extension' (trying to render $name)"
            );
        }

        $engineName = $this->extensionTemplateEngineMatch[$extension];
        return $this->templateEngines[$engineName]->render($name, $data);
    }

    public function addTemplateEngine(string $name, TemplateEngineInterface $templateEngine, array $extensions)
    {
        $this->templateEngines[$name] = $templateEngine;
        foreach ($extensions as $extension) {
            $this->extensionTemplateEngineMatch[$extension] = $name;
        }
    }
}
