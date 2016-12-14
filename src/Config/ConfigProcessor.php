<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\Config;

use Hatcher\Exception;
use Noodlehaus\Config;

class ConfigProcessor extends Config
{

    private $recursion;

    public function __construct($path, $recursion = 0)
    {
        if ($recursion > 100) {
            throw new Exception('Recursive config import detected');
        }
        $this->recursion = $recursion;
        parent::__construct($path);

        $basePath = dirname($path);
        $this->data = $this->parseImports($this->data, $basePath);
    }

    private function parseImports($parsedArray, $basePath)
    {
        foreach ($parsedArray as $k => $v) {
            if ($k == '_imports') {
                if (!is_array($v)) {
                    throw new Exception('Invalid config format. "imports" should be an array');
                }

                unset($parsedArray[$k]);
                foreach ($v as $import) {
                    $this->processImport($import, $parsedArray, $basePath);
                }
            } elseif (is_array($v)) {
                $parsedArray[$k] = $this->parseImports($v, $basePath);
            }
        }

        return $parsedArray;
    }

    private function processImport($import, &$parsedArray, $basePath)
    {
        if (empty($import)) {
            throw new Exception('invalid import value');
        }
        $config = new self($basePath . '/' . $import, $this->recursion + 1);
        $parsedArray = array_replace_recursive($parsedArray, $config->all());
    }
}
