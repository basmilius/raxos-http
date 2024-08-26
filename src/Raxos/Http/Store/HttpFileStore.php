<?php
declare(strict_types=1);

namespace Raxos\Http\Store;

use Raxos\Foundation\Collection\ReadonlyMap;
use Raxos\Http\HttpFile;
use function array_is_list;

/**
 * Class HttpFileStore
 *
 * @extends ReadonlyMap<HttpFile[]>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Store
 * @since 1.1.0
 */
final readonly class HttpFileStore extends ReadonlyMap
{

    /**
     * Returns the file store from globals.
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.1.0
     */
    public static function fromGlobals(): self
    {
        $files = [];

        foreach ($_FILES as $name => $value) {
            if (array_is_list($value)) {
                $files[$name] = [];

                foreach ($value as $file) {
                    $files[$name][] = new HttpFile($file);
                }
            } else {
                $files[$name] = [new HttpFile($value)];
            }
        }

        return new self($files);
    }

}
