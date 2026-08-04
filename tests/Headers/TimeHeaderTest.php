<?php

declare(strict_types=1);

namespace Thesis\Headers;

use Testo\Assert;
use Testo\Codecov\Covers;
use Testo\Expect;
use Testo\Test;

#[Test]
#[Covers(TimeHeader::class)]
final class TimeHeaderTest
{
    public function encodesDateTimeWithDefaultFormat(): void
    {
        $header = new TimeHeader('X-Time');
        $value = new \DateTimeImmutable('2026-07-31 12:34:56.123456+03:00');

        Assert::same($header->encode($value), '2026-07-31T12:34:56.123456+03:00');
    }

    public function decodesDateTimeWithDefaultFormat(): void
    {
        $header = new TimeHeader('X-Time');

        $value = $header->decode('2026-07-31T12:34:56.123456+03:00');

        Assert::instanceOf($value, \DateTimeImmutable::class);
        Assert::same($value->format('Y-m-d\TH:i:s.uP'), '2026-07-31T12:34:56.123456+03:00');
    }

    public function rejectsMalformedDateTime(): void
    {
        Expect::exception(CannotDecodeHeader::class)
            ->withMessage('Header "X-Time" has malformed value "not-a-date", expected format "Y-m-d\TH:i:s.uP".');

        new TimeHeader('X-Time')->decode('not-a-date');
    }
}
