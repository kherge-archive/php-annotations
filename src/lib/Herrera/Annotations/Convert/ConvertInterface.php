<?php

namespace Herrera\Annotations\Convert;

use Herrera\Annotations\Exception\ConvertException;

/**
 * Defines how a converting class must be implemented.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
interface ConvertInterface
{
    /**
     * Returns the converted result for the list of tokens.
     *
     * @param array $tokens The list of tokens.
     *
     * @return mixed The converted result.
     *
     * @throws ConvertException If the tokens could not be converted.
     */
    public function convert(array $tokens);
}
