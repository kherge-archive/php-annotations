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
     * Formatting options:
     *
     * ```
     * array(
     *     'indent' => array(   // indentation options
     *         'break' => "\n", // the line break character to use
     *         'char' => ' ',   // the indentation character to repeat
     *         'size' => 0,     // the number of times to repeat "char"
     *     )
     * )
     * ```
     *
     * @param array $tokens The tokens.
     * @param array $format The formatting options.
     *
     * @return string The string.
     *
     * @throws ConvertException If a token is invalid.
     */
    public static function toString(array $tokens, array $format = array())
    {
        $format = array_replace_recursive(
            array(
                'indent' => array(
                    'break' => "\n",
                    'char' => ' ',
                    'size' => 0,
                ),
            ),
            $format
        );

        $level = 0;
        $indent = function () use ($format, &$level) {
            $string = '';

            if ($format['indent']['size']) {
                $string .= $format['indent']['break'];
                $string .= str_repeat(
                    $format['indent']['char'],
                    $format['indent']['size'] * $level
                );
            }

            return $string;
        };

        $string = '';

        foreach ($tokens as $index => $token) {
            if (!isset($token[0])) {
                throw ConvertException::invalidToken($index);
            }

            if ((DocLexer::T_OPEN_CURLY_BRACES === $token[0])
                || (DocLexer::T_OPEN_PARENTHESIS === $token[0])) {
                $level++;
            } elseif ((DocLexer::T_CLOSE_CURLY_BRACES === $token[0])
                || (DocLexer::T_CLOSE_PARENTHESIS === $token[0])) {
                if ($level--) {
                    $string .= $indent();
                }
            } elseif (isset($tokens[$index - 1])
                && (DocLexer::T_COMMA === $tokens[$index - 1][0])) {
                $string .= $indent();
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

                    if ((DocLexer::T_OPEN_PARENTHESIS === $token[0])
                        || (DocLexer::T_OPEN_CURLY_BRACES === $token[0])) {
                        $string .= $indent();
                    }
            }
        }

        return $string;
    }
}
