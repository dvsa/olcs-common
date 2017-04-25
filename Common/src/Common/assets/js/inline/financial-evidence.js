$(function() {
  "use strict";

  var F = OLCS.formHelper;

  function willUpload() {
    return F.isChecked("evidence", "uploadNow", "1");
  }

  function willPost() {
      return F.isChecked("evidence", "uploadNow", "0");
  }

  OLCS.cascadeForm({
    cascade: false,
    rulesets: {
      "evidence": {
        "selector:#files": willUpload,
        "selector:#uploadedFileCount": willUpload // show/hide the validation error as well
      },
      "sendByPost": willPost
    }
  });
});
