<?php

namespace Herrera\Annotations\Tests;

use Doctrine\Common\Annotations\DocLexer;
use Herrera\Annotations\Tokenizer;
use Herrera\PHPUnit\TestCase;

class TokenizerTest extends TestCase
{
    /**
     * @var Tokenizer
     */
    private $tokenizer;

    public function getDocblocks()
    {
        return array(
            array(
                <<<DOCBLOCK
/**
 * Ahem.
 *
 * There is no
 *
 * comment here.
 */
DOCBLOCK
                ,
                array(),
                array(),
            ),
            array(
                <<<DOCBLOCK
/**
 * @simple
 */
DOCBLOCK
                ,
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'simple'),
                ),
                array(),
            ),
            array(
                <<<DOCBLOCK
/**
 * @simple()
 */
DOCBLOCK
                ,
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'simple'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                array(),
            ),
            array(
                <<<DOCBLOCK
/**
 * @simple ()
 */
DOCBLOCK
                ,
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'simple'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                array(),
            ),
            array(
                <<<DOCBLOCK
/**
 * @simple(
 * )
 */
DOCBLOCK
                ,
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'simple'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                array(),
            ),
            array(
                <<<DOCBLOCK
/**
 * @simple()
 * @simple\\aliased()
 */
DOCBLOCK
                ,
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'Very\\Simple'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'Very\\Simple\\aliased'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                array(),
                array(
                    'simple' => 'Very\\Simple'
                ),
            ),
            array(
                <<<DOCBLOCK
/**
 * @\\simple\\ simple()
 */
DOCBLOCK
                ,
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, '\simple\simple'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                array(),
            ),
            array(
                <<<DOCBLOCK
/**
 * @ignored
 * \@this\too
 * @!again
 * @but\\not\\this\\one
 */
DOCBLOCK
                ,
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'but\not\this\one')
                ),
                array('ignored'),
            ),
            array(
                <<<DOCBLOCK
/**
 * @a("value")
 */
DOCBLOCK
                ,
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'a'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_STRING, 'value'),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                array(),
            ),
            array(
                <<<DOCBLOCK
/**
 * @an(assigned="value")
 */
DOCBLOCK
                ,
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'an'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_IDENTIFIER, 'assigned'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_STRING, 'value'),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                array(),
            ),
            array(
                <<<DOCBLOCK
/**
 * @an(assigned="value",and="another")
 */
DOCBLOCK
                ,
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'an'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_IDENTIFIER, 'assigned'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_STRING, 'value'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_IDENTIFIER, 'and'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_STRING, 'another'),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                array(),
            ),
            array(
                <<<DOCBLOCK
/**
 * @types(false, 1.23, 123, null, "string", true)
 */
DOCBLOCK
                ,
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'types'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_FALSE, 'false'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_FLOAT, '1.23'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_INTEGER, '123'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_NULL, 'null'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_STRING, 'string'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_TRUE, 'true'),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                array(),
            ),
            array(
                <<<DOCBLOCK
/**
 * @array({})
 */
DOCBLOCK
                ,
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'array'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_OPEN_CURLY_BRACES),
                    array(DocLexer::T_CLOSE_CURLY_BRACES),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                array(),
            ),
            array(
                <<<DOCBLOCK
/**
 * @array({
 *     a=false,
 *     b: 1.23,
 *     c=123,
 *     123: null,
 *     e="string",
 *     "f": true,
 * })
 */
DOCBLOCK
                ,
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'array'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_OPEN_CURLY_BRACES),
                    array(DocLexer::T_IDENTIFIER, 'a'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_FALSE, 'false'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_IDENTIFIER, 'b'),
                    array(DocLexer::T_COLON),
                    array(DocLexer::T_FLOAT, '1.23'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_IDENTIFIER, 'c'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_INTEGER, '123'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_INTEGER, '123'),
                    array(DocLexer::T_COLON),
                    array(DocLexer::T_NULL, 'null'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_IDENTIFIER, 'e'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_STRING, 'string'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_STRING, 'f'),
                    array(DocLexer::T_COLON),
                    array(DocLexer::T_TRUE, 'true'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_CLOSE_CURLY_BRACES),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                array(),
            ),
            array(
                <<<DOCBLOCK
/**
 * @array({
 *     a=false,
 *     b=1.23,
 *     c=123,
 *     d=null,
 *     e="string",
 *     f=true
 * },{
 *     a=false,
 *     b=1.23,
 *     c=123,
 *     d=null,
 *     e="string",
 *     f=true
 * })
 */
DOCBLOCK
                ,
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'array'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_OPEN_CURLY_BRACES),
                    array(DocLexer::T_IDENTIFIER, 'a'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_FALSE, 'false'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_IDENTIFIER, 'b'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_FLOAT, '1.23'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_IDENTIFIER, 'c'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_INTEGER, '123'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_IDENTIFIER, 'd'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_NULL, 'null'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_IDENTIFIER, 'e'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_STRING, 'string'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_IDENTIFIER, 'f'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_TRUE, 'true'),
                    array(DocLexer::T_CLOSE_CURLY_BRACES),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_OPEN_CURLY_BRACES),
                    array(DocLexer::T_IDENTIFIER, 'a'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_FALSE, 'false'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_IDENTIFIER, 'b'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_FLOAT, '1.23'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_IDENTIFIER, 'c'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_INTEGER, '123'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_IDENTIFIER, 'd'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_NULL, 'null'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_IDENTIFIER, 'e'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_STRING, 'string'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_IDENTIFIER, 'f'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_TRUE, 'true'),
                    array(DocLexer::T_CLOSE_CURLY_BRACES),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                array(),
            ),
            array(
                <<<DOCBLOCK
/**
 * @a(@sub)
 */
DOCBLOCK
                ,
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'a'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'sub'),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                array(),
            ),
        );
    }

    public function testInit()
    {
        $this->assertInstanceOf(
            'Doctrine\\Common\\Annotations\\DocLexer',
            $this->getPropertyValue($this->tokenizer, 'lexer')
        );
    }

    /**
     * @dataProvider getDocblocks
     */
    public function testParse($docblock, $tokens, $ignored, array $aliases = array())
    {
        if ($ignored) {
            $this->setPropertyValue($this->tokenizer, 'ignored', $ignored);
        }

        $this->assertSame(
            $tokens,
            $this->tokenizer->parse($docblock, $aliases)
        );
    }

    public function testParseInvalidArrayCommas()
    {
        $this->setExpectedException(
            'Herrera\\Annotations\\Exception\\SyntaxException',
            'Expected Value, received \'@\' at position 5.'
        );

        $this->tokenizer->ignore(array('ignored'));

        $this->tokenizer->parse('/**@a(1,@ignored)*/');
    }

    public function testParseInvalidIdentifier()
    {
        $this->setExpectedException(
            'Herrera\\Annotations\\Exception\\SyntaxException',
            'Expected namespace separator or identifier, received \'\\\' at position 1.'
        );

        $this->tokenizer->parse('/**@\\*/');
    }

    public function testParseInvalidPlainValue()
    {
        $this->setExpectedException(
            'Herrera\\Annotations\\Exception\\SyntaxException',
            'Expected PlainValue, received \'!\' at position 3.'
        );

        $this->tokenizer->parse('/**@a(!)*/');
    }

    public function testParseInvalidMatch()
    {
        $this->setExpectedException(
            'Herrera\\Annotations\\Exception\\SyntaxException',
            'Expected Doctrine\\Common\\Annotations\\DocLexer::T_CLOSE_CURLY_BRACES, received \')\' at position 5.'
        );

        $this->tokenizer->parse('/**@a({x)');
    }

    public function testParseInvalidMatchAny()
    {
        $this->setExpectedException(
            'Herrera\\Annotations\\Exception\\SyntaxException',
            'Expected Doctrine\\Common\\Annotations\\DocLexer::T_INTEGER or Doctrine\\Common\\Annotations\\DocLexer::T_STRING, received \'@\' at position 4.'
        );

        $this->tokenizer->parse('/**@a({@:1})');
    }

    public function testIgnored()
    {
        $ignore = array(
            'abc',
            'def',
        );

        $this->tokenizer->ignore($ignore);

        $this->assertEquals(
            $ignore,
            $this->getPropertyValue($this->tokenizer, 'ignored')
        );
    }

    protected function setUp()
    {
        $this->tokenizer = new Tokenizer();
    }
}
