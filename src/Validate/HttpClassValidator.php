<?php
declare(strict_types=1);

namespace Raxos\Http\Validate;

use BackedEnum;
use Raxos\Contract\Http\HttpRequestModelInterface;
use Raxos\Contract\Http\Validate\{ConstraintAttributeInterface, ConstraintExceptionInterface, TransformerExceptionInterface, TransformerInterface, ValidatorExceptionInterface};
use Raxos\Foundation\Util\{ReflectionUtil, Singleton};
use Raxos\Http\Validate\Attribute\Property;
use Raxos\Http\Validate\Error\{InvalidValueTransformerException, MissingConstraintException, ReflectionErrorException, UnvalidatableException, ValidationNotOkException};
use Raxos\Http\Validate\Transformer\{BooleanTransformer, FloatTransformer, IntegerTransformer};
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use function array_find;
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

    private ReflectionClass $classRef;
    private ?ReflectionMethod $constructorRef;
    private array $parameterRefs;
    private array $propertyRefs;
    private array $data;
    private array $errors = [];
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
            $this->constructorRef = $this->classRef->getConstructor();
            $this->parameterRefs = $this->constructorRef?->getParameters() ?? [];
            $this->propertyRefs = $this->classRef->getProperties();
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

        foreach ($this->propertyRefs as $propertyRef) {
            /** @var ReflectionAttribute<Property> $propertyAttr */
            $propertyAttr = $propertyRef->getAttributes(Property::class)[0] ?? null;

            if ($propertyAttr === null) {
                continue;
            }

            $this->validateProperty($propertyAttr->newInstance(), $propertyRef);
        }
    }

    /**
     * Validates a single property of the class.
     *
     * @param Property $propertyAttr
     * @param ReflectionProperty $propertyRef
     *
     * @return void
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    private function validateProperty(Property $propertyAttr, ReflectionProperty $propertyRef): void
    {
        $isOptional = $propertyAttr->optional;

        if (!is_bool($isOptional)) {
            $isOptional = $isOptional($propertyRef->name, $propertyAttr);
        }

        try {
            $propertyKey = $propertyAttr->alias ?? $propertyRef->name;
            [$propertyValue, $isDefaultValue] = $this->getValue($propertyAttr, $propertyRef, $propertyKey, $isOptional);
            $propertyTypes = ReflectionUtil::getTypes($propertyRef->getType());
            $propertyType = $propertyTypes[0] ?? null;

            if ($propertyType !== null && isset(self::BUILTIN_TRANSFORMERS[$propertyType])) {
                $transformer = Singleton::get(self::BUILTIN_TRANSFORMERS[$propertyType]);
                $propertyValue = $transformer->transform($propertyValue);
            } elseif (is_subclass_of($propertyType, HttpRequestModelInterface::class)) {
                if ($propertyValue !== null) {
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
                    $propertyValue = $propertyType::tryFrom($propertyValue);
                }

                if ($propertyValue === null && !in_array('null', $propertyTypes, true)) {
                    throw new InvalidValueTransformerException(sprintf('Invalid enum value for enum %s.', $propertyType));
                }
            }

            if (!$isDefaultValue && $propertyValue !== null) {
                /** @var ReflectionAttribute<ConstraintAttributeInterface>[] $constraints */
                $constraints = $propertyRef->getAttributes(ConstraintAttributeInterface::class, ReflectionAttribute::IS_INSTANCEOF);

                foreach ($constraints as $constraint) {
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
     * @param Property $propertyAttr
     * @param ReflectionProperty $propertyRef
     * @param string $propertyKey
     * @param bool $isOptional
     *
     * @return array{0: mixed, 1: bool}
     * @throws ConstraintExceptionInterface
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    private function getValue(Property $propertyAttr, ReflectionProperty $propertyRef, string $propertyKey, bool $isOptional): array
    {
        if (array_key_exists($propertyKey, $this->data)) {
            $value = $this->data[$propertyKey];

            if ($value !== null && (!is_string($value) || mb_trim($value) !== '')) {
                return [$value, false];
            }
        }

        if (!$isOptional) {
            throw new MissingConstraintException($propertyKey);
        }

        $value = $propertyRef->hasDefaultValue() ? $propertyRef->getDefaultValue() : null;

        if ($propertyRef->isPromoted()) {
            /** @var ReflectionParameter|null $parameterRef */
            $parameterRef = array_find($this->parameterRefs, static fn(ReflectionParameter $parameter) => $parameter->name === $propertyRef->name);

            if ($parameterRef?->isDefaultValueAvailable()) {
                $value = $parameterRef->getDefaultValue();
            }
        }

        if ($value === null && !$propertyRef->getType()?->allowsNull()) {
            throw new MissingConstraintException($propertyKey);
        }

        return [$value, true];
    }

}
