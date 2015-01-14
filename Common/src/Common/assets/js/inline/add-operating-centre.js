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
            return vehicles.val() > vehicles.data('current') || trailers.val() > trailers.data('current');
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
