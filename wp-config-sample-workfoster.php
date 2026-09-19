<?php
/**
 * Workfoster — wp-config template.
 *
 * The real `wp-config.php` is NOT in this repository: it holds the database
 * password and the site's authentication salts. Copy this file to
 * `wp-config.php`, fill in your own database details, and paste a fresh set of
 * salts from https://api.wordpress.org/secret-key/1.1/salt/
 *
 * @package Workfoster
 */

// ** Database settings ** //
define( 'DB_NAME', 'workfoster_db' );
define( 'DB_USER', 'workfoster_user' );
define( 'DB_PASSWORD', 'CHANGE_ME' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', 'utf8mb4_unicode_ci' );

/**
 * Authentication unique keys and salts.
 *
 * Generate your own at:
 * https://api.wordpress.org/secret-key/1.1/salt/
 */
define( 'AUTH_KEY',         'put your unique phrase here' );
define( 'SECURE_AUTH_KEY',  'put your unique phrase here' );
define( 'LOGGED_IN_KEY',    'put your unique phrase here' );
define( 'NONCE_KEY',        'put your unique phrase here' );
define( 'AUTH_SALT',        'put your unique phrase here' );
define( 'SECURE_AUTH_SALT', 'put your unique phrase here' );
define( 'LOGGED_IN_SALT',   'put your unique phrase here' );
define( 'NONCE_SALT',       'put your unique phrase here' );

$table_prefix = 'wp_';

/**
 * Environment.
 *
 * `local` activates the dev mail catcher in wp-content/mu-plugins, which writes
 * outgoing email to disk instead of sending it. Set this to 'staging' or
 * 'production' on a real server and configure an SMTP plugin.
 */
define( 'WP_ENVIRONMENT_TYPE', 'local' );

// Development settings — turn all three off on anything public.
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );

define( 'WP_MEMORY_LIMIT', '512M' );
define( 'DISALLOW_FILE_EDIT', true );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';
