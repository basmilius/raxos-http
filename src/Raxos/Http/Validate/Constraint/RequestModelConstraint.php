<?php
declare(strict_types=1);

namespace Raxos\Http\Validate\Constraint;

use Raxos\Http\Validate\Error\FieldException;
use Raxos\Http\Validate\RequestField;
use Raxos\Http\Validate\RequestModel;
use Raxos\Http\Validate\Validator;
use function is_array;

/**
 * Class RequestModelConstraint
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Validate\Constraint
 * @since 1.0.0
 */
final class RequestModelConstraint extends Constraint
{

    private ?RequestModel $model = null;

    /**
     * RequestModelConstraint constructor.
     *
     * @param string $requestModelClass
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(private string $requestModelClass)
    {
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function transform(mixed $data): RequestModel
    {
        return $this->model;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function validate(RequestField $field, mixed $data): void
    {
        if (!is_array($data)) {
            throw new FieldException($field, '{{name}} does not meet the request requirements.');
        }

        $this->model = Validator::validate($this->requestModelClass, (array)$data);
    }

}
