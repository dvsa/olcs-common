$(function() {
  "use strict";

  function requiresInformation() {
    return OLCS.formHelper("data").find("[value=Y]:checked").length > 0;
  }

  OLCS.cascadeForm({
    cascade: false,
    rulesets: {
      "data": {
        "*": function() {
          return true;
        },
        "selector:.highlight-box": requiresInformation,
        "selector:#file": requiresInformation
      }
    }
  });
});
