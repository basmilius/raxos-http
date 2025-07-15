<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Transformer;

use Raxos\Http\Validate\Contract\TransformerInterface;
use Raxos\Http\Validate\Error\HttpTransformerException;
use function in_array;

/**
 * Class BooleanTransformer
 *
 * @implements TransformerInterface<bool>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Transformer
 * @since 1.7.0
 */
final readonly class BooleanTransformer implements TransformerInterface
{

    public const array FALSE = [false, 0, '0', 'false', 'no', 'off'];
    public const array TRUE = [true, 1, '1', 'true', 'yes', 'on'];

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function transform(mixed $value): bool
    {
        $isFalse = in_array($value, self::FALSE, true);
        $isTrue = in_array($value, self::TRUE, true);

        if (!$isFalse && !$isTrue) {
            throw HttpTransformerException::invalidValue('The value was not a boolean value.');
        }

        return $isTrue;
    }

}
