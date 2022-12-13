<?php
declare(strict_types=1);

namespace Raxos\Http;

/**
 * Interface HttpSendFileInterface
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http
 * @since 1.0.2
 */
interface HttpSendFileInterface
{

    /**
     * Gets the bytes read per iteration.
     *
     * @return int
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.2
     */
    public function getBytes(): int;

    /**
     * Gets the content disposition filename.
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.2
     */
    public function getContentDisposition(): string;

    /**
     * Gets the content disposition type.
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.2
     */
    public function getContentDispositionType(): string;

    /**
     * Gets the content type.
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.2
     */
    public function getContentType(): string;

    /**
     * Gets the file path.
     *
     * @return string
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.2
     */
    public function getPath(): string;

    /**
     * Gets the amount of seconds to throttle.
     *
     * @return float
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.2
     */
    public function getThrottle(): float;

    /**
     * Sets the amount of bytes sent per iteration.
     *
     * @param int $bytes
     *
     * @return $this
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.2
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
     * @since 1.0.2
     */
    public function setContentDisposition(string $name, string $type): static;

    /**
     * Sets the content type.
     *
     * @param string $contentType
     *
     * @return $this
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.2
     */
    public function setContentType(string $contentType): static;

    /**
     * Sets the amount of time to throttle.
     *
     * @param float $throttle
     *
     * @return $this
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.2
     */
    public function setThrottle(float $throttle): static;

    /**
     * Handles the file sending.
     *
     * @param string|null $rangeHeader
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.2
     */
    public function handle(?string $rangeHeader): void;

}
