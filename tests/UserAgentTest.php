<?php
declare(strict_types=1);

use Raxos\Http\UserAgent;

covers(UserAgent::class);

it('identifies common browsers and platforms without losing the original header', function (string $raw, ?string $browser, ?string $platform, ?string $version): void {
    $agent = new UserAgent($raw);
    expect($agent->browser)->toBe($browser)->and($agent->platform)->toBe($platform)->and($agent->version)->toBe($version)
        ->and((string)$agent)->toBe($raw)->and($agent->jsonSerialize())->toBe(['user_agent' => $raw, 'browser' => $browser, 'platform' => $platform, 'version' => $version])
        ->and($agent->isChrome())->toBe($browser === 'Chrome')->and($agent->isFirefox())->toBe($browser === 'Firefox')
        ->and($agent->isSafari())->toBe($browser === 'Safari')->and($agent->isEdgium())->toBe($browser === 'Edg')
        ->and($agent->isMicrosoftEdge())->toBe(in_array($browser, ['Edg', 'Edge'], true))->and($agent->isInternetExplorer())->toBe($browser === 'MSIE');
})->with([
    ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36', 'Chrome', 'Windows', '120.0.0.0'],
    ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 Version/17.0 Safari/605.1.15', 'Safari', 'Macintosh', '17.0'],
    ['Mozilla/5.0 (X11; Linux x86_64; rv:122.0) Gecko/20100101 Firefox/122.0', 'Firefox', 'Linux', '122.0'],
    ['Mozilla/5.0 (Windows NT 10.0) Chrome/120.0 Safari/537.36 Edg/120.1', 'Edg', 'Windows', '120.1'],
    ['Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko', 'MSIE', 'Windows', '11.0'],
    ['Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 CriOS/120.0 Safari/604.1', 'Chrome', 'iPhone', '120.0'],
    ['Mozilla/5.0 (X11; CrOS x86_64 1) Chrome/120.0', 'Chrome', 'Chrome OS', '120.0'],
    ['Iceweasel/10.0', 'Firefox', null, '10.0'],
    ['curl/8.5.0', 'curl', null, '8.5.0'],
    ['UnitAgent/1.2', 'UnitAgent', null, '1.2'],
    ['', null, null, null],
    ['Mozilla/5.0', null, null, null],
    ['Mozilla/5.0 AppleWebKit/537.36 Version/17.0 Safari/537.36', 'Safari', null, '17.0'],
]);

it('compares dotted versions numerically and treats unknown versions as zero', function (): void {
    $agent = new UserAgent('Chrome/120.10');
    expect($agent->versionAtLeast('120.9'))->toBeTrue()->and($agent->versionAtLeast('120.10'))->toBeTrue()
        ->and($agent->versionAtLeast('121'))->toBeFalse()->and(new UserAgent('')->versionAtLeast('1'))->toBeFalse();
});
