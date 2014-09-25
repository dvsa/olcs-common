$(function() {
  "use strict";

  function showTables() {
    return OLCS.formHelper("data", "hasEnteredReg")
    .filter(":checked")
    .val() === "Y";
  }

  OLCS.cascadeForm({
    form: "form",
    cascade: false,
    rulesets: {
      "small": showTables,
      "medium": showTables,
      "large": showTables
    }
  });
});
