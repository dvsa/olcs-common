$(function() {
  "use strict";

  OLCS.cascadeForm({
    cascade: false,
    form: "#lva-safety-providers",
    rulesets: {
      'not-applicable' : {
        ".hint": function() {
          return OLCS.formHelper.isChecked("data", "isExternal");
        }
      },
    }
  });
});