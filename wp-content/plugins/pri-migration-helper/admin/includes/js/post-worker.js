// Object selection listener.
jQuery(document).ready(function ($) {

  $('#pmh-post-worker-object-type').on('change', function (e) {

    e.preventDefault();

    const objectType = $(this).val();

    if (!objectType) return;

    $('#pmh-post-worker-object-name').after('<span>Loading...</span>');

    if (objectType) {

      $.ajax({
        type: 'post',
        url: ajax_object.ajax_url,
        data: {
          action: 'pmh_post_worker_select_object_type',
          objectType: objectType,
        },
        success: function (response) {
          if (response.object_names) {

            $('#pmh-post-worker-object-name').empty();

            $('#pmh-post-worker-object-name').next('span').remove();

            $('#pmh-post-worker-object-name').append(`<option value="">Object names loaded</option>`);

            $.each(response.object_names, function (i, el) {
              $('#pmh-post-worker-object-name').append(`<option value="${el}">${el}</option>`);
            });
          }
        },
        error: function (response) {
          console.log(response);
          $('#pmh-post-worker-object-name').next('span').remove();
        },
      });
    }
  });
});

// Sample selection listener.
jQuery(document).ready(function ($) {

  $('#pmh-post-worker-object-name, #pmh-post-worker-paged-process').on('change', function (e) {

    e.preventDefault();

    const
      objectType = $('#pmh-post-worker-object-type').val(),
      objectName = $('#pmh-post-worker-object-name').val(),
      pagedProcess = $('#pmh-post-worker-paged-process').val()
      ;

    if (!objectName) return;

    $('#pmh-post-worker-sample-table-content').find('tr:gt(0)').remove();
    $('#pmh-post-worker-sample-table-content').append('<tr><td colspan="4">Loading...</td></tr>');

    if (objectName) {

      $.ajax({
        type: 'post',
        url: ajax_object.ajax_url,
        data: {
          action: 'pmh_post_worker_get_sample',
          objectType: objectType,
          objectName: objectName,
          pagedProcess: pagedProcess,
        },
        success: function (response) {
          if (response.samples.length > 0) {

            $('#pmh-post-worker-sample-table-content').find('tr:gt(0)').remove();

            $.each(response.samples, function (i, el) {
              $('#pmh-post-worker-sample-table-content').append(`<tr><td>${el.old_url}</td><td>${el.id}</td><td>${el.type}</td><td>${el.activated}</td></tr>`);
            });
          } else {

            $('#pmh-post-worker-sample-table-content').find('tr:gt(0)').remove();
            $('#pmh-post-worker-sample-table-content').append('<tr><td colspan="4">No post to process</td></tr>');
          }

          if (response.total) {
            $('#pmh-post-worker-process-post-total').val(response.total);
          }
        },
        error: function (response) {
          console.log(response);
          $('#pmh-post-worker-sample-table-content').find('tr:gt(0)').remove();
          $('#pmh-post-worker-sample-table-content').append('<tr><td colspan="4">Error</td></tr>');
        },
      });
    }
  });
});

// Run Process
jQuery(document).ready(function ($) {

  function runProcess() {

    const
      objectType = $('#pmh-post-worker-object-type').val(),
      objectName = $('#pmh-post-worker-object-name').val(),
      pagedProcess = $('#pmh-post-worker-paged-process').val()
      ;

    $.ajax({
      type: 'post',
      url: ajax_object.ajax_url,
      data: {
        action: 'pmh_post_worker_run_process',
        objectType: objectType,
        objectName: objectName,
        pagedProcess: pagedProcess,
      },
      success: function (response) {
        $('#pmh-post-worker-logs').prepend(response.log);
        if (response.next_paged_process) {
          $('#pmh-post-worker-paged-process').val(response.next_paged_process);
          runProcess();
        } else {
          $('#pmh-post-worker-logs').prepend('- Process Finished -');
        }
      },
      error: function (response) {
        console.log(response);
      },
    });
  }

  $('#pmh-post-worker-run-process').on('click', function (e) {

    e.preventDefault();

    runProcess();
  });
});

// Delete Logs.
jQuery(document).ready(function ($) {
  $('#pmh-post-worker-logs-delete').on('click', function (e) {
    e.preventDefault();
    $('#pmh-post-worker-logs').empty();
  });
});
