<?php
/**
 * This is the configuration file that's used for the functional tests and WP-CLI commands.
 */

$_root_dir = dirname( __DIR__ );
$_env_dir  = __DIR__;

require_once $_root_dir . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createUnsafeImmutable( $_env_dir );
$dotenv->safeLoad();

// Test with WordPress debug mode (default).
define( 'WP_DEBUG', true );

define( 'WP_PLUGIN_DIR', dirname( dirname( __DIR__ ) ) );

// WARNING WARNING WARNING!
// These tests will DROP ALL TABLES in the database with the prefix named below.
// DO NOT use a production database or one that is shared with something else.
define( 'DB_NAME',     getenv( 'TEST_SITE_DB_NAME' ) ?: 'wordpress_test' );
define( 'DB_USER',     getenv( 'TEST_SITE_DB_USER' ) ?: 'root' );
define( 'DB_PASSWORD', getenv( 'TEST_SITE_DB_PASSWORD' ) ?: '' );
define( 'DB_HOST',     getenv( 'TEST_SITE_DB_HOST' ) ?: 'localhost' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = getenv( 'TEST_SITE_TABLE_PREFIX' ) ?: 'wp_';

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
