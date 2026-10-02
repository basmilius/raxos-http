<?php
declare(strict_types=1);

namespace RaxosTests\Http;

use Raxos\Http\HttpRequest;

final readonly class JsonRequest extends HttpRequest
{
    public function body(): ?string
    {
        return $this->parameters->get('fixture_body');
    }
}
