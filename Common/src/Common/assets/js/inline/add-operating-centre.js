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

  /**
   * Horrible POC async file upload
   */
  var handleResponse = OLCS.normaliseResponse(function(response) {
    var y = $(".modal__wrapper").scrollTop();
    F.render(".modal__content", response.body);
    $(".modal__wrapper").scrollTop(y);
  });

  $(".file-upload").on("change", function(e) {
    e.preventDefault();
    e.stopPropagation();

    // @TODO multiple?
    var file = e.target.files[0];

    var xhr = new XMLHttpRequest();

    var elem = $("[value=Upload]");
    elem.val("Uploading…");

    /*
    xhr.upload.addEventListener("progress", function(e) {
      var pc = Math.round((e.loaded * 100) / e.total);
      if (pc !== 100) {
        elem.val(pc);
      } else {
        elem.val("Processing…");
      }
    });
    */

    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4) {
        OLCS.preloader.hide();
        handleResponse(xhr.responseText);
      }
    };

    // make sure we take the form data as it stands
    var fd = new FormData($("form").get(0));

    // @TODO calculate from what was clicked
    fd.append("advertisements[file][file-controls][file]", file);
    fd.append("advertisements[file][file-controls][upload]", "Upload");

    xhr.open("POST", $("form").attr("action"), true);  // @TODO confirm third param
    xhr.setRequestHeader("X-Inline-Upload", true);
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xhr.send(fd);
    OLCS.preloader.show();
  });

  $(".remove").on("click", function(e) {
    e.preventDefault();

    var button = $(this);
    var form   = $(this).parents("form");

    F.pressButton(form, button);

    OLCS.submitForm({
      form: form,
      success: handleResponse
    });
  });
});
