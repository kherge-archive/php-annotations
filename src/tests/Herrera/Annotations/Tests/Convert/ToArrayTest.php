<?php

namespace Herrera\Annotations\Tests\Convert;

use Doctrine\Common\Annotations\DocLexer;
use Herrera\Annotations\Convert\ToArray;
use Herrera\Annotations\Tokens;
use Herrera\Annotations\Sequence;
use Herrera\PHPUnit\TestCase;

class ToArrayTest extends TestCase
{
    /**
     * @var ToArray
     */
    protected $converter;

    public function getToken()
    {
        return array(

            array(
                array(),
                array()
            ),

            array(
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'simple'),
                ),
                array(
                    (object) array(
                        'name' => 'simple',
                        'values' => array(),
                    )
                )
            ),

            array(
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'simple'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                array(
                    (object) array(
                        'name' => 'simple',
                        'values' => array(),
                    )
                )
            ),

            array(
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'a'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_STRING, 'value'),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                array(
                    (object) array(
                        'name' => 'a',
                        'values' => array(
                            'value'
                        ),
                    )
                )
            ),

            array(
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'an'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_IDENTIFIER, 'assigned'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_STRING, 'value'),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                array(
                    (object) array(
                        'name' => 'an',
                        'values' => array(
                            'assigned' => 'value',
                        ),
                    )
                )
            ),

            array(
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
                array(
                    (object) array(
                        'name' => 'an',
                        'values' => array(
                            'assigned' => 'value',
                            'and' => 'another',
                        ),
                    )
                )
            ),

            array(
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'types'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_FALSE, 'FALSE'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_FLOAT, '1.23'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_INTEGER, '123'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_NULL, 'NULL'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_STRING, 'string'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_TRUE, 'TRUE'),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                array(
                    (object) array(
                        'name' => 'types',
                        'values' => array(
                            false,
                            1.23,
                            123,
                            null,
                            'string',
                            true
                        ),
                    )
                )
            ),

            array(
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'array'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_OPEN_CURLY_BRACES),
                    array(DocLexer::T_CLOSE_CURLY_BRACES),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                array(
                    (object) array(
                        'name' => 'array',
                        'values' => array(
                            array()
                        ),
                    )
                )
            ),

            array(
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'array'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_OPEN_CURLY_BRACES),
                    array(DocLexer::T_IDENTIFIER, 'a'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_FALSE, 'FALSE'),
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
                    array(DocLexer::T_NULL, 'NULL'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_IDENTIFIER, 'e'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_STRING, 'string'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_STRING, 'f'),
                    array(DocLexer::T_COLON),
                    array(DocLexer::T_TRUE, 'TRUE'),
                    array(DocLexer::T_CLOSE_CURLY_BRACES),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                array(
                    (object) array(
                        'name' => 'array',
                        'values' => array(
                            array(
                                'a' => false,
                                'b' => 1.23,
                                'c' => 123,
                                123 => null,
                                'e' => 'string',
                                'f' => true,
                            )
                        ),
                    )
                )
            ),

            array(
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'array'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_OPEN_CURLY_BRACES),
                    array(DocLexer::T_IDENTIFIER, 'a'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_FALSE, 'FALSE'),
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
                    array(DocLexer::T_NULL, 'NULL'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_IDENTIFIER, 'e'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_STRING, 'string'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_STRING, 'f'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_TRUE, 'TRUE'),
                    array(DocLexer::T_CLOSE_CURLY_BRACES),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_OPEN_CURLY_BRACES),
                    array(DocLexer::T_IDENTIFIER, 'a'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_FALSE, 'FALSE'),
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
                    array(DocLexer::T_NULL, 'NULL'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_IDENTIFIER, 'e'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_STRING, 'string'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_STRING, 'f'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_TRUE, 'TRUE'),
                    array(DocLexer::T_CLOSE_CURLY_BRACES),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                array(
                    (object) array(
                        'name' => 'array',
                        'values' => array(
                            array(
                                'a' => false,
                                'b' => 1.23,
                                'c' => 123,
                                'd' => null,
                                'e' => 'string',
                                'f' => true,
                            ),
                            array(
                                'a' => false,
                                'b' => 1.23,
                                'c' => 123,
                                'd' => null,
                                'e' => 'string',
                                'f' => true,
                            )
                        ),
                    )
                )
            ),

            array(
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'a'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'sub'),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                array(
                    (object) array(
                        'name' => 'a',
                        'values' => array(
                            (object) array(
                                'name' => 'sub',
                                'values' => array()
                            )
                        ),
                    )
                )
            ),

            array(
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'My\\Annotation'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_IDENTIFIER, 'name'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_STRING, 'this is the name'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_IDENTIFIER, 'an'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'Assigned\\Annotation'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_IDENTIFIER, 'array'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_OPEN_CURLY_BRACES),
                    array(DocLexer::T_INTEGER, '123'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_STRING, 'sub test'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_IDENTIFIER, 'also'),
                    array(DocLexer::T_COLON),
                    array(DocLexer::T_STRING, 'assigned'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'One\\More\\Annotation'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_STRING, '!'),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                    array(DocLexer::T_CLOSE_CURLY_BRACES),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                array(
                    (object) array(
                        'name' => 'My\\Annotation',
                        'values' => array(
                            'name' => 'this is the name',
                            'an' => (object) array(
                                'name' => 'Assigned\\Annotation',
                                'values' => array(),
                            ),
                            'array' => array(
                                123,
                                'sub test',
                                'also' => 'assigned',
                                (object) array(
                                    'name' => 'One\\More\\Annotation',
                                    'values' => array(
                                        '!'
                                    )
                                )
                            )
                        ),
                    )
                )
            ),

        );
    }

    /**
     * @dataProvider getToken
     */
    public function testConvert($tokens, $expected)
    {
        $this->assertEquals(
            $expected,
            $this->converter->convert(new Sequence($tokens))
        );
    }

    public function testConvertMissingId()
    {
        $this->setExpectedException(
            'Herrera\\Annotations\\Exception\\UnexpectedTokenException',
            'The annotation (beginning at 3) is missing its identifier.'
        );

        $this->converter->convert(
            new Tokens(
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'top'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_AT),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                )
            )
        );
    }

    protected function setUp()
    {
        $this->converter = new ToArray();
    }
}
