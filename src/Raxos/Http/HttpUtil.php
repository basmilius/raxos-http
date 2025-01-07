<?php
declare(strict_types=1);

namespace Raxos\Http;

use function function_exists;
use function getallheaders;
use function Raxos\Foundation\isCommandLineInterface;
use function str_starts_with;
use function strtolower;
use function strtr;
use function substr;

/**
 * Class HttpUtil
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http
 * @since 1.0.0
 */
final class HttpUtil
{

    /**
     * Gets all request headers.
     *
     * @return array<string, string>
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public static function getAllHeaders(): array
    {
        if (isCommandLineInterface()) {
            return [];
        }

        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        } else {
            $headers = [];

            foreach ($_SERVER as $name => $value) {
                if (!str_starts_with($name, 'HTTP_')) {
                    continue;
                }

                $name = substr($name, 5);
                $name = strtr($name, ['_' => '-']);

                $headers[$name] = $value;
            }
        }

        $result = [];

        foreach ($headers as $name => $value) {
            $result[strtolower($name)] = $value;
        }

        return $result;
    }

}
