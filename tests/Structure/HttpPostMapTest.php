<?php
declare(strict_types=1);

use Raxos\Http\Structure\HttpPostMap;

covers(HttpPostMap::class);

it('captures globals without retaining references to later mutations', function (): void {
    $previous = $GLOBALS['_POST'];
    try {
        $GLOBALS['_POST'] = ['zero' => '0', 'empty' => '', 'nested' => ['a' => 'b']];
        $map = HttpPostMap::createFromGlobals();
        $GLOBALS['_POST'] = [];
        expect($map->toArray())->toBe(['zero' => '0', 'empty' => '', 'nested' => ['a' => 'b']]);
    } finally {
        $GLOBALS['_POST'] = $previous;
    }
});
