<?php
declare(strict_types=1);

namespace Raxos\Http\Response;

use Raxos\Http\{HttpHeader, HttpRequest, HttpResponse, HttpResponseCode};
use Raxos\Http\Structure\HttpHeadersMap;
use RuntimeException;
use function filemtime;
use function gmdate;
use function is_file;
use function md5_file;
use function mime_content_type;
use function readfile;
use function strtotime;

/**
 * Class FileHttpResponse
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Response
 * @since 2.1.0
 */
final class FileHttpResponse extends HttpResponse
{

    /**
     * FileHttpResponse constructor.
     *
     * @param string $path
     * @param HttpRequest $request
     * @param HttpHeadersMap $headers
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.1.0
     */
    public function __construct(
        public string $path,
        public HttpRequest $request,
        HttpHeadersMap $headers = new HttpHeadersMap()
    )
    {
        parent::__construct($headers);
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.1.0
     */
    public function send(): void
    {
        if (!is_file($this->path)) {
            throw new RuntimeException("File '{$this->path}' does not exist.");
        }

        if (!$this->headers->has(HttpHeader::CONTENT_TYPE)) {
            $this->headers->add(HttpHeader::CONTENT_TYPE, mime_content_type($this->path));
        }

        $etag = md5_file($this->path);
        $modified = filemtime($this->path);

        $etagMatches = $this->request->headers->get(HttpHeader::IF_NONE_MATCH) === $etag;
        $modifiedMatches = $this->request->headers->get(HttpHeader::IF_MODIFIED_SINCE) === $modified;

        if ($etagMatches || $modifiedMatches) {
            $this->responseCode = HttpResponseCode::NOT_MODIFIED;
        } else {
            $this->responseCode = HttpResponseCode::OK;
        }

        $this->headers->add(HttpHeader::CACHE_CONTROL, 'public, max-age=31536000');
        $this->headers->add(HttpHeader::ETAG, $etag);
        $this->headers->add(HttpHeader::EXPIRES, gmdate('D, d M Y H:i:s \G\M\T', strtotime('+ 1 year')));
        $this->headers->add(HttpHeader::LAST_MODIFIED, gmdate('D, d M Y H:i:s \G\M\T', $modified));
        $this->headers->add(HttpHeader::PRAGMA, 'cache');

        parent::send();
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 2.1.0
     */
    protected function sendBody(): void
    {
        if ($this->responseCode !== HttpResponseCode::OK) {
            return;
        }

        readfile($this->path);
    }

}
