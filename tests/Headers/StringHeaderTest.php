<?php

declare(strict_types=1);

namespace Thesis\Headers;

use Testo\Assert;
use Testo\Codecov\Covers;
use Testo\Test;

#[Test]
#[Covers(StringHeader::class)]
final class StringHeaderTest
{
    public function encodesStringValueAsIs(): void
    {
        $header = new StringHeader('X-Name');

        Assert::same($header->encode('value'), 'value');
    }

    public function decodesStringValueAsIs(): void
    {
        $header = new StringHeader('X-Name');

        Assert::same($header->decode('value'), 'value');
    }

    public function decodesEmptyStringValue(): void
    {
        $header = new StringHeader('X-Name');

        Assert::same($header->decode(''), '');
    }
}
