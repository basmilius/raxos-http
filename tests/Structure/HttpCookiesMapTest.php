<?php
declare(strict_types=1);

use Raxos\Http\Structure\HttpCookiesMap;

covers(HttpCookiesMap::class);

it('captures globals without retaining references to later mutations', function (): void {
    $previous = $GLOBALS['_COOKIE'];
    try {
        $GLOBALS['_COOKIE'] = ['session' => 'value', 'zero' => '0'];
        $map = HttpCookiesMap::createFromGlobals();
        $GLOBALS['_COOKIE'] = [];
        expect($map->toArray())->toBe(['session' => 'value', 'zero' => '0']);
    } finally {
        $GLOBALS['_COOKIE'] = $previous;
    }
});
