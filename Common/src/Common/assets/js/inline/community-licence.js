OLCS.ready(function() {
  "use strict";

  if (document.body.className.search("internal") === -1) {
    return;
  }

  var tableSelector = "form [data-group*='table']";

  function checkStatus(allowedStatuses, maxLength) {
    
    if (maxLength === null) {
      maxLength = Infinity;
    }
    
    return function (length, callback, selectedInputs) {
      
      if (length < 1 || length > maxLength) {
        return callback(true);
      }

      var action = $(selectedInputs[0]).data('status');
      
      callback(allowedStatuses.indexOf(action) === -1);
    };
  };

  OLCS.conditionalButton({
    container: tableSelector,
    label: "Void",
    predicate: function (length, callback) {
      callback(length < 1);
    }
  });

  OLCS.conditionalButton({
    container: tableSelector,
    label: "Restore",
    predicate: checkStatus(['cl_sts_withdrawn', 'cl_sts_suspended'])
  });
});
