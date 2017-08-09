/**
 * TinyMCE PopUp-Window Javascript File
 *
 * Description: Main Javascript File for the tinymce popup window, which
 *              includes a table with search option for polls.
 *
 * @package Pinpoll
 * @subpackage Pinpoll/tinymce
 *
 */

//GLOBAL VARS

//passed arguments from pinpoll_tinymce.js
var passed_arguments = top.tinymce.activeEditor.windowManager.getParams();
var $ = passed_arguments.jquery;
var jq_context = document.getElementsByTagName("body")[0];
var plugin_url = passed_arguments.url;

//Call Method to disable insert button by default
pinpoll_disable_insert();

/**
 * Listener: Button name=btnSearch
 * Description: If the search button is clicked, this method calls the pinpoll
 *              API v1/polls/datatables with search option, to receive all
 *              polls matching the users input. If success, data will be loaded
 *              in the table.
 *
 * @type {String}
 */
$("button[name='btnSearch']", jq_context).click(function(event) {
  event.preventDefault();

  $("#pp-search-container", jq_context).attr('style', 'display:inline');

  var method = "POST";
  var asynch = true;
  //Get searckey from input field
  var searchkey = $("input[name='searchText']", jq_context).val();
  var bodyData = {
    'columns[0][data]': 'id',
    'columns[0][name]': 'id',
    'columns[0][searchable]': 'true',
    'columns[0][orderable]': 'true',
    'columns[0][search][value]': '',
    'columns[0][search][regex]': 'false',
    'columns[1][data]': 'question',
    'columns[1][name]': 'question',
    'columns[1][searchable]': 'true',
    'columns[1][orderable]': 'true',
    'columns[1][search][value]': '',
    'columns[1][search][regex]': 'false',
    'order[0][column]': '0',
    'order[0][dir]': 'desc',
    'search[value]' : searchkey
  };

  var table = document.getElementById('pollData');

  //API call v1/polls/datatables
  $.ajax({
    url : ppBaseURL + '/polls/datatables',
    type : 'POST',
    headers: {
      'Authorization' : ppJwt,
    },
    data : bodyData,
    dataType : 'json',
    success: function(response) { //success, fill table with data
      $('#pollData tr', jq_context).slice(1).remove();

      if(response['recordsFiltered'] === 0) {
        var row = table.insertRow(1);
        var emptyMessage = row.insertCell(0);
        emptyMessage.setAttribute('colspan', '3');
        emptyMessage.innerHTML = ppEmptyMessage;
      } else {
        $(response['data']).each(function(i){
          var row = table.insertRow(i+1);
          var select = row.insertCell(0);
          var pollId = row.insertCell(1);
          var question = row.insertCell(2);

          select.innerHTML = '<input type="checkbox" name="poll-checkbox" id="poll-checkbox" value="' + response['data'][i]['id'] + '"></input>';
          pollId.innerHTML = response['data'][i]['id'];
          question.innerHTML = response['data'][i]['question'];
        });
        pinpoll_register_click_event();
      }
    },
    error: function(jqXHR, textStatus, errorThrown) { //error, try refresh token
      console.info('In Error Function DATATABLES Level 1');
      $.ajax({
        url : ppBaseURL + '/auth/refresh',
        type : 'POST',
        crossDomain : 'true',
        headers : {
          'Authorization' : ppJwt,
        },
        dataType : 'json',
        success: function (data, textStatus, jqXHR) {
          ppJwt = jqXHR.getResponseHeader('Authorization');

          //saving jwt token in wordpress db
          $.post( plugin_url + '/pinpoll-store-jwt.php', { 'ppjwt' : ppJwt }, function(data) {
            $.ajax({ //do api call again
              url : ppBaseURL + '/polls/datatables',
              type : 'POST',
              headers: {
                'Authorization' : ppJwt,
              },
              data : bodyData,
              dataType : 'json',
              success: function(response) {
                $('#pollData tr', jq_context).slice(1).remove();

                if(response['recordsFiltered'] == 0) {
                  var row = table.insertRow(1);
                  var emptyMessage = row.insertCell(0);
                  emptyMessage.setAttribute('colspan', '3');
                  emptyMessage.innerHTML = ppEmptyMessage;
                } else {
                  $(response['data']).each(function(i){
                    var row = table.insertRow(i+1);
                    var select = row.insertCell(0);
                    var pollId = row.insertCell(1);
                    var question = row.insertCell(2);

                    select.innerHTML = '<input type="checkbox" class="poll-checkbox" id="poll-checkbox" value="' + response['data'][i]['id'] + '"></input>';
                    pollId.innerHTML = response['data'][i]['id'];
                    question.innerHTML = response['data'][i]['question'];
                  });
                  pinpoll_register_click_event();
                }
              },
              error : function() {
                console.info('In Error Function DATATABLES Level 2');
              }
            });

          } ).fail(function() {
            console.info('TOKEN NOT SAVED, storage failed');
          });

        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.info('In Error Function REFRESH');
          if(jqXHR.status === 400 || jqXHR.status === 401) {
            $('#pollData tr', jq_context).slice(1).remove();
            var row = table.insertRow(1);
            var expiredMessage = row.insertCell(0);
            expiredMessage.setAttribute('colspan', '3');
            expiredMessage.setAttribute('style', 'color:red');
            expiredMessage.innerHTML = ppSessionExpiredMessage;
          }

        }
      });
    }
  });

});

/**
 * Error
 * Description: Error function which is used more than one times in ajax posts
 */
function error() {
  var table = document.getElementById('pollData');
  $('#pollData tr', jq_context).slice(1).remove();
  var row = table.insertRow(1);
  var errorMessage = row.insertCell(0);
  errorMessage.setAttribute('colspan', '3');
  errorMessage.setAttribute('style', 'color: red');
  errorMessage.innerHTML = errorMessage;
}

/**
 * Listener: form submit
 * Description: If user clicks "Insert Polls" than the method creates a string,
 *              which are shortcodes, that will be inserted in the editor.
 *
 * @type {Array}
 */
$("form", jq_context).submit(function(event) {
  event.preventDefault();
  var ids = [];
  var count = 0;
  var shortcode = '';

  //collect all votes which are checked
  $("input[type='checkbox']", jq_context).each(function() {
    if(this.checked) {
      ids[count] = $(this).val();
      count++;
    }
  });

  if(ids.length > 1) {
    $(ids).each(function(i) {
      shortcode = shortcode + '[pinpoll id="' + ids[i] + '"]';
    });
  } else {
    shortcode = '[pinpoll id="' + ids[0] + '"]';
  }

  passed_arguments.editor.selection.setContent(shortcode);
  passed_arguments.editor.windowManager.close();
});

/**
 * Disable Button "Insert Poll"
 * Description: Disable button #pp-button-insert by default
 */
function pinpoll_disable_insert() {
  $('#pp-button-insert', jq_context).attr('style', 'display:none');
}

/**
 * Enable Disable Button "Insert Poll"
 * Description: If user choose a poll the button "Insert Poll" will be shown,
 *              otherwise it will be hidden.
 */
$(document).on('change', 'input[type="checkbox"][name="poll-checkbox"]', function() {
  var boxes = document.getElementsByName('poll-checkbox');
  var disabled = true;

  for (var i = 0; i < boxes.length; i++) {
    if(boxes[i].checked) {
      disabled = false;
    }
  }

  if(!disabled) {
    $('#pp-button-insert', jq_context).removeAttr('style');
  } else {
    if(!document.getElementById('pp-button-insert').hasAttribute('style')) {
      $('#pp-button-insert', jq_context).attr('style', 'display:none');
    }
  }
});

/**
 * Register Click Event
 * Description: Register click event, so that a user can select polls on table
 *              row click.
 */
function pinpoll_register_click_event() {
  $('.pollTable tbody tr', jq_context).click(function(evt) {
    if(evt.target.type !== 'checkbox') {
      $(':checkbox', this).trigger('click');
    }
  });
}
