<?php

declare(strict_types=1);

namespace Thesis\Headers;

/**
 * @api
 *
 * @implements Header<\DateTimeImmutable>
 */
final readonly class TimeHeader implements Header
{
    /**
     * @param non-empty-string $format
     */
    public function __construct(
        public string $name,
        private string $format = DATE_RFC3339,
    ) {}

    public function encode(mixed $value): string
    {
        return $value->format($this->format);
    }

    public function decode(string $encoded): mixed
    {
        $value = \DateTimeImmutable::createFromFormat($this->format, $encoded);
        $errors = \DateTimeImmutable::getLastErrors();

        if ($value === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            throw new CannotDecodeHeader(\sprintf(
                'Header "%s" has malformed value "%s", expected format "%s".',
                $this->name,
                $encoded,
                $this->format,
            ));
        }

        return $value;
    }
}
