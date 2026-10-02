<?php
declare(strict_types=1);

use Raxos\Http\HttpFile;

covers(HttpFile::class);

it('retains upload metadata and marks every error as invalid', function (int $error): void {
    $file = new HttpFile(['name' => 'unit.txt', 'type' => 'text/plain', 'tmp_name' => '/tmp/unit', 'size' => 0, 'error' => $error]);
    expect($file->isValid)->toBe($error === UPLOAD_ERR_OK)->and($file->__debugInfo())
        ->toBe(['content_type' => 'text/plain', 'name' => 'unit.txt', 'size' => 0, 'temporary_file' => '/tmp/unit']);
})->with([UPLOAD_ERR_OK, UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE, UPLOAD_ERR_PARTIAL, UPLOAD_ERR_NO_FILE, UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE, UPLOAD_ERR_EXTENSION]);
