<?php
declare(strict_types=1);

namespace Raxos\Http;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use JsonSerializable;
use Stringable;
use function array_intersect;
use function array_map;
use function array_search;
use function array_unique;
use function count;
use function ctype_upper;
use function is_array;
use function is_numeric;
use function preg_grep;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function reset;
use function str_starts_with;
use function strlen;
use function strtolower;
use function substr;
use function version_compare;

/**
 * Class UserAgent
 *
 * @author Bas Milius <bas@mili.us>
 * @package Raxos\Http
 * @since 1.0.0
 */
readonly class UserAgent implements JsonSerializable, Stringable
{

    public ?string $browser;
    public ?string $platform;
    public ?string $version;

    /**
     * UserAgent constructor.
     *
     * @param string $userAgent
     *
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public function __construct(protected string $userAgent)
    {
        $platform = null;

        if (preg_match('/\((.*?)\)/m', $userAgent, $parentMatches)) {
            preg_match_all('/(?P<platform>BB\d+;|Android|CrOS|Tizen|iPhone|iPad|iPod|Linux|(Open|Net|Free)BSD|Macintosh|Windows(\ Phone)?|Silk|linux-gnu|BlackBerry|PlayBook|X11|(New\ )?Nintendo\ (WiiU?|3?DS|Switch)|Xbox(\ One)?)(?:\ [^;]*)?(?:;|$)/imx', $parentMatches[1], $result);

            $priority = ['Xbox One', 'Xbox', 'Windows Phone', 'Tizen', 'Android', 'FreeBSD', 'NetBSD', 'OpenBSD', 'CrOS', 'X11'];
            $result['platform'] = array_unique($result['platform']);

            if (count($result['platform']) > 1) {
                if ($keys = array_intersect($priority, $result['platform'])) {
                    $platform = reset($keys);
                } else {
                    $platform = $result['platform'][0];
                }
            } elseif (isset($result['platform'][0])) {
                $platform = $result['platform'][0];
            }
        }

        if ($platform === 'linux-gnu' || $platform === 'X11') {
            $platform = 'Linux';
        } elseif ($platform === 'CrOS') {
            $platform = 'Chrome OS';
        }

        preg_match_all('%(?P<browser>Camino|Kindle(\ Fire)?|Firefox|Iceweasel|IceCat|Safari|MSIE|Trident|AppleWebKit|TizenBrowser|Chrome|Vivaldi|IEMobile|Opera|OPR|Silk|Midori|Edg|Edge|CriOS|UCBrowser|Puffin|SamsungBrowser|Baiduspider|Googlebot|YandexBot|bingbot|Lynx|Version|Wget|curl|Valve\ Steam\ Tenfoot|NintendoBrowser|PLAYSTATION\ (\d|Vita)+)\)?;?(?:[:/ ](?P<version>[\dA-Z.]+)|/[A-Z]*)%ix', $userAgent, $result);

        if (!isset($result['browser'][0], $result['version'][0])) {
            if (preg_match('%^(?!Mozilla)(?P<browser>[A-Z\d\-]+)(/(?P<version>[\dA-Z.]+))?%ix', $userAgent, $result)) {
                $this->browser = $result['browser'];
                $this->platform = $platform;
                $this->version = $result['version'];
            }

            return;
        }

        if (preg_match('/rv:(?P<version>[\dA-Z.]+)/i', $userAgent, $versionResult)) {
            $versionResult = $versionResult['version'];
        }

        $browser = $result['browser'][0];
        $version = $result['version'][0];

        $lowerBrowser = array_map(strtolower(...), $result['browser']);

        $find = static function (string|array $search, int &$key, string &$value = null) use ($lowerBrowser): bool {
            if (!is_array($search)) {
                $search = [$search];
            }

            foreach ($search as $val) {
                $xkey = array_search(strtolower($val), $lowerBrowser, true);

                if ($xkey !== false) {
                    $value = $val;
                    $key = (int)$xkey;

                    return true;
                }
            }

            return false;
        };

        $key = 0;
        $val = '';

        if ($browser === 'Iceweasel' || strtolower($browser) === 'icecat') {
            $browser = 'Firefox';
        } elseif ($find('Playstation Vita', $key)) {
            $browser = 'Browser';
            $platform = 'PlayStation Vita';
        } elseif ($find(['Kindle Fire', 'Silk'], $key, $val)) {
            $browser = $val === 'Silk' ? 'Silk' : 'Kindle';
            $platform = 'Kindle Fire';

            if (!($version = $result['version'][$key]) || !is_numeric($version[0])) {
                $version = $result['version'][array_search('Version', $result['browser'])];
            }
        } elseif ($platform === 'Nintendo 3DS' || $find('NintendoBrowser', $key)) {
            $browser = 'NintendoBrowser';
            $version = $result['version'][$key];
        } elseif ($find('Kindle', $key, $platform)) {
            $browser = $result['browser'][$key];
            $version = $result['version'][$key];
        } elseif ($find('OPR', $key)) {
            $browser = 'Opera Next';
            $version = $result['version'][$key];
        } elseif ($find('Opera', $key, $browser)) {
            $find('Version', $key);
            $version = $result['version'][$key];
        } elseif ($find('Puffin', $key, $browser)) {
            $version = $result['version'][$key];

            if (strlen($version) > 3) {
                $part = substr($version, -2);

                if (ctype_upper($part)) {
                    $version = substr($version, 0, -2);
                    $flags = ['IP' => 'iPhone', 'IT' => 'iPad', 'AP' => 'Android', 'AT' => 'Android', 'WP' => 'Windows Phone', 'WT' => 'Windows'];

                    if (isset($flags[$part])) {
                        $platform = $flags[$part];
                    }
                }
            }
        } elseif ($find(['IEMobile', 'Edg', 'Edge', 'Midori', 'Vivaldi', 'SamsungBrowser', 'Valve Steam Tenfoot', 'Chrome'], $key, $browser)) {
            $version = $result['version'][$key];
        } elseif ($versionResult && $find('Trident', $key)) {
            $browser = 'MSIE';
            $version = $versionResult;
        } elseif ($find('UCBrowser', $key)) {
            $browser = 'UC Browser';
            $version = $result['version'][$key];
        } elseif ($find('CriOS', $key)) {
            $browser = 'Chrome';
            $version = $result['version'][$key];
        } elseif ($browser === 'AppleWebKit') {
            if ($platform === 'Android' && !($key = 0)) {
                $browser = 'Android Browser';
            } elseif (str_starts_with($platform, 'BB')) {
                $browser = 'BlackBerry Browser';
                $platform = 'BlackBerry';
            } elseif ($platform === 'BlackBerry' || $platform === 'PlayBook') {
                $browser = 'BlackBerry Browser';
            } else {
                $find('Safari', $key, $browser) || $find('TizenBrowser', $key, $browser);
            }

            $find('Version', $key);
            $version = $result['version'][$key];
        } elseif ($pKey = preg_grep('/playstation \d/i', array_map(strtolower(...), $result['browser']))) {
            $pKey = reset($pKey);
            $browser = 'NetFront';
            $platform = 'PlayStation ' . preg_replace('/\D/', '', $pKey);
        }

        $this->browser = $browser;
        $this->platform = $platform;
        $this->version = $version;
    }

    /**
     * Returns TRUE if the browser is Google Chrome.
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    #[Pure]
    public final function isChrome(): bool
    {
        return $this->browser === 'Chrome';
    }

    /**
     * Returns TRUE if the browser is Microsoft Edge (Chromium Edition).
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    #[Pure]
    public final function isEdgium(): bool
    {
        return $this->browser === 'Edg';
    }

    /**
     * Returns TRUE if the browser is Mozilla Firefox.
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    #[Pure]
    public final function isFirefox(): bool
    {
        return $this->browser === 'Firefox';
    }

    /**
     * Returns TRUE if the browser is Microsoft Internet Explorer.
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    #[Pure]
    public final function isInternetExplorer(): bool
    {
        return $this->browser === 'MSIE';
    }

    /**
     * Returns TRUE if the browser is Microsoft Edge.
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    #[Pure]
    public final function isMicrosoftEdge(): bool
    {
        return $this->browser === 'Edg' || $this->browser === 'Edge';
    }

    /**
     * Return TRUE if the browser is Apple Safari.
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    #[Pure]
    public final function isSafari(): bool
    {
        return $this->browser === 'Safari';
    }

    /**
     * Returns TRUE if the browser version is at least the given version.
     *
     * @param string $version
     *
     * @return bool
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function versionAtLeast(string $version): bool
    {
        return version_compare($this->version ?? '0.0.0', $version, '>=');
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    #[ArrayShape([
        'user_agent' => "string",
        'browser' => "string|null",
        'platform' => "string|null",
        'version' => "string|null"
    ])]
    public final function jsonSerialize(): array
    {
        return [
            'user_agent' => $this->userAgent,
            'browser' => $this->browser,
            'platform' => $this->platform,
            'version' => $this->version
        ];
    }

    /**
     * {@inheritdoc}
     * @author Bas Milius <bas@mili.us>
     * @since 1.0.0
     */
    public final function __toString(): string
    {
        return $this->userAgent;
    }

}
