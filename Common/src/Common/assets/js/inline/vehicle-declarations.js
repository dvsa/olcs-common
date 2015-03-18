$(function() {
  "use strict";

  function limoChecked(value) {
    return function() {
      return OLCS.formHelper.isChecked("limousinesNoveltyVehicles", "psvLimousines", value);
    };
  }

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
        "psvSmallVhlNotes": smallOperation("Y"),
        "psvSmallVhlScotland": smallOperation("N"),
        "psvSmallVhlUndertakings": smallOperation("N"),
        "psvSmallVhlConfirmation": smallOperation("N"),
        "selector:.js-small-vhl-label": smallOperation("N")
      },
      "limousinesNoveltyVehicles": {
        "label:limousinesNoveltyVehicles\\[psvNoLimousineConfirmationLabel\\]": limoChecked("N"),
        "parent:.js-no-confirmation": limoChecked("N"),

        "label:limousinesNoveltyVehicles\\[psvOnlyLimousinesConfirmationLabel\\]": limoChecked("Y"),
        "parent:.js-only-confirmation": limoChecked("Y")
      }
    }
  });
});
