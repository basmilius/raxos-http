<?php
declare(strict_types=1);

namespace Raxos\Http\Structure;

use Raxos\Collection\Map;
use Raxos\Http\HttpFile;
use function array_is_list;

/**
 * Class HttpFilesMap
 *
 * @extends Map<string, HttpFile[]>
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http\Structure
 * @since 1.2.0
 */
final class HttpFilesMap extends Map
{

    /**
     * Creates from the global request.
     *
     * @return self
     * @author Bas Milius <bas@mili.us>
     * @since 1.2.0
     */
    public static function createFromGlobals(): self
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
