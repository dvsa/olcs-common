OLCS.ready(function() {

  // jshint newcap:false

  "use strict";

  // get a nicer alias for our form helper
  var F = OLCS.formHelper;

  // change the URL in the browsers history so that they can't get back
  // to 'application/create' using the browser's back button
  if (history.pushState) {
    $(window).on('unload',function () {
      var targetLocation = $("body").data("target");
      if (targetLocation) {
        history.replaceState(null, null, targetLocation+"type-of-licence");
      }
    });
  }

  // @todo Make the cascadeForm component use event delegation and remove this setup function afterwards
  function setupCascade() {

    // cache some input lookups
    var niFlag       = F("type-of-licence", "operator-location");
    var operatorType = F("type-of-licence", "operator-type");

    // set up a cascade form with the appropriate rules
    OLCS.cascadeForm({
      form: "form",
      cascade: false,
      rulesets: {
        "type-of-licence": {
          "selector:.js-difference-guidance": function() {
            return niFlag.filter(":checked").val() === "N";
          }
        },
        // operator location is *always* shown
        "operator-location": true,
        // operator type only shown when location has been completed
        // and value is great britain
        "operator-type": function() {
          return niFlag.filter(":checked").val() === "N";
        },
        // licence type is nested; the first rule defines when to show the fieldset
        // (in this case if the licence is NI or the user has chosen an operator type)
        "licence-type": {
          "*": function() {
            return (
              // NI...
              niFlag.filter(":checked").val() === "Y" ||
              // ... any location checked and any operator type checked
              niFlag.filter(":checked").length && operatorType.filter(":checked").length
            );
          },
          // this rule relates to an element within the fieldset
          "licence-type=ltyp_sr": function() {
            return operatorType.filter(":checked").val() === "lcat_psv";
          }
        }
      },
      submit: function() {
        // if we're not showing operator type yet, select a default so we don't get
        // any backend errors
        if (F("operator-type").is(":hidden")) {
          operatorType.first().prop("checked", true);
        }
        // ditto licence type; what we set here doesn't matter since as soon as the user
        // interacts with the form again we clear these fields
        if (F("licence-type").is(":hidden")) {
          F("type-of-licence", "licence-type").first().prop("checked", true);
        }
      }
    });

    // @todo - integrate below code into above cascadeForm component
    $('#operator-location, #operator-type').find('[type="radio"]').change(function() {
      if (niFlag.filter(':checked').val() === 'N') {
        if (operatorType.filter(':checked').val() === 'lcat_psv') {
            $('#typeOfLicence-hint-goods').hide();
            $('#typeOfLicence-hint-psv').show();
        } 
        else if (operatorType.filter(':checked').val() === 'lcat_gv') {
            $('#typeOfLicence-hint-psv').hide();
            $('#typeOfLicence-hint-goods').show();
        }
      } else {
        $('#typeOfLicence-hint-psv').hide();
        $('#typeOfLicence-hint-goods').show();
      }
    }).change();
    
  }

  setupCascade();

  OLCS.eventEmitter.on("render", function() {
    setupCascade();
  });

});
