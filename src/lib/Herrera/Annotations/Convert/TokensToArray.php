<?php

namespace Herrera\Annotations\Convert;

use Doctrine\Common\Annotations\DocLexer;
use Herrera\Annotations\Exception\ConvertException;

/**
 * Converts a list of tokens into an easy to use PHP array.
 *
 * This is the basic structure of the resulting array:
 *
 * ```
 * array(
 *     (object) array(
 *         'name' => 'The\\Annotation\\Name',
 *         'values' => array(
 *             // the values in the annotation: (1,something=2,@etc)
 *         )
 *     )
 * )
 * ```
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class TokensToArray implements ConvertInterface
{
    /**
     * The currently active annotation.
     *
     * @var object
     */
    private $current;

    /**
     * The current token offset.
     *
     * @var integer
     */
    private $offset;

    /**
     * The stack of references.
     *
     * @var array
     */
    private $references;

    /**
     * The converted array result.
     *
     * @var array
     */
    private $result;

    /**
     * The list of tokens to process.
     *
     * @var array
     */
    private $tokens;

    /**
     * The list of tokens that require a value.
     *
     * @var array
     */
    private static $valued = array(
        DocLexer::T_FLOAT => true,
        DocLexer::T_IDENTIFIER => true,
        DocLexer::T_INTEGER => true,
        DocLexer::T_STRING => true,
    );

    /**
     * The active list of values.
     *
     * @var array
     */
    private $values;

    /**
     * {@inheritDoc}
     */
    public function convert(array $tokens)
    {
        $this->reset($tokens);

        $count = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            $this->handle();
            $this->offset++;
        }

        return $this->result;
    }

    /**
     * Removes references to the current annotation and its values.
     */
    private function end()
    {
        if (empty($this->references)) {
            unset($this->current);
            unset($this->values);
        }
    }

    /**
     * Ends accepting values for the current list.
     */
    private function endList()
    {
        end($this->references);

        $key = key($this->references);

        $this->values =& $this->references[$key];

        unset($this->references[$key]);
    }

    /**
     * Ends accepting values for the current annotation.
     */
    private function endValues()
    {
        end($this->references);

        $key = key($this->references);

        $this->current = &$this->references[$key];
        $this->values = &$this->current->values;

        unset($this->references[$key]);
    }

    /**
     * Processes the current token.
     */
    private function handle()
    {
        $assign = false;
        $token = $this->token($this->offset);

        switch ($token[0]) {
            case DocLexer::T_AT:
                $this->start();

                break;
            case DocLexer::T_CLOSE_CURLY_BRACES:
                $this->endList();

                break;
            case DocLexer::T_CLOSE_PARENTHESIS:
                $this->endValues();
                $this->end();

                break;
            case DocLexer::T_OPEN_CURLY_BRACES:
                $this->startList();

                break;
            case DocLexer::T_OPEN_PARENTHESIS:
                $this->startValues();

                break;
            case DocLexer::T_FALSE:
                $assign = true;
                $token[1] = false;

                break;
            case DocLexer::T_FLOAT:
                $assign = true;
                $token[1] = (float) $token[1];

                break;
            case DocLexer::T_INTEGER:
                $assign = true;
                $token[1] = (int) $token[1];

                break;
            case DocLexer::T_NULL:
                $assign = true;
                $token[1] = null;

                break;
            case DocLexer::T_STRING:
                $assign = true;

                break;
            case DocLexer::T_TRUE:
                $assign = true;
                $token[1] = true;

                break;
            case DocLexer::T_COLON:
            case DocLexer::T_COMMA:
            case DocLexer::T_EQUALS:
                break;
        }

        // assigning a value?
        if ($assign) {

            // make sure the value isn't actually a key
            if ((null !== ($op = $this->token($this->offset + 1)))
                && (DocLexer::T_COLON !== $op[0])
                && (DocLexer::T_EQUALS !== $op[0])) {

                // check if the value is set using a key.
                if (null !== ($key = $this->key($this->offset))) {
                    $this->values[$key] = $token[1];
                } else {
                    $this->values[] = $token[1];
                }
            }
        }
    }

    /**
     * Returns the key name if available.
     *
     * @param integer $offset The offset of a possibly assigned value.
     *
     * @return string The key, if any.
     */
    private function key($offset)
    {
        // get the "key" and assignment operator
        $op = $this->token($offset - 1);
        $key = $this->token($offset - 2);

        // do we have both?
        if ($op && $key) {

            // make sure that the operator is an assignment operator
            if ((DocLexer::T_COLON === $op[0])
                || (DocLexer::T_EQUALS === $op[0])) {
                return $key[1];
            }
        }

        return null;
    }

    /**
     * Resets the converter's state.
     *
     * @param array $tokens The new tokens.
     */
    private function reset(array $tokens)
    {
        $this->current = null;
        $this->offset = 0;
        $this->references = array();
        $this->result = array();
        $this->tokens = array_values($tokens);
        $this->values = null;
    }

    /**
     * Begins a new annotation.
     */
    private function start()
    {
        // make sure we have its name
        if ((null === ($name = $this->token($this->offset + 1)))
            || (DocLexer::T_IDENTIFIER !== $name[0])) {
            throw new ConvertException(
                sprintf(
                    'The annotation (beginning at %d) is missing its identifier.',
                    $this->offset
                )
            );
        }

        $new = (object) array(
            'name' => $name[1],
            'values' => array(),
        );

        // if we have an existing one, make it nested
        if ($this->current) {

            // if set using a key, use the key
            if (null !== ($key = $this->key($this->offset))) {
                $this->values[$key] = $new;
            } else {
                $this->values[] = $new;
            }

            $this->references[] = $this->current;
            $this->current = $new;

            // if this is new, treat it as a "root" annotation
        } else {
            $this->current = $new;
            $this->result[] = $new;
        }
    }

    /**
     * Begins accepting values for a list.
     */
    private function startList()
    {
        // if the list is set using a key, use it
        if (null !== ($key = $this->key($this->offset))) {
            $this->values[$key] = array();
            $this->references[] = &$this->values;
            $this->values = &$this->values[$key];

            // just append the new list
        } else {
            $this->values[] = array();
            $this->references[] = &$this->values;

            end($this->values);

            $this->values = &$this->values[key($this->values)];
        }
    }

    /**
     * Beings accept values for an annotation.
     */
    private function startValues()
    {
        $this->values = &$this->current->values;
    }

    /**
     * Validates the token at the offset and returns it.
     *
     * @param integer $offset The offset.
     *
     * @return array The validated token.
     *
     * @throws ConvertException If the token is not valid.
     */
    private function token($offset)
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
