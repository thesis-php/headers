<?php

declare(strict_types=1);

namespace Thesis\Headers;

/**
 * @api
 *
 * @implements Header<bool>
 */
final readonly class BoolHeader implements Header
{
    public function __construct(
        public string $name,
    ) {}

    public function encode(mixed $value): string
    {
        return $value ? '1' : '0';
    }

    public function decode(string $encoded): mixed
    {
        return match ($encoded) {
            '1' => true,
            '0' => false,
            default => throw new CannotDecodeHeader(\sprintf(
                'Header "%s" has malformed value "%s", expected a boolean.',
                $this->name,
                $encoded,
            )),
        };
    }
}
