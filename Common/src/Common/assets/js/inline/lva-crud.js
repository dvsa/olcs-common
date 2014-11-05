/**
 * @NOTE This could potentially be DRYed up, as this is going to be fairly common behaviour throughout LVA
 */
OLCS.ready(function() {
  "use strict";

  OLCS.conditionalButton({
    container: 'form [data-group*="table"]',
    label: 'Edit',
    predicate: function (length, callback) {
      callback(length != 1);
    }
  });

  OLCS.conditionalButton({
    container: 'form [data-group*="table"]',
    label: 'Delete',
    predicate: function (length, callback) {
      callback(length < 1);
    }
  });
});
