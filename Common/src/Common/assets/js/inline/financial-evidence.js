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
      "evidence": {
        "selector:#files": willUpload
      },
      "sendByPost": willPost
    }
  });
});
