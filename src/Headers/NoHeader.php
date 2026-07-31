<?php

declare(strict_types=1);

namespace Thesis\Headers;

/**
 * @api
 */
final class NoHeader extends HeaderException
{
    /**
     * @param Header<*> $header
     */
    public function __construct(Header $header)
    {
        parent::__construct(\sprintf('No header "%s" (%s)', $header->name, $header::class));
    }
}
