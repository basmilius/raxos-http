<?php
declare(strict_types=1);

namespace Raxos\Http\Contract;

/**
 * Interface HttpSendFileInterface
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Contract
 * @since 1.7.0
 */
interface HttpSendFileInterface
{

    /**
     * Returns the bytes read per iteration.
     *
     * @var int
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public int $bytes {
        get;
    }

    /**
     * Returns the content disposition filename.
     *
     * @var string
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public string $contentDisposition {
        get;
    }

    /**
     * Returns the content disposition type.
     *
     * @var string
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public string $contentDispositionType {
        get;
    }

    /**
     * Returns the content type.
     *
     * @var string
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public string $contentType {
        get;
    }

    /**
     * Returns the file path.
     *
     * @var string
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public string $path {
        get;
    }

    /**
     * Returns the number of seconds to throttle.
     *
     * @var float
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public float $throttle {
        get;
    }

    /**
     * Sets the number of bytes sent per iteration.
     *
     * @param int $bytes
     *
     * @return $this
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function setBytes(int $bytes): static;

    /**
     * Sets the content disposition.
     *
     * @param string $name
     * @param string $type
     *
     * @return $this
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function setContentDisposition(string $name, string $type): static;

    /**
     * Sets the content type.
     *
     * @param string $contentType
     *
     * @return $this
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function setContentType(string $contentType): static;

    /**
     * Sets the amount of time to throttle.
     *
     * @param float $throttle
     *
     * @return $this
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function setThrottle(float $throttle): static;

    /**
     * Handles the file sending.
     *
     * @param string|null $rangeHeader
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.7.0
     */
    public function handle(?string $rangeHeader): void;

}
