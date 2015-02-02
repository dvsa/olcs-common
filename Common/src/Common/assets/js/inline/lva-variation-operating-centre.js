OLCS.ready(function() {
  "use strict";
  
  var tableSelector = "form [data-group*='table']";
  
  function checkAction(allowedActions) {
    return function (length, callback, selectedInputs) {
      if (length < 1) {
        return callback(true);
      }

      var action = $(selectedInputs[0]).data('action');
      
      callback(allowedActions.indexOf(action) === -1);
    };
  };
  
  OLCS.conditionalButton({
    container: tableSelector,
    label: "Edit",
    predicate: checkAction(['E', 'U', 'A'])
  });

  OLCS.conditionalButton({
    container: tableSelector,
    label: "Delete",
    predicate: checkAction(['A', 'E', 'U'])
  });

  OLCS.conditionalButton({
    container: tableSelector,
    label: "Restore",
    predicate: checkAction(['C', 'D'])
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
