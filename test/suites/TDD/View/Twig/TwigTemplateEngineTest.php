<?php

/**
 * @license see LICENSE
 */

namespace Hatcher\Test\TDD\View\Twig;

use Hatcher\Test\HatcherTestCase;
use Hatcher\View\Twig\TwigTemplateEngine;

/**
 * @covers Hatcher\View\Twig\TwigTemplateEngine
 * @covers Hatcher\View\Twig\TwigApplicationLoader
 * @covers Hatcher\View\Twig\TwigExtensionHatcher
 * @covers Hatcher\View\Twig\TokenParser\HatcherTokenParser
 * @covers Hatcher\View\Twig\Node\HatcherInclude
 */
class TwigTemplateEngineTest extends HatcherTestCase
{

    public function testRender()
    {
        $tte = new TwigTemplateEngine($this->generateApplication());
        $this->assertEquals('foo from frontend', $tte->render('@Module:frontend/foo.twig', []));
        $this->assertEquals('bar from frontend', $tte->render('@Module:frontend/bar.twig', []));
        $this->assertEquals(
            '[foo from frontend bar from frontend] from frontend',
            $tte->render('@Module:frontend/foobar.twig', [])
        );
        $this->assertEquals(
            '[foo from app bar from frontend] from frontend',
            $tte->render('@Module:frontend/foo:app+bar:frontend.twig', [])
        );
    }
}
