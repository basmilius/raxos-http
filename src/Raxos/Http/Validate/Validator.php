<?php
declare(strict_types=1);

namespace Raxos\Http\Validate;

use Raxos\Http\Validate\Attribute\{Field, Optional};
use Raxos\Http\Validate\Constraint\Constraint;
use Raxos\Http\Validate\Error\{ValidatorException};
use function in_array;
use function is_subclass_of;
use function sprintf;

/**
 * Class Validator
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate
 * @since 1.0.0
 */
final class Validator
{

    private static array $attributes = [
        Field::class,
        Optional::class,
        Constraint::class
    ];

    /**
     * Returns TRUE if the given attribute class should be handled
     * by our validator.
     *
     * @param string $attributeClass
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public static function isAttributeSupported(string $attributeClass): bool
    {
        if (in_array($attributeClass, self::$attributes, true)) {
            return true;
        }

        foreach (self::$attributes as $attribute) {
            if (is_subclass_of($attributeClass, $attribute)) {
                self::$attributes[] = $attributeClass;

                return true;
            }
        }

        return false;
    }

    /**
     * Validates the given request using the given request model.
     *
     * @template T of RequestModel
     *
     * @param class-string<T> $requestModelClass
     * @param array $request
     *
     * @return T&RequestModel
     * @throws ValidatorException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public static function validate(string $requestModelClass, array $request): RequestModel
    {
        if (!is_subclass_of($requestModelClass, RequestModel::class)) {
            throw ValidatorException::invalidModel(sprintf('%s is not a valid %s.', $requestModelClass, RequestModel::class));
        }

        /** @var RequestModel $model */
        $model = new $requestModelClass($request);
        $model->validate();

        return $model;
    }

}
