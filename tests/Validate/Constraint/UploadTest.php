<?php
declare(strict_types=1);

use Raxos\Http\HttpFile;
use Raxos\Http\Validate\Constraint\Upload;
use Raxos\Http\Validate\Error\UploadConstraintException;
use function RaxosTests\Http\constraintProperty;

covers(Upload::class);

it('preserves valid uploads and rejects errored uploads or ordinary values', function (): void {
    $input = ['name' => 'unit.txt', 'type' => 'text/plain', 'tmp_name' => '/tmp/unit.txt', 'size' => 12, 'error' => UPLOAD_ERR_OK];
    $file = new HttpFile($input);
    expect(new Upload()->check(constraintProperty(), $file))->toBe($file);
    $input['error'] = UPLOAD_ERR_NO_FILE;
    expect(fn() => new Upload()->check(constraintProperty(), new HttpFile($input)))->toThrow(UploadConstraintException::class);
    expect(fn() => new Upload()->check(constraintProperty(), []))->toThrow(UploadConstraintException::class);
});
