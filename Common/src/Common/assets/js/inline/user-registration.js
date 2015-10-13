OLCS.ready(function() {
  "use strict";

  function hasValue(fieldset, field, value) {
    return function () {
      return OLCS.formHelper(fieldset, field).filter(":checked").val() === value;
    };
  }

  OLCS.cascadeForm({
    form: "form[method=post]",
    rulesets: {
      "fields": {
        "*": true,
        "licenceNumber": hasValue("fields", "isLicenceHolder", "Y"),
        "organisationName": hasValue("fields", "isLicenceHolder", "N"),
        "#businessType": hasValue("fields", "isLicenceHolder", "N")
      }
    }
  });
});
