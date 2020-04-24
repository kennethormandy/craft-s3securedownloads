# Release Notes for S3 Secure Downloads

## Unreleased

## 3.0.0-beta.1 - 2020-04-23

### Added
- Added [before and after signing events](https://github.com/kennethormandy/craft-s3securedownloads#events) to hook onto
- Added testing framework (currently need full install and S3 buckets to run)
- Adds url pre-signing and force file download tests
- Adds support for AWS Signature Version 4 signing process

### Changed
- Sets minimum version to Craft v3.1.5, same as craftcms/aws-s3 plugin
- Added 

### Fixed
- Added support for downloads in folders (not just subfolders on the asset bucket) #3 #4

## [2.2.1](https://github.com/kennethormandy/craft-s3securedownloads/releases/tag/v2.2.1) - 2020-01-05

### Added
- Added better example to README

### Fixed
- Replaces asset `id` with `uid` in URL to proxying action controller. The result is URLs that still point internally to an action that checks if the user is logged in (if enabled in the settings), but now they end with the `uid` rather than the `id`, ex: `get-file&uid=a1a1a111-b2b2-cc33-4dd4-eeeee5e55555`
- Removes custom function to convert environment variables in settings, in favour of Craft’s

## [2.2.0](https://github.com/kennethormandy/craft-s3securedownloads/releases/tag/v2.2.0) - 2020-01-05

### Added
- Added support for the custom asset volume base URL (#2)

### Fixed
- Fixed Changelog formatting, path in `composer.json`
- Fixed used of requireLoggedInUser setting for Craft 3
- Fixed possibility of leading slash in resource name

### Changed
- Removes lockfile

## [2.1.0](https://github.com/kennethormandy/craft-s3securedownloads/releases/tag/v2.1.0) - 2019-12-16

### Added
- Initial version tagged for the Craft CMS Plugin Store

## [2.0.0](https://github.com/kennethormandy/craft-s3securedownloads/releases/tag/v2.0.0) - 2019-12-03

### Added
- Initial version ported to Craft CMS 3

<!--

## [0.0.0 - YYYY-MM-DD](https://github.com/kennethormandy/craft-s3securedownloads/releases/tag/v0.0.0)

### Added
### Changed
### Deprecated
### Removed
### Fixed
### Security

-->
