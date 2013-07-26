<?php

namespace Herrera\Annotations\Exception;

use Doctrine\Common\Annotations\DocLexer;

/**
 * This exception is thrown when a syntax violation is encountered.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class SyntaxException extends Exception
{
    /**
     * Creates a new exception.
     *
     * @param string   $expected The expected token.
     * @param array    $token    The actual token.
     * @param DocLexer $lexer    The lexer.
     *
     * @return SyntaxException The new exception.
     */
    public static function create(
        $expected,
        array $token = null,
        DocLexer $lexer = null
    ) {
        if ((null === $token) && $lexer) {
            $token = $lexer->lookahead;
        }

        $message = "Expected $expected, received ";

        if ($token) {
            $message .= "'{$token['value']}' at position {$token['position']}.";
        } else {
            $message .= 'end of string.';
        }

        return new self($message);
    }
}
