<?php
declare(strict_types=1);

use Raxos\Http\Structure\HttpHeadersMap;

covers(HttpHeadersMap::class);

it('normalizes every mutation and supports first-value, all-values and default access', function (): void {
    $headers = new HttpHeadersMap(['X-Values' => ['0', 'second'], 'Empty' => []]);
    expect($headers->get('x-values'))->toBe('0')->and($headers->getAll('X-VALUES'))->toBe(['0', 'second'])
        ->and($headers->get('empty', 'default'))->toBe('default')->and($headers->getAll('missing'))->toBe([]);
    $headers->add('X-VALUES', 'third');
    expect($headers->getAll('x-values'))->toBe(['0', 'second', 'third']);
    $headers->set('X-VALUES', 'new');
    expect($headers->getAll('x-values'))->toBe(['new']);
    $headers->unset('X-VaLuEs');
    expect($headers->has('x-values'))->toBeFalse()->and($headers->get('x-values', false))->toBeFalse();
    expect(HttpHeadersMap::createFromGlobals()->toArray())->toBe([]);
});
