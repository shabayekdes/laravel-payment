# Release Notes

## [Unreleased](https://github.com/shabayekdes/laravel-payment/compare/main...develop)

## [v0.7.1 (2022-02-05)](https://github.com/shabayekdes/laravel-payment/compare/v0.7.0...v0.7.1)

### Added
- Added **Billable** trait ([#29](https://github.com/shabayekdes/laravel-payment/pull/29))
- Added currency and country config

### Enhanced
- Enhanced test helpers

## [v0.7.0 (2022-01-15)](https://github.com/shabayekdes/laravel-payment/compare/v0.6.0...v0.7.0)

### Added
- Added get errors message if payment gateway is failed ([#14](https://github.com/shabayekdes/laravel-payment/pull/14))
- Added get payment success status

### Changed
- Changed handling error exception to store errors in array instead throw exception 

## [v0.6.0 (2022-01-08)](https://github.com/shabayekdes/laravel-payment/compare/v0.5.1...v0.6.0)

### Added
- Added verify paymob status from gateway ([#3](https://github.com/shabayekdes/laravel-payment/pull/3))
- Added configuration for styleci to fix code style ([#3](https://github.com/shabayekdes/laravel-payment/pull/3))

### Fixed
- Fixed code style to PRS4 standards

## [v0.5.1 (2022-01-07)](https://github.com/shabayekdes/laravel-payment/compare/v0.5.0...v0.5.1)

### Added
- Add credentails paymob property ([#2](https://github.com/shabayekdes/laravel-payment/pull/2))
- Add github action workflow ([#2](https://github.com/shabayekdes/laravel-payment/pull/2))

### Changed
- Convert payment to singleton class and add payment facade aliase ([#2](https://github.com/shabayekdes/laravel-payment/pull/2))

## [v0.5 (2022-01-07)](https://github.com/shabayekdes/laravel-payment/compare/v0.4.1...develop)

### Changed
- Set order total amount when add items ([#1](https://github.com/shabayekdes/laravel-payment/pull/1))
