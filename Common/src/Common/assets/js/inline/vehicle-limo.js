$(function() {
  "use strict";

  $(".psv-show-large").show();

  function limoChecked(value) {
    return function() {
      return OLCS.formHelper.isChecked("limousinesNoveltyVehicles", "psvLimousines", value);
    };
  }

  function show15g() {
    return function() {
      return OLCS.formHelper.isChecked("limousinesNoveltyVehicles", "psvLimousines", 'Y');
    }
  }

  OLCS.cascadeForm({
    cascade: false,
    rulesets: {
      "limousinesNoveltyVehicles": {
        "label:limousinesNoveltyVehicles\\[psvNoLimousineConfirmationLabel\\]": limoChecked("N"),
        "date:limousinesNoveltyVehicles\\[psvNoLimousineConfirmation\\]": limoChecked("N"),
        "label:limousinesNoveltyVehicles\\[psvOnlyLimousinesConfirmationLabel\\]": show15g(),
        "date:limousinesNoveltyVehicles\\[psvOnlyLimousinesConfirmation\\]": show15g(),
      },
    }
  });
});
