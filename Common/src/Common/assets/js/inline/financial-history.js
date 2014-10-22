$(function() {
  "use strict";

  // quick helper to DRY up our definitions a bit
  function checked(fieldset, input) {
    return function() {
      return OLCS.formHelper.isChecked("dataLicences" + fieldset, "prev" + input);
    };
  }

  function requiresInformation() {
    return OLCS.formHelper("data").find("[type=radio][value=Y]:checked").length > 0;
  }

  OLCS.cascadeForm({
    form: "form",
    cascade: false,
    rulesets: {
      /**
       * Financial history
       */
      "data": {
        "*": function() {
          return true;
        },
        "selector:.highlight-box": requiresInformation,
        "selector:#file": requiresInformation
      },

      /**
       * Convictions & penalties 
       * @todo this needs moving when we get onto this section
       */
      "table": function() {
        return OLCS.formHelper.isChecked("data", "prevConviction");
      }
    }
  });
});
