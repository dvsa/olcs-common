$(function() {
  "use strict";

  function showTables() {
    return OLCS.formHelper.isChecked("data", "hasEnteredReg") || !OLCS.formHelper.findInput("data", "hasEnteredReg").length;
  }

  function showNotice() {
    return OLCS.formHelper.findInput("data", "hasEnteredReg").length && !OLCS.formHelper.isChecked("data", "hasEnteredReg");
  }

  OLCS.cascadeForm({
    cascade: false,
    rulesets: {
      "table": showTables,
      "data": {
        "selector:#notice": showNotice
      }
    }
  });
});
