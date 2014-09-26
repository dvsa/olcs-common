$(function() {
  "use strict";

  OLCS.cascadeForm({
    cascade: false,
    rulesets: {
      "table": function() {
        return OLCS.formHelper.isChecked("data", "prevConviction");
      }
    }
  });
});
