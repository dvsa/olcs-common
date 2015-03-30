OLCS.ready(function() {
  "use strict";

  var tableSelector = "form [data-group*='table']";

  OLCS.conditionalButton({
    container: tableSelector,
    label: "Edit",
    predicate: {
      max: 1,
      allow: ["E", "U", "A"]
    }
  });

  OLCS.conditionalButton({
    container: tableSelector,
    label: "Delete",
    predicate: {
      allow: ["A", "E", "U"]
    }
  });

  OLCS.conditionalButton({
    container: tableSelector,
    label: "Restore",
    predicate: {
      allow: ["C", "D"]
    }
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
