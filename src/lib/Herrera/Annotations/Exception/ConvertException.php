<?php

namespace Herrera\Annotations\Exception;

/**
 * This exception is thrown if there is a conversion error.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class ConvertException extends Exception
{
    /**
     * Returns an exception for an invalid token.
     *
     * @param integer $index The array index for the token.
     *
     * @return ConvertException The new exception.
     */
    public static function invalidToken($index)
    {
        return new self("The token at index $index is not valid.");
    }

    /**
     * Returns an exception for an unrecognized token.
     *
     * @param mixed $token The unrecognized token.
     *
     * @return ConvertException The new exception.
     */
    public static function unrecognizedToken($token)
    {
        return new self("The token \"$token\" is not recognized.");
    }
}
