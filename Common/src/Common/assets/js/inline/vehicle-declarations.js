$(function () {
  "use strict";

  // Global variables
  var allowSubmit = false;
  var activeElements = []; // Will hold the currently active steps for the selected vehicle size
  var formStage = 0; // Current step index

  // Initial setup based on selected vehicle size
  updateVehicleSize();

  // === Event Listeners ===

  // When vehicle size radio button changes, update steps shown
  $('input[name="psvVehicleSize[size]"]').change(function () {
    updateVehicleSize();
  });

  // Handle Save and Continue button click
  $("button[id=\"form-actions[saveAndContinue]\"]").on("click", function () {
    if (formStage < activeElements.length - 1) {
      formStage++;
      showElementMap(activeElements);
    }
    // Otherwise, allow form submission (handled separately)
  });

  // Handle Save button click — redirect back to overview
  $("button[id=\"form-actions[save]\"]").on("click", function () {
    returnToOverview();
  });

  // Handle the Back link
  $("a.govuk-back-link").on("click", function (e) {
    e.preventDefault();

    // If we're still on the vehicle size selector, go back to overview
    if (!$("fieldset[name=\"psvVehicleSize\"]").is(":hidden")) {
      returnToOverview();
    }

    // If we're on a later stage, move back one step
    if (formStage > 0) {
      formStage--;
      showElementMap(activeElements);
    }
    // If we're on the first stage of the form (not vehicle size), reset to start
    else if (formStage === 0) {
      var formStages = activeElements[formStage];
      for (var i = 0; i < formStages.length; i++) {
        $(formStages[i]).hide();
      }

      $("fieldset[name=\"psvVehicleSize\"]").show();
      $("input[name=\"psvVehicleSize[size]\"]").prop("checked", false);
    }
  });

  // Prevent form submission unless allowSubmit is true
  $("#lva-vehicles-declarations").on("submit", function (e) {
    if (!allowSubmit) {
      e.preventDefault();
    }
  });

  // === Map of what elements to show for each vehicle size and step ===
  var elementVisabilityMaps = {
    "psvvs_small": [[".psv-small-vh-section", ".psv-show-small"], [], []],
    "psvvs_medium_large": [[], [], []],
    "psvvs_both": [[], [], []]
  };

  // === Functions ===

  // Decide which steps to show based on selected vehicle size
  function updateVehicleSize() {
    var selectedSize = getVehicleSize();

    switch (selectedSize) {
      case "psvvs_small":
        // Hide irrelevant elements
        $(".psv-show-large").hide();
        $(".psv-show-both").hide();
        $("fieldset[name=\"psvVehicleSize\"]").hide();
        $(".psv-small-vh-section").parent().hide();

        // Set the correct step list and show first step
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
        // Fallback — hide all
        $(".psv-show-small, .psv-show-large, .psv-show-both").hide();
    }
  }

  // Show the current step and hide the previous one (if any)
  function showElementMap(elementMap) {
    if (formStage > 0) {
      console.log("Hiding form stage: " + (formStage - 1));
      hideElements(elementMap[formStage - 1]);
    }

    console.log("Showing form stage: " + formStage);
    showElements(elementMap[formStage]);
  }

  // Utility to hide a group of elements
  function hideElements(elements) {
    for (var i = 0; i < elements.length; i++) {
      $(elements[i]).hide();
    }
  }

  // Utility to show a group of elements
  function showElements(elements) {
    for (var i = 0; i < elements.length; i++) {
      $(elements[i]).show();
    }
  }

  // Redirect back to previous page (removes "vehicles-declarations" from the URL)
  function returnToOverview() {
    var currentUrl = window.location.href;
    var newUrl = currentUrl.replace("/vehicles-declarations/", "");
    window.location.href = newUrl;
  }

  // Get the selected vehicle size value
  function getVehicleSize() {
    return $("input[name=\"psvVehicleSize[size]\"]:checked").val();
  }

  // Check if selected size is "small"
  function isVehicleSizeSmall() {
    return getVehicleSize() === "psvvs_small";
  }

  // Helper function for limo confirmation rule (value is 'Y' or 'N')
  function limoChecked(value) {
    return function () {
      return OLCS.formHelper.isChecked("limousinesNoveltyVehicles", "psvLimousines", value);
    };
  }

  // Helper for handling small vehicle intention logic
  function smallOperation(answer) {
    return function () {
      var elem = OLCS.formHelper("smallVehiclesIntention", "psvOperateSmallVhl");
      if (elem.length === 0) {
        return true;
      }
      return OLCS.formHelper.isChecked("smallVehiclesIntention", "psvOperateSmallVhl", answer);
    };
  }

  // Show specific fields (15g) only when vehicle size is NOT small and limousines is 'Y'
  function show15g() {
    return function () {
      return !isVehicleSizeSmall() &&
        OLCS.formHelper.isChecked("limousinesNoveltyVehicles", "psvLimousines", "Y");
    };
  }

  // Cascading logic for conditionally showing/hiding related fields
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
