<?php

namespace Herrera\Annotations\Tests;

use Doctrine\Common\Annotations\DocLexer;
use Herrera\Annotations\Sequence;
use Herrera\Annotations\Tokens;
use Herrera\PHPUnit\TestCase;

class SequenceTest extends TestCase
{
    public function getInvalidToken()
    {
        return array(
            array(
                0,
                array(
                    array(DocLexer::T_IDENTIFIER, 'test')
                )
            )
        );
    }

    public function getNotUsedToken()
    {
        return array(
            array(
                0,
                array(
                    array(DocLexer::T_NAMESPACE_SEPARATOR)
                )
            ),
            array(
                0,
                array(
                    array(DocLexer::T_NONE)
                )
            )
        );
    }

    public function getValidToken()
    {
        return array(
            array(
                0,
                array(
                    array(DocLexer::T_AT)
                )
            )
        );
    }

    public function testConstruct()
    {
        $original = array(
            array(DocLexer::T_AT),
            array(DocLexer::T_IDENTIFIER, 'test')
        );

        $tokens = new Tokens($original);
        $sequence = new Sequence($tokens);

        $this->assertEquals($original, $sequence->getArray());
    }

    public function testCurrent()
    {
        $tokens = array(
            array(101),
            array(100,'Namespaced\\Annotation'),
            array(109),
            array(2,'123'),
            array(104),
            array(3,'string'),
            array(104),
            array(4,'1.23'),
            array(104),
            array(100,'AN_IDENTIFIER'),
            array(104),
            array(101),
            array(100,'Nested\\Annotation'),
            array(109),
            array(103),
            array(104),
            array(108),
            array(100,'named'),
            array(112),
            array(3,'value'),
            array(104),
            array(102),
            array(104),
            array(100,'another'),
            array(105),
            array(3,'value'),
            array(104),
            array(110,'true'),
            array(104),
            array(106,'false'),
            array(104),
            array(111,'null'),
            array(103)
        );

        $sequence = new Sequence($tokens);

        foreach ($tokens as $token) {
            $this->assertEquals($token, $sequence->current());
            $sequence->next();
        }
    }

    /**
     * @expectedException \Herrera\Annotations\Exception\InvalidArgumentException
     * @expectedExceptionMessage The $tokens argument must be an array or instance of Tokens.
     */
    public function testConstructInvalid()
    {
        new Sequence(123);
    }

    /**
     * @dataProvider getInvalidToken
     */
    public function testCurrentInvalid($offset, $tokens)
    {
        $sequence = new Sequence($tokens);

        if (0 < $offset) {
            for ($i = 0; $i <= $offset; $i++) {
                $sequence->next();
            }
        }

        $this->setExpectedException(
            'Herrera\\Annotations\\Exception\\UnexpectedTokenException',
            "Token #$offset ({$tokens[$offset][0]}) is not expected here."
        );

        $sequence->current();
    }

    /**
     * @dataProvider getNotUsedToken
     */
    public function testCurrentNotUsed($offset, $tokens)
    {
        $sequence = new Sequence($tokens);

        if (0 < $offset) {
            for ($i = 0; $i <= $offset; $i++) {
                $sequence->next();
            }
        }

        $this->setExpectedException(
            'Herrera\\Annotations\\Exception\\UnexpectedTokenException',
            "Token #$offset ({$tokens[$offset][0]}) is not used by this library."
        );

        $sequence->current();
    }

    /**
     * @dataProvider getValidToken
     */
    public function testCurrentValid($offset, $tokens)
    {
        $sequence = new Sequence($tokens);

        if (0 < $offset) {
            for ($i = 0; $i <= $offset; $i++) {
                $sequence->next();
            }
        }

        $this->assertEquals($tokens[$offset], $sequence->current());
    }
}
