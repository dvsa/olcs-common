$(function() {
  "use strict";

  function limoChecked(value) {
    return function() {
      return OLCS.formHelper.isChecked("limousinesNoveltyVehicles", "psvLimousines", value);
    };
  }

  OLCS.cascadeForm({
    cascade: false,
    rulesets: {
      /**
       * @TODO as part of https://jira.i-env.net/browse/OLCS-4319
       *
       * This isn't as simple as you'd think because the fields which need
       * hiding all live in the same fieldset, whereas cascadeForm was built
       * to satisfy the use case where related inputs are grouped into related fieldsets
       *
      "smallVehiclesIntention": {
        "psvSmallVhlNotes": function() {
          return OLCS.formHelper.isChecked("smallVehiclesIntention", "psvOperateSmallVhl");
        }
      },
      */
      "limousinesNoveltyVehicles": {
        "label:limousinesNoveltyVehicles\\[psvNoLimousineConfirmationLabel\\]": limoChecked("N"),
        "parent:.js-no-confirmation": limoChecked("N"),

        "label:limousinesNoveltyVehicles\\[psvOnlyLimousinesConfirmationLabel\\]": limoChecked("Y"),
        "parent:.js-only-confirmation": limoChecked("Y")
      }
    }
  });
});
