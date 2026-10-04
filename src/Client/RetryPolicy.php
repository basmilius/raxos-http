<?php
declare(strict_types=1);

namespace Raxos\Http\Client;

use Closure;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Raxos\Error\InvalidArgumentException;
use function array_is_list;
use function hrtime;
use function in_array;
use function is_finite;
use function is_int;
use function max;
use function min;
use function preg_match;
use function strtotime;
use function time;
use function usleep;

/**
 * Class RetryPolicy
 *
 * Bounded retries; unsafe methods require explicit opt-in and Retry-After never exceeds the budget.
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Client
 * @since 3.3.0
 */
final readonly class RetryPolicy
{

    /**
     * Measures elapsed time monotonically so wall-clock changes cannot extend the retry budget.
     *
     * @var Closure():float
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    private Closure $clock;

    /**
     * Waits between attempts; an injected sleeper permits deterministic deadline tests.
     *
     * @var Closure(float):void
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    private Closure $sleep;

    /**
     * Interprets HTTP-date Retry-After values independently from elapsed-time measurement.
     *
     * @var Closure():int
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    private Closure $wallClock;

    /**
     * Bounds attempts and elapsed time in seconds. Unsafe methods require explicit retry opt-in.
     *
     * @param int $maxAttempts
     * @param float $maxElapsed
     * @param float $baseDelay
     * @param float $maxDelay
     * @param bool $retryUnsafe
     * @param list<int> $statuses
     * @param callable():float|null $clock
     * @param callable(float):void|null $sleep
     * @param callable():int|null $wallClock
     *
     * @throws InvalidArgumentException
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function __construct(
        public int $maxAttempts = 3,
        public float $maxElapsed = 5.0,
        public float $baseDelay = 0.1,
        public float $maxDelay = 2.0,
        public bool $retryUnsafe = false,
        public array $statuses = [429, 502, 503, 504],
        ?callable $clock = null,
        ?callable $sleep = null,
        ?callable $wallClock = null
    )
    {
        $validDuration = is_finite($maxElapsed) && $maxElapsed > 0;
        $validDelays = is_finite($baseDelay) && is_finite($maxDelay) && $baseDelay >= 0 && $maxDelay >= $baseDelay;

        if ($maxAttempts < 1 || !$validDuration || !$validDelays) {
            throw new InvalidArgumentException('Retry attempts, duration and delays must define a finite positive budget.');
        }

        if (!array_is_list($statuses)) {
            throw new InvalidArgumentException('Retry statuses must be a list of HTTP status codes.');
        }

        foreach ($statuses as $status) {
            if (!is_int($status) || $status < 100 || $status > 599) {
                throw new InvalidArgumentException('Retry statuses must be integer HTTP status codes.');
            }
        }

        $this->clock = Closure::fromCallable($clock ?? static fn(): float => hrtime(true) / 1_000_000_000);
        $this->sleep = Closure::fromCallable($sleep ?? static function (float $seconds): void {
            usleep((int)($seconds * 1_000_000));
        });
        $this->wallClock = Closure::fromCallable($wallClock ?? time(...));
    }

    /**
     * Each attempt receives its remaining timeout in seconds. Non-replayable bodies receive a single attempt.
     *
     * @param callable(float):ResponseInterface $request
     * @param string $method
     * @param bool $replayable
     *
     * @return ResponseInterface
     * @throws GuzzleException
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    public function execute(
        callable $request,
        string $method,
        bool $replayable = true
    ): ResponseInterface
    {
        $started = ($this->clock)();
        $safe = $this->retryUnsafe || in_array($method, ['GET', 'HEAD', 'OPTIONS', 'PUT', 'DELETE', 'TRACE'], true);

        for ($attempt = 1; ; ++$attempt) {
            $response = null;
            $error = null;

            try {
                $response = $request(max(0.000001, $this->maxElapsed - (($this->clock)() - $started)));
            } catch (GuzzleException $failure) {
                $error = $failure;
                $response = $failure instanceof RequestException ? $failure->getResponse() : null;
            }

            $retryable = $response === null
                ? $error !== null
                : in_array($response->getStatusCode(), $this->statuses, true);
            $retry = $safe && $replayable && $retryable && $attempt < $this->maxAttempts;
            $delay = $this->retryDelay($response, $attempt);

            $remaining = $this->maxElapsed - (($this->clock)() - $started);

            if (!$retry || $delay > $this->maxDelay || $delay >= $remaining) {
                if ($error !== null) {
                    throw $error;
                }

                return $response;
            }

            ($this->sleep)($delay);

            if (($this->clock)() - $started >= $this->maxElapsed) {
                if ($error !== null) {
                    throw $error;
                }

                return $response;
            }
        }
    }

    /**
     * Combines bounded exponential backoff with Retry-After seconds or an HTTP-date.
     *
     * @param ResponseInterface|null $response
     * @param int $attempt
     *
     * @return float
     * @author Bas Milius <bas@mili.us>
     * @since 3.3.0
     */
    private function retryDelay(
        ?ResponseInterface $response,
        int $attempt
    ): float
    {
        $delay = min($this->maxDelay, $this->baseDelay * (2 ** min($attempt - 1, 30)));
        $retryAfter = $response?->getHeaderLine('Retry-After') ?? '';

        if ($retryAfter === '') {
            return $delay;
        }

        $seconds = preg_match('/^[0-9]+$/D', $retryAfter)
            ? (float)$retryAfter
            : max(0, (strtotime($retryAfter) ?: ($this->wallClock)()) - ($this->wallClock)());

        return max($delay, $seconds);
    }

}
