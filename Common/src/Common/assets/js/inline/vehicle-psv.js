$(function() {
  "use strict";

  function showTables() {
    return OLCS.formHelper.isChecked("data", "hasEnteredReg");
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
