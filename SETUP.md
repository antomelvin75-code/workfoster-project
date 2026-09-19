# Setting the project up from this repository

This repo contains the **project-specific code only** — the child theme, the
dev-environment mu-plugin, the documentation and a database dump. WordPress core,
the parent theme and the four plugins are not committed; they are installed from
wordpress.org at the versions listed below.

That keeps the repository small and makes it obvious what was actually written for
this project versus what came off the shelf.

---

## What you need

- PHP 8.1+ with the `gd` extension enabled
- MySQL / MariaDB
- Apache or Nginx
- Optionally [WP-CLI](https://wp-cli.org/) — the fast path below uses it

---

## Fast path (WP-CLI)

From an empty directory:

```bash
# 1. WordPress core
wp core download --version=7.1.1

# 2. Config — then edit the DB details
cp wp-config-sample-workfoster.php wp-config.php

# 3. Database
mysql -u root -e "CREATE DATABASE workfoster_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
wp db import database/workfoster.sql

# 4. Theme and plugins at the versions this build was made against
wp theme install hello-elementor --version=3.5.1
wp plugin install elementor --version=4.2.4 --activate
wp plugin install header-footer-elementor --version=2.9.4 --activate
wp plugin install contact-form-7 --version=6.1.7 --activate
wp plugin install wp-job-manager --version=2.4.7 --activate

# 5. Copy this repo's wp-content over the fresh install, then:
wp theme activate hello-elementor-child

# 6. Point the site at your own URL (handles serialised data correctly)
wp search-replace 'http://localhost:8080/workfoster' 'https://your-domain.example' --all-tables --precise

# 7. Permalinks
wp rewrite flush --hard
```

---

## Manual path (no WP-CLI)

1. Download WordPress 7.1.1 and extract it.
2. Copy `wp-content/themes/hello-elementor-child/` and `wp-content/mu-plugins/`
   from this repo into the new `wp-content/`.
3. Create a database and import `database/workfoster.sql` through phpMyAdmin.
4. Copy `wp-config-sample-workfoster.php` to `wp-config.php` and fill in the
   database details plus fresh salts from
   <https://api.wordpress.org/secret-key/1.1/salt/>.
5. Install **Hello Elementor**, **Elementor**, **Elementor Header & Footer
   Builder**, **Contact Form 7** and **WP Job Manager** from the plugin/theme
   directories, then activate the **Workfoster** child theme.
6. **Change the URLs.** The dump contains `http://localhost:8080/workfoster`
   throughout. Do **not** run a plain find-and-replace in SQL — many of those URLs
   sit inside serialised PHP (Elementor page data especially) and a naive replace
   corrupts the length prefixes, which breaks every page layout. Use WP-CLI's
   `search-replace`, or the free [Better Search Replace] plugin, both of which
   unserialise properly.
7. Settings → Permalinks → Save, to flush rewrite rules.

[Better Search Replace]: https://wordpress.org/plugins/better-search-replace/

---

## Login

| | |
|---|---|
| User | `wf_admin` |
| Password | `Workfoster@2026` |

Change this immediately on anything reachable from the internet.

---

## Before putting it on a public server

- Set `WP_DEBUG`, `WP_DEBUG_LOG` and `WP_DEBUG_DISPLAY` to `false`.
- Set `WP_ENVIRONMENT_TYPE` to `production`. This disables the bundled mail
  catcher, which otherwise swallows all outgoing email; install an SMTP plugin in
  its place.
- Change the admin password and the two form recipient addresses
  (Contact → Contact Forms → Mail).
- Replace the placeholder content flagged in `PROJECT-DOCUMENTATION.md` §7 —
  the testimonials, statistics, certifications and contact details are invented.
