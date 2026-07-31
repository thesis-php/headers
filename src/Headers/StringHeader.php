<?php

declare(strict_types=1);

namespace Thesis\Headers;

/**
 * @api
 *
 * @implements Header<string>
 */
final readonly class StringHeader implements Header
{
    public function __construct(
        public string $name,
    ) {}

    public function encode(mixed $value): string
    {
        return $value;
    }

    public function decode(string $encoded): mixed
    {
        return $encoded;
    }
}
