# Changelog

All notable changes to the php-cypher-dsl project will be documented in this
file. A changelog has been kept from version 5.0.0 onwards.

The format is based on [Keep a Changelog], and this project adheres to
[Semantic Versioning].

## 7.0.0 - T.B.D.

### Added

- Added the `Direction` enum to replace the `Relationship::DIR_*` constants.

### Changed

- Changed the minimum required PHP version to 8.1.
- Changed the signature of many functions to use PHP 8 union types.

### Removed

- Removed unnecessary dependency for the `openssl` PHP library.
- Removed `ErrorTrait` in favor of PHP 8.0 union types.
- Removed `CastTrait` in favor of the static functions in `CastUtils`.
- Removed `NameGenerationTrait` in favor of the static functions in `NameUtils`.
- Removed `EscapeTrait` in favor of the static functions in `NameUtils`.
- Removed `Relationship::DIR_UNI`, `Relationship::DIR_LEFT` and `Relationship::DIR_RIGHT` constants
  in favor of the `Direction` enum.

## 6.0.0 - 2023-09-19

### Changed

- Change the license from the GPL-v2.0-or-later license to the more permissive
  MIT license.

## 5.0.0 - 2023-01-09

### Added

- Add a blank changelog.

[keep a changelog]: https://keepachangelog.com/en/1.0.0/
[semantic versioning]: https://semver.org/spec/v2.0.0.html
