OLCS.ready(function() {
  "use strict";
  // @TODO: there is a later story to modalise LVA behaviour
  // internally. For now, the easiest way to suppress it
  // is to short circuit based on the app class. Not pretty,
  // but will be removed early 2015
  if (document.body.className.search("selfserve") === -1) {
    return;
  }

  var tableSelector = "form [data-group*='table']";

  /**
   * Always bind some generic edit and delete buttons as they're
   * common across most (all?) CRUD forms
   */
  OLCS.conditionalButton({
    container: tableSelector,
    label: "Edit",
    predicate: function (length, callback) {
      callback(length !== 1);
    }
  });

  OLCS.conditionalButton({
    container: tableSelector,
    label: "Delete",
    predicate: function (length, callback) {
      callback(length < 1);
    }
  });

  OLCS.crudTableHandler();
});
