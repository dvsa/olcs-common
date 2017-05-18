$(function() {
  "use strict";

  var F = OLCS.formHelper;

  function willUpload() {
    return F.isChecked("evidence", "uploadNow", "1");
  }

  function willUploadLater() {
    return F.isChecked("evidence", "uploadNow", "2");
  }

  function willPost() {
    return F.isChecked("evidence", "uploadNow", "0");
  }

  OLCS.cascadeForm({
    cascade: false,
    rulesets: {
      "evidence": {
        "#files": willUpload,
        "#uploadedFileCount": willUpload, // show/hide the validation error as well
        ".send-by-post": willPost,
        ".upload-later": willUploadLater
      }
    }
  });
});