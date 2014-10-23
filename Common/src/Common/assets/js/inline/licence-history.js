$(function() {
  "use strict";

  // quick helper to DRY up our definitions a bit
  function checked(fieldset) {
    return function() {
      return OLCS.formHelper.isChecked(fieldset, "question");
    };
  }

  OLCS.cascadeForm({
    form: "form",
    cascade: false,
    rulesets: {
      "current[table]": checked("current"),
      "applied[table]": checked("applied"),
      "refused[table]": checked("refused"),
      "revoked[table]": checked("revoked"),
      "public-inquiry[table]": checked("public-inquiry"),
      "disqualified[table]": checked("disqualified"),
      "held[table]": checked("held")
    }
  });
});
