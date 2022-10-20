# Change Log
All notable changes to this project will be documented in this file, formatted via [this recommendation](http://keepachangelog.com/).

## [1.4.4] - 2020-12-17
### Fixed
- Form abandonment via external links not always detected on some mobile devices.

## [1.4.3] - 2020-08-05
### Changed
- Password field values are no longer stored by default, can be enabled with `wpforms_process_save_password_form_abandonment` filter.

### Fixed
- Abandoned entries are counting towards entry limits defined via Form Locker addon settings.
- Prevent abandoned entry duplicates creation when 2 AJAX-based forms are present on the same page and only one of them was submitted.

## [1.4.2] - 2020-06-10
### Fixed
- Entry must have the "completed" type after its creation through non-ajax form.

## [1.4.1] - 2020-04-30
### Fixed
- Prevent 'Abandoned' (duplicate) entry on successful form submit.

## [1.4.0] - 2020-01-15
### Added
- Access Controls compatibility (WPForms 1.5.8).

## [1.3.0] - 2020-01-09s
### Added
- Tracking closing of the window or tab by listening to the `beforeunload` event.

### Changed
- Do not send duplicate abandonment notifications if 'no duplicates' option is enabled.

## [1.2.0] - 2019-07-23
### Added
- Complete translations for French and Portuguese (Brazilian).

### Fixed
- Form abandonment records only last checkbox field selection.
- "Prevent duplicate abandoned entries" saves duplicate entries.

## [1.1.0] - 2019-02-06
### Added
- Complete translations for Spanish, Italian, Japanese, and German.

### Fixed
- Typos, grammar, and other i18n related issues.

## [1.0.2] - 2018-02-12
### Fixed
- Conflict with email notifications configured with conditional logic causing notifications to send when they should not.

## [1.0.1] - 2017-02-01
### Fixed
- Incorrect version in updater which caused WordPress to think an update was available.

## [1.0.0] - 2017-02-01
### Added
- Initial release.
