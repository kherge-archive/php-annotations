<?php

namespace Herrera\Annotations\Convert;

use Doctrine\Common\Annotations\DocLexer;
use Herrera\Annotations\Exception\ConvertException;

/**
 * Provides common functionality for converts that loop through tokens.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
abstract class AbstractConverter implements ConvertInterface
{
    /**
     * The current offset.
     *
     * @var integer
     */
    protected $offset;

    /**
     * The converted result.
     *
     * @var mixed
     */
    protected $result;

    /**
     * The list of tokens being processed.
     *
     * @var array
     */
    protected $tokens;

    /**
     * The list of tokens that require a value.
     *
     * @var array
     */
    protected static $valued = array(
        DocLexer::T_FLOAT => true,
        DocLexer::T_IDENTIFIER => true,
        DocLexer::T_INTEGER => true,
        DocLexer::T_STRING => true,
    );

    /**
     * {@inheritDoc}
     */
    public function convert(array $tokens)
    {
        $tokens = array_values($tokens);
        $count = count($tokens);

        $this->reset($tokens);

        for ($this->offset = 0;
            $this->offset < $count;
            $this->offset++) {
            $this->handle();
        }

        return $this->result;
    }

    /**
     * Processes the token at the current offset.
     */
    abstract protected function handle();

    /**
     * Resets the state of the converter.
     */
    abstract protected function reset(array $tokens);

    /**
     * Validates the token at the offset and returns it.
     *
     * @param integer $offset The offset.
     *
     * @return array The validated token.
     *
     * @throws ConvertException If the token is not valid.
     */
    protected function token($offset)
    {
        if (isset($this->tokens[$offset])) {
            $token = $this->tokens[$offset];

            if (!isset($token[0])) {
                throw ConvertException::invalidToken($offset);
            }

            if (isset(self::$valued[$token[0]])
                && self::$valued[$token[0]]
                && !isset($token[1])) {
                throw ConvertException::invalidToken($offset);
            }

            return $token;
        }

        // @codeCoverageIgnoreStart
        return null;
        // @codeCoverageIgnoreEnd
    }
}
