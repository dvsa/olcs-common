$(function() {
  "use strict";

  // quick helper to DRY up our definitions a bit
  function checked(fieldset, input) {
    return function() {
      return OLCS.formHelper("dataLicences" + fieldset, "prev" + input)
      .filter(":checked")
      .val() === "Y";
    };
  }

  OLCS.cascadeForm({
    form: "form",
    rulesets: {
      "table-licences-current": checked("Current", "HasLicence"),
      "table-licences-applied": checked("Applied", "HadLicence"),
      "table-licences-refused": checked("Refused", "BeenRefused"),
      "table-licences-revoked": checked("Revoked", "BeenRevoked"),
      "table-licences-public-inquiry": checked("PublicInquiry", "BeenAtPi"),
      "table-licences-disqualified": checked("Disqualified", "BeenDisqualifiedTc"),
      "table-licences-held": checked("Held", "PurchasedAssets")
    }
  });
});
