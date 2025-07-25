# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.0.8] - 2025-07-21
### Changed
- Internal updates.

## [3.0.7] - 2025-04-28
### Changed
- Internal updates.

## [3.0.6] - 2025-03-31
### Changed
- Internal updates.

## [3.0.5] - 2025-03-21
### Changed
- Internal updates.

## [3.0.4] - 2025-03-12
### Changed
- Internal updates.

## [3.0.3] - 2025-03-05
### Changed
- Internal updates.

## [3.0.2] - 2025-02-24
### Changed
- Update dependencies.

## [3.0.1] - 2024-11-25
### Changed
- Updated package dependencies. [#40258]

## [3.0.0] - 2024-11-14
### Removed
- General: Update minimum PHP version to 7.2. [#40147]

## [2.0.5] - 2024-11-04
### Added
- Enable test coverage. [#39961]

## [2.0.4] - 2024-09-05
### Changed
- Update dependencies.

## [2.0.3] - 2024-08-23
### Changed
- Updated package dependencies. [#39004]

## [2.0.2] - 2024-04-25
### Changed
- Update dependencies.

## [2.0.1] - 2024-03-12
### Changed
- Internal updates.

## [2.0.0] - 2023-11-20
### Changed
- Replaced usage of strpos() with str_starts_with(). [#34135]
- Updated required PHP version to >= 7.0. [#34192]

## [1.7.27] - 2023-09-19

- Minor internal updates.

## [1.7.26] - 2023-08-23
### Changed
- Updated package dependencies. [#32605]

## [1.7.25] - 2023-04-10
### Added
- Add Jetpack Autoloader package suggestion. [#29988]

## [1.7.24] - 2023-02-20
### Changed
- Minor internal updates.

## [1.7.23] - 2023-01-11
### Changed
- Updated package dependencies.

## [1.7.22] - 2022-12-19
### Changed
- Updated package dependencies.

## [1.7.21] - 2022-12-02
### Changed
- Updated package dependencies. [#27688]

## [1.7.20] - 2022-11-22
### Changed
- Updated package dependencies. [#27043]

## [1.7.19] - 2022-11-07
### Changed
- Updated package dependencies. [#27278]

## [1.7.18] - 2022-07-26
### Changed
- Updated package dependencies. [#25158]

## [1.7.17] - 2022-06-21
### Changed
- Renaming master to trunk.

## [1.7.16] - 2022-06-14
### Changed
- Updated package dependencies. [#24529]

## [1.7.15] - 2022-05-10

## [1.7.14] - 2022-04-26
### Changed
- Updated package dependencies.

## [1.7.13] - 2022-04-05
### Changed
- Updated package dependencies.

## [1.7.12] - 2022-03-02
### Changed
- Updated package dependencies.

## [1.7.11] - 2022-02-22
### Changed
- Updated package dependencies.

## [1.7.10] - 2022-01-25
### Changed
- Updated package dependencies.

## [1.7.9] - 2022-01-04
### Changed
- Switch to pcov for code coverage.
- Updated package dependencies

## [1.7.8] - 2021-12-14
### Changed
- Updated package dependencies.

## [1.7.7] - 2021-11-22
### Changed
- Updated package dependencies

## [1.7.6] - 2021-11-02
### Changed
- Set `convertDeprecationsToExceptions` true in PHPUnit config.
- Update PHPUnit configs to include just what needs coverage rather than include everything then try to exclude stuff that doesn't.

## [1.7.5] - 2021-10-26
### Changed
- Updated package dependencies.

## [1.7.4] - 2021-10-13
### Changed
- Updated package dependencies.

## [1.7.3] - 2021-10-12
### Changed
- Updated package dependencies

## [1.7.2] - 2021-09-28
### Changed
- Updated package dependencies.

## [1.7.1] - 2021-08-30
### Changed
- Run composer update on test-php command instead of phpunit
- Tests: update PHPUnit polyfills dependency (yoast/phpunit-polyfills).

## [1.7.0] - 2021-06-29
### Changed
- Allow any argument to be passed.
- Improve documentation.

## [1.6.1] - 2021-06-15
### Changed
- Updated package dependencies.

## [1.6.0] - 2021-05-25
### Removed
- Removed filter from the final Redirect URL

## [1.5.5] - 2021-04-27
### Changed
- Updated package dependencies.

## [1.5.4] - 2021-03-30
### Added
- Composer alias for dev-master, to improve dependencies

### Changed
- Update package dependencies.
- Userless Connection: Redirect "userless" users to the "Plans" page

### Fixed
- Use `composer update` rather than `install` in scripts, as composer.lock isn't checked in.

## [1.5.3] - 2021-02-23

- CI: Make tests more generic

## [1.5.2] - 2021-01-26

- Update dependencies to latest stable

## [1.5.1] - 2021-01-26

- Add mirror-repo information to all current composer packages
- Monorepo: Reorganize all projects

## [1.5.0] - 2021-01-05

- Update dependency brain/monkey to v2.6.0
- Pin dependencies
- Packages: Update for PHP 8 testing
- Pin dependency brain/monkey to 2.5.0

## [1.4.1] - 2020-11-24

- Status: Introduce get_site_suffix method

## [1.4.0] - 2020-10-27

- Masterbar: Add Admin Menu endpoint

## [1.3.0] - 2020-08-14

- Packages: Update filenames after #16810
- CI: Try collect js coverage
- Docker: Add package testing shortcut

## [1.2.0] - 2020-06-16

- Add a trailing / to jetpack.com/redirect URLs.

## [1.1.0] - 2020-05-22

- add filter to Redirect::get_url

## 1.0.0 - 2020-04-24

- Create Jetpack Redirect package

[3.0.8]: https://github.com/Automattic/jetpack-redirect/compare/v3.0.7...v3.0.8
[3.0.7]: https://github.com/Automattic/jetpack-redirect/compare/v3.0.6...v3.0.7
[3.0.6]: https://github.com/Automattic/jetpack-redirect/compare/v3.0.5...v3.0.6
[3.0.5]: https://github.com/Automattic/jetpack-redirect/compare/v3.0.4...v3.0.5
[3.0.4]: https://github.com/Automattic/jetpack-redirect/compare/v3.0.3...v3.0.4
[3.0.3]: https://github.com/Automattic/jetpack-redirect/compare/v3.0.2...v3.0.3
[3.0.2]: https://github.com/Automattic/jetpack-redirect/compare/v3.0.1...v3.0.2
[3.0.1]: https://github.com/Automattic/jetpack-redirect/compare/v3.0.0...v3.0.1
[3.0.0]: https://github.com/Automattic/jetpack-redirect/compare/v2.0.5...v3.0.0
[2.0.5]: https://github.com/Automattic/jetpack-redirect/compare/v2.0.4...v2.0.5
[2.0.4]: https://github.com/Automattic/jetpack-redirect/compare/v2.0.3...v2.0.4
[2.0.3]: https://github.com/Automattic/jetpack-redirect/compare/v2.0.2...v2.0.3
[2.0.2]: https://github.com/Automattic/jetpack-redirect/compare/v2.0.1...v2.0.2
[2.0.1]: https://github.com/Automattic/jetpack-redirect/compare/v2.0.0...v2.0.1
[2.0.0]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.27...v2.0.0
[1.7.27]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.26...v1.7.27
[1.7.26]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.25...v1.7.26
[1.7.25]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.24...v1.7.25
[1.7.24]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.23...v1.7.24
[1.7.23]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.22...v1.7.23
[1.7.22]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.21...v1.7.22
[1.7.21]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.20...v1.7.21
[1.7.20]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.19...v1.7.20
[1.7.19]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.18...v1.7.19
[1.7.18]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.17...v1.7.18
[1.7.17]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.16...v1.7.17
[1.7.16]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.15...v1.7.16
[1.7.15]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.14...v1.7.15
[1.7.14]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.13...v1.7.14
[1.7.13]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.12...v1.7.13
[1.7.12]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.11...v1.7.12
[1.7.11]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.10...v1.7.11
[1.7.10]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.9...v1.7.10
[1.7.9]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.8...v1.7.9
[1.7.8]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.7...v1.7.8
[1.7.7]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.6...v1.7.7
[1.7.6]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.5...v1.7.6
[1.7.5]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.4...v1.7.5
[1.7.4]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.3...v1.7.4
[1.7.3]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.2...v1.7.3
[1.7.2]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.1...v1.7.2
[1.7.1]: https://github.com/Automattic/jetpack-redirect/compare/v1.7.0...v1.7.1
[1.7.0]: https://github.com/Automattic/jetpack-redirect/compare/v1.6.1...v1.7.0
[1.6.1]: https://github.com/Automattic/jetpack-redirect/compare/v1.6.0...v1.6.1
[1.6.0]: https://github.com/Automattic/jetpack-redirect/compare/v1.5.5...v1.6.0
[1.5.5]: https://github.com/Automattic/jetpack-redirect/compare/v1.5.4...v1.5.5
[1.5.4]: https://github.com/Automattic/jetpack-redirect/compare/v1.5.3...v1.5.4
[1.5.3]: https://github.com/Automattic/jetpack-redirect/compare/v1.5.2...v1.5.3
[1.5.2]: https://github.com/Automattic/jetpack-redirect/compare/v1.5.1...v1.5.2
[1.5.1]: https://github.com/Automattic/jetpack-redirect/compare/v1.5.0...v1.5.1
[1.5.0]: https://github.com/Automattic/jetpack-redirect/compare/v1.4.1...v1.5.0
[1.4.1]: https://github.com/Automattic/jetpack-redirect/compare/v1.4.0...v1.4.1
[1.4.0]: https://github.com/Automattic/jetpack-redirect/compare/v1.3.0...v1.4.0
[1.3.0]: https://github.com/Automattic/jetpack-redirect/compare/v1.2.0...v1.3.0
[1.2.0]: https://github.com/Automattic/jetpack-redirect/compare/v1.1.0...v1.2.0
[1.1.0]: https://github.com/Automattic/jetpack-redirect/compare/v1.0.0...v1.1.0
