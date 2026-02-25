# AGENTS.md

## Project overview

This repository contains `bm-gppa-format-acf-date.php`, a WordPress must-use plugin (mu-plugin) that formats ACF (Advanced Custom Fields) date fields for Gravity Forms with GPPA (Gravity Perks Populate Anything). It converts ACF's `Ymd` date format into human-readable Dutch or English date strings based on the field's CSS class (`acf-date-nl-tekst` / `acf-date-en-tekst`) or the form ID (form 5 = English).

## Cursor Cloud specific instructions

### WordPress local dev environment

A local WordPress instance is installed at `/tmp/wordpress/` using SQLite (no MySQL required). The plugin file is symlinked into the WordPress mu-plugins directory:

```
/tmp/wordpress/wp-content/mu-plugins/bm-gppa-format-acf-date.php -> /workspace/bm-gppa-format-acf-date.php
```

**Starting the dev server:**
```bash
cd /tmp/wordpress && php -S localhost:8080 -t /tmp/wordpress/ &
```

**WordPress admin credentials:** `admin` / `admin` at `http://localhost:8080/wp-admin/`

### Linting

- **PHP syntax check:** `php -l bm-gppa-format-acf-date.php`
- **PHPCS (optional, WordPress uses its own coding style, not PSR12):** `~/.config/composer/vendor/bin/phpcs --standard=PSR12 bm-gppa-format-acf-date.php`
  - Note: The plugin follows WordPress coding conventions (spaces inside parentheses, etc.), so PSR12 linting will report style warnings. This is expected.

### Testing the plugin filter

The plugin has no formal test suite. To verify the filter works, create a PHP script that loads WordPress and calls `bm_gppa_format_acf_date_nl()` directly with mock field objects. The function is automatically registered as a mu-plugin on WordPress load.

### Gotchas

- The plugin file has no WordPress plugin header (`Plugin Name:` comment), so it must be loaded as a **mu-plugin** (in `wp-content/mu-plugins/`), not as a regular plugin.
- The WordPress dev environment uses SQLite via the `sqlite-database-integration` plugin, so no MySQL/MariaDB service is needed.
- PHP's built-in web server is single-threaded; avoid concurrent requests during testing.
