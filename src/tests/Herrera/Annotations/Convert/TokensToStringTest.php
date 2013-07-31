<?php

namespace Herrera\Annotations\Tests\Convert;

use Herrera\Annotations\Convert\TokensToString;
use Herrera\Annotations\Tokenizer;
use Herrera\PHPUnit\TestCase;

class TokensToStringTest extends TestCase
{
    /**
     * @var TokensToString
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
                ''
            ),

            array(
                <<<DOCBLOCK
/**
 * @simple
 */
DOCBLOCK
                ,
                '@simple'
            ),

            array(
                <<<DOCBLOCK
/**
 * @simple()
 */
DOCBLOCK
                ,
                '@simple()'
            ),

            array(
                <<<DOCBLOCK
/**
 * @a("value")
 */
DOCBLOCK
                ,
                '@a("value")'
            ),

            array(
                <<<DOCBLOCK
/**
 * @an(assigned="value")
 */
DOCBLOCK
                ,
                '@an(assigned="value")'
            ),

            array(
                <<<DOCBLOCK
/**
 * @an(assigned="value",and="another")
 */
DOCBLOCK
                ,
                '@an(assigned="value",and="another")'
            ),

            array(
                <<<DOCBLOCK
/**
 * @types(FALSE, 1.23, 123, null, "string", true)
 */
DOCBLOCK
                ,
                '@types(FALSE,1.23,123,null,"string",true)'
            ),

            array(
                <<<DOCBLOCK
/**
 * @array({})
 */
DOCBLOCK
                ,
                '@array({})'
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
                '@array({a=false,b:1.23,c=123,123:null,e="string","f":true,})'
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
                '@array({a=false,b=1.23,c=123,d=null,e="string",f=true},{a=false,b=1.23,c=123,d=null,e="string",f=true})'
            ),

            array(
                <<<DOCBLOCK
/**
 * @a(@sub)
 */
DOCBLOCK
                ,
                '@a(@sub)'
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
                '@My\\Annotation(name="this is the name",an=@Assigned\\Annotation,array={123,"sub test",also:"assigned",@One\\More\\Annotation("!")})'
            ),

        );
    }

    /**
     * @dataProvider getDocblocks
     */
    public function testConvert($docblock, $expected)
    {
        $this->assertEquals(
            $expected,
            $this->converter->convert(
                $this->tokenizer->parse($docblock)
            )
        );
    }

    public function testConvertWithFormatting()
    {
        $docblock = <<<DOCBLOCK
/**
 * @Empty()
 * @MadeUp({123: "test"})
 * @ORM\JoinTable(name="myTableJoin",joinColumns={@ORM\JoinColumn(name="joinA",referencedColumnName="refA")},inverseJoinColumns={@ORM\JoinColumn(name="joinB",referencedColumnName="refB")})
 */
DOCBLOCK;

        $expected = <<<EXPECTED
@Empty()
@MadeUp(
    {
        123: "test"
    }
)
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

        $this->converter
             ->setIndentSize(4)
             ->useColonSpace(true);

        $this->assertEquals(
            $expected,
            $this->converter->convert(
                $this->tokenizer->parse($docblock)
            )
        );
    }

    public function testConvertInvalidMap()
    {
        $this->setExpectedException(
            'Herrera\\Annotations\\Exception\\ConvertException',
            'The token "999" is not recognized.'
        );

        $this->converter->convert(
            array(
                array(999)
            )
        );
    }

    public function testSetBreak()
    {
        $this->assertSame(
            $this->converter,
            $this->converter->setBreakChar("\r")
        );

        $this->assertEquals(
            "\r",
            $this->getPropertyValue($this->converter, 'break')
        );
    }

    public function testSetIndentChar()
    {
        $this->assertSame(
            $this->converter,
            $this->converter->setIndentChar("\t")
        );

        $this->assertEquals(
            "\t",
            $this->getPropertyValue($this->converter, 'char')
        );
    }

    public function testSetIndentSize()
    {
        $this->assertSame(
            $this->converter,
            $this->converter->setIndentSize(100)
        );

        $this->assertSame(
            100,
            $this->getPropertyValue($this->converter, 'size')
        );
    }

    public function testUseColonSpace()
    {
        $this->assertSame(
            $this->converter,
            $this->converter->useColonSpace(true)
        );

        $this->assertTrue($this->getPropertyValue($this->converter, 'space'));
    }

    protected function setUp()
    {
        $this->converter = new TokensToString();
        $this->tokenizer = new Tokenizer();
    }
}
