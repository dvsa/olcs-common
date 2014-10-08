$(function() {

  "use strict";

  // get a nicer alias for our form helper
  var F = OLCS.formHelper;

  // cache some input lookups
  var niFlag       = F("type-of-licence", "operator-location");
  var operatorType = F("type-of-licence", "operator-type");

  // set up a cascade form with the appropriate rules
  OLCS.cascadeForm({
    form: "form",
    rulesets: {
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
        F("licence-type", "licence-type").first().prop("checked", true);
      }
    }
  });
});
