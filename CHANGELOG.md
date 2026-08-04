# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.2.0] - 2026-08-05

### Changed

- **BC break**: Changed the default `TimeHeader` format from `DATE_RFC3339` to RFC 3339 with microseconds (`Y-m-d\TH:i:s.uP`).

## [0.1.1] - 2026-08-04

### Added

- Added `BoolHeader` for boolean headers encoded as `1` and `0`.
- Added `IntHeader` for integer headers with optional range constraints.
