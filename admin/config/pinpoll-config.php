<?php
/**
 * Global Configuration File
 *
 * Description: Contains all necessary urls and constants which are needed in the plugin.
 * Notice:      All files are using this config except /tinymce, there you have to set the base url.
 *              (Search for TODO)
 *
 * @package Pinpoll
 * @subpackage Pinpoll/admin/config
 *
 */

//WORDPRESS URLS
define( 'PINPOLL_SETTINGS_URL', 'admin.php?page=settings' );
define( 'PINPOLL_PLUGIN_NAME', 'pinpoll' );
define( 'PINPOLL_ICON_URL','pinpoll/images/pinpoll_wp-icon_alpha.png' );
define( 'PINPOLL_SWITCH_ACCOUNT_URL', 'admin.php?page=pinpoll/pinpoll-settings.php' );
define( 'PINPOLL_REVIEW_URL', 'https://wordpress.org/support/plugin/pinpoll/reviews/#postform' );

//WORDPRESS CONSTANTS
define( 'POLLS_PER_PAGE', 10 );

//LIVE URLS
define( 'PINPOLL_BASE_URL', 'https://pinpoll.com/v1' );
define( 'PINPOLL_EMBED_IFRAME', 'https://pinpoll.com/embed' );
define( 'PINPOLL_STATS_BASE_URL', 'https://pinpoll.com/v1/stats/top' );
define( 'PINPOLL_FEEDBACK_BASE_URL', 'https://pinpoll.com/v1/feedback' );
define( 'PINPOLL_COCKPIT_BASE_URL', 'https://pinpoll.com/cockpit' );
define( 'PINPOLL_JS_URL', 'https://pinpoll.com' );
define( 'PINPOLL_RESETPW_URL', 'https://pinpoll.com/password/email' );


//Uncomment for testing local
/*
define( 'PINPOLL_BASE_URL', 'http://localhost:90/v1' );
define( 'PINPOLL_EMBED_IFRAME', 'http://localhost:90/embed' );
define( 'PINPOLL_STATS_BASE_URL', 'http://localhost:90/v1/stats/top' );
define( 'PINPOLL_FEEDBACK_BASE_URL', 'http://localhost:90/v1/feedback' );
define( 'PINPOLL_COCKPIT_BASE_URL', 'http://localhost:90/cockpit' );
define( 'PINPOLL_JS_URL', 'http://localhost:90' );
define( 'PINPOLL_RESETPW_URL', 'http://localhost:90/password/email' );*/


//API KEY
define( 'PINPOLL_API_KEY', 'da8987dc47d9b245718387730c50b5440d2afcd2' );

?>
