$(function() {
  "use strict";

  var F = OLCS.formHelper;

  function willUpload() {
    return F.isChecked("evidence", "uploadNow");
  }
  function willPost() {
    return !willUpload();
  }

  OLCS.cascadeForm({
    cascade: false,
    rulesets: {
      "upload": willUpload,
      "sendByPost": willPost
    }
  });
});
