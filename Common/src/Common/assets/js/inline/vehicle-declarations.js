$(function () {
  "use strict";

  updateVehicleSize();

  // Event Listeners
  $('input[name="psvVehicleSize[size]"]').change(function () {
    updateVehicleSize();
  });

  $('button[id="form-actions[saveAndContinue]"]').on('click', function () {
    if (formStage < activeElements.length - 1) {
      formStage++;
      showElementMap(activeElements);
    }
    // else => submit form
  });

  $('button[id="form-actions[save]"]').on("click", function () {
    returnToOverview();
  });

  $("a.govuk-back-link").on("click", function (e) {
    e.preventDefault();

    // If vehicle size selector is NOT hidden, return to overview
    if (!$("fieldset[name='psvVehicleSize']").is(":hidden")) {
      returnToOverview();
    }

    if (formStage > 0) {
      formStage--;
      showElementMap(activeElements);
    } else if (formStage === 0) {
      // Hide previous form stage
      let formStages = activeElements[formStage];
      formStages.forEach(step => {
        $(step).hide();
      });

      // Show vehicle size selector and reset radio buttons
      $("fieldset[name='psvVehicleSize']").show();
      $('input[name="psvVehicleSize[size]"]').prop("checked", false);
    }
  });

  let allowSubmit = false;
  // Disable form submit until we're ready
  $("#lva-vehicles-declarations").on("submit", function (e) {
    if (!allowSubmit) {
      e.preventDefault();
    }
  });

  const elementVisabilityMaps = {
    "psvvs_small": [[".psv-small-vh-section", ".psv-show-small"], [], []],
    "psvvs_medium_large": [[], [], []],
    "psvvs_both": [[], [], []]
  };
  let activeElements = [];
  let formStage = 0;

  function updateVehicleSize() {
    switch (getVehicleSize()) {
      case "psvvs_small":
        // Hide elements in old form that aren't needed
        $(".psv-show-large").hide();
        $(".psv-show-both").hide();
        $("fieldset[name='psvVehicleSize']").hide();
        $(".psv-small-vh-section").parent().hide();

        // Find element map for this case and show elements
        activeElements = elementVisabilityMaps.psvvs_small;
        showElementMap(activeElements);
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
      default:
        $(".psv-show-small").hide();
        $(".psv-show-large").hide();
        $(".psv-show-both").hide();
    }
  }

  function showElementMap(elementMap) {
    // Hide previous form stage
    if (formStage > 0) { // There is a form stage before current
      console.log(`Hiding form stage: ${formStage - 1}`);
      hideElements(elementMap[formStage - 1]);
    }

    // Show current form stage
    console.log(`Showing form stage: ${formStage}`);
    showElements(elementMap[formStage])
  }

  function hideElements(prevElements) {
    prevElements.forEach(prevElement => {
      $(prevElement).hide();
    });
  }

  function showElements(elements) {
    elements.forEach(element => {
      $(element).show();
    });
  }

  function returnToOverview() {
    const currenlUrl = window.location.href;
    const newUrl = currenlUrl.replace("/vehicles-declarations/", "");
    window.location.href = newUrl;
  }

  function getVehicleSize() {
    return $('input[name="psvVehicleSize[size]"]:checked').val();
  }

  function isVehicleSizeSmall() {
    return getVehicleSize() === "psvvs_small";
  }

  function limoChecked(value) {
    return function () {
      return OLCS.formHelper.isChecked("limousinesNoveltyVehicles", "psvLimousines", value);
    };
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
      return !isVehicleSizeSmall() && OLCS.formHelper.isChecked("limousinesNoveltyVehicles", "psvLimousines", 'Y');
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
        "date:limousinesNoveltyVehicles\\[psvOnlyLimousinesConfirmation\\]": show15g(),
      },
    }
  });
});
