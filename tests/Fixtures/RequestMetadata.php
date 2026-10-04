<?php
declare(strict_types=1);

namespace RaxosTests\Http;

use Attribute;
use Raxos\Contract\Http\Validate\ConstraintAttributeInterface;
use Raxos\Http\Validate\Attribute\Property;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class MetadataProbeConstraint implements ConstraintAttributeInterface
{

    public static int $instances = 0;

    public function __construct()
    {
        ++self::$instances;
    }

    public function check(ReflectionProperty $property, mixed $value): mixed
    {
        return $value;
    }

}

final class MetadataInput
{

    #[Property(alias: 'display_name', optional: true)]
    #[MetadataProbeConstraint]
    public string $name = 'fallback';

    #[Property(optional: true)]
    public string $missing;

    public string $ignored;

    public function __construct(
        #[Property(optional: true)]
        public ?int $count = 0,
        #[Property(optional: true)]
        public ?string $note = null
    ) {}

}
