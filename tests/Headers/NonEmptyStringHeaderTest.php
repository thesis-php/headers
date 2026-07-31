<?php

declare(strict_types=1);

namespace Thesis\Headers;

use Testo\Assert;
use Testo\Codecov\Covers;
use Testo\Expect;
use Testo\Test;

#[Test]
#[Covers(NonEmptyStringHeader::class)]
final class NonEmptyStringHeaderTest
{
    public function encodesStringValueAsIs(): void
    {
        $header = new NonEmptyStringHeader('X-Name');

        Assert::same($header->encode('value'), 'value');
    }

    public function decodesNonEmptyStringValue(): void
    {
        $header = new NonEmptyStringHeader('X-Name');

        Assert::same($header->decode('value'), 'value');
    }

    public function rejectsEmptyString(): void
    {
        Expect::exception(CannotDecodeHeader::class)
            ->withMessage('Header "X-Name" has an empty value, expected a non-empty string.');

        new NonEmptyStringHeader('X-Name')->decode('');
    }
}
