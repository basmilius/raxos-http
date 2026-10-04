<?php
declare(strict_types=1);

namespace RaxosTests\Http;

use Raxos\Database\Orm\{Model, ModelArrayList};
use Raxos\Database\Orm\Error\NotFoundException;

final class ConstraintModel extends Model
{

    public function __construct(public int $id = 1) {}

    public static function single(array|string|int $primaryKey): ?static
    {
        if ($primaryKey === 'error') {
            throw new NotFoundException(self::class, $primaryKey);
        }

        return $primaryKey === 1 ? new self() : null;
    }

    public static function find(array $primaryKeys): ModelArrayList
    {
        if (in_array('error', $primaryKeys, true)) {
            throw new NotFoundException(self::class, 'error');
        }

        return new ModelArrayList(array_map(static fn(int $id): self => new self($id), array_values(array_filter($primaryKeys, static fn(mixed $id): bool => is_int($id) && $id > 0))));
    }

}

final class ModelConstraintFields
{

    public ConstraintModel $model;
    public string $invalid;

}
