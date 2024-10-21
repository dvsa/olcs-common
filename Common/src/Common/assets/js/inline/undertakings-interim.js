$(function () {
  "use strict";

  OLCS.cascadeForm({
    cascade: false,
    rulesets: {
      "interim": {
        "#applicationInterimReason": function () {
          var check = OLCS.formHelper.isChecked("interim", "goodsApplicationInterim");
          $("#applicationInterimReason").parents('.govuk-form-group--error').toggle(check);
          return check
        },
        ".interimFee": function () {
          return OLCS.formHelper.isChecked("interim", "goodsApplicationInterim");
        },
        "#application-interim-reason": function() {
          return OLCS.formHelper.isChecked("interim", "goodsApplicationInterim");
        },

          ".typeOfLicence-guidance-restricted": function () {
            var check = OLCS.formHelper.isChecked("interim", "goodsApplicationInterim");
            $("#applicationInterimReason").parents('.govuk-form-group--error').toggle(check);
            return check;
          },
        "#interimFee": function ()  {
          var check = OLCS.formHelper.isChecked("interim", "goodsApplicationInterim");
          $("#applicationInterimReason").parents('.govuk-form-group--error').toggle(check);
          return check;
          },
        }
      }
  });
});
