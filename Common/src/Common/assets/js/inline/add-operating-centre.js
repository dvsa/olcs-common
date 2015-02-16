$(function() {
  "use strict";

  var F = OLCS.formHelper;

  var vehicles = F("data", "noOfVehiclesRequired");
  var trailers = F("data", "noOfTrailersRequired");

  function hasAdvertisements() {
    return F.isChecked("advertisements", "adPlaced");
  }

  OLCS.cascadeForm({
    cascade: false,
    rulesets: {
      "advertisements": {
        "*": function() {

          if (vehicles.data('current')) {
            var increased = vehicles.val() > vehicles.data('current') || trailers.val() > trailers.data('current');

            // @todo this really needs to happen on the onChange for adPlaced, rather than here!
            F.setRadioByValue("advertisements", "adPlaced", increased ? "Y" : "N");

            return increased;
          }

          return true;
        },
        "label:adPlacedIn": hasAdvertisements,
        "label:adPlacedDate": hasAdvertisements,
        "selector:#file": hasAdvertisements
      }
    }
  });
});
