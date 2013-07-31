<?php

namespace Herrera\Annotations\Tests\Convert;

use Doctrine\Common\Annotations\DocLexer;
use Herrera\Annotations\Convert\TokensToArray;
use Herrera\Annotations\Tokenizer;
use Herrera\PHPUnit\TestCase;

class TokensToArrayTest extends TestCase
{
    /**
     * @var TokensToArray
     */
    private $converter;

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
                array(
                )
            ),

            array(
                <<<DOCBLOCK
/**
 * @simple
 */
DOCBLOCK
                ,
                array(
                    (object) array(
                        'name' => 'simple',
                        'values' => array(),
                    )
                )
            ),

            array(
                <<<DOCBLOCK
/**
 * @simple()
 */
DOCBLOCK
                ,
                array(
                    (object) array(
                        'name' => 'simple',
                        'values' => array(),
                    )
                )
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
                    (object) array(
                        'name' => 'but\\not\\this\\one',
                        'values' => array(),
                    )
                ),
                array('ignored')
            ),

            array(
                <<<DOCBLOCK
/**
 * @a("value")
 */
DOCBLOCK
                ,
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
                <<<DOCBLOCK
/**
 * @an(assigned="value")
 */
DOCBLOCK
                ,
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
                <<<DOCBLOCK
/**
 * @an(assigned="value",and="another")
 */
DOCBLOCK
                ,
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
                <<<DOCBLOCK
/**
 * @types(false, 1.23, 123, null, "string", true)
 */
DOCBLOCK
                ,
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
                <<<DOCBLOCK
/**
 * @array({})
 */
DOCBLOCK
                ,
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
                <<<DOCBLOCK
/**
 * @a(@sub)
 */
DOCBLOCK
                ,
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
                <<<DOCBLOCK
/**
 * @My\Annotation(
 *     name="this is the name",
 *     an=@Assigned\Annotation,
 *     array={
 *         123,
 *         "sub test",
 *         also: "assigned",
 *         @One\More\Annotation("!")
 *     }
 * )
 */
DOCBLOCK
                ,
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
     * @dataProvider getDocblocks
     */
    public function testConvert($docblock, $expected, $ignore = array())
    {
        $this->tokenizer->ignore($ignore);

        $this->assertEquals(
            $expected,
            $this->converter->convert(
                $this->tokenizer->parse($docblock)
            )
        );
    }

    public function testConvertInvalidToken()
    {
        $this->setExpectedException(
            'Herrera\\Annotations\\Exception\\ConvertException',
            'The token at index 0 is not valid.'
        );

        $this->converter->convert(array(array()));
    }

    public function testConvertInvalidTokenMissingValue()
    {
        $this->setExpectedException(
            'Herrera\\Annotations\\Exception\\ConvertException',
            'The token at index 1 is not valid.'
        );

        $this->converter->convert(
            array(
                array(DocLexer::T_AT),
                array(DocLexer::T_IDENTIFIER),
            )
        );
    }

    public function testConvertMissingIdentifier()
    {
        $this->setExpectedException(
            'Herrera\\Annotations\\Exception\\ConvertException',
            'The annotation (beginning at 0) is missing its identifier.'
        );

        $this->converter->convert(
            array(
                array(DocLexer::T_AT),
                array(DocLexer::T_OPEN_PARENTHESIS),
            )
        );
    }

    protected function setUp()
    {
        $this->converter = new TokensToArray();
        $this->tokenizer = new Tokenizer();
    }
}
