<?php
declare(strict_types=1);

namespace Raxos\Http;

use function connection_aborted;
use function explode;
use function fclose;
use function feof;
use function filesize;
use function flush;
use function fopen;
use function fread;
use function fseek;
use function header;
use function http_response_code;
use function intval;
use function ob_end_clean;
use function ob_get_level;
use function strlen;
use function usleep;

/**
 * Class HttpSendFile
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http
 * @since 1.0.0
 */
class HttpSendFile implements HttpSendFileInterface
{

    /**
     * HttpSendFile constructor.
     *
     * @param string $path
     * @param string $contentDisposition
     * @param string $contentDispositionType
     * @param string $contentType
     * @param int $bytes
     * @param float $throttle
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(
        protected string $path,
        protected string $contentDisposition = 'file',
        protected string $contentDispositionType = 'inline',
        protected string $contentType = 'application/octet-stream',
        protected int $bytes = 40960,
        protected float $throttle = 0.1
    )
    {
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getBytes(): int
    {
        return $this->bytes;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getContentDisposition(): string
    {
        return $this->contentDisposition;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getContentDispositionType(): string
    {
        return $this->contentDispositionType;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getPath(): string
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function getThrottle(): float
    {
        return $this->throttle;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function setBytes(int $bytes): static
    {
        $this->bytes = $bytes;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function setContentDisposition(string $name, string $type): static
    {
        $this->contentDisposition = $name;
        $this->contentDispositionType = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function setContentType(string $contentType): static
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function setThrottle(float $throttle): static
    {
        $this->throttle = $throttle;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function handle(?string $rangeHeader): void
    {
        while (ob_get_level()) {
            ob_end_clean();
        }

        $size = filesize($this->path);

        header('Accept-Ranges: bytes');
        header("Content-Disposition: {$this->contentDispositionType}; filename=\"{$this->contentDisposition}\"");
        header("Content-Type: {$this->contentType}");

        header('Cache-control: private');
        header('Pragma: private');
        header('Expires: Wed, 13 Mar 1996 06:00:00 GMT');

        if ($rangeHeader !== null) {
            [, $range] = explode('=', $rangeHeader);
            [$range] = explode(',', $range, 2);
            [$range, $rangeEnd] = explode('-', $range);

            $range = intval($range);
            $rangeEnd = intval($rangeEnd);
            $newLength = $rangeEnd - $range + 1;

            http_response_code(HttpResponseCode::PARTIAL_CONTENT->value);
            header("Content-Length: {$newLength}");
            header("Content-Range: bytes {$range}-{$rangeEnd}/{$size}");
        } else {
            $newLength = $size;
            header("Content-Length: {$size}");
        }

        $bytesSend = 0;
        $handle = fopen($this->path, 'rb');

        if ($rangeHeader !== null) {
            fseek($handle, $range);
        }

        while (!feof($handle) && !connection_aborted() && $bytesSend < $newLength) {
            $buffer = fread($handle, $this->bytes);

            echo $buffer;

            flush();
            usleep($this->throttle * 1000000);

            $bytesSend += strlen($buffer);
        }

        fclose($handle);
    }

}
