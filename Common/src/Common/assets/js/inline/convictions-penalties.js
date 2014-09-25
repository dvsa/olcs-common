$(function() {
  "use strict";

  OLCS.cascadeForm({
    form: "form",
    cascade: false,
    rulesets: {
      "table": function() {
        return OLCS.formHelper("data", "prevConviction")
        .filter(":checked")
        .val() === "Y";
      }
    }
  });
});
