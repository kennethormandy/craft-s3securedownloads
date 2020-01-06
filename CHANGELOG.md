# Release Notes for S3 Secure Downloads

<!--

## [0.0.0 - YYYY-MM-DD](https://github.com/kennethormandy/craft-s3securedownloads/releases/tag/v0.0.0)

### Added
### Changed
### Deprecated
### Removed
### Fixed
### Security

-->

## Next

## [2.2.1 - 2020-01-05](https://github.com/kennethormandy/craft-s3securedownloads/releases/tag/v2.2.1)

### Added

- Added better example to README

### Fixed

- Replaces asset `id` with `uid` in URL to proxying action controller. The result is URLs that still point internally to an action that checks if the user is logged in (if enabled in the settings), but now they end with the `uid` rather than the `id`, ex: `get-file&uid=a1a1a111-b2b2-cc33-4dd4-eeeee5e55555`
- Removes custom function to convert environment variables in settings, in favour of Craft’s

## [2.2.0 - 2020-01-05](https://github.com/kennethormandy/craft-s3securedownloads/releases/tag/v2.2.0)

### Added

- Added support for the custom asset volume base URL (#2)

### Fixed

- Fixed Changelog formatting, path in `composer.json`
- Fixed used of requireLoggedInUser setting for Craft 3
- Fixed possibility of leading slash in resource name

### Changed

- Removes lockfile

## [2.1.0 - 2019-12-16](https://github.com/kennethormandy/craft-s3securedownloads/releases/tag/v2.1.0)

### Added

- Initial version tagged for the Craft CMS Plugin Store

## [2.0.0 - 2019-12-03](https://github.com/kennethormandy/craft-s3securedownloads/releases/tag/v2.0.0)

### Added

- Initial version ported to Craft CMS 3
