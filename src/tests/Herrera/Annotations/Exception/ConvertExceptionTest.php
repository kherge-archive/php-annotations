<?php

namespace Herrera\Annotations\Tests\Exception;

use Herrera\Annotations\Exception\ConvertException;
use Herrera\PHPUnit\TestCase;

class ConvertExceptionTest extends TestCase
{
    public function testInvalidToken()
    {
        $exception = ConvertException::invalidToken(123);

        $this->assertEquals(
            'The token at index 123 is not valid.',
            $exception->getMessage()
        );
    }

    public function testUnrecognizedToken()
    {
        $exception = ConvertException::unrecognizedToken('x');

        $this->assertEquals(
            'The token "x" is not recognized.',
            $exception->getMessage()
        );
    }
}
