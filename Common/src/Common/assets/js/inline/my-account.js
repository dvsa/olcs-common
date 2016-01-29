OLCS.ready(function() {
  "use strict";

  var F = OLCS.formHelper;
  var emailAddressChanged = false;

  F.input("main", "emailAddress").change(function() {
    emailAddressChanged = true;
  });

  function showEmailConfirm() {
    return emailAddressChanged || F.containsErrors(F.fieldset("main"));
  }

  OLCS.cascadeForm({
    form: "form[method=post]",
    rulesets: {
      "main": {
        "*": true,
        "emailConfirm": showEmailConfirm
      }
    }
  });
});
