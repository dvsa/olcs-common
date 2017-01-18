$(function() {
  "use strict";

  // jshint newcap:false
  var formId = "#OperatingCentre";
  var F = OLCS.formHelper;

  var vehicles = F("data", "noOfVehiclesRequired");
  var trailers = F("data", "noOfTrailersRequired");

  var isVariation = vehicles.data("current");

  function hasAdvertisements() {
    return F.isChecked("advertisements", "adPlaced", "Y");
  }

  function isSendingByPost() {
    return F.isChecked("advertisements", "adPlaced", "N");
  }

  /**
   * lo-fi mechanism to detect if we're a variation or not. No point
   * binding a load of stuff when it's totally irrelevant.
   */
  if (isVariation) {
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
  }

  function variationShowHide() {
    if (vehicles.data("current")) {
      // show the advertisements section if the OC's auth has increased at all
      if (vehicles.val() > vehicles.data("current") || trailers.val() > trailers.data("current")) {
        OLCS.eventEmitter.emit('show:advertisements:*');
        $('[data-group="advertisements"]').show();
      } else {
        OLCS.eventEmitter.emit('hide:advertisements:*');
        $('[data-group="advertisements"]').hide();
      }
    }
  }

  $(window).load(function() {
    variationShowHide();
  })

  $('#noOfVehiclesRequired, #noOfTrailersRequired').change(function() {
    variationShowHide();
  });

  OLCS.cascadeForm({
    form: formId,
    cascade: false,
    rulesets: {
      "advertisements": {
        "label:adPlacedIn": hasAdvertisements,
        ".adPlacedDate": hasAdvertisements,
        ".file-uploader": hasAdvertisements,
        ".ad-send-by-post": isSendingByPost
      }
    }
  });

});
