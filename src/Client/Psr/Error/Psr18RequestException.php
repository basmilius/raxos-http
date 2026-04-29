<?php
declare(strict_types=1);

namespace Raxos\Http\Client\Psr\Error;

use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use RuntimeException;
use Throwable;

/**
 * Class Psr18RequestException
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Client\Psr\Error
 * @since 2.0.0
 */
final class Psr18RequestException extends RuntimeException implements RequestExceptionInterface
{

    /**
     * Psr18RequestException constructor.
     *
     * @param RequestInterface $request
     * @param string $message
     * @param Throwable|null $previous
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function __construct(
        private readonly RequestInterface $request,
        string $message = '',
        ?Throwable $previous = null
    )
    {
        parent::__construct($message, 0, $previous);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.0.0
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

}
