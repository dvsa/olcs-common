$(function() {
  "use strict";

  // quick helper to DRY up our definitions a bit
  function checked(fieldset, input) {
    return function() {
      return OLCS.formHelper.isChecked("dataLicences" + fieldset, "prev" + input);
    };
  }

  function requiresInformation() {
    return OLCS.formHelper("data").find("[value=Y]:checked").length > 0;
  }

  OLCS.cascadeForm({
    form: "form",
    cascade: false,
    rulesets: {
      /**
       * Financial history
       */
      "data": {
        "*": function() {
          return true;
        },
        "selector:.highlight-box": requiresInformation,
        "selector:#file": requiresInformation
      },

      /**
       * Licence history
       */
      "table-licences-current": checked("Current", "HasLicence"),
      "table-licences-applied": checked("Applied", "HadLicence"),
      "table-licences-refused": checked("Refused", "BeenRefused"),
      "table-licences-revoked": checked("Revoked", "BeenRevoked"),
      "table-licences-public-inquiry": checked("PublicInquiry", "BeenAtPi"),
      "table-licences-disqualified": checked("Disqualified", "BeenDisqualifiedTc"),
      "table-licences-held": checked("Held", "PurchasedAssets"),

      /**
       * Convictions & penalties
       */
      "table": function() {
        return OLCS.formHelper.isChecked("data", "prevConviction");
      }
    }
  });
});
