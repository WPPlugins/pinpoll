<?php
/*
 * Pinpoll Plugin File
 *
 * @link https://pinpoll.com
 * @since 3.0.0
 * @package Pinpoll
 *
 * Plugin Name: Pinpoll
 * Plugin URI: https://pinpoll.com
 * Text Domain: pinpoll
 * Domain Path: /lang
 * Description: Create fun polls & understand your audience!
 * Version: 3.0.10
 * Min WP Version: 3.3.0
 * Author: Pinpoll
 * Author URI: https://pinpoll.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

 //Security
 if ( ! defined( 'ABSPATH' ) ) { exit(); }

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
 die;
}

//INCLUDES config, texts
require_once( 'admin/config/pinpoll-config.php' );
require_once( 'admin/functions/pinpoll-functions.php' );
require_once( 'admin/resources/pinpoll-texts.php' );


/**
 * Classname: Pinpoll
 * Description: Initiate the plugin, with all configurations
 *
 * @package Pinpoll
 *
 */
class Pinpoll {

    /**
     * construct where admin hooks are performed
     * which register settings, call menu structure, ...
     */
    public function __construct() {

      //hooks while loading plugin
      add_action( 'admin_init', array( $this, 'check_version' ) );
      // Don't run anything else in the plugin, if we're on an incompatible WordPress version
      if ( ! self::compatible_version() ) {
           return;
      }
      register_activation_hook( __FILE__, array( $this, 'pinpoll_on_activation' ) );
      add_action( 'admin_init', array( $this, 'pinpoll_settings' ) );
      add_action( 'admin_menu', array( $this, 'pinpoll_settings_menu' ) );
      add_action( 'admin_init', array( $this, 'init' ) );
      add_action( 'admin_enqueue_scripts', array( $this, 'load_styles' ) );
      add_shortcode( 'pinpoll', array( $this, 'pinpoll_shortcode_handler' ) );
      add_action( 'admin_init', array( $this, 'pinpoll_tinymce_button' ) );
      add_action( 'plugins_loaded', array( $this, 'pinpoll_load_translation' ) );
      add_action( 'admin_enqueue_scripts', array( $this, 'pinpoll_scripts' ) );
      add_action( 'admin_enqueue_scripts', array( $this, 'pinpoll_global' ) );
      add_action( 'wp_enqueue_scripts', array( $this, 'pinpoll_global' ) );
      //load localization vars to access in tinymce Editor
      foreach ( array('post.php','post-new.php') as $hook ) {
        add_action( "admin_head-$hook", array( $this, 'pinpoll_tinyMCE_translation' ) );
      }

    }


    /**
     * Loads global.js
     */
    function pinpoll_global() {
      wp_enqueue_script( 'pinpoll_global', PINPOLL_JS_URL.'/global.js');
    }
  /**
   * Localization
   * Description: Load localization vars to ensure i18n in js files
   */
  function pinpoll_scripts() {
    $version = pinpoll_get_version();
    $texts = pinpoll_get_switchaccount_texts();
    wp_enqueue_script( 'pinpoll-switch-account', plugin_dir_url( __FILE__ ) . 'admin/js/pinpoll_login_validate.js', array(), $version );
    wp_localize_script( 'pinpoll-switch-account', 'ppTrans', array(
      'emailMessage' => $texts['emailmessage'],
      'passwordMessage'=> $texts['passwordmessage']
    ) );
  }

    // The primary sanity check, automatically disable the plugin on activation if it doesn't// meet minimum requirements.static
	function activation_check() {
		if ( ! self::compatible_version() ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( __( '<div class="wrap"><div class="error notice notice-error is-dismissible"> <p> Pinpoll requires WordPress 3.3 and PHP 5.5 or higher! </p> </div></div>', 'pinpoll' ) );
		}
	}

	// The backup sanity check, in case the plugin is activated in a weird way,
	// or the versions change after activation.
	function check_version() {
		if ( ! self::compatible_version() ) {
			if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
				deactivate_plugins( plugin_basename( __FILE__ ) );
				add_action( 'admin_notices', array( $this, 'disabled_notice' ) );

				if ( isset( $_GET['activate'] ) ) {
					unset( $_GET['activate'] );
				}
			}
		}
	}

	function disabled_notice() {
		echo '<div class="wrap"><div class="error notice notice-error is-dismissible"><p>' . esc_html__( 'Pinpoll requires WordPress 3.3 and PHP 5.5 or higher!', 'pinpoll' ) . '</p> </div></div>';
	}

	static function compatible_version() {
	  if ( version_compare( PHP_VERSION, '5.5', '<' ) ) {
      return false;
    }
    if ( version_compare( $GLOBALS['wp_version'], '3.3', '<' ) ) {
			return false;
		}
		return true;
	}




    /**
     * Localization TinyMCE
     * Description: Load localization vars in /tinymce/pinpoll_tinymce.js
     */
    function pinpoll_tinyMCE_translation() {
      $texts = pinpoll_get_tinymce_texts();

      ?>
      <script type="text/javascript">
        var ppTinyMCETrans = {
          'insertText' : '<?php printf( $texts['insert'] ); ?>',
          'quickInsertText' : '<?php printf( $texts['quickinsert'] ); ?>',
          'quickInsertLabel' : '<?php printf( $texts['quickinsertlabel'] ) ?>',
          'selectPollText' : '<?php printf( $texts['selectpoll'] ); ?>',
          'selectPollTitle' : '<?php printf( $texts['title'] ); ?>'
        }
      </script>
      <?php
    }

    /**
     * i18n
     * Description: Load translation files
     */
    function pinpoll_load_translation() {
      load_plugin_textdomain( 'pinpoll', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
    }

    /**
     * CSS
     * Description: Load css files
     */
    function load_styles() {
      $version = pinpoll_get_version();
      wp_enqueue_style( 'pp-common-styles', plugins_url( '/css/pinpoll-common-style.css', __FILE__ ), array(), $version );
      wp_enqueue_style( 'pp-fontawesome', plugins_url( '/css/font-awesome.css', __FILE__ ), array(), $version );
      wp_enqueue_style( 'pp-sweetalert', plugins_url( '/css/sweetalert2.min.css', __FILE__ ), array(), $version );
    }

    /**
     * Register Settings
     * Description: Register Pinpoll plugin in admin menu
     */
    function pinpoll_settings() {
      register_setting('pinpoll_settings', 'pinpoll_account');
    }

    /**
     * Register Settings Menu
     * Description: Adds menu navigation to the wordpress sidebar
     */
    function pinpoll_settings_menu() {

      $texts = pinpoll_get_menuslug_texts();

      add_menu_page(
        $texts['title'], //Page Title
        $texts['title'], //Menu Title
        'edit_posts', // User role level
        'dashboard', // Menu slug
        array($this, 'pinpoll_dashboard'), //Callback funtion
        plugins_url( PINPOLL_ICON_URL ), //Icon
        '25.000000000000000002' //Menu position
      );

     add_submenu_page(
        'dashboard', //Super menu slug
        $texts['dashboard'], //Page Title
        $texts['dashboard'], //Menu Title
        'edit_posts', //User role
        'dashboard', //Menu slug
        array($this, 'pinpoll_dashboard') //Callback function
     );

     add_submenu_page(
        'dashboard',
        $texts['polls'],
        $texts['polls'],
        'edit_posts',
        'allpolls',
        array($this, 'pinpoll_list_all_polls')
      );

      add_submenu_page(
        'dashboard',
        $texts['create'],
        $texts['create'],
        'edit_posts',
        'createpoll',
        array($this, 'pinpoll_create_poll')
      );

      add_submenu_page(
        null,
        '',
        '',
        'edit_posts',
        'switchaccount',
        array($this, 'pinpoll_switch_account')
      );

      add_submenu_page(
        'dashboard',
        $texts['settings'],
        $texts['settings'],
        'edit_posts',
        'settings',
        array($this, 'pinpoll_account_status')
      );
    }

    /**
     * Dashboard Page
     * Description: If user clicks on "Dashboard" in admin menu,
     *              pinpoll-dashboard.php file will be shown.
     */
    function pinpoll_dashboard() {
      require_once('admin/pinpoll-dashboard.php');
    }

   /**
    * Account Status Page
    * Description: If user clicks on "Dashboard" in admin menu,
    *              pinpoll-account-status.php file will be shown.
    */
    function pinpoll_account_status() {
      require_once('admin/pinpoll-account-status.php');

    }

   /**
    * Polls Page
    * Description: If user clicks on "Dashboard" in admin menu,
    *              pinpoll-list-polls.php file will be shown.
    */
    function pinpoll_list_all_polls() {
      require_once('admin/pinpoll-all-polls.php');
    }

   /**
    * Create Poll Page
    * Description: If user clicks on "Dashboard" in admin menu,
    *              pinpoll-create-poll.php file will be shown.
    */
    function pinpoll_create_poll() {
      require_once('admin/pinpoll-create-poll.php');
    }

   /**
    * Switch Account Page
    * Description: If user clicks on "Dashboard" in admin menu,
    *              pinpoll-switch-account.php file will be shown.
    */
    function pinpoll_switch_account() {
      require_once('admin/pinpoll-switch-account.php');
    }

    /**
     * Helper Method Activation
     * Description: Add option to wp_options which helps to determine if the
     *              plugin is activated the first time.
     */
    function pinpoll_on_activation() {
      self::activation_check();
      add_option('pinpoll_plugin_on_activation', 'yes');
      add_option('pinpoll_account', array(
        'email' => '',
        'appkey' => ''
      ));
      add_option('pinpoll_jwt', '');
      add_option('pinpoll_feedback', array(
        'date' => '',
        'pollCreated' => ''
      ));
    }

    /**
     * Redirect to Pinpoll Settings
     * Desription: Redirects to the plugin page
     *             if pinpoll plugin is activated the first time
     */
    function init() {
      if(get_option('pinpoll_plugin_on_activation') == 'yes') {
        //$this->init_db(); no longer needed
        update_option('pinpoll_plugin_on_activation', 'no');
        wp_redirect(PINPOLL_SETTINGS_URL);
      }
    }

    /**
     * Shortcode Handling
     * Description: Handle shortcode [pinpoll] in posts and pages.
     *              Html code will be generated instead of the tag.
     *
     * @param  array $atts attributes of tag pinpoll
     * @return html        poll
     */
    function pinpoll_shortcode_handler( $atts, $content = null ) {
      if (!empty($atts)) {
        $a = shortcode_atts(array(
            'id' => '404'
          ), $atts
        );
        $id = $a['id'];
      }else if (!empty($content)) {
        $matches = array();
        preg_match('/\/embed\/(\d+)/', $content, $matches);
        $id = $matches[1];
      }
      ob_start();
      ?>
      <div data-pinpoll-id="<?php printf( $id ); ?>"></div>
      <?php
      return ob_get_clean();
    }

    /**
     * Init TinyMCE Buttons
     * Description: Add TinyMCE buttons in editor if user can edit_posts
     *              and pages
     */
    function pinpoll_tinymce_button() {
      if( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
        add_filter('mce_buttons', array($this, 'pinpoll_register_tinymce_button'));
        add_filter('mce_external_plugins', array($this, 'pinpoll_add_tinymce_button'));
      }
    }

    /**
     * Register TinyMCE Buttons
     * Description: Add TinyMCE Buttons in the button array of wordpress editor
     *
     * @param  array  $buttons  tinymce buttons
     * @return array  $buttons  tinymce buttons included pinpoll
     */
    function pinpoll_register_tinymce_button( $buttons ) {
      array_push( $buttons, 'pinpollButton');
      return $buttons;
    }

    /**
     * Add TinyMCE Buttons
     * Description: Adding pinpoll-tinymce.js file, that js file is registered
     *
     * @param   array   $plugin_array   plugin array
     * @return  array   $plugin_array   plugin_array
     */
    function pinpoll_add_tinymce_button( $plugin_array ) {
      $plugin_array['pinpoll_button_script'] = plugins_url( '/tinymce/pinpoll_tinymce.js', __FILE__ );
      return $plugin_array;
    }
}

$Pinpoll = new Pinpoll();
?>
