$(function() {
  "use strict";

  function hasAdvertisements() {
    return OLCS.formHelper.isChecked("advertisements", "adPlaced");
  }

  OLCS.cascadeForm({
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
