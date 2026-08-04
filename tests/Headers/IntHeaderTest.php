<?php

declare(strict_types=1);

namespace Thesis\Headers;

use Testo\Assert;
use Testo\Codecov\Covers;
use Testo\Expect;
use Testo\Test;

#[Test]
#[Covers(IntHeader::class)]
final class IntHeaderTest
{
    public function encodesIntValueAsString(): void
    {
        $header = new IntHeader('X-Count');

        Assert::same($header->encode(42), '42');
    }

    public function decodesIntValue(): void
    {
        $header = new IntHeader('X-Count');

        Assert::same($header->decode('-42'), -42);
    }

    public function decodesZeroValue(): void
    {
        $header = new IntHeader('X-Count');

        Assert::same($header->decode('0'), 0);
    }

    public function rejectsMalformedInteger(): void
    {
        Expect::exception(CannotDecodeHeader::class)
            ->withMessage('Header "X-Count" has malformed value "42abc", expected an integer.');

        new IntHeader('X-Count')->decode('42abc');
    }

    public function decodesPositiveInt(): void
    {
        $header = IntHeader::positive('X-Count');

        Assert::same($header->decode('1'), 1);
    }

    public function rejectsZeroForPositiveInt(): void
    {
        Expect::exception(CannotDecodeHeader::class)
            ->withMessage('Header "X-Count" has value "0" less than minimum 1.');

        IntHeader::positive('X-Count')->decode('0');
    }

    public function decodesNonNegativeInt(): void
    {
        $header = IntHeader::nonNegative('X-Count');

        Assert::same($header->decode('0'), 0);
    }

    public function rejectsNegativeForNonNegativeInt(): void
    {
        Expect::exception(CannotDecodeHeader::class)
            ->withMessage('Header "X-Count" has value "-1" less than minimum 0.');

        IntHeader::nonNegative('X-Count')->decode('-1');
    }

    public function decodesNonPositiveInt(): void
    {
        $header = IntHeader::nonPositive('X-Count');

        Assert::same($header->decode('0'), 0);
    }

    public function rejectsPositiveForNonPositiveInt(): void
    {
        Expect::exception(CannotDecodeHeader::class)
            ->withMessage('Header "X-Count" has value "1" greater than maximum 0.');

        IntHeader::nonPositive('X-Count')->decode('1');
    }

    public function decodesNegativeInt(): void
    {
        $header = IntHeader::negative('X-Count');

        Assert::same($header->decode('-1'), -1);
    }

    public function rejectsZeroForNegativeInt(): void
    {
        Expect::exception(CannotDecodeHeader::class)
            ->withMessage('Header "X-Count" has value "0" greater than maximum -1.');

        IntHeader::negative('X-Count')->decode('0');
    }

    public function rejectsInvalidRange(): void
    {
        Expect::exception(\InvalidArgumentException::class)
            ->withMessage('Header "X-Count" minimum value 10 is greater than maximum value 5.');

        new IntHeader('X-Count', min: 10, max: 5);
    }
}
