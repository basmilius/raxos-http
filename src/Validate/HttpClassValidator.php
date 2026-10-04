<?php
declare(strict_types=1);

namespace Raxos\Http\Validate;

use BackedEnum;
use Raxos\Contract\Http\HttpRequestModelInterface;
use Raxos\Contract\Http\Validate\ConstraintExceptionInterface;
use Raxos\Contract\Http\Validate\TransformerExceptionInterface;
use Raxos\Contract\Http\Validate\TransformerInterface;
use Raxos\Contract\Http\Validate\ValidatorExceptionInterface;
use Raxos\Foundation\Util\Singleton;
use Raxos\Http\Validate\Error\InvalidValueTransformerException;
use Raxos\Http\Validate\Error\MissingConstraintException;
use Raxos\Http\Validate\Error\ReflectionErrorException;
use Raxos\Http\Validate\Error\UnvalidatableException;
use Raxos\Http\Validate\Error\ValidationNotOkException;
use Raxos\Http\Validate\Transformer\BooleanTransformer;
use Raxos\Http\Validate\Transformer\FloatTransformer;
use Raxos\Http\Validate\Transformer\IntegerTransformer;
use ReflectionClass;
use ReflectionException;
use TypeError;
use function array_key_exists;
use function in_array;
use function is_bool;
use function is_string;
use function is_subclass_of;
use function mb_trim;
use function sprintf;

/**
 * Class HttpClassValidator
 *
 * Validates raw request input against reflected property rules before model hydration.
 *
 * @template TClass of object
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate
 * @since 1.7.0
 */
final class HttpClassValidator
{

    private const array BUILTIN_TRANSFORMERS = [
        'bool' => BooleanTransformer::class,
        'float' => FloatTransformer::class,
        'int' => IntegerTransformer::class
    ];

    /**
     * Retains reflected model metadata for repeated validations.
     *
     * @var ReflectionClass
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    private ReflectionClass $classRef;

    /**
     * Reuses request property metadata across validations without evaluating conditional rules early.
     *
     * @var list<RequestPropertyMetadata>
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    private array $metadata;

    /**
     * Retains raw input until constraints have validated it for model hydration.
     *
     * @var array
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    private array $data;

    /**
     * Accumulates property validation failures before returning the validation result.
     *
     * @var array
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    private array $errors = [];

    /**
     * Retains the validated model after successful hydration.
     *
     * @var array
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    private array $result = [];

    /**
     * HttpClassValidator constructor.
     *
     * @param class-string<TClass> $class
     *
     * @throws ValidatorExceptionInterface
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function __construct(
        public string $class
    )
    {
        if (!is_subclass_of($class, HttpRequestModelInterface::class)) {
            throw new UnvalidatableException($class);
        }

        try {
            $this->classRef = new ReflectionClass($class);
            $this->metadata = [];

            foreach ($this->classRef->getProperties() as $property) {
                $metadata = RequestPropertyMetadata::from($property, $this->classRef);

                if ($metadata !== null) {
                    $this->metadata[] = $metadata;
                }
            }
        } catch (ReflectionException $err) {
            throw new ReflectionErrorException($err);
        }
    }

    /**
     * Returns the validated class.
     *
     * @return TClass
     * @throws ValidatorExceptionInterface
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function get(): object
    {
        if (!empty($this->errors)) {
            throw new ValidationNotOkException($this->errors);
        }

        try {
            return $this->classRef->newInstanceArgs($this->result);
        } catch (ReflectionException $err) {
            throw new ReflectionErrorException($err);
        }
    }

    /**
     * Validate the data with the class.
     *
     * @param array $data
     *
     * @return void
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function validate(array $data): void
    {
        $this->data = $data;
        $this->errors = [];
        $this->result = [];

        foreach ($this->metadata as $metadata) {
            $this->validateProperty($metadata);
        }
    }

    /**
     * Validates a single property of the class.
     *
     * @param RequestPropertyMetadata $metadata
     *
     * @return void
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    private function validateProperty(RequestPropertyMetadata $metadata): void
    {
        $propertyAttr = $metadata->attribute;
        $propertyRef = $metadata->property;
        $isOptional = $propertyAttr->optional;

        if (!is_bool($isOptional)) {
            $isOptional = $isOptional($propertyRef->name, $propertyAttr);
        }

        try {
            $propertyKey = $metadata->name();
            [$propertyValue, $isDefaultValue] = $this->getValue($metadata, $isOptional);
            $propertyTypes = $metadata->types;
            $propertyType = $propertyTypes[0] ?? null;

            if ($propertyType !== null && isset(self::BUILTIN_TRANSFORMERS[$propertyType])) {
                if ($propertyValue !== null || !$propertyRef->getType()?->allowsNull()) {
                    $transformer = Singleton::get(self::BUILTIN_TRANSFORMERS[$propertyType]);
                    $propertyValue = $transformer->transform($propertyValue);
                }
            } elseif (is_subclass_of($propertyType, HttpRequestModelInterface::class)) {
                if ($propertyValue !== null) {
                    if (!is_array($propertyValue)) {
                        throw new InvalidValueTransformerException('Expected a nested input object.');
                    }
                    $validator = new self($propertyType);
                    $validator->validate($propertyValue);
                    $propertyValue = $validator->get();
                } elseif ($isOptional) {
                    $propertyValue = null;
                } else {
                    throw new MissingConstraintException($propertyKey);
                }
            } elseif (is_subclass_of($propertyType, BackedEnum::class)) {
                if ($propertyValue !== null) {
                    $original = $propertyValue;

                    try {
                        $propertyValue = $propertyType::tryFrom($propertyValue);
                    } catch (TypeError) {
                        throw new InvalidValueTransformerException(sprintf('Invalid value type for enum %s.', $propertyType));
                    }

                    if ($propertyValue === null) {
                        throw new InvalidValueTransformerException(sprintf('Invalid enum value "%s" for enum %s.', $original, $propertyType));
                    }
                }

                if ($propertyValue === null && !in_array('null', $propertyTypes, true)) {
                    throw new InvalidValueTransformerException(sprintf('Invalid enum value for enum %s.', $propertyType));
                }
            }

            if (!$isDefaultValue && $propertyValue !== null) {
                foreach ($metadata->constraints as $constraint) {
                    $constraint = $constraint->newInstance();

                    if ($constraint instanceof TransformerInterface) {
                        $propertyValue = $constraint->transform($propertyValue);
                    }

                    $propertyValue = $constraint->check($propertyRef, $propertyValue);
                }
            }

            $this->result[$propertyRef->name] = $propertyValue;
        } catch (ConstraintExceptionInterface|TransformerExceptionInterface|ValidatorExceptionInterface $err) {
            $this->errors[$propertyKey] = $err;
        }
    }

    /**
     * Gets a property value.
     *
     * @param RequestPropertyMetadata $metadata
     * @param bool $isOptional
     *
     * @return array{0: mixed, 1: bool}
     * @throws ConstraintExceptionInterface
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    private function getValue(
        RequestPropertyMetadata $metadata,
        bool $isOptional
    ): array
    {
        $propertyKey = $metadata->name();
        $propertyRef = $metadata->property;

        if (array_key_exists($propertyKey, $this->data)) {
            $value = $this->data[$propertyKey];

            if ($value !== null && (!is_string($value) || mb_trim($value) !== '')) {
                return [$value, false];
            }
        }

        if (!$isOptional) {
            throw new MissingConstraintException($propertyKey);
        }

        $value = $metadata->default;

        if ($value === null && !$propertyRef->getType()?->allowsNull()) {
            throw new MissingConstraintException($propertyKey);
        }

        return [$value, true];
    }

}
