$(function() {
  "use strict";

    updateVehicleSize()

    $('input[name="psvVehicleSize[size]"]').change(function() {
      updateVehicleSize();
    })

    function updateVehicleSize() {
      switch (getVehicleSize()) {
        case "psvvs_small":
          $(".psv-show-large").hide();
          $(".psv-show-both").hide();
          $(".psv-show-small").show();
          break;
        case "psvvs_medium_large":
          $(".psv-show-small").hide();
          $(".psv-show-both").hide();
          $(".psv-show-large").show();
          break;
        case "psvvs_both":
          $(".psv-show-small").hide();
          $(".psv-show-large").hide();
          $(".psv-show-both").show();
          break;
        default :
          $(".psv-show-small").hide();
          $(".psv-show-large").hide();
          $(".psv-show-both").hide();
    }
  }

  function getVehicleSize()
  {
    return $('input[name="psvVehicleSize[size]"]:checked').val();
  }

  function isVehicleSizeSmall()
  {
     return getVehicleSize() === "psvvs_small";
  }

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

  function show15g() {
    return function() {
       return !isVehicleSizeSmall() && OLCS.formHelper.isChecked("limousinesNoveltyVehicles", "psvLimousines", 'Y');
    }
  }

  OLCS.cascadeForm({
    cascade: false,
    rulesets: {
      "smallVehiclesIntention": {
        "psvSmallVhlNotes": smallOperation("Y"),
        "psvSmallVhlScotland": smallOperation("N"),
        "psvSmallVhlUndertakings": smallOperation("N"),
        "psvSmallVhlConfirmation": smallOperation("N")
      },
      "limousinesNoveltyVehicles": {
        "label:limousinesNoveltyVehicles\\[psvNoLimousineConfirmationLabel\\]": limoChecked("N"),
        "date:limousinesNoveltyVehicles\\[psvNoLimousineConfirmation\\]": limoChecked("N"),

        "label:limousinesNoveltyVehicles\\[psvOnlyLimousinesConfirmationLabel\\]": show15g(),
        "date:limousinesNoveltyVehicles\\[psvOnlyLimousinesConfirmation\\]": show15g(),
      },
    }
  });
});