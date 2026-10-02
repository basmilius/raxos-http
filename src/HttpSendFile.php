<?php
declare(strict_types=1);

namespace Raxos\Http;

use Raxos\Contract\Http\HttpSendFileInterface;
use Raxos\Error\InvalidArgumentException;
use RuntimeException;
use function connection_aborted;
use function fclose;
use function feof;
use function flush;
use function fopen;
use function fread;
use function fseek;
use function fstat;
use function header;
use function http_response_code;
use function is_finite;
use function max;
use function min;
use function ob_end_clean;
use function ob_get_level;
use function preg_match;
use function round;
use function strlen;
use function trim;
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
        public protected(set) float $throttle = 0.0
    )
    {
        $this->setBytes($bytes);
        $this->setThrottle($throttle);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function setBytes(int $bytes): self
    {
        if ($bytes < 1) {
            throw new InvalidArgumentException('Chunk size must be positive.');
        }

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
        if (!is_finite($throttle) || $throttle < 0) {
            throw new InvalidArgumentException('Throttle must be finite and nonnegative.');
        }

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

        $handle = fopen($this->path, 'rb');

        if ($handle === false) {
            throw new RuntimeException('Unable to open response file.');
        }

        try {
            $size = fstat($handle)['size'];
            $range = 0;
            $rangeEnd = $size - 1;

            header('Accept-Ranges: bytes');
            header("Content-Disposition: {$this->contentDispositionType}; filename=\"{$this->contentDisposition}\"");
            header("Content-Type: {$this->contentType}");

            header('Cache-control: private');
            header('Pragma: private');
            header('Expires: Wed, 13 Mar 1996 06:00:00 GMT');

            if ($rangeHeader !== null) {
                if (!preg_match('/^bytes=(\d*)-(\d*)$/D', trim($rangeHeader), $matches) || ($matches[1] === '' && $matches[2] === '') || $size === 0) {
                    http_response_code(HttpResponseCode::RANGE_NOT_SATISFIABLE->value);
                    header("Content-Range: bytes */{$size}");
                    return;
                }

                if ($matches[1] === '') {
                    $suffix = (int)$matches[2];
                    $range = max(0, $size - $suffix);
                } else {
                    $range = (int)$matches[1];
                    $rangeEnd = $matches[2] === '' ? $size - 1 : min($size - 1, (int)$matches[2]);
                }

                if ($range >= $size || $range > $rangeEnd) {
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

            if ($rangeHeader !== null) {
                fseek($handle, $range);
            }

            while (!feof($handle) && !connection_aborted() && $bytesSend < $newLength) {
                $buffer = fread($handle, min($this->bytes, $newLength - $bytesSend));

                if ($buffer === false || $buffer === '') {
                    break;
                }

                echo $buffer;

                flush();
                if ($this->throttle > 0) {
                    usleep((int)round($this->throttle * 1000000));
                }

                $bytesSend += strlen($buffer);
            }

        } finally {
            fclose($handle);
        }
    }

}
