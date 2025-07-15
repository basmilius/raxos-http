<?php
declare(strict_types=1);

namespace Raxos\Http\Validate;

use Raxos\Http\Validate\Error\HttpValidatorException;

/**
 * Class HttpValidator
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate
 * @since 1.7.0
 */
final class HttpValidator
{

    /**
     * Validates the data with the given class.
     *
     * @template TClass
     *
     * @param class-string<TClass> $class
     * @param array<string, mixed> $data
     *
     * @return TClass
     * @throws HttpValidatorException
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public static function validate(string $class, array $data): mixed
    {
        $validator = new HttpClassValidator($class);
        $validator->validate($data);

        return $validator->get();
    }

}
