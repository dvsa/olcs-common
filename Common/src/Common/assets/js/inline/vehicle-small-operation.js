$(function() {
  "use strict";

  function smallOperation(answer) {
    return function() {
      var elem = OLCS.formHelper("smallVehiclesIntention", "psvOperateSmallVhl");
      if (elem.length === 0) {
        return true;
      }
      return OLCS.formHelper.isChecked("smallVehiclesIntention", "psvOperateSmallVhl", answer);
    };
  }

  OLCS.cascadeForm({
    cascade: false,
    rulesets: {
      "smallVehiclesIntention": {
        "psvSmallVhlConfirmation": smallOperation("N")
      },
    }
  });
});
