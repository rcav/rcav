<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */

define('DB_NAME', 'rcav2');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/** Enable revisions and limit post revisions to 5 copies */
define('WP_POST_REVISIONS', true );
define('WP_POST_REVISIONS', 4 );



//define('WP_HOME', 'http://' . $_SERVER['HTTP_HOST']);
//define('WP_HOME', 'http://10.0.1.11/');
// add the next line if you have a subdirectory install
//define('WP_SITEURL', WP_HOME . 'http://10.0.1.11/');


/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'YPf*$f:^o!o7!/01BJJNF#D^5fw2k:#~rlS)E|Ujg6qL.p(y3]xQ+-wTs:Ly.J9v');
define('SECURE_AUTH_KEY',  '-Rhm;&wu(qe+_ K~k|+[2_Z|ZI(q>+5S #@LhWI-^T4b0zsd7!1|)<g|NeiPCb}n');
define('LOGGED_IN_KEY',    '<xkDI+8w$79[5+&+o<=)pcpEXzr5n~BZyq~7i|^&czQ=p4t|=yM)#s/-]PsaYBYZ');
define('NONCE_KEY',        'dp2Y)E&D6rf-LBT`kHdFE{0~nUgL8_(@A1~8kURs$mHW{rL*0yzaFRFz2.zxj1&|');
define('AUTH_SALT',        'w}M6[ @~w9DO}S}-Q_)D![-0.*-lVuRuFs#m0=l|RNm5$Fnqu7Q)PiYp4]atT]k^');
define('SECURE_AUTH_SALT', 'LX8L86x0sOGSi/U7%-l+CaXh<Mdli=>BCQ2k|-sM0anLqD,+(7Fu57RL, IEPy_f');
define('LOGGED_IN_SALT',   '=t@Q$pn.-S1X5wG+()i,<;@i*FNRLtaM-Lu6i5.usO:zt&D|,M$gGl1d+^`xsuI/');
define('NONCE_SALT',       'cPirc]iu^Yu$UE[JI!Ae:nJGBQknp3r-95#!=6w@aG?FJx8Q*gA&+oJwh~M,U}R$');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
