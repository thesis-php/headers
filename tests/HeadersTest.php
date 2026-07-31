<?php

declare(strict_types=1);

namespace Thesis;

use Testo\Assert;
use Testo\Codecov\Covers;
use Testo\Expect;
use Testo\Test;
use Thesis\Headers\Header;
use Thesis\Headers\NoHeader;

#[Test]
#[Covers(Headers::class)]
final class HeadersTest
{
    public function getsAndMemoizesDecodedRawHeader(): void
    {
        $header = new CountingHeader('X-Test');
        $headers = new Headers(['X-Test' => 'raw']);

        Assert::same($headers->get($header), 'decoded:raw');
        Assert::same($headers->get($header), 'decoded:raw');
        Assert::same($header->decodeCalls, 1);
    }

    public function findReturnsNullForMissingHeader(): void
    {
        $headers = new Headers();

        Assert::null($headers->find(new CountingHeader('X-Missing')));
    }

    public function getThrowsWhenHeaderIsMissing(): void
    {
        Expect::exception(NoHeader::class)
            ->withMessageContaining('No header "X-Missing"');

        new Headers()->get(new CountingHeader('X-Missing'));
    }

    public function withReturnsCopyThatEncodesInsertedValue(): void
    {
        $header = new CountingHeader('X-Test');
        $headers = new Headers();

        $copy = $headers->with($header, 'value');

        Assert::false($headers->has($header));
        Assert::true($copy->has($header));
        Assert::same($copy->get($header), 'value');
        Assert::same($copy->encode(), ['X-Test' => 'encoded:value']);
    }

    public function withDefaultUsesDefaultOnlyWhenHeaderIsMissing(): void
    {
        $header = new CountingHeader('X-Test');
        $headers = new Headers(['X-Test' => 'raw']);
        $calls = 0;

        $sameHeaders = $headers->withDefault($header, static function () use (&$calls): string {
            ++$calls;

            return 'default';
        });
        $withDefault = new Headers()->withDefault($header, static function () use (&$calls): string {
            ++$calls;

            return 'default';
        });

        Assert::same($sameHeaders, $headers);
        Assert::same($withDefault->get($header), 'default');
        Assert::same($calls, 1);
    }

    public function withMergedPrefersHeadersFromRightSide(): void
    {
        $header = new CountingHeader('X-Test');
        $base = new Headers(['X-Test' => 'left']);
        $override = new Headers(['X-Test' => 'right']);

        $merged = $base->withMerged($override);

        Assert::same($merged->get($header), 'decoded:right');
    }

    public function withoutReturnsCopyWithoutSelectedHeaders(): void
    {
        $first = new CountingHeader('X-First');
        $second = new CountingHeader('X-Second');
        $headers = new Headers(['X-First' => 'one', 'X-Second' => 'two']);

        $copy = $headers->without($first);

        Assert::true($headers->has($first));
        Assert::false($copy->has($first));
        Assert::true($copy->has($second));
        Assert::same($copy->encode(), ['X-Second' => 'two']);
    }
}

/**
 * @implements Header<string>
 */
final class CountingHeader implements Header
{
    public int $decodeCalls = 0;

    public function __construct(
        public string $name,
    ) {}

    public function encode(mixed $value): string
    {
        return 'encoded:' . $value;
    }

    public function decode(string $encoded): mixed
    {
        ++$this->decodeCalls;

        return 'decoded:' . $encoded;
    }
}
