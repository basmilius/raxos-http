<?php
declare(strict_types=1);

namespace Raxos\Http\Validate;

use JsonSerializable;
use Raxos\Http\Validate\Attribute\Field;
use Raxos\Http\Validate\Attribute\Optional;
use Raxos\Http\Validate\Constraint\Boolean;
use Raxos\Http\Validate\Constraint\Constraint;
use Raxos\Http\Validate\Constraint\Integer;
use Raxos\Http\Validate\Constraint\RequestModelConstraint;
use Raxos\Http\Validate\Constraint\Text;
use Raxos\Http\Validate\Error\FieldException;
use Raxos\Http\Validate\Error\FieldModelException;
use Raxos\Http\Validate\Error\ValidationException;
use Raxos\Http\Validate\Error\ValidatorException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use function array_filter;
use function array_key_exists;
use function array_map;
use function array_values;
use function count;
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
    public final function __construct(private array $data)
    {
        $this->prepare();
    }

    /**
     * Validates the request.
     *
     * @throws ValidationException
     * @throws ValidatorException
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function validate(): void
    {
        $errors = [];

        foreach (self::$fields[static::class] as $field) {
            $constraint = $field->getConstraint();
            $name = $field->getName();
            $property = $field->getFieldProperty();

            unset($this->{$name});

            if (!array_key_exists($property, $this->data)) {
                if ($field->isOptional()) {
                    $this->values[$name] = $field->getDefaultValue();

                    continue;
                }

                $errors[$property] = new FieldException($field, '{{name}} is required');

                continue;
            }

            try {
                $value = $this->data[$property];

                $constraint->validate($field, $value);

                $value = $constraint->transform($value);
                $valueType = gettype($value);
                $valueType = match ($valueType) {
                    'boolean' => 'bool',
                    'integer' => 'int',
                    'object' => get_class($value),
                    default => $valueType
                };

                if (!in_array($valueType, $field->getTypes())) {
                    throw new ValidatorException(sprintf('Value type %s is not assignable to %s.', $valueType, implode('|', $field->getTypes())), ValidationException::ERR_INVALID_TYPE);
                }

                $this->values[$name] = $value;
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
        $attributes = array_filter($attributes, fn(ReflectionAttribute $attr) => Validator::isAttributeSupported($attr->getName()));

        $types = [];
        $propertyType = $property->getType();

        if ($propertyType instanceof ReflectionNamedType) {
            $types[] = $propertyType;
        } else if ($propertyType instanceof ReflectionUnionType) {
            $types = $propertyType->getTypes();
        }

        $types = array_map(fn(ReflectionNamedType $type) => $type->getName(), $types);

        if ($propertyType->allowsNull()) {
            $types[] = 'null';
        }

        $fields = array_filter($attributes, fn(ReflectionAttribute $attr) => $attr->getName() === Field::class);
        $fields = array_map(fn(ReflectionAttribute $attr) => $attr->newInstance(), $fields);
        $fields = array_values($fields);

        $constraints = array_filter($attributes, fn(ReflectionAttribute $attr) => is_subclass_of($attr->getName(), Constraint::class));
        $constraints = array_map(fn(ReflectionAttribute $attr) => $attr->newInstance(), $constraints);
        $constraints = array_values($constraints);

        $requestModel = array_filter($types, fn(string $type) => is_subclass_of($type, RequestModel::class));

        if (!empty($requestModel) && count($requestModel) === 1) {
            $constraints = [
                new RequestModelConstraint($requestModel[0])
            ];
        } else if (empty($constraints)) {
            $constraints = match ($types[0]) {
                'bool' => [new Boolean()],
                'int' => [new Integer()],
                'string' => [new Text()],
                default => throw new ValidatorException(sprintf('Property %s::$%s must have a constraint.', $class->getName(), $property->getName()), ValidationException::ERR_MISSING_CONSTRAINT)
            };
        }

        self::$fields[static::class][] = new RequestField(
            $class->getName(),
            $property->getName(),
            $fields[0],
            $constraints[0],
            !empty(array_filter($attributes, fn(ReflectionAttribute $attr) => $attr->getName() === Optional::class)),
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
            throw new ValidatorException(sprintf('Property %s does not exist on request model %s.', $name, static::class), ValidatorException::ERR_INVALID_PROPERTY);
        }

        return $this->values[$name];
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
