<?php
/**
 * Global Functions File
 *
 * Description: Contains functions used throughout this plugin.
 * Notice:      All files are using this config except /tinymce, there you have to set the base url.
 *
 * @package Pinpoll
 * @subpackage Pinpoll/admin/config
 *
 */

/**
 * Returns current plugin version.
 *
 * @return string Plugin version
 */
 function pinpoll_get_version() {
   $plugin_data = get_plugin_data(trailingslashit(dirname(dirname( dirname( __FILE__ )))) .'pinpoll.php', false, false);
   $plugin_version = $plugin_data["Version"];
   return $plugin_version;
 }

function pinpoll_enqueue_script($name, $text= array()) {
  $version = pinpoll_get_version();
  wp_enqueue_script( $name, trailingslashit(plugins_url()).'pinpoll/admin/js/'.$name , array(), $version, true);
  if ($text && count($text)>0) {
    wp_localize_script($name, 'ppTrans', $text );
  }
}

function pinpoll_include_script($name) {
  $version = pinpoll_get_version();
  //$version  = date("ymd-Gis", filemtime(trailingslashit(dirname(dirname( __FILE__ )). 'js/'.$name)));
  return '<script type="text/javascript" src="'.trailingslashit(plugins_url()).'pinpoll/admin/js/'.$name.'?ver='.$version.'"></script>';
}

?>
