$(function () {
  "use strict";

  var allowSubmit = true;
  var formStage = parseInt($("#miniStep").val(), 10) || 0;
  var steps = [];

  // === Get selected vehicle size ===
  var initialSize = getVehicleSize();
  steps = getStepsForSize(initialSize);

  // === Initial load ===
  showStep(formStage);

  // === Adjust button text ===
  $("button[id='form-actions[saveAndContinue]']").text("continue");
  $("button[id='form-actions[save]']").text("return to overview");

  // === Save and Continue ===
  $("button[id='form-actions[saveAndContinue]']").on("click", function () {
    if (formStage === 0) {
      var selectedSize = getVehicleSize();

      if (!selectedSize) {
        $("#miniStep").val(0);
        return;
      }

      // Generate step list based on selected size
      steps = getStepsForSize(selectedSize);

      // Clear next step inputs
      if (steps[1]) {
        steps[1].resetFn();
      }

      // Auto-select "No" for small vehicle undertakings
      if (selectedSize === "psvvs_small" || selectedSize === "psvvs_both") {
        $("input[name='smallVehiclesIntention[psvOperateSmallVhl]'][value='N']")
          .prop("checked", true)
          .trigger("change");
      }

      showStep(1);
      return false;
    }

    // Allow form to submit for steps beyond step 1
    $("#miniStep").val(formStage);
  });

  // === Back link ===
  $("a.govuk-back-link").on("click", function (e) {
    e.preventDefault();
    clearValidationErrors();

    if (formStage > 0) {
      showStep(formStage - 1);
    } else {
      returnToOverview();
    }
  });

  // === Save and return ===
  $("button[id='form-actions[save]']").on("click", function (e) {
    e.preventDefault();
    returnToOverview();
  });

  // === Form block ===
  $("#lva-vehicles-declarations").on("submit", function (e) {
    if (!allowSubmit) {
      e.preventDefault();
    }
  });

  // === Step functions ===
  function showStep(index) {
    hideAllFormSteps();
    if (steps[index]) {
      showElements(steps[index].elements);
    }
    formStage = index;
    $("#miniStep").val(index);
    triggerCascade();
  }

  function hideAllFormSteps() {
    for (var i = 0; i < steps.length; i++) {
      hideElements(steps[i].elements);
    }
    $(".psv-operate-small-vh-section").parent().hide();
  }

  function hideElements(elements) {
    for (var i = 0; i < elements.length; i++) {
      $(elements[i]).hide();
    }
  }

  function showElements(elements) {
    for (var i = 0; i < elements.length; i++) {
      $(elements[i]).show();
    }
  }

  function getVehicleSize() {
    return $("input[name='psvVehicleSize[size]']:checked").val();
  }

  function returnToOverview() {
    var currentUrl = window.location.href;
    window.location.href = currentUrl.replace("/vehicles-declarations/", "");
  }

  function clearValidationErrors() {
    $(".validation-summary").remove();
    $(".validation-wrapper").removeClass("validation-wrapper");
    $(".govuk-error-message").remove();
  }

  function triggerCascade() {
    $("input[type='radio']:checked, input[type='checkbox'], select").each(function () {
      $(this).trigger("change");
    });
  }

  // === Dynamic steps based on vehicle size ===
  function getStepsForSize(size) {
    switch (size) {
      case "psvvs_small":
        return [
          {
            name: "vehicleSize",
            elements: ["fieldset[name='psvVehicleSize']"],
            resetFn: function () {}
          },
          {
            name: "smallVehicleDetails",
            elements: [
              "fieldset[name='smallVehiclesIntention']",
              "fieldset[name='limousinesNoveltyVehicles']"
            ],
            resetFn: function () {
              // Clear values for step 2 inputs
              $("fieldset[name='smallVehiclesIntention'] input[type='checkbox']").prop("checked", false);
              $("fieldset[name='limousinesNoveltyVehicles'] input[type='radio']").prop("checked", false);
            }
          }
        ];
      case "psvvs_medium_large":
        return [
          {}
        ];
      case "psvvs_both":
        return [
          {},
        ];
      default:
        return [
          {
            name: "vehicleSize",
            elements: ["fieldset[name='psvVehicleSize']"],
            resetFn: function () {}
          }
        ];
    }
  }

  // === Cascade Rules ===
  // might need to changed depending on vehicle size
  function limoChecked(value) {
    if (getVehicleSize() === "psvvs_small") {
      return false;
    }
  }

  function smallOperation(answer) {
    return function () {
      var elem = OLCS.formHelper("smallVehiclesIntention", "psvOperateSmallVhl");
      if (elem.length === 0) {
        return true;
      }
      return OLCS.formHelper.isChecked("smallVehiclesIntention", "psvOperateSmallVhl", answer);
    };
  }

  function show15g() {
    return function () {
      return getVehicleSize() !== "psvvs_small" &&
        OLCS.formHelper.isChecked("limousinesNoveltyVehicles", "psvLimousines", "Y");
    };
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
        "date:limousinesNoveltyVehicles\\[psvOnlyLimousinesConfirmation\\]": show15g()
      }
    }
  });
});
