OLCS.ready(function() {
  "use strict";

  OLCS.conditionalButton({
    form: '#lva-psv-discs',
    label: 'Replace',
    predicate: function (length, callback) {
      callback(length < 1);
    }
  });

  OLCS.conditionalButton({
    form: '#lva-psv-discs',
    label: 'Void',
    predicate: function (length, callback) {
      callback(length < 1);
    }
  });
});
