<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Attribute;
use Raxos\Contract\Http\Validate\ConstraintAttributeInterface;
use Raxos\Http\HttpFile;
use Raxos\Http\Validate\Error\UploadConstraintException;
use ReflectionProperty;

/**
 * Class Upload
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 1.7.0
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Upload implements ConstraintAttributeInterface
{

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function check(ReflectionProperty $property, mixed $value): HttpFile
    {
        if (!($value instanceof HttpFile)) {
            throw new UploadConstraintException();
        }

        if (!$value->isValid) {
            throw new UploadConstraintException();
        }

        return $value;
    }

}
