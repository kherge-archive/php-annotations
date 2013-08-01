<?php

namespace Herrera\Annotations\Tests;

use Doctrine\Common\Annotations\DocLexer;
use Herrera\Annotations\Tokens;
use Herrera\PHPUnit\TestCase;

class TokensTest extends TestCase
{
    /**
     * @var Tokens
     */
    private $tokens;

    public function getTokenAndValue()
    {
        return array(
            array(array(DocLexer::T_FALSE, 'false'), false),
            array(array(DocLexer::T_FLOAT, '1.23'), 1.23),
            array(array(DocLexer::T_INTEGER, '123'), 123),
            array(array(DocLexer::T_NULL, 'null'), null),
            array(array(DocLexer::T_STRING, 'test'), 'test'),
            array(array(DocLexer::T_TRUE, 'TRUE'), true),
        );
    }

    public function getTokenWithValue()
    {
        return array(
            array(DocLexer::T_FALSE),
            array(DocLexer::T_FLOAT),
            array(DocLexer::T_INTEGER),
            array(DocLexer::T_NULL),
            array(DocLexer::T_STRING),
            array(DocLexer::T_TRUE),
        );
    }

    public function testConstruct()
    {
        $tokens = array(
            1 => array(DocLexer::T_AT),
            4 => array(DocLexer::T_IDENTIFIER, 'test')
        );

        $expected = array(
            0 => array(DocLexer::T_AT),
            1 => array(DocLexer::T_IDENTIFIER, 'test')
        );

        $this->assertEquals(
            $expected,
            $this->getPropertyValue(
                new Tokens($tokens),
                'tokens'
            )
        );
    }

    public function testCount()
    {
        $this->assertEquals(2, count($this->tokens));
    }

    public function testCurrent()
    {
        $this->assertEquals(
            array(DocLexer::T_AT),
            $this->tokens->current()
        );
    }

    public function testGetArray()
    {
        $this->assertEquals(
            array(
                array(DocLexer::T_AT),
                array(DocLexer::T_IDENTIFIER, 'test')
            ),
            $this->tokens->getArray()
        );
    }

    public function testGetToken()
    {
        $this->assertEquals(
            array(DocLexer::T_AT),
            $this->tokens->getToken(0)
        );
    }

    public function testGetTokenMissingId()
    {
        $tokens = new Tokens(array(array()));

        $this->setExpectedException(
            'Herrera\\Annotations\\Exception\\InvalidTokenException',
            'Token #0 is missing its token identifier.'
        );

        $tokens->getToken(0);
    }

    public function testGetTokenInvalid()
    {
        $tokens = new Tokens(array(array('test')));

        $this->setExpectedException(
            'Herrera\\Annotations\\Exception\\InvalidTokenException',
            'Token #0 does not have a valid token identifier.'
        );

        $tokens->getToken(0);
    }

    /**
     * @dataProvider getTokenWithValue
     */
    public function testGetTokenMissingValue($token)
    {
        $tokens = new Tokens(array(array($token)));

        $this->setExpectedException(
            'Herrera\\Annotations\\Exception\\InvalidTokenException',
            "Token #0 ($token) is missing its value."
        );

        $tokens->current(0);
    }

    /**
     * @dataProvider getTokenAndValue
     */
    public function testGetValue($token, $expected)
    {
        $tokens = new Tokens(array($token));

        $this->assertSame($expected, $tokens->getValue(0));
    }

    public function testGetValueNotExpected()
    {
        $tokens = new Tokens(
            array(
                array(DocLexer::T_AT)
            )
        );

        $this->setExpectedException(
            'Herrera\\Annotations\\Exception\\LogicException',
            'Token #0 (101) is not expected to have a value.'
        );

        $tokens->getValue(0);
    }

    public function testKey()
    {
        $this->assertEquals(0, $this->tokens->key());
    }

    /**
     * @depends testKey
     */
    public function testNext()
    {
        $this->tokens->next();

        $this->assertEquals(1, $this->tokens->key());
    }

    public function testOffsetExists()
    {
        $this->assertTrue($this->tokens->offsetExists(1));
        $this->assertFalse($this->tokens->offsetExists(2));
    }

    public function testOffsetGet()
    {
        $this->assertEquals(
            array(DocLexer::T_IDENTIFIER, 'test'),
            $this->tokens->offsetGet(1)
        );
    }

    /**
     * @expectedException \Herrera\Annotations\Exception\OutOfRangeException
     * @expectedExceptionMessage  No value is set at offset 2.
     */
    public function testOffsetGetNotSet()
    {
        $this->tokens->offsetGet(2);
    }

    /**
     * @expectedException \Herrera\Annotations\Exception\LogicException
     * @expectedExceptionMessage New values cannot be added to the list of tokens.
     */
    public function testOffsetSet()
    {
        $this->tokens->offsetSet(1, 123);
    }

    /**
     * @expectedException \Herrera\Annotations\Exception\LogicException
     * @expectedExceptionMessage Existing tokens cannot be removed from the list of tokens.
     */
    public function testOffsetUnset()
    {
        $this->tokens->offsetUnset(1);
    }

    /**
     * @depends testKey
     * @depends testNext
     */
    public function testRewind()
    {
        $this->tokens->next();
        $this->tokens->rewind();

        $this->assertEquals(0, $this->tokens->key());
    }

    /**
     * @depends testNext
     */
    public function testValid()
    {
        $this->tokens->next();

        $this->assertTrue($this->tokens->valid());

        $this->tokens->next();

        $this->assertFalse($this->tokens->valid());
    }

    protected function setUp()
    {
        $this->tokens = new Tokens(
            array(
                array(DocLexer::T_AT),
                array(DocLexer::T_IDENTIFIER, 'test')
            )
        );
    }
}
