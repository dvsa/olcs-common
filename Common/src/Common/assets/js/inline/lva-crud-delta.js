OLCS.ready(function() {
  "use strict";

  var tableSelector = "form [data-group*='table']";

  function checkAction(allowedActions, maxLength) {

    if (maxLength === null) {
      maxLength = Infinity;
    }

    return function (length, disable, selectedInputs) {

      if (length < 1 || length > maxLength) {
        return disable(true);
      }

      var actions = $.map(selectedInputs, function(input) {
        return $(input).data("action");
      });

      disable(
        // if we have any actions NOT in our allowed list; disable
        $(actions).not(allowedActions).length > 0
      );
    };
  }

  OLCS.conditionalButton({
    container: tableSelector,
    label: "Edit",
    predicate: checkAction(["E", "U", "A"], 1)
  });

  OLCS.conditionalButton({
    container: tableSelector,
    label: "Delete",
    predicate: checkAction(["A", "E", "U"])
  });

  OLCS.conditionalButton({
    container: tableSelector,
    label: "Restore",
    predicate: checkAction(["C", "D"])
  });

  // @TODO: there is a later story to modalise LVA behaviour
  // internally. For now, the easiest way to suppress it
  // is to short circuit based on the app class. Not pretty,
  // but will be removed early 2015
  if (document.body.className.search("selfserve") === -1) {
    return;
  }

  OLCS.crudTableHandler();
});
