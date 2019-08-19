<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'ogoloshen' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'W%v#Z,(W/@uCS[*.`WLH%_&F1U1<13jqGhiM2(xD~|!#%CSD:$>&wx:=ZvAzw^$c' );
define( 'SECURE_AUTH_KEY',  '|XuN0~Nm(bk9/Di!i6#==N@!!l({j2t;DrY+8U[o7es+/MUoDu=fSUWmSJ^zkfS!' );
define( 'LOGGED_IN_KEY',    '(Mc[/P}Q^:@{r95i3]TuuJ};ZgiUU]|g;DyAqsd}AD+bLL6!(<;%jRuB#t|5r@<k' );
define( 'NONCE_KEY',        'nMNjbLVgVO}rUq~nsCp#PZk*HIS@Xj0}2h-mv*potMP/y2%}z;~9}PZ ^m,f!I<3' );
define( 'AUTH_SALT',        'J[#mS?^:1G|B>yd#ad5xXFN/Rzfr7u5haDmL&O#Zz;$iiM+xp2^|j&);u4t`1BuD' );
define( 'SECURE_AUTH_SALT', '?32rtE`8:B.rGKU$V8[n?]};hZ+JN,#VKMmz6I_W;.0[o1{}X+#%QxzqsJR|@oH2' );
define( 'LOGGED_IN_SALT',   '_KQrJ2QJ !]=h<QwzduEs>Oo:l.,JP@PI)(+f{cm+Rlz@B xE$d+OG63w%h0ygd8' );
define( 'NONCE_SALT',       'Meb|+C18g^F-.wTG{~[R3*A|k&55hr:_ pGh9&_EGjagfsCo+TMITO9BUm0GVG^y' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
