<?php
declare(strict_types=1);

namespace RaxosTests\Http;

use Raxos\Contract\Http\HttpRequestModelInterface;
use Raxos\Http\Validate\Attribute\Property;
use Raxos\Http\Validate\Constraint\{Choice, Email, Min, MinLength};

enum UnitState: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}

final readonly class AddressInput implements HttpRequestModelInterface
{
    public function __construct(#[Property] #[MinLength(2)] public string $city)
    {
    }
}

final readonly class UnitInput implements HttpRequestModelInterface
{
    public function __construct(
        #[Property(alias: 'email_address')]
        #[Email]
        public string $email,
        #[Property]
        #[Min(1)]
        public int $count,
        #[Property]
        public float $price,
        #[Property]
        public bool $enabled,
        #[Property]
        public UnitState $state,
        #[Property]
        public AddressInput $address,
        #[Property(optional: true)]
        public ?string $note = null,
        #[Property(optional: true)]
        #[Choice(['valid'])]
        public string $default = 'trusted-default',
        public string $ignored = 'ignored',
    ) {
    }
}

final readonly class ScalarInput implements HttpRequestModelInterface
{
    public function __construct(#[Property] public int $value)
    {
    }
}

final readonly class NullableInput implements HttpRequestModelInterface
{
    public function __construct(#[Property(optional: true)] public ?int $value = null, #[Property(optional: true)] public ?UnitState $state = null)
    {
    }
}

final readonly class OptionalWithoutDefaultInput implements HttpRequestModelInterface
{
    public function __construct(#[Property(optional: true)] public int $value)
    {
    }
}

final class ConstraintFields
{
    public mixed $value;
    public AddressInput $address;
    public string $invalidNested;
}

function constraintProperty(string $name = 'value'): \ReflectionProperty
{
    return new \ReflectionProperty(ConstraintFields::class, $name);
}

function responseBody(\Raxos\Http\HttpResponse $response): string
{
    ob_start();
    try {
        new \ReflectionMethod($response, 'sendBody')->invoke($response);
        return ob_get_contents();
    } finally {
        ob_end_clean();
    }
}
