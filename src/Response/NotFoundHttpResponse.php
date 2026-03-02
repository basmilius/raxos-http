<?php
declare(strict_types=1);

namespace Raxos\Http\Response;

use Raxos\Http\{HttpResponse, HttpResponseCode};

/**
 * Class NotFoundHttpResponse
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Response
 * @since 2.1.0
 */
final class NotFoundHttpResponse extends HttpResponse
{

    /**
     * NotFoundHttpResponse constructor.
     *
     * @author Bas Milius <bas@mili.us>
     * @since 2.1.0
     */
    public function __construct()
    {
        parent::__construct(responseCode: HttpResponseCode::NOT_FOUND);
    }

}
