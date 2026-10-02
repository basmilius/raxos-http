<?php
declare(strict_types=1);

use Raxos\Http\Structure\HttpServerMap;

covers(HttpServerMap::class);

it('captures globals without retaining references to later mutations', function (): void {
    $previous = $GLOBALS['_SERVER'];
    try {
        $GLOBALS['_SERVER'] = ['REQUEST_METHOD' => 'POST', 'HTTPS' => 'on'];
        $map = HttpServerMap::createFromGlobals();
        $GLOBALS['_SERVER'] = [];
        expect($map->toArray())->toBe(['REQUEST_METHOD' => 'POST', 'HTTPS' => 'on']);
    } finally {
        $GLOBALS['_SERVER'] = $previous;
    }
});
