<?php
declare(strict_types=1);

use Raxos\Http\HttpSendFile;

$workspace = dirname(__DIR__, 3) . '/vendor/autoload.php';
require is_file($workspace) ? $workspace : dirname(__DIR__, 2) . '/vendor/autoload.php';

$sender = new HttpSendFile($argv[1], bytes: 7);
$sender->handle($argv[2] === 'none' ? null : $argv[2]);
fwrite(STDERR, (string)(http_response_code() ?: 200));
