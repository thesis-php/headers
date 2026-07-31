# Thesis Headers

Typed headers for PHP libraries and applications.

- Decode raw string headers into typed values
- Encode typed values back to wire-safe strings
- Preserve unknown or untouched raw values as-is
- Memoize decoded values
- Compose immutable header sets with `with()`, `without()` and `withMerged()`
- Built-in string, non-empty string, enum and time headers

## Requirements

- PHP 8.4+

## Installation

```shell
composer require thesis/headers
```

## Quick start

Define headers once as constants and use them for typed reads and writes:

```php
use Thesis\Headers;
use Thesis\Headers\BackedEnumHeader;
use Thesis\Headers\NonEmptyStringHeader;
use Thesis\Headers\TimeHeader;

enum ContentType: string
{
    case Html = 'text/html';
    case Json = 'application/json';
}

const CONTENT_TYPE = new BackedEnumHeader(ContentType::class, 'content-type');
const DATE = new TimeHeader('date', DATE_W3C);
const REQUEST_ID = new NonEmptyStringHeader('x-request-id');

$raw = [
    'content-type' => 'application/json',
    'date' => '2026-07-31T12:00:00+00:00',
];

$headers = new Headers($raw);

var_dump($headers->get(CONTENT_TYPE)); // ContentType::Json
var_dump($headers->get(DATE)); // DateTimeImmutable

$newHeaders = $headers
    ->with(CONTENT_TYPE, ContentType::Html) // replace header
    ->withDefault(REQUEST_ID, uniqid(...)) // set header only when missing
    ->without(DATE) // remove header
    ->withMerged($overrides) // merge, right side wins
;

var_dump($newHeaders->encode());
// [
//     'content-type' => 'text/html',
//     'x-request-id' => '6a6c90cae6b1d',
// ]
```
