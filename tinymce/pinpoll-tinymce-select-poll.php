<?php

//INCLUDES wp-load, config, texts
include_once( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/wp-load.php' );
require_once( dirname( dirname( __FILE__ ) ) . '/admin/config/pinpoll-config.php' );
require_once( dirname( dirname( __FILE__ ) ) . '/admin/resources/pinpoll-texts.php' );

/**
 * Classname:   PinpollTinyMCE
 * Description: Contains HTML that is loaded in the tinymce popup window.
 *
 * @package Pinpoll
 * @subpackage Pinpoll/tinymce
 *
 */
class PinpollTinyMCE
{
  //Default construct
  function __construct()
  {
    $this->do_select_poll_page();
  }

  /**
   * Popup Window
   * Description: Show table with search option in popup window
   */
  function do_select_poll_page() {

    $jwt = get_option('pinpoll_jwt');
    $texts = pinpoll_get_tinymce_texts();

    ?>
    <html>
    <head>
      <meta http-equiv="cache control" content="no-cache" />
      <meta http-equiv="expires" content="0" />
      <meta http-equiv="pragma" content="no-cache" />
    </head>
    <body>
      <form>
        <label for="txtSearch"/><?php printf( $texts['search'] ); ?> </label>
        <input type="text" id="txtSearch" name="searchText"></input>
        <button id="pp-button-search" name="btnSearch"><?php printf( $texts['btnsearch'] ) ?></button>
        <div id="pp-search-container">
          <hr>
          <div class="scrollableTable">
            <table id="pollData" class="pollTable">
                <th style="width:10%;"><?php printf( $texts['tableselect'] ); ?></th>
                <th style="width:20%"><?php printf( $texts['tableid'] ); ?></th>
                <th style="width:70%"><?php printf( $texts['tablequestion'] ); ?></th>
            </table>
          </div>
          <hr>
          <input type="submit" id="pp-button-insert" value="<?php printf( $texts['insert'] ); ?>"></input>
        </div>
      </form>
      <script type="text/javascript">
        var ppJwt = '<?php printf( $jwt ); ?>';
        var ppEmptyMessage = '<?php printf( $texts['emptytable'] ); ?>';
        var ppErrorMessage = '<?php printf( $texts['error'] ); ?>';
        var ppSessionExpiredMessage = '<?php printf( $texts['sessionexpired'] ); ?>'
        var ppBaseURL = '<?php printf( PINPOLL_BASE_URL ); ?>';
      </script>
      <script type="text/javascript" src="<?php printf( plugins_url( 'pinpoll_tinymce_table.js', __FILE__ ) ) ?>">
      </script>
      <link rel="stylesheet" href="<?php printf( plugins_url( 'pinpoll-tinymce.css', __FILE__ ) ) ?>" type="text/css" media="all"></link>
    </body>
    </html>
    <?php
  }
}

$PinpollTinyMCE = new PinpollTinyMCE();
?>
