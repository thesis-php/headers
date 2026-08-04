<?php

declare(strict_types=1);

namespace Thesis\Headers;

use Testo\Assert;
use Testo\Codecov\Covers;
use Testo\Expect;
use Testo\Test;

#[Test]
#[Covers(BoolHeader::class)]
final class BoolHeaderTest
{
    public function encodesTrueAsOne(): void
    {
        $header = new BoolHeader('X-Flag');

        Assert::same($header->encode(true), '1');
    }

    public function encodesFalseAsZero(): void
    {
        $header = new BoolHeader('X-Flag');

        Assert::same($header->encode(false), '0');
    }

    public function decodesOneAsTrue(): void
    {
        $header = new BoolHeader('X-Flag');

        Assert::same($header->decode('1'), true);
    }

    public function decodesZeroAsFalse(): void
    {
        $header = new BoolHeader('X-Flag');

        Assert::same($header->decode('0'), false);
    }

    public function rejectsMalformedBoolean(): void
    {
        Expect::exception(CannotDecodeHeader::class)
            ->withMessage('Header "X-Flag" has malformed value "true", expected a boolean.');

        new BoolHeader('X-Flag')->decode('true');
    }
}
