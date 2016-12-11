<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\View\Twig\TokenParser;

use Hatcher\View\Twig\Node\HatcherInclude;
use Twig_Error_Syntax;
use Twig_NodeInterface;
use Twig_Token ;

class HatcherTokenParser extends \Twig_TokenParser
{
    public function parse(Twig_Token $token)
    {

        $stream = $this->parser->getStream();

        $subCommand = $stream
            ->expect(Twig_Token::NAME_TYPE)
            ->getValue();


        switch ($subCommand) {
            case 'include':
                return $this->parseInclude($token);
            case 'embed':
                return $this->parseInclude();
            case 'extends':
                return $this->parseInclude();
            default:
                $subTags = ['include', 'embed', 'extends'];
                throw new Twig_Error_Syntax(
                    sprintf(
                        'Unexpected hatcher sub tag. Twig was looking for one of the following sub tag %s.',
                        '"' . implode('", "', $subTags) . '"'
                    ),
                    $stream->getCurrent()->getLine(),
                    $stream->getSourceContext()->getName()
                );
                break;
        }
    }

    private function parseFrom()
    {

        $stream = $this->parser->getStream();

        if ($stream->nextIf(Twig_Token::NAME_TYPE, 'from')) {
            if ($stream->test(Twig_Token::NAME_TYPE)) {
                $fromType = $stream->next()->getValue();
                switch ($fromType) {
                    case 'Module':
                        $name = $stream->expect(Twig_Token::STRING_TYPE);
                        return ['Module', $name->getValue()];
                    case 'App':
                        return ['App', null];
                    default:
                        $acceptedTypes = ['Module', 'App'];
                        throw new Twig_Error_Syntax(
                            sprintf(
                                'Invalid segment type "%s". Twig was looking for one of the following type %s.',
                                $fromType,
                                '"' . implode('", "', $acceptedTypes) . '"'
                            ),
                            $stream->getCurrent()->getLine(),
                            $stream->getSourceContext()->getName()
                        );
                }
            } else {
                throw new Twig_Error_Syntax(
                    sprintf('"from" keyword requires a segment type to be specified.'),
                    $stream->getCurrent()->getLine(),
                    $stream->getSourceContext()->getName()
                );
            }
        } else {
            $name = $stream->getSourceContext()->getName();
            $namespace = substr($name, 1, strpos($name, '/') - 1);

            return explode(':', $namespace);
        }
    }

    private function parseInclude(Twig_Token $token)
    {

        $expr = $this->parser->getExpressionParser()->parseExpression();

        $stream = $this->parser->getStream();

        $from = $this->parseFrom();

        $ignoreMissing = false;
        if ($stream->nextIf(Twig_Token::NAME_TYPE, 'ignore')) {
            $stream->expect(Twig_Token::NAME_TYPE, 'missing');

            $ignoreMissing = true;
        }

        $variables = null;
        if ($stream->nextIf(Twig_Token::NAME_TYPE, 'with')) {
            $variables = $this->parser->getExpressionParser()->parseExpression();
        }

        $only = false;
        if ($stream->nextIf(Twig_Token::NAME_TYPE, 'only')) {
            $only = true;
        }

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new HatcherInclude($expr, $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag(), $from);
    }

    private function parseExtends()
    {
    }

    private function parseEmbed()
    {
    }

    public function getTag()
    {
        return 'hatcher';
    }
}
