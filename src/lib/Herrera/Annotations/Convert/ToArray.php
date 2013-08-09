<?php

namespace Herrera\Annotations\Convert;

use Doctrine\Common\Annotations\DocLexer;
use Herrera\Annotations\Exception\UnexpectedTokenException;
use Herrera\Annotations\Tokens;

/**
 * Converts a series of tokens into a simple array.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class ToArray extends AbstractConvert
{
    /**
     * The currently active annotation.
     *
     * @var object
     */
    private $current;

    /**
     * The stack of references.
     *
     * @var array
     */
    private $references;

    /**
     * The active list of values reference.
     *
     * @var array
     */
    private $values;

    /**
     * {@inheritDoc}
     */
    protected function handle()
    {
        $assign = false;
        $offset = $this->tokens->key();
        $token = $this->tokens->current();

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
            case DocLexer::T_FLOAT:
            case DocLexer::T_INTEGER:
            case DocLexer::T_NULL:
            case DocLexer::T_STRING:
            case DocLexer::T_TRUE:
                $assign = true;
                $token[1] = $this->tokens->getValue($offset);
                break;
            case DocLexer::T_COLON:
            case DocLexer::T_COMMA:
            case DocLexer::T_EQUALS:
                break;
        }

        // assigning a value?
        if ($assign) {

            // make sure the value isn't actually a key
            if ((null !== ($op = $this->tokens->getToken($offset + 1)))
                && (DocLexer::T_COLON !== $op[0])
                && (DocLexer::T_EQUALS !== $op[0])) {

                // check if the value is set using a key.
                if (null !== ($key = $this->key($offset))) {
                    $this->values[$key] = $token[1];
                } else {
                    $this->values[] = $token[1];
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function reset(Tokens $tokens)
    {
        unset($this->current);
        unset($this->values);

        $this->current = null;
        $this->references = array();
        $this->result = array();
        $this->tokens = $tokens;
        $this->values = null;
    }

    /**
     * Removes references to the current annotation and its values.
     */
    private function end()
    {
        if (empty($this->references)) {
            unset($this->current);
            unset($this->values);

            $this->current = null;
            $this->values = null;
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
     * Returns the key name if available.
     *
     * @param integer $offset The offset of a possibly assigned value.
     *
     * @return string The key, if any.
     */
    private function key($offset)
    {
        // get the "key" and assignment operator
        $op = $this->tokens->getToken($offset - 1);
        $key = $this->tokens->getToken($offset - 2);

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
     * Begins a new annotation.
     */
    private function start()
    {
        // make sure we have its name
        $name = $this->tokens->getToken($this->tokens->key() + 1);

        if ((null === $name)
            || (DocLexer::T_IDENTIFIER !== $name[0])) {
            throw UnexpectedTokenException::create(
                'The annotation (beginning at %d) is missing its identifier.',
                $this->tokens->key()
            );
        }

        $new = (object) array(
            'name' => $name[1],
            'values' => array(),
        );

        // if we have an existing one, make it nested
        if ($this->current && (null !== $this->values)) {

            // if set using a key, use the key
            if (null !== ($key = $this->key($this->tokens->key()))) {
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
        if (null !== ($key = $this->key($this->tokens->key()))) {
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
}
