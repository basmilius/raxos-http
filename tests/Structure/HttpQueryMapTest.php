<?php
declare(strict_types=1);

use Raxos\Http\Structure\HttpQueryMap;

covers(HttpQueryMap::class);

it('parses escaped, nested, repeated and empty query values', function (): void {
    expect(HttpQueryMap::createFromString('name=h%C3%A9llo+world&zero=0&empty=&ids[]=1&ids[]=2&last=a&last=b')->toArray())
        ->toBe(['name' => 'héllo world', 'zero' => '0', 'empty' => '', 'ids' => ['1', '2'], 'last' => 'b']);
    expect(HttpQueryMap::createFromString('')->toArray())->toBe([]);
});

it('reads only the globals query string', function (): void {
    $previous = $_SERVER;
    try {
        $_SERVER = ['QUERY_STRING' => 'unit=1'];
        expect(HttpQueryMap::createFromGlobals()->get('unit'))->toBe('1');
        $_SERVER = [];
        expect(HttpQueryMap::createFromGlobals()->toArray())->toBe([]);
    } finally {
        $_SERVER = $previous;
    }
});
