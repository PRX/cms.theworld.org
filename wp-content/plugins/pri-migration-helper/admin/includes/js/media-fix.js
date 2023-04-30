jQuery(document).ready(function ($) {

  const fGetObjSettings = function () {
    return {
      iPaged: parseInt($('#pmh-media-fix-form [name="pmh-media-fix-paged"]').val()),
      iPerPage: parseInt($('#pmh-media-fix-form [name="pmh-media-fix-perpage"]').val()),
      sPerIds: $('#pmh-media-fix-form [name="pmh-media-fix-ids"]').val(),
    }
  }

  var fPrintLog = function (log) {
    console.log('printing log.')

    let sTextAreaVal = $('#pmh-post-worker-logs').val();

    sTextAreaVal = sTextAreaVal + log;

    $('#pmh-post-worker-logs').val(sTextAreaVal);
  }

  const fGetSampleMediaFix = function () {

    const ObjSettings = fGetObjSettings();

    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: {
        action: 'media_fix_sample',
        i_paged: ObjSettings.iPaged,
        i_perpage: ObjSettings.iPerPage,
        s_per_ids: ObjSettings.sPerIds,
      },
      dataType: "JSON",
      success: function (response) {
        console.log(arguments);
        fPrintLog(response.log);
      }
    });
  }

  const fRunMediaFix = function () {

    const ObjSettings = fGetObjSettings();

    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: {
        action: 'media_fix_run',
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
          fRunMediaFix();
        }
      }
    });
  }

  const fRunPostsFix = function () {

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
        }
      }
    });
  }

  $('#pmh-post-worker-media-sample').on('click', function (e) {

    e.preventDefault();

    fGetSampleMediaFix();
  });

  $('#pmh-post-worker-media-fix').on('click', function (e) {

    e.preventDefault();

    fRunMediaFix();
  });

  $('#pmh-post-worker-posts-fix').on('click', function (e) {

    e.preventDefault();

    fRunPostsFix();
  });
});
