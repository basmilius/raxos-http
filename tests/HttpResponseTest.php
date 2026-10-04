<?php
declare(strict_types=1);

use Raxos\Http\{HttpResponse, HttpResponseCode};
use Raxos\Http\Response\NoContentHttpResponse;

covers(HttpResponse::class);

it('adds and replaces headers and changes status fluently', function (): void {
    $response = new NoContentHttpResponse();
    expect($response->header('X-Unit', 'a'))->toBe($response)->and($response->header('X-Unit', 'b'))->toBe($response)
        ->and($response->headers->getAll('X-Unit'))->toBe(['a', 'b']);
    expect($response->header('X-Unit', 'c', true))->toBe($response)->and($response->headers->getAll('X-Unit'))->toBe(['c'])
        ->and($response->responseCode(HttpResponseCode::ACCEPTED))->toBe($response)->and($response->responseCode)->toBe(HttpResponseCode::ACCEPTED);
});

it('sends status and headers before the body and restores output buffering', function (): void {
    $response = new class extends HttpResponse {

        public array $events = [];

        protected function sendResponseCode(): void
        {
            $this->events[] = 'status';
        }

        protected function sendHeaders(): void
        {
            $this->events[] = 'headers';
        }

        protected function sendBody(): void
        {
            $this->events[] = 'body';
            echo 'value';
        }

    };
    $level = ob_get_level();
    ob_start();
    $response->send();
    expect(ob_get_clean())->toBe('value')->and($response->events)->toBe(['status', 'headers', 'body'])->and(ob_get_level())->toBe($level);
});

it('cleans up its output buffer when sending a body throws', function (): void {
    $response = new class extends HttpResponse {

        protected function sendResponseCode(): void {}

        protected function sendHeaders(): void {}

        protected function sendBody(): void
        {
            throw new LogicException('body');
        }

    };
    $level = ob_get_level();
    try {
        expect(fn() => $response->send())->toThrow(LogicException::class, 'body');
        expect(ob_get_level())->toBe($level);
    } finally {
        while (ob_get_level() > $level) {
            ob_end_clean();
        }
    }
});
