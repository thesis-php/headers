<?php

declare(strict_types=1);

namespace Thesis;

use Thesis\Headers\CannotDecodeHeader;
use Thesis\Headers\Header;
use Thesis\Headers\NoHeader;

/**
 * @api
 */
final class Headers
{
    /**
     * A value is one of:
     *  - string: raw encoded header straight from the wire, not yet decoded;
     *  - array{string, mixed}: [original encoded, decoded value] — decoded from the wire and memoized;
     *  - array{Header<mixed>, mixed}: [codec, value] — set via with(), not yet encoded.
     *
     * @var array<string, string|array{string|Header<mixed>, mixed}>
     */
    private array $values;

    /**
     * @param array<string, string> $raw
     */
    public function __construct(array $raw = [])
    {
        $this->values = $raw;
    }

    /**
     * @param Header<*> $header
     */
    public function has(Header $header): bool
    {
        return isset($this->values[$header->name]);
    }

    /**
     * @template T
     * @param Header<T> $header
     * @return T
     * @throws NoHeader
     * @throws CannotDecodeHeader
     */
    public function get(Header $header): mixed
    {
        $entry = $this->values[$header->name] ?? throw new NoHeader($header);

        if (\is_array($entry)) {
            return $entry[1];
        }

        $value = $header->decode($entry);
        $this->values[$header->name] = [$entry, $value];

        return $value;
    }

    /**
     * @template T
     * @param Header<T> $header
     * @return ?T
     * @throws CannotDecodeHeader
     */
    public function find(Header $header): mixed
    {
        $entry = $this->values[$header->name] ?? null;

        if ($entry === null) {
            return null;
        }

        if (\is_array($entry)) {
            return $entry[1];
        }

        $value = $header->decode($entry);
        $this->values[$header->name] = [$entry, $value];

        return $value;
    }

    /**
     * @template T
     * @param Header<T> $header
     * @param T $value
     */
    public function with(Header $header, mixed $value): self
    {
        $copy = clone $this;
        $copy->values[$header->name] = [$header, $value];

        return $copy;
    }

    /**
     * @template T
     * @param Header<T> $header
     * @param T|\Closure(): T $default
     */
    public function withDefault(Header $header, mixed $default): self
    {
        if ($this->has($header)) {
            return $this;
        }

        return $this->with($header, $default instanceof \Closure ? $default() : $default);
    }

    public function withMerged(self $headers): self
    {
        $merged = new self();

        $merged->values = [
            ...$this->values,
            ...$headers->values,
        ];

        return $merged;
    }

    /**
     * @param Header<*> ...$headers
     */
    public function without(Header ...$headers): self
    {
        $copy = clone $this;

        foreach ($headers as $header) {
            unset($copy->values[$header->name]);
        }

        return $copy;
    }

    /**
     * @return array<string, string>
     */
    public function encode(): array
    {
        return array_map(
            static fn($entry) => match (true) {
                \is_string($entry) => $entry,
                \is_string($entry[0]) => $entry[0],
                default => $entry[0]->encode($entry[1]),
            },
            $this->values,
        );
    }
}
