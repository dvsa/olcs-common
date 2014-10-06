OLCS.ready(function() {
  "use strict";

  OLCS.conditionalButton({
    form: '#application_vehicle-safety_discs-psv_form',
    label: 'Replace',
    predicate: function (length, callback) {
      callback(length < 1);
    }
  });

  OLCS.conditionalButton({
    form: '#application_vehicle-safety_discs-psv_form',
    label: 'Void',
    predicate: function (length, callback) {
      callback(length < 1);
    }
  });
});
