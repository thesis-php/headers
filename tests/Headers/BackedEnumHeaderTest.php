<?php

declare(strict_types=1);

namespace Thesis\Headers;

use Testo\Assert;
use Testo\Codecov\Covers;
use Testo\Expect;
use Testo\Test;

#[Test]
#[Covers(BackedEnumHeader::class)]
final class BackedEnumHeaderTest
{
    public function encodesStringBackedEnumValue(): void
    {
        $header = new BackedEnumHeader(TestStatus::class, 'X-Status');

        Assert::same($header->encode(TestStatus::Published), 'published');
    }

    public function decodesStringBackedEnumValue(): void
    {
        $header = new BackedEnumHeader(TestStatus::class, 'X-Status');

        Assert::same($header->decode('draft'), TestStatus::Draft);
    }

    public function encodesIntBackedEnumValue(): void
    {
        $header = new BackedEnumHeader(TestCode::class, 'X-Code');

        Assert::same($header->encode(TestCode::Ok), '200');
    }

    public function decodesIntBackedEnumValue(): void
    {
        $header = new BackedEnumHeader(TestCode::class, 'X-Code');

        Assert::same($header->decode('200'), TestCode::Ok);
    }

    public function rejectsInvalidEnumValue(): void
    {
        Expect::exception(CannotDecodeHeader::class)
            ->withMessage('Header "X-Status" has invalid value "archived" for enum Thesis\Headers\TestStatus.')
            ->withPrevious(\ValueError::class);

        new BackedEnumHeader(TestStatus::class, 'X-Status')->decode('archived');
    }

    public function rejectsMalformedIntBackedEnumValue(): void
    {
        Expect::exception(CannotDecodeHeader::class)
            ->withMessage('Header "X-Code" has invalid value "200abc" for enum Thesis\Headers\TestCode.');

        new BackedEnumHeader(TestCode::class, 'X-Code')->decode('200abc');
    }
}

enum TestStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
}

enum TestCode: int
{
    case Ok = 200;
}
