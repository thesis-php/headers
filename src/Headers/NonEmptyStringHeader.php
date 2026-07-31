<?php

declare(strict_types=1);

namespace Thesis\Headers;

/**
 * @api
 *
 * @implements Header<non-empty-string>
 */
final readonly class NonEmptyStringHeader implements Header
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
        if ($encoded === '') {
            throw new CannotDecodeHeader(\sprintf(
                'Header "%s" has an empty value, expected a non-empty string.',
                $this->name,
            ));
        }

        return $encoded;
    }
}
