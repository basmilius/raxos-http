<?php
declare(strict_types=1);

namespace Raxos\Http;

use Raxos\Contract\Http\HttpSendFileInterface;
use function connection_aborted;
use function count;
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
final class HttpSendFile implements HttpSendFileInterface
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
        public protected(set) string $path,
        public protected(set) string $contentDisposition = 'file',
        public protected(set) string $contentDispositionType = 'inline',
        public protected(set) string $contentType = 'application/octet-stream',
        public protected(set) int $bytes = 40960,
        public protected(set) float $throttle = 0.1
    ) {}

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function setBytes(int $bytes): self
    {
        $this->bytes = $bytes;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function setContentDisposition(string $name, string $type): self
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
    public final function setContentType(string $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function setThrottle(float $throttle): self
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
            $parts = explode('=', $rangeHeader, 2);

            if (count($parts) !== 2 || $parts[0] !== 'bytes') {
                http_response_code(HttpResponseCode::RANGE_NOT_SATISFIABLE->value);
                header("Content-Range: bytes */{$size}");
                return;
            }

            [$rangePart] = explode(',', $parts[1], 2);
            $rangeParts = explode('-', $rangePart, 2);

            if (count($rangeParts) !== 2) {
                http_response_code(HttpResponseCode::RANGE_NOT_SATISFIABLE->value);
                header("Content-Range: bytes */{$size}");
                return;
            }

            $range = $rangeParts[0] === '' ? 0 : (int)$rangeParts[0];
            $rangeEnd = $rangeParts[1] === '' ? $size - 1 : (int)$rangeParts[1];

            if ($range < 0 || $rangeEnd >= $size || $range > $rangeEnd) {
                http_response_code(HttpResponseCode::RANGE_NOT_SATISFIABLE->value);
                header("Content-Range: bytes */{$size}");
                return;
            }

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

        if ($handle === false) {
            return;
        }

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
