<?php

namespace Herrera\Annotations\Tests;

use Doctrine\Common\Annotations\DocLexer;
use Herrera\Annotations\Sequence;
use Herrera\Annotations\Test\TestTokens;
use Herrera\Annotations\Tokens;

class SequenceTest extends TestTokens
{
    public function getTokens()
    {
        $tokens = parent::getTokens();

        foreach ($tokens as $i => $list) {
            $tokens[$i] = array($list);
        }

        return $tokens;
    }

    public function testConstruct()
    {
        $tokens = array(
            array(123)
        );

        $sequence = new Sequence($tokens);

        $this->assertEquals(
            $tokens,
            $this->getPropertyValue($sequence, 'tokens')
        );
    }

    public function testConstructWithTokens()
    {
        $tokens = array(
            array(123)
        );

        $sequence = new Sequence(
            new Tokens($tokens)
        );

        $this->assertEquals(
            $tokens,
            $this->getPropertyValue($sequence, 'tokens')
        );
    }

    public function testConstructInvalid()
    {
        $this->setExpectedException(
            'Herrera\\Annotations\\Exception\\InvalidArgumentException',
            'The $tokens argument must be an array or instance of Tokens.'
        );

        new Sequence(123);
    }

    /**
     * @dataProvider getTokens
     */
    public function testCurrent($tokens)
    {
        $sequence = new Sequence($tokens);

        foreach ($tokens as $list) {
            $this->assertEquals($list, $sequence->current());

            $sequence->next();
        }
    }
}
