<?php
declare(strict_types=1);

use Raxos\Http\Structure\HttpFilesMap;

covers(HttpFilesMap::class);

it('normalizes single files and lists of upload records', function (): void {
    $previous = $_FILES;
    $file = ['name' => 'unit.txt', 'type' => 'text/plain', 'tmp_name' => '/tmp/unit', 'size' => 12, 'error' => UPLOAD_ERR_OK];
    try {
        $_FILES = ['single' => $file, 'list' => [$file, [...$file, 'name' => 'second.txt']]];
        $files = HttpFilesMap::createFromGlobals();
        expect($files->get('single')[0]->name)->toBe('unit.txt')->and(array_column($files->get('list'), 'name'))->toBe(['unit.txt', 'second.txt']);
    } finally {
        $_FILES = $previous;
    }
});

it('normalizes PHPs parallel multipart arrays and preserves nested field keys', function (): void {
    $previous = $_FILES;
    try {
        $_FILES = ['photos' => ['name' => ['first' => 'a.txt', 'group' => ['b.txt']], 'type' => ['first' => 'text/plain', 'group' => ['text/plain']], 'tmp_name' => ['first' => '/tmp/a', 'group' => ['/tmp/b']], 'size' => ['first' => 2, 'group' => [3]], 'error' => ['first' => 0, 'group' => [0]]]];
        $files = HttpFilesMap::createFromGlobals()->get('photos');
        expect($files['first']->name)->toBe('a.txt')->and($files['first']->size)->toBe(2)
            ->and($files['group'][0]->name)->toBe('b.txt')->and($files['group'][0]->size)->toBe(3);
    } finally {
        $_FILES = $previous;
    }
});
