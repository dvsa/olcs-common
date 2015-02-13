OLCS.ready(function() {
  "use strict";

  if (document.body.className.search("internal") === -1) {
    return;
  }

  var tableSelector = "form [data-group*='table']";

  OLCS.conditionalButton({
    container: tableSelector,
    label: "Void",
    predicate: function (length, callback) {
      callback(length < 1);
    }
  });
});
