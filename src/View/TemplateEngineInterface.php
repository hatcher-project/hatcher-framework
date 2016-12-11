<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\View;

interface TemplateEngineInterface
{

    /**
     * Renders a template given a name and data
     * @param $name
     * @param $data
     * @return mixed
     */
    public function render(string $name, array $data = []);
}
