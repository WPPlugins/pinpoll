<?php
//INCLUDES wp-load to enable wp funtions
include_once( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/wp-load.php' );

/**
 * Classname:   PinpollStoreJWT
 * Description: Helper class to store jwt-token from tinymce popup
 *
 * @package Pinpoll
 * @subpackage Pinpoll/tinymce
 *
 */
class PinpollStoreJWT
{
  //Default construct
  function __construct()
  {
    $this->pinpoll_store_jwt_in_db();
  }

  /**
   * Store JWT
   * Description: Store received JWT in wp_options table
   */
  function pinpoll_store_jwt_in_db() {
    $jwt = isset( $_POST['ppjwt'] ) ? $_POST['ppjwt'] : '';
    if( !empty( $jwt ) ) {
      update_option('pinpoll_jwt', $jwt);
    }
  }
}

$PinpollStoreJWT = new PinpollStoreJWT();

?>
