$(function () {
  "use strict";
//
  var authorityRequested = OLCS.formHelper('authority-requested', 'authorityRequested');

  OLCS.cascadeForm({
    cascade: false,
    rulesets: {
      "interim": {
        "#applicationInterimReason": function () {
          var check = OLCS.formHelper.isChecked("interim", "interim");
          $("#applicationInterimReason").parents('.validation-wrapper').toggle(check);
          return check
        },
        ".interimFee": function () {
          return OLCS.formHelper.isChecked("interim", "interim");
        },
        "#application-interim-reason": function() {
          return OLCS.formHelper.isChecked("interim", "interim");
        },

          ".typeOfLicence-guidance-restricted": function () {
            var check = OLCS.formHelper.isChecked("interim", "interim");
            $("#applicationInterimReason").parents('.validation-wrapper').toggle(check);
            return check;
          },
        "#interimFee": function ()  {
          var check = OLCS.formHelper.isChecked("interim", "interim");
          $("#applicationInterimReason").parents('.validation-wrapper').toggle(check);
          return check;
        },
        }
      }
  });
});
