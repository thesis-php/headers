<?php

declare(strict_types=1);

namespace Thesis\Headers;

/**
 * @api
 *
 * @template T of \BackedEnum
 * @implements Header<T>
 */
final readonly class BackedEnumHeader implements Header
{
    private bool $isInt;

    /**
     * @param class-string<T> $class
     */
    public function __construct(
        private string $class,
        public string $name,
    ) {
        $this->isInt = new \ReflectionEnum($class)->getBackingType()?->getName() === 'int';
    }

    public function encode(mixed $value): string
    {
        return (string) $value->value;
    }

    public function decode(string $encoded): mixed
    {
        $value = $encoded;

        if ($this->isInt) {
            $value = (int) $value;

            if ((string) $value !== $encoded) {
                throw new CannotDecodeHeader(\sprintf(
                    'Header "%s" has invalid value "%s" for enum %s.',
                    $this->name,
                    $encoded,
                    $this->class,
                ));
            }
        }

        try {
            return $this->class::from($value);
        } catch (\ValueError $e) {
            throw new CannotDecodeHeader(\sprintf(
                'Header "%s" has invalid value "%s" for enum %s.',
                $this->name,
                $encoded,
                $this->class,
            ), previous: $e);
        }
    }
}
