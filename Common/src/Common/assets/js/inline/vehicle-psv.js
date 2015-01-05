$(function() {
  "use strict";

  function showTables() {
    return OLCS.formHelper.isChecked("data", "hasEnteredReg") || !OLCS.formHelper.findInput("data", "hasEnteredReg").length;
  }

  OLCS.cascadeForm({
    cascade: false,
    rulesets: {
      "small": showTables,
      "medium": showTables,
      "large": showTables
    }
  });
});
