/**
 * TinyMCE Javascript Basefile
 *
 * Description: Base file, which enables a button in wordpress editor.
 *
 * @package Pinpoll
 * @subpackage Pinpoll/tinymce
 *
 */

(function($) {
  tinymce.create('tinymce.plugins.PinpollButtons', {
    init : function(editor, url) {
      editor.addButton( 'pinpollButton', {
        title : ppTinyMCETrans.insertText,
        type : 'menubutton',
        image : url + '/pinpoll_wp-icon.png',
        tooltip : ppTinyMCETrans.insertText,
        menu : [
          {
            text : ppTinyMCETrans.quickInsertText,
            onclick : function() {
              editor.windowManager.open({
                title : ppTinyMCETrans.insertText,
                body : [{
                  type : 'textbox',
                  name : 'pollId',
                  label : ppTinyMCETrans.quickInsertLabel,
                }],
                onsubmit : function( e ) {
                  if(e.data.pollId !== '' && !isNaN(e.data.pollId)) {
                    editor.insertContent('[pinpoll id="' + e.data.pollId + '"]');
                  }

                }
              });
            }
          },
          {
            text : ppTinyMCETrans.selectPollText,
            onclick : function( e ) {
              editor.windowManager.open({
                title : ppTinyMCETrans.selectPollTitle,
                file : url + '/pinpoll-tinymce-select-poll.php',
                width: 500,
                height: 300,
                onsubmit : function( e ) {
                  editor.insertContent('[pinpoll id="' + e.data.testId+ '"]');
                },
                classes: 'pp-poll-container',
              },
              {
                editor : editor,
                jquery : $,
                url : url,
                wpWindow : window,
              }
            );
            }
          }
        ]
      });
    },
    createControl : function(n, cm) {
      return null;
    },
  });
  tinymce.PluginManager.add( 'pinpoll_button_script', tinymce.plugins.PinpollButtons );
})(jQuery);
