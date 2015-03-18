$(function() {
  "use strict";

  // jshint newcap:false

  var F = OLCS.formHelper;

  var vehicles = F("data", "noOfVehiclesRequired");
  var trailers = F("data", "noOfTrailersRequired");

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

    OLCS.eventEmitter.once("hide:modal", function() {
      OLCS.eventEmitter.off("show:advertisements:*", showAds);
      OLCS.eventEmitter.off("hide:advertisements:*", hideAds);
    });

    /**
     * On first showing the modal, explicitly select 'no' on the ads placed
     * field. This won't fire if the edit/add was fullscreen but that's
     * fine because the cascadeForm will trigger and hide it anyway
     * due to the advertisements:* rule
     *
     * @NOTE: we get lucky here... this JS is only actually *loaded* via a
     * modal in the situation described above. However, the JS is inlined
     * before the show:modal event fires hence why this works, but that's
     * flakey...
     */
    OLCS.eventEmitter.once("show:modal", function() {
      F.selectRadio("advertisements", "adPlaced", "N");
    });
  }

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
});
