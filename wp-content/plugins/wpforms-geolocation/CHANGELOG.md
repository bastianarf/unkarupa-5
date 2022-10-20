# Change Log
All notable changes to this project will be documented in this file, formatted via [this recommendation](http://keepachangelog.com/).

## [2.0.0] - 2021-02-18
### Added
- New Places Providers selection: Google Places, Algolia Places.
- Address and Text fields can have address autocomplete enabled on typing.
- Display a map before or after the field to select location on a map without typing.
- Retrieve user's current location with a browser prompt and prefill address/text fields with address autocomplete enabled.
- Added own WPForms geolocation API endpoint to retrieve users geolocation based their IP address.

### Changed
- Removed map image preview from email notifications due to Google API restrictions.

### Fixed
- Geolocation: display and save only existing data (sometimes ZIP code may be missing).

## [1.2.0] - 2019-07-23
### Added
- Complete translations for French and Portuguese (Brazilian).

## [1.1.1] - 2019-02-26
### Fixed
- Geolocation provider fallback logic.
- Referencing geolocation providers no longer accessible.

## [1.1.0] - 2019-02-06
### Added
- Complete translations for Spanish, Italian, Japanese, and German.

### Fixed
- Typos, grammar, and other i18n related issues.

## [1.0.3] - 2017-09-28
### Changed
- Use HTTPS when requesting location data via ipinfo.io
- Use bundled SSL certificates (since WordPress 3.7) to verify properly target sites SSL certificates

## [1.0.2]
### Changed
- Always use SSL connection to check user IPs location data
- Always verify SSL certificates of the services we use to get location data

## [1.0.1] - 2016-08-04
### Fixed
- Bug preventing IP addresses from processing

## [1.0.0] - 2016-08-03
### Added
- Initial release
