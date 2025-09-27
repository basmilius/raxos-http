<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Transformer;

use Raxos\Contract\Http\Validate\TransformerInterface;
use Raxos\Http\Validate\Error\InvalidValueTransformerException;

/**
 * Class IntegerTransformer
 *
 * @implements TransformerInterface<int>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Transformer
 * @since 1.7.0
 */
final readonly class IntegerTransformer implements TransformerInterface
{

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function transform(mixed $value): int
    {
        if (!is_numeric($value)) {
            throw new InvalidValueTransformerException('Expected an integer value.');
        }

        return (int)$value;
    }

}
