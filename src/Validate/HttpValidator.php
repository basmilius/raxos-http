<?php
declare(strict_types=1);

namespace Raxos\Http\Validate;

use Raxos\Contract\Http\Validate\ValidatorExceptionInterface;

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
     * @template TClass of object
     *
     * @param class-string<TClass> $class
     * @param array<string, mixed> $data
     *
     * @return TClass
     * @throws ValidatorExceptionInterface
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public static function validate(string $class, array $data): object
    {
        $validator = new HttpClassValidator($class);
        $validator->validate($data);

        return $validator->get();
    }

}
