<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Contract;

use Raxos\Http\Validate\Error\HttpTransformerException;

/**
 * Interface TransformerInterface
 *
 * @template T of mixed
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Contract
 * @since 1.7.0
 */
interface TransformerInterface
{

    /**
     * Transforms the value.
     *
     * @param mixed $value
     *
     * @return T
     * @throws HttpTransformerException
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function transform(mixed $value): mixed;

}
