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
            } elseif (is_array($value['name'])) {
                $files[$name] = self::createFile($value);
            } else {
                $files[$name] = [self::createFile($value)];
            }
        }

        return new self($files);
    }

    /**
     * Converts parallel multipart fields into upload objects.
     *
     * @param array $file
     * @return HttpFile|array
     * @author Bas Milius <bas@mili.us>
     * @since 3.2.0
     */
    private static function createFile(array $file): HttpFile|array
    {
        if (!is_array($file['name'])) {
            return new HttpFile($file);
        }

        $files = [];

        foreach ($file['name'] as $key => $name) {
            $files[$key] = self::createFile(array_map(static fn(mixed $field): mixed => is_array($field) ? $field[$key] : $field, $file));
        }

        return $files;
    }
}
