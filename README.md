<a href="https://bas.dev">
    <img src="https://bmcdn.nl/assets/branding/logo.svg" alt="Bas Milius" height="48" />
</a>

---

# Raxos HTTP

Incoming requests, outgoing responses, HTTP clients, validation and file streaming.

[Documentation](https://raxos.dev/http/) | [Packagist](https://packagist.org/packages/raxos/http) | [Raxos](https://github.com/basmilius/raxos)

- Typed request maps, headers, methods, status codes and uploaded files.
- JSON, HTML, redirects, files and empty response types.
- A client using Guzzle and PSR-7, PSR-17 and PSR-18 adapters.
- Validation through PHP attributes and streamed byte-range responses.

## Installation

Requires PHP 8.5 or later. Enable the `ctype`, `fileinfo`, `mbstring` PHP extensions. Composer checks the remaining package and extension dependencies declared in [composer.json](composer.json).

```sh
composer require "raxos/http:^3.2"
```

## Usage

```php
<?php
declare(strict_types=1);

use Raxos\Http\HttpMethod;
use Raxos\Http\HttpRequest;
use Raxos\Http\Response\JsonHttpResponse;

require __DIR__ . '/vendor/autoload.php';

$request = HttpRequest::create(method: HttpMethod::GET, uri: '/products?limit=25');
$limit = (int)$request->query->get('limit');

$response = new JsonHttpResponse(['items' => [], 'limit' => $limit]);
$response->send();
```

Use `HttpRequest::createFromGlobals()` at an HTTP entry point. Invalid or scalar JSON bodies are rejected when parsed as structured JSON. File streaming defaults to no throttle; invalid or multiple byte ranges return HTTP 416. Set a positive throttle explicitly when needed.

## Documentation

- [Requests and responses](https://raxos.dev/http/requests-and-responses)
- [HTTP client](https://raxos.dev/http/http-client)
- [Request validation](https://raxos.dev/http/validation)

## Testing

Run this library's Pest suite from the Raxos workspace:

```sh
git clone --recurse-submodules https://github.com/basmilius/raxos.git
cd raxos
composer install
vendor/bin/pest --testsuite=http
```

See [Testing Raxos](https://github.com/basmilius/raxos/blob/main/TESTING.md) for PHP extensions, integration services and coverage commands. The library's [Tests workflow](.github/workflows/tests.yml) also runs in GitHub Actions.

## License

[MIT](LICENSE). Copyright (c) 2017 - present Bas Milius.
