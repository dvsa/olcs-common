$(function() {
  "use strict";

  function hasAdvertisements() {
    return OLCS.formHelper("advertisements", "adPlaced")
    .filter(":checked")
    .val() === "Y";
  }

  OLCS.cascadeForm({
    form: "form",
    cascade: false,
    rulesets: {
      "advertisements": {
        "*": function() {
          return true;
        },
        "label:adPlacedIn": hasAdvertisements,
        "label:adPlacedDate": hasAdvertisements,
        "selector:#file": hasAdvertisements
      }
    }
  });
});
