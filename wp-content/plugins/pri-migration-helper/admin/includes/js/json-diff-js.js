jQuery(document).ready(function ($) {

  $('#input_json_url_clear').on("click", function (e) {

    e.preventDefault();

    $('#input_json_url_clear').text('Clearing Results . . .');

    $('#json-diff-notice .notice').remove();

    $('#json-diff-form').trigger('json_diff_clear');

    return false;
  });

  $('#json-diff-form').on("submit", function (e) {

    e.preventDefault();

    $('[name="input_json_url_submit"]').val('Comparing . . .');

    $('#json-diff-form').trigger('json_diff_save_rows');
    $('#json-diff-form').trigger('json_diff_check_row');

    return false;
  });


  $('#json-diff-form').on('json_diff_clear', function () {

    $.ajax({
      type: 'post',
      url: ajax_object.ajax_url,
      data: {
        action: 'json_diff_clear',
      },
      success: function (response) {
        jQuery('#json-diff-ajax-return').empty();
        $('#input_json_url_clear').text('Clear Results');
      },
    });
  });


  $('#json-diff-form').on('json_diff_save_rows', function () {

    var
      json_url_1 = $('#json_url_1').val(),
      json_url_2 = $('#json_url_2').val();

    $.ajax({
      type: 'post',
      url: ajax_object.ajax_url,
      data: {
        action: 'json_diff_save_rows',
        url_1: json_url_1,
        url_2: json_url_2,
      }
    });
  });

  $('#json-diff-form').on('json_diff_check_row', function () {

    var
      json_url_1 = $('#json_url_1').val(),
      json_url_2 = $('#json_url_2').val(),
      json_url_row = $('#json_url_row').val(),
      url_1 = json_url_1.split(/\r?\n/),
      url_2 = json_url_2.split(/\r?\n/),
      row = json_url_row ? parseInt(json_url_row) - 1 : 0
      ;

    if (
      row < url_1.length &&
      typeof url_1[row] === 'string' &&
      typeof url_2[row] === 'string'
    ) {

      $.ajax({
        type: 'post',
        url: ajax_object.ajax_url,
        data: {
          action: 'json_diff_process',
          url_1: url_1[row],
          url_2: url_2[row],
          row: row,
        },
        success: function (response) {
          jQuery('#json-diff-ajax-return').prepend(response.html);

          var next_row = parseInt(response.next_row) + 1;

          if (typeof url_1[next_row - 1] === 'string') {
            jQuery('#json_url_row').val(next_row);
            $('#json-diff-form').trigger('json_diff_check_row');
            $('[name="input_json_url_submit"]').val('Comparing row ' + next_row);
          } else {
            jQuery('#json_url_row').val(1);
            //jQuery('#json-diff-ajax-return').prepend($('<tr><td colspan=5 style="background-color: #ffa7a7;">End</td></tr>'));
            jQuery('#json-diff-notice').prepend($('<div class="notice notice-success inline"><p>End</p></div>'));
            $('[name="input_json_url_submit"]').val('Compare');
          }
        },
        error: function (response) {
          jQuery('#json-diff-ajax-return').html('<div">Error</div>');
        },
      });
    }
  });

});
