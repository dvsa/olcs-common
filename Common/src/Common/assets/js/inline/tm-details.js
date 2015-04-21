OLCS.ready(function() {
  "use strict";

  var F = OLCS.formHelper;

  OLCS.cascadeForm({
    form: "#lva-transport-manager-details",
    cascade: false,
    rulesets: {
      "declarations": {
        // Hide the whole declaration fieldset, until the tmType radio has been checked
        "*": function() {
          return F.isChecked("responsibilities", "tmType", "tm_t_I")
            || F.isChecked("responsibilities", "tmType", "tm_t_E");
        },
        "selector:.tm-details-declaration-internal": function() {
          return F.isChecked("responsibilities", "tmType", "tm_t_I");
        },
        "selector:.tm-details-declaration-external": function() {
          return F.isChecked("responsibilities", "tmType", "tm_t_E");
        }
      }
    }
  });
});
