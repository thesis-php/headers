<?php

declare(strict_types=1);

namespace Thesis\Headers;

/**
 * @api
 *
 * @template T of int = int
 * @implements Header<T>
 */
final readonly class IntHeader implements Header
{
    /**
     * @return self<negative-int>
     */
    public static function negative(string $name): self
    {
        /** @var self<negative-int> */
        return new self($name, max: -1);
    }

    /**
     * @return self<non-positive-int>
     */
    public static function nonPositive(string $name): self
    {
        /** @var self<non-positive-int> */
        return new self($name, max: 0);
    }

    /**
     * @return self<non-negative-int>
     */
    public static function nonNegative(string $name): self
    {
        /** @var self<non-negative-int> */
        return new self($name, min: 0);
    }

    /**
     * @return self<positive-int>
     */
    public static function positive(string $name): self
    {
        /** @var self<positive-int> */
        return new self($name, min: 1);
    }

    public function __construct(
        public string $name,
        private ?int $min = null,
        private ?int $max = null,
    ) {
        if ($this->min !== null && $this->max !== null && $this->min > $this->max) {
            throw new \InvalidArgumentException(\sprintf(
                'Header "%s" minimum value %d is greater than maximum value %d.',
                $this->name,
                $this->min,
                $this->max,
            ));
        }
    }

    public function encode(mixed $value): string
    {
        return (string) $value;
    }

    public function decode(string $encoded): mixed
    {
        $value = (int) $encoded;

        if ((string) $value !== $encoded) {
            throw new CannotDecodeHeader(\sprintf(
                'Header "%s" has malformed value "%s", expected an integer.',
                $this->name,
                $encoded,
            ));
        }

        if ($this->min !== null && $value < $this->min) {
            throw new CannotDecodeHeader(\sprintf(
                'Header "%s" has value "%s" less than minimum %d.',
                $this->name,
                $encoded,
                $this->min,
            ));
        }

        if ($this->max !== null && $value > $this->max) {
            throw new CannotDecodeHeader(\sprintf(
                'Header "%s" has value "%s" greater than maximum %d.',
                $this->name,
                $encoded,
                $this->max,
            ));
        }

        /** @var T */
        return $value;
    }
}
