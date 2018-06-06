# Changelog

All notable changes to phar-io/version are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [Unreleased]

This release contains **breaking changes**

### Added

- [#11](https://github.com/phar-io/version/issues/11): [**breaking change**] Added support for pre-release version suffixes. Supported values are:
  - `dev`
  - `beta` (also abbreviated form `b`)
  - `rc`
  - `alpha` (also abbreviated form `a`)
  - `patch` (also abbreviated form `p`)

  All values can be followed by a number, e.g. `beta3`. 

  When comparing versions, the pre-release suffix is taken into account. Example:
`1.5.0 > 1.5.0-beta1 > 1.5.0-alpha3 > 1.5.0-alpha2 > 1.5.0-dev11`

### Changed

- reorganized the source directories

### Fixed

[#10](https://github.com/phar-io/version/issues/10): Version numbers containing 
a numeric suffix as seen in Debian packages are now supported.  

[Unreleased]: https://github.com/phar-io/version/compare/1.0.1...HEAD
