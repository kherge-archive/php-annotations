<?php

namespace Herrera\Annotations\Convert;

use Doctrine\Common\Annotations\DocLexer;
use Herrera\Annotations\Exception\ConvertException;

/**
 * Converts a list of tokens into their string representation.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class TokensToString extends AbstractConverter
{
    /**
     * The line break character(s).
     *
     * @var string
     */
    private $break = "\n";

    /**
     * The indentation character.
     *
     * @var string
     */
    private $char = ' ';

    /**
     * The current indentation level.
     *
     * @var integer
     */
    private $level;

    /**
     * The flag used to add a space after a colon (assignment).
     *
     * @var boolean
     */
    private $space = false;

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
     * The indentation size.
     *
     * @var integer
     */
    private $size = 0;

    /**
     * Sets the line break character(s) used for indentation.
     *
     * @param string $break The character(s).
     *
     * @return TokensToString The converter.
     */
    public function setBreakChar($break)
    {
        $this->break = $break;

        return $this;
    }

    /**
     * Sets the repeated indentation character.
     *
     * @param string $char The character.
     *
     * @return TokensToString The converter.
     */
    public function setIndentChar($char)
    {
        $this->char = $char;

        return $this;
    }

    /**
     * Sets the size of the indentation.
     *
     * @param integer $size The size.
     *
     * @return TokensToString The converter.
     */
    public function setIndentSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Sets the flag that determines if a space is added after a colon.
     *
     * @param boolean $space Add the space?
     *
     * @return TokensToString The converter.
     */
    public function useColonSpace($space)
    {
        $this->space = $space;

        return $this;
    }

    /**
     * Processes the current token.
     */
    protected function handle()
    {
        $token = $this->token($this->offset);

        // increase indent level if opening a values list
        if ((DocLexer::T_OPEN_CURLY_BRACES === $token[0])
            || (DocLexer::T_OPEN_PARENTHESIS === $token[0])) {
            $this->level++;

            // decrease indent level if closing values list
        } elseif ((DocLexer::T_CLOSE_CURLY_BRACES === $token[0])
            || (DocLexer::T_CLOSE_PARENTHESIS === $token[0])) {
            $past = $this->token($this->offset - 1);

            // indent if level > 0
            if ((0 < $this->level--)
                && (DocLexer::T_OPEN_CURLY_BRACES !== $past[0])
                && (DocLexer::T_OPEN_PARENTHESIS !== $past[0])) {
                $this->indent($this->result);
            }

            // indent of a comma preceded this token
        } elseif ((null !== ($past = $this->token($this->offset - 1)))
            && (DocLexer::T_COMMA === $past[0])) {
            $this->indent($this->result);
        }

        switch ($token[0]) {
            // simple append
            case DocLexer::T_FALSE:
            case DocLexer::T_FLOAT:
            case DocLexer::T_IDENTIFIER:
            case DocLexer::T_INTEGER:
            case DocLexer::T_NULL:
            case DocLexer::T_TRUE:
                $this->result .= $token[1];
                break;

            // quote the string
            case DocLexer::T_STRING:
                $this->result .= "\"{$token[1]}\"";
                break;

            // separate root level annotations
            /** @noinspection PhpMissingBreakStatementInspection */
            case DocLexer::T_AT:
                if ($this->result && (0 === $this->level)) {
                    $this->result .= $this->break;
                }

                // no break

            // map characters using a table
            default:
                if (!isset(self::$map[$token[0]])) {
                    throw ConvertException::unrecognizedToken($token[0]);
                }

                $this->result .= self::$map[$token[0]];

                $open = $this->token($this->offset - 1);
                $open = ((DocLexer::T_OPEN_CURLY_BRACES === $open[0])
                            || (DocLexer::T_OPEN_PARENTHESIS === $open[0]));

                $close = $this->token($this->offset + 1);
                $close = ((DocLexer::T_CLOSE_CURLY_BRACES === $close[0])
                            || (DocLexer::T_CLOSE_PARENTHESIS === $close[0]));

                // add the colon space, if required
                if ($this->space && (DocLexer::T_COLON === $token[0])) {
                    $this->result .= ' ';

                    // indent after starting a values list
                } elseif ((DocLexer::T_OPEN_PARENTHESIS === $token[0])
                    || (DocLexer::T_OPEN_CURLY_BRACES === $token[0])) {
                    $next = $this->token($this->offset + 1);

                    if ((DocLexer::T_CLOSE_CURLY_BRACES !== $next[0])
                        && (DocLexer::T_CLOSE_PARENTHESIS !== $next[0])) {
                        $this->indent($this->result);
                    }
                }
        }
    }

    /**
     * Resets the converter's state.
     *
     * @param array $tokens The new tokens.
     */
    protected function reset(array $tokens)
    {
        $this->level = 0;
        $this->offset = 0;
        $this->result = '';
        $this->tokens = array_values($tokens);
    }

    /**
     * Adds indentation to the string.
     *
     * @param string &$string The string.
     */
    private function indent(&$string)
    {
        if ($this->size) {
            $string .= $this->break;
            $string .= str_repeat($this->char, $this->size * $this->level);
        }
    }
}
