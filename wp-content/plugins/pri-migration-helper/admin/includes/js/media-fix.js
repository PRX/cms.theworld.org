jQuery(document).ready(function ($) {

  const fGetObjSettings = function () {
    return {
      iPaged: parseInt($('#pmh-media-fix-form [name="pmh-media-fix-paged"]').val()),
      iPerPage: parseInt($('#pmh-media-fix-form [name="pmh-media-fix-perpage"]').val()),
      sPerIds: $('#pmh-media-fix-form [name="pmh-media-fix-ids"]').val(),
      iLastId: $('#pmh-media-fix-form [name="pmh-media-last-id"]').val(),
    }
  }

  var fPrintLog = function (log) {
    console.log('printing log.')

    let sTextAreaVal = $('#pmh-post-worker-logs').val();

    sTextAreaVal = sTextAreaVal + log;

    $('#pmh-post-worker-logs').val(sTextAreaVal);
  }

  const fGetSampleMediaFix = function (callBack) {

    const ObjSettings = fGetObjSettings();

    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: {
        action: 'media_fix_sample',
        i_paged: ObjSettings.iPaged,
        i_perpage: ObjSettings.iPerPage,
        s_per_ids: ObjSettings.sPerIds,
        i_last_id: ObjSettings.iLastId,
      },
      dataType: "JSON",
      success: function (response) {
        console.log(arguments);
        fPrintLog(response.log);
        if (typeof callBack === 'function') {
          callBack();
        }
      }
    });
  }

  const fRunMediaFix = function (callBack) {

    const ObjSettings = fGetObjSettings();

    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: {
        action: 'media_fix_run',
        i_paged: ObjSettings.iPaged,
        i_perpage: ObjSettings.iPerPage,
        s_per_ids: ObjSettings.sPerIds,
        i_last_id: ObjSettings.iLastId,
      },
      dataType: "JSON",
      success: function (response) {
        console.log(arguments);
        fPrintLog(response.log);

        if (response.last_media_id) {
          $('#pmh-media-fix-form [name="pmh-media-last-id"]').val(response.last_media_id);
        }

        if (response.next_paged_process) {
          $('#pmh-media-fix-form [name="pmh-media-fix-paged"]').val(response.next_paged_process);
          fRunMediaFix();
        } else {
          if (typeof callBack === 'function') {
            callBack(response);
          }
        }
      }
    });
  }

  const fRunPostsFix = function (callBack) {

    const ObjSettings = fGetObjSettings();

    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: {
        action: 'posts_fix_run',
        i_paged: ObjSettings.iPaged,
        i_perpage: ObjSettings.iPerPage,
        s_per_ids: ObjSettings.sPerIds,
      },
      dataType: "JSON",
      success: function (response) {
        console.log(arguments);
        fPrintLog(response.log);

        if (response.next_paged_process) {
          $('#pmh-media-fix-form [name="pmh-media-fix-paged"]').val(response.next_paged_process);
          fRunPostsFix();
        } else {
          if (typeof callBack === 'function') {
            callBack(response);
          }
        }
      }
    });
  }

  $('#pmh-media-fix-form').on('process-start', function (e) {
    // $('#pmh-media-fix-form [name="pmh-media-fix-paged"]').attr('readonly', 'readonly');

    const ObjSettings = fGetObjSettings();

    if ('' != ObjSettings.sPerIds) {
      $('#pmh-media-fix-form [name="pmh-media-fix-ids"]').attr('readonly', true);
    }
  });

  $('#pmh-media-fix-form').on('process-stop', function (e) {
    // $('#pmh-media-fix-form [name="pmh-media-fix-paged"]').removeAttr('readonly');
  });

  $('#pmh-media-fix-form').on('process-media-finished', function (e) {
    const ObjSettings = fGetObjSettings();
    if ('' === ObjSettings.sPerIds) {
      $('#pmh-post-worker-posts-fix').removeAttr('disabled');
    }
  });

  $('#pmh-post-worker-media-sample').on('click', function (e) {

    $('#pmh-media-fix-form').trigger('process-start');

    e.preventDefault();

    fGetSampleMediaFix(function () {

      $('#pmh-media-fix-form').trigger('process-stop');
    });
  });

  $('#pmh-post-worker-media-fix').on('click', function (e) {

    $('#pmh-media-fix-form').trigger('process-start');

    e.preventDefault();

    fRunMediaFix(function (response) {

      if (response.count_processed === 0) {
        $('#pmh-media-fix-form').trigger('process-media-finished');
      }

      $('#pmh-media-fix-form').trigger('process-stop');
    });
  });

  $('#pmh-post-worker-posts-fix').on('click', function (e) {

    $('#pmh-media-fix-form').trigger('process-start');

    e.preventDefault();

    fRunPostsFix(function (response) {

      if (response.count_processed === 0) {
        $('#pmh-media-fix-form').trigger('process-media-finished');
      }

      $('#pmh-media-fix-form').trigger('process-stop');
    });
  });
});
