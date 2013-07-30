<?php

namespace Herrera\Annotations\Tests;

use Herrera\Annotations\Convert;
use Herrera\Annotations\Tokenize;
use Herrera\PHPUnit\TestCase;

class ConvertTest extends TestCase
{
    /**
     * @var Tokenize
     */
    private $tokenize;

    public function testToString()
    {
        $original = <<<DOCBLOCK
/**
 * This is the original docblock.
 *
 * @My\Annotation(
 *     123,
 *     "string",
 *     1.23,
 *     AN_IDENTIFIER,
 *     {
 *         key="value",
 *         another: "thing"
 *     },
 *     true,
 *     null,
 *     false
 * )
 */
DOCBLOCK;

        $expected = <<<EXPECTED
@My\Annotation(123,"string",1.23,AN_IDENTIFIER,{key="value",another:"thing"},true,null,false)
EXPECTED;

        $this->assertEquals(
            $expected,
            Convert::toString($this->tokenize->parse($original))
        );
    }

    public function testToStringFormatted()
    {
        $original = <<<DOCBLOCK
/**
 * @ORM\JoinTable(
 *     name="myTableJoin",
 *     joinColumns={
 *         @ORM\JoinColumn(name="joinA",referencedColumnName="refA")
 *     },
 *     inverseJoinColumns={
 *         @ORM\JoinColumn(name="joinB",referencedColumnName="refB")
 *     }
 * )
 */
DOCBLOCK;

        $expected = <<<EXPECTED
@ORM\JoinTable(
    name="myTableJoin",
    joinColumns={
        @ORM\JoinColumn(
            name="joinA",
            referencedColumnName="refA"
        )
    },
    inverseJoinColumns={
        @ORM\JoinColumn(
            name="joinB",
            referencedColumnName="refB"
        )
    }
)
EXPECTED;

        $this->assertEquals(
            $expected,
            Convert::toString(
                $this->tokenize->parse($original),
                array(
                    'indent' => array(
                        'size' => 4
                    )
                )
            )
        );
    }

    public function testToStringInvalidToken()
    {
        $this->setExpectedException(
            'Herrera\\Annotations\\Exception\\ConvertException',
            'The token at index 0 is not valid.'
        );

        Convert::toString(array(array()));
    }

    public function testToStringUnrecognizedToken()
    {
        $this->setExpectedException(
            'Herrera\\Annotations\\Exception\\ConvertException',
            'The token "?" is not recognized.'
        );

        Convert::toString(array(array('?')));
    }

    protected function setUp()
    {
        $this->tokenize = new Tokenize();
    }
}
