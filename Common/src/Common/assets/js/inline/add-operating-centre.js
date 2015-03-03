$(function() {
  "use strict";

  // jshint newcap:false

  var F = OLCS.formHelper;

  var vehicles = F("data", "noOfVehiclesRequired");
  var trailers = F("data", "noOfTrailersRequired");

  function hasAdvertisements() {
    return F.isChecked("advertisements", "adPlaced");
  }

  OLCS.cascadeForm({
    form: "#OperatingCentre",
    cascade: false,
    rulesets: {
      "advertisements": {
        "*": function() {
          if (vehicles.data("current")) {
            return vehicles.val() > vehicles.data("current") || trailers.val() > trailers.data("current");
          }

          return true;
        },
        "label:adPlacedIn": hasAdvertisements,
        "label:adPlacedDate": hasAdvertisements,
        "selector:#file": hasAdvertisements
      }
    }
  });

  /**
   * lo-fi mechanism to detect if we're a variation or not. No point
   * binding a load of stuff when it's totally irrelevant.
   */
  if (vehicles.data("current")) {
    var showAds = OLCS.eventEmitter.on("show:advertisements:*", function() {
      F.selectRadio("advertisements", "adPlaced", "Y");
    });

    var hideAds = OLCS.eventEmitter.on("hide:advertisements:*", function() {
      F.selectRadio("advertisements", "adPlaced", "N");
    });

    var showModal = OLCS.eventEmitter.on("show:modal", function() {
      F.selectRadio("advertisements", "adPlaced", "N");
    });

    var hideModal = OLCS.eventEmitter.on("hide:modal", function() {
      OLCS.eventEmitter.off("show:advertisements:*", showAds);
      OLCS.eventEmitter.off("hide:advertisements:*", hideAds);
      OLCS.eventEmitter.off("show:modal", showModal);
      OLCS.eventEmitter.off("hide:modal", hideModal);
    });
  }
});
