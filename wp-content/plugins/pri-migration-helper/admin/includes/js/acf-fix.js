function updateAcfFieldSelect() {
  const typeSelect = document.getElementById("pmh-acf-fix-post-type");
  const acfFieldSelect = document.getElementById("pmh-acf-field");
  const metaKeys = ajax_object.pri_fields;

  console.log(metaKeys);

  // Clear the acfFieldSelect options
  acfFieldSelect.innerHTML = "";

  // Populate the acfFieldSelect with the corresponding sub array keys
  for (let key in metaKeys[typeSelect.value]) {
    let option = document.createElement("option");
    option.value = key;
    option.text = key;
    acfFieldSelect.add(option);
  }
}

jQuery(document).ready(function ($) {
  const fGetObjSettings = function () {
    return {
      iPaged: parseInt($('#pmh-acf-fix-form [name="pmh-acf-fix-paged"]').val()),
      iPerPage: parseInt(
        $('#pmh-acf-fix-form [name="pmh-acf-fix-perpage"]').val()
      ),
      sPerIds: $('#pmh-acf-fix-form [name="pmh-acf-fix-ids"]').val(),
      sPostType: $('#pmh-acf-fix-form [name="pmh-acf-fix-post-type"]').val(),
      sField: $('#pmh-acf-fix-form [name="pmh-acf-field"]').val(),
    };
  };

  var fPrintLog = function (log) {
    console.log("printing log.");

    let sTextAreaVal = $("#pmh-post-worker-logs").val();

    sTextAreaVal = sTextAreaVal + log;

    $("#pmh-post-worker-logs").val(sTextAreaVal);
  };

  const fRunAcfFix = function (callBack) {
    const ObjSettings = fGetObjSettings();

    $.ajax({
      type: "POST",
      url: ajax_object.ajax_url,
      data: {
        action: "acf_fix_run",
        i_paged: ObjSettings.iPaged,
        i_perpage: ObjSettings.iPerPage,
        s_per_ids: ObjSettings.sPerIds,
        s_post_type: ObjSettings.sPostType,
        s_field: ObjSettings.sField,
      },
      dataType: "JSON",
      success: function (response) {
        console.log(arguments);
        fPrintLog(response.log);

        if (response.next_paged_process) {
          $('#pmh-acf-fix-form [name="pmh-acf-fix-paged"]').val(
            response.next_paged_process
          );
          fRunAcfFix();
        } else {
          if (typeof callBack === 'function') {
            callBack();
          }
        }
      },
    });
  };

  updateAcfFieldSelect();

  $('#pmh-acf-fix-form').on('process-start', function (e) {
    // $('#pmh-acf-fix-form [name="pmh-acf-fix-paged"]').attr('readonly', 'readonly');
  });

  $('#pmh-acf-fix-form').on('process-stop', function (e) {
    // $('#pmh-acf-fix-form [name="pmh-acf-fix-paged"]').removeAttr('readonly');
  });

  $("#pmh-post-worker-acf-fix").on("click", function (e) {
    e.preventDefault();

    $('#pmh-acf-fix-form').trigger('process-start');

    fRunAcfFix(function () {

      $('#pmh-acf-fix-form').trigger('process-stop');
    });
  });
});
