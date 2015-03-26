OLCS.ready(function() {
  "use strict";

  if (document.body.className.search("internal") === -1) {
    return;
  }

  var tableSelector = "form [data-group*='table']";

  // @TODO: DRY this up with lva-crud-delta; it's
  // a carbon copy except for the data attribute used
  function checkStatus(allowedStatuses, maxLength) {

    if (maxLength === null) {
      maxLength = Infinity;
    }

    return function (length, callback, selectedInputs) {

      if (length < 1 || length > maxLength) {
        return callback(false);
      }

      var action = $(selectedInputs[0]).data("status");

      callback($.inArray(action, allowedStatuses) !== -1);
    };
  }

  OLCS.conditionalButton({
    container: tableSelector,
    label: "Void",
    predicate: function(length, callback) {
      callback(length >= 1);
    }
  });

  OLCS.conditionalButton({
    container: tableSelector,
    label: "Restore",
    predicate: checkStatus(["cl_sts_withdrawn", "cl_sts_suspended"])
  });

  OLCS.conditionalButton({
    container: tableSelector,
    label: "Stop",
    predicate: checkStatus(["cl_sts_active"])
  });

  OLCS.conditionalButton({
    container: tableSelector,
    label: "Reprint",
    predicate: checkStatus(["cl_sts_active"])
  });
});
