<?php

namespace Herrera\Annotations\Tests\Convert;

use Doctrine\Common\Annotations\DocLexer;
use Herrera\Annotations\Convert\ToString;
use Herrera\Annotations\Tokens;
use Herrera\Annotations\Sequence;
use Herrera\PHPUnit\TestCase;

class ToStringTest extends TestCase
{
    /**
     * @var ToString
     */
    protected $converter;

    public function getToken()
    {
        return array(

            array(
                array(),
                ''
            ),

            array(
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'simple'),
                ),
                '@simple'
            ),

            array(
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'simple'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                '@simple()'
            ),

            array(
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'a'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_STRING, 'value'),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                '@a("value")'
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
                '@an(assigned="value")'
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
                '@an(assigned="value",and="another")'
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
                    array(DocLexer::T_NULL, 'null'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_STRING, 'string'),
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_TRUE, 'true'),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                '@types(FALSE,1.23,123,null,"string",true)'
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
                '@array({})'
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
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_CLOSE_CURLY_BRACES),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                '@array({a=FALSE,b:1.23,c=123,123:NULL,e="string","f":TRUE,})'
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
                    array(DocLexer::T_IDENTIFIER, 'f'),
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
                    array(DocLexer::T_IDENTIFIER, 'f'),
                    array(DocLexer::T_EQUALS),
                    array(DocLexer::T_TRUE, 'TRUE'),
                    array(DocLexer::T_CLOSE_CURLY_BRACES),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                '@array({a=FALSE,b=1.23,c=123,d=NULL,e="string",f=TRUE},{a=FALSE,b=1.23,c=123,d=NULL,e="string",f=TRUE})'
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
                '@a(@sub)'
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
                '@My\\Annotation(name="this is the name",an=@Assigned\\Annotation,array={123,"sub test",also:"assigned",@One\\More\\Annotation("!")})'
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

    public function testConvertFormatted()
    {
        $this->setPropertyValue($this->converter, 'size', 4);
        $this->setPropertyValue($this->converter, 'space', true);

        $tokens = new Tokens(
            array(
                array(DocLexer::T_AT),
                array(DocLexer::T_IDENTIFIER, 'Empty'),
                array(DocLexer::T_OPEN_PARENTHESIS),
                array(DocLexer::T_CLOSE_PARENTHESIS),
                array(DocLexer::T_AT),
                array(DocLexer::T_IDENTIFIER, 'MadeUp'),
                array(DocLexer::T_OPEN_PARENTHESIS),
                array(DocLexer::T_OPEN_CURLY_BRACES),
                array(DocLexer::T_INTEGER, '123'),
                array(DocLexer::T_COLON),
                array(DocLexer::T_STRING, 'test'),
                array(DocLexer::T_CLOSE_CURLY_BRACES),
                array(DocLexer::T_CLOSE_PARENTHESIS),
                array(DocLexer::T_AT),
                array(DocLexer::T_IDENTIFIER, 'ORM\\JoinTable'),
                array(DocLexer::T_OPEN_PARENTHESIS),
                array(DocLexer::T_IDENTIFIER, 'name'),
                array(DocLexer::T_EQUALS),
                array(DocLexer::T_STRING, 'myTableJoin'),
                array(DocLexer::T_COMMA),
                array(DocLexer::T_IDENTIFIER, 'joinColumns'),
                array(DocLexer::T_EQUALS),
                array(DocLexer::T_OPEN_CURLY_BRACES),
                array(DocLexer::T_AT),
                array(DocLexer::T_IDENTIFIER, 'ORM\\JoinColumn'),
                array(DocLexer::T_OPEN_PARENTHESIS),
                array(DocLexer::T_IDENTIFIER, 'name'),
                array(DocLexer::T_EQUALS),
                array(DocLexer::T_STRING, 'joinA'),
                array(DocLexer::T_COMMA),
                array(DocLexer::T_IDENTIFIER, 'referencedColumnName'),
                array(DocLexer::T_EQUALS),
                array(DocLexer::T_STRING, 'refA'),
                array(DocLexer::T_CLOSE_PARENTHESIS),
                array(DocLexer::T_CLOSE_CURLY_BRACES),
                array(DocLexer::T_COMMA),
                array(DocLexer::T_IDENTIFIER, 'inverseJoinColumns'),
                array(DocLexer::T_EQUALS),
                array(DocLexer::T_OPEN_CURLY_BRACES),
                array(DocLexer::T_AT),
                array(DocLexer::T_IDENTIFIER, 'ORM\\JoinColumn'),
                array(DocLexer::T_OPEN_PARENTHESIS),
                array(DocLexer::T_IDENTIFIER, 'name'),
                array(DocLexer::T_EQUALS),
                array(DocLexer::T_STRING, 'joinB'),
                array(DocLexer::T_COMMA),
                array(DocLexer::T_IDENTIFIER, 'referencedColumnName'),
                array(DocLexer::T_EQUALS),
                array(DocLexer::T_STRING, 'refB'),
                array(DocLexer::T_CLOSE_PARENTHESIS),
                array(DocLexer::T_CLOSE_CURLY_BRACES),
                array(DocLexer::T_CLOSE_PARENTHESIS),
            )
        );

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

        $this->assertEquals($expected, $this->converter->convert($tokens));
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
        $this->converter = new ToString();
    }
}
