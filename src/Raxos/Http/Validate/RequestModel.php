<?php
declare(strict_types=1);

namespace Raxos\Http\Validate;

use JsonSerializable;
use Raxos\Http\HttpFile;
use Raxos\Http\Validate\Attribute\{Field, Optional};
use Raxos\Http\Validate\Constraint\{Boolean, Constraint, FileConstraint, Integer, RequestModelConstraint, Text};
use Raxos\Http\Validate\Error\{FieldException, FieldModelException, ValidationException, ValidatorException};
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use function array_any;
use function array_filter;
use function array_key_exists;
use function array_map;
use function array_values;
use function get_class;
use function gettype;
use function implode;
use function in_array;
use function is_subclass_of;
use function sprintf;

/**
 * Class RequestModel
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate
 * @since 1.0.0
 */
abstract class RequestModel implements JsonSerializable
{

    /** @var RequestField[][] */
    private static array $fields = [];
    private static array $didPrepare = [];

    private array $values = [];

    /**
     * RequestModel constructor.
     *
     * @param array $data
     *
     * @throws ValidatorException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function __construct(
        private readonly array $data
    )
    {
        $this->prepare();
    }

    /**
     * Validates the request.
     *
     * @throws ValidatorException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function validate(): void
    {
        $errors = [];

        foreach (self::$fields[static::class] as $field) {
            $constraint = $field->constraint;
            $name = $field->name;
            $property = $field->property;

            unset($this->{$name});

            if (!array_key_exists($property, $this->data)) {
                if ($field->isOptional) {
                    $this->values[$name] = $field->defaultValue;

                    continue;
                }

                $errors[$property] = new FieldException($field, '{{name}} is required.');

                continue;
            }

            try {
                $value = $this->data[$property];

                if ($value === null && $field->isOptional) {
                    $this->values[$name] = null;
                } else {
                    $constraint->validate($field, $value);

                    $value = $constraint->transform($value);
                    $valueType = gettype($value);
                    $valueType = match ($valueType) {
                        'boolean' => 'bool',
                        'integer' => 'int',
                        'double' => 'float',
                        'object' => get_class($value),
                        'NULL' => 'null',
                        default => $valueType
                    };

                    if (!in_array($valueType, $field->types, true) && !array_any($field->types, static fn(string $type) => is_subclass_of($valueType, $type))) {
                        throw ValidatorException::invalidType(sprintf('Value type %s is not assignable to %s.', $valueType, implode('|', $field->types)));
                    }

                    $this->values[$name] = $value;
                }
            } catch (FieldException $err) {
                $errors[$property] = $err;
            } catch (ValidationException $err) {
                $errors[$property] = new FieldModelException($field, $err);
            }
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    /**
     * Prepares the model.
     *
     * @throws ValidatorException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    private function prepare(): void
    {
        if (isset(self::$didPrepare[static::class])) {
            return;
        }

        self::$fields[static::class] = [];

        $class = new ReflectionClass(static::class);
        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $this->prepareProperty($class, $property);
        }

        self::$didPrepare[static::class] = true;
    }

    /**
     * Prepares a property of the model.
     *
     * @param ReflectionClass $class
     * @param ReflectionProperty $property
     *
     * @throws ValidatorException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    private function prepareProperty(ReflectionClass $class, ReflectionProperty $property): void
    {
        $attributes = $property->getAttributes();
        $attributes = array_filter($attributes, static fn(ReflectionAttribute $attr) => Validator::isAttributeSupported($attr->getName()));

        $types = [];
        $propertyType = $property->getType();

        if ($propertyType instanceof ReflectionNamedType) {
            $types[] = $propertyType;
        } elseif ($propertyType instanceof ReflectionUnionType) {
            $types = $propertyType->getTypes();
        }

        $types = array_map(static fn(ReflectionNamedType $type) => $type->getName(), $types);

        if ($propertyType->allowsNull()) {
            $types[] = 'null';
        }

        $fields = array_filter($attributes, static fn(ReflectionAttribute $attr) => $attr->getName() === Field::class);
        $fields = array_map(static fn(ReflectionAttribute $attr) => $attr->newInstance(), $fields);
        $fields = array_values($fields);

        $constraints = array_filter($attributes, static fn(ReflectionAttribute $attr) => is_subclass_of($attr->getName(), Constraint::class));
        $constraints = array_map(static fn(ReflectionAttribute $attr) => $attr->newInstance(), $constraints);
        $constraints = array_values($constraints);

        if (is_subclass_of($types[0], self::class)) {
            $constraints[] = new RequestModelConstraint($types[0]);
        } elseif ($types[0] === HttpFile::class) {
            $constraints[] = new FileConstraint();
        } elseif (empty($constraints)) {
            $constraints = match ($types[0]) {
                'bool' => [new Boolean()],
                'int' => [new Integer()],
                'string' => [new Text()],
                default => throw ValidatorException::missingConstraint(sprintf('Property %s::$%s must have a constraint.', $class->name, $property->name))
            };
        }

        self::$fields[static::class][] = new RequestField(
            $class->name,
            $property->name,
            $fields[0],
            $constraints[0],
            !empty(array_filter($attributes, static fn(ReflectionAttribute $attr) => $attr->getName() === Optional::class)),
            $types,
            $property->hasDefaultValue() ? $property->getDefaultValue() : null
        );
    }

    /**
     * Gets a model value.
     *
     * @param string $name
     *
     * @return mixed
     * @throws ValidatorException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __get(string $name): mixed
    {
        if (!array_key_exists($name, $this->values)) {
            throw ValidatorException::invalidProperty(sprintf('Property %s does not exist on request model %s.', $name, static::class));
        }

        return $this->values[$name];
    }

    /**
     * Returns TRUE if a field with the given name exists.
     *
     * @param string $name
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.16
     */
    public function __isset(string $name): bool
    {
        return array_key_exists($name, $this->values);
    }

    /**
     * Setter.
     *
     * @param string $name
     * @param mixed $value
     *
     * @return void
     * @throws ValidatorException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.16
     */
    public function __set(string $name, mixed $value): void
    {
        throw ValidatorException::immutable('Cannot modify request model instance.');
    }

    /**
     * Unsetter.
     *
     * @param string $name
     *
     * @return void
     * @throws ValidatorException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.16
     */
    public function __unset(string $name): void
    {
        throw ValidatorException::immutable('Cannot modify request model instance.');
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function jsonSerialize(): array
    {
        return $this->values;
    }

}
