<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Transformer;

use Raxos\Http\Validate\Contract\TransformerInterface;
use Raxos\Http\Validate\Error\HttpTransformerException;

/**
 * Class FloatTransformer
 *
 * @implements TransformerInterface<float>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Transformer
 * @since 1.7.0
 */
final readonly class FloatTransformer implements TransformerInterface
{

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function transform(mixed $value): float
    {
        if (!is_numeric($value)) {
            throw HttpTransformerException::invalidValue('Expected an integer value.');
        }

        return (float)$value;
    }

}
