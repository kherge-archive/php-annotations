<?php

namespace Herrera\Annotations;

use Doctrine\Common\Annotations\DocLexer;
use Herrera\Annotations\Exception\ConvertException;

/**
 * Converts the tokens into another format.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class Convert
{
    /**
     * The token to character map.
     *
     * @var array
     */
    private static $map = array(
        DocLexer::T_AT => '@',
        DocLexer::T_CLOSE_CURLY_BRACES => '}',
        DocLexer::T_CLOSE_PARENTHESIS => ')',
        DocLexer::T_COLON => ':',
        DocLexer::T_COMMA => ',',
        DocLexer::T_EQUALS => '=',
        DocLexer::T_NAMESPACE_SEPARATOR => '\\',
        DocLexer::T_OPEN_CURLY_BRACES => '{',
        DocLexer::T_OPEN_PARENTHESIS => '(',
    );

    /**
     * Converts the tokens back into a string.
     *
     * @param array $tokens The tokens.
     *
     * @return string The string.
     *
     * @throws ConvertException If a token is invalid.
     */
    public static function toString(array $tokens)
    {
        $string = '';

        foreach ($tokens as $index => $token) {
            if (!isset($token[0])) {
                throw ConvertException::invalidToken($index);
            }

            switch ($token[0]) {
                case DocLexer::T_FALSE:
                case DocLexer::T_FLOAT:
                case DocLexer::T_IDENTIFIER:
                case DocLexer::T_INTEGER:
                case DocLexer::T_NULL:
                case DocLexer::T_TRUE:
                    $string .= $token[1];
                    break;
                case DocLexer::T_STRING:
                    $string .= "\"{$token[1]}\"";
                    break;
                default:
                    if (!isset(self::$map[$token[0]])) {
                        throw ConvertException::unrecognizedToken($token[0]);
                    }

                    $string .= self::$map[$token[0]];
            }
        }

        return $string;
    }
}
