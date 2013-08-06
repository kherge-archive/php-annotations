<?php

namespace Herrera\Annotations\Tests\Convert;

use Doctrine\Common\Annotations\DocLexer;
use Herrera\Annotations\Convert\ToXml;
use Herrera\Annotations\Tokens;
use Herrera\Annotations\Sequence;
use Herrera\PHPUnit\TestCase;

class ToXmlTest extends TestCase
{
    /**
     * @var ToXml
     */
    protected $converter;

    public function getToken()
    {
        return array(

            array(
                array(),
                <<<XML
<?xml version="1.0"?>
<annotations/>

XML
            ),

            array(
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'simple'),
                ),
                <<<XML
<?xml version="1.0"?>
<annotations>
  <annotation name="simple"/>
</annotations>

XML
            ),

            array(
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'simple'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                <<<XML
<?xml version="1.0"?>
<annotations>
  <annotation name="simple"/>
</annotations>

XML
            ),

            array(
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'simple1'),
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'simple2'),
                ),
                <<<XML
<?xml version="1.0"?>
<annotations>
  <annotation name="simple1"/>
  <annotation name="simple2"/>
</annotations>

XML
            ),

            array(
                array(
                    array(DocLexer::T_AT),
                    array(DocLexer::T_IDENTIFIER, 'a'),
                    array(DocLexer::T_OPEN_PARENTHESIS),
                    array(DocLexer::T_STRING, 'value'),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                <<<XML
<?xml version="1.0"?>
<annotations>
  <annotation name="a">
    <value type="string">value</value>
  </annotation>
</annotations>

XML
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
                <<<XML
<?xml version="1.0"?>
<annotations>
  <annotation name="an">
    <value key="assigned" type="string">value</value>
  </annotation>
</annotations>

XML
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
                <<<XML
<?xml version="1.0"?>
<annotations>
  <annotation name="an">
    <value key="assigned" type="string">value</value>
    <value key="and" type="string">another</value>
  </annotation>
</annotations>

XML
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
                <<<XML
<?xml version="1.0"?>
<annotations>
  <annotation name="types">
    <value type="boolean">0</value>
    <value type="float">1.23</value>
    <value type="integer">123</value>
    <value type="null"/>
    <value type="string">string</value>
    <value type="boolean">1</value>
  </annotation>
</annotations>

XML
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
                <<<XML
<?xml version="1.0"?>
<annotations>
  <annotation name="array">
    <values/>
  </annotation>
</annotations>

XML
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
                <<<XML
<?xml version="1.0"?>
<annotations>
  <annotation name="array">
    <values>
      <value key="a" type="boolean">0</value>
      <value key="b" type="float">1.23</value>
      <value key="c" type="integer">123</value>
      <value key="123" type="null"/>
      <value key="e" type="string">string</value>
      <value key="f" type="boolean">1</value>
    </values>
  </annotation>
</annotations>

XML
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
                <<<XML
<?xml version="1.0"?>
<annotations>
  <annotation name="array">
    <values>
      <value key="a" type="boolean">0</value>
      <value key="b" type="float">1.23</value>
      <value key="c" type="integer">123</value>
      <value key="d" type="null"/>
      <value key="e" type="string">string</value>
      <value key="f" type="boolean">1</value>
    </values>
    <values>
      <value key="a" type="boolean">0</value>
      <value key="b" type="float">1.23</value>
      <value key="c" type="integer">123</value>
      <value key="d" type="null"/>
      <value key="e" type="string">string</value>
      <value key="f" type="boolean">1</value>
    </values>
  </annotation>
</annotations>

XML
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
                <<<XML
<?xml version="1.0"?>
<annotations>
  <annotation name="a">
    <annotation name="sub"/>
  </annotation>
</annotations>

XML
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
                    array(DocLexer::T_COMMA),
                    array(DocLexer::T_IDENTIFIER, 'SOME_CONSTANT'),
                    array(DocLexer::T_CLOSE_PARENTHESIS),
                ),
                <<<XML
<?xml version="1.0"?>
<annotations>
  <annotation name="My\\Annotation">
    <value key="name" type="string">this is the name</value>
    <annotation key="an" name="Assigned\\Annotation"/>
    <values key="array">
      <value type="integer">123</value>
      <value type="string">sub test</value>
      <value key="also" type="string">assigned</value>
      <annotation name="One\\More\\Annotation">
        <value type="string">!</value>
      </annotation>
    </values>
    <value type="constant">SOME_CONSTANT</value>
  </annotation>
</annotations>

XML
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
            $this->converter->convert(new Sequence($tokens))->saveXML()
        );
    }

    protected function setUp()
    {
        $this->converter = new ToXml();
    }
}
