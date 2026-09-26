=== SMNTCS Admin Maintenance ===

Contributors:       nielslange
Tags:               maintenance, maintenance mode, admin, dashboard, lockdown
Requires at least:  3.4
Tested up to:       7.1
Requires PHP:       7.4
Stable tag:         2.6
License:            GPL v2 or later
License URI:        https://www.gnu.org/licenses/gpl-2.0.html

Puts the WordPress admin area into maintenance mode, so only the user you choose can log in while you work on the site.

== Description ==

SMNTCS Admin Maintenance puts the [WordPress admin area](https://wordpress.org/documentation/article/administration-screens/) into maintenance mode. While it is on, only the user you grant access can log in. Everyone else sees a message on the login screen that the admin area is in maintenance mode.

This is useful when you migrate a site, run large updates or change settings that other editors should not touch in the meantime. The front end of your site stays online the whole time.

= Features =

* Turn admin maintenance mode on and off in the Customizer
* Choose the one user who can still log in
* The public site stays available

== Installation ==

1. Upload `smntcs-admin-maintenance` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the `Plugins` menu in WordPress.
3. Go to `Appearance » Customize » Admin Maintenance` and put the Administration Screens into maintenance mode if necessary.

== Changelog ==

= 2.6 (2026.09.26) =

- Test up to WordPress 7.1
- Update development dependencies and GitHub Actions

= 2.5 (2026.08.14) =

- Tested up to WordPress 7.0

= 2.4 (2025.04.02) =

- Tested up to WordPress 6.8

= 2.3 (2024.10.29) =

- Tested up to WordPress 6.7
- Migrated e2e tests from Cypress to Playwright

= 2.2 (2024.10.02) =

- Tested up to WordPress 6.6

= 2.1 (2023.10.15) =

- Tested up to WordPress 6.4

= 2.0 (2022.12.03) =

- Tested up to WordPress 6.1

= 1.10 (2022.05.07) =

- Tested up to WordPress 6.0

= 1.9 (2022.04.20) =

- Tested up to WordPress 5.9

= 1.8 (2022.01.09) =

- Tested up to WordPress 5.8

= 1.7 (2020.12.22) =

- Tested up to WordPress 5.6
- Added e2e tests

= 1.6 (2019.10.13) =

- Tested up to WordPress 5.3

= 1.5 (2019.05.13) =

- Tested up to WordPress 5.2

= 1.4 (2019.04.04) =

- Refactored based on PHPCS and WPCS

= 1.3 (2019.03.20) =

- Tested up to WordPress 5.1

= 1.2 (2018.03.18) =

- Added README.md

= 1.1 (2018.03.18) =

- Added language support

= 1.0 (2018.03.18) =

- Initial release
