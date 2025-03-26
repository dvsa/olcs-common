$(function() {
  "use strict";

  updateVehicleSize()

  $('input[name="psvVehicleSize[size]"]').change(function() {
    smallVehicleNewLogic();
    updateVehicleSize();
  })

  const stepFlows = {
    'psvvs_small': [['.psv-show-small', 'psv-small-vh-section'],[], []],
    'psvvs_medium_large': [[], [], []],
    'psvvs_both': [[], [], []]
  }
  let currentStep = 0;

  function updateVehicleSize() {
    switch (getVehicleSize()) {
      case "psvvs_small":
        // Hide elements in old form that aren't needed
        $(".psv-show-large").hide();
        $(".psv-show-both").hide();
        $("fieldset[name='psvVehicleSize']").hide();
        $('.psv-small-vh-section').parent().hide();
        // $(".psv-small-vh-section").show();
        // $(".psv-show-small").show();

        // Find flow for this case and start flow
        showStep(stepFlows.psvvs_small[currentStep])
        break;
      case "psvvs_medium_large":
        $(".psv-show-small").hide();
        $(".psv-show-both").hide();
        $(".psv-show-large").show();
        // Find flow for this case and start flow
        showStep(stepFlows.psvvs_medium_large[currentStep])
        break;
      case "psvvs_both":
        $(".psv-show-small").hide();
        $(".psv-show-large").hide();
        $(".psv-show-both").show();
        // Find flow for this case and start flow
        showStep(stepFlows.psvvs_both[currentStep])
        break;
      default :
        $(".psv-show-small").hide();
        $(".psv-show-large").hide();
        $(".psv-show-both").hide();
    }
  }

  function showStep(step) {
    step.forEach((className) => {
      $(className).show();
    })
  }

  $('button[id="form-actions[saveAndContinue]"]').on('click', function(e) {
    if(currentStep < 1){
      currentStep ++;
      showStep(currentStep)
    }
    // else => submit form
  });

  $("a.govuk-back-link").on('click', function(e) {
    if(currentStep >= 0){
      currentStep--;
    } else {
      returnToOverview()
    }
  });

  let allowSubmit = false;
  // Disable form submit until we're ready
  $("#lva-vehicles-declarations").on('submit', function(e) {
    if (!allowSubmit){
      e.preventDefault();
    }
  });

  function smallVehicleNewLogic(){
    console.log('hererererer')

    $('button[id="form-actions[save]"]').on('click', function(e) {
      console.log('her44444444')
      returnToOverview();
    })
  }

  function returnToOverview(){
    const currenlUrl = window.location.href;
    const newUrl = currenlUrl.replace('/vehicles-declarations/', '');
    window.location.href = newUrl;
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
