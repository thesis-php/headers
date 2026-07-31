<?php

declare(strict_types=1);

namespace Thesis\Headers;

/**
 * @api
 *
 * @template T
 */
interface Header
{
    public string $name { get; }

    /**
     * @param T $value
     */
    public function encode(mixed $value): string;

    /**
     * @return T
     * @throws CannotDecodeHeader
     */
    public function decode(string $encoded): mixed;
}
