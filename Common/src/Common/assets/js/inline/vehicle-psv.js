$(function() {
  "use strict";

  function showTables() {
    return OLCS.formHelper.isChecked("data", "hasEnteredReg") || !OLCS.formHelper.findInput("data", "hasEnteredReg").length;
  }

  var tableSelector = "form [data-group*='small'], form [data-group*='medium'], form [data-group*='large']";

  /**
   * Always bind some generic edit and delete buttons as they're
   * common across most (all?) CRUD forms
   */
  OLCS.conditionalButton({
    container: tableSelector,
    label: "Edit",
    predicate: function (length, callback) {
      callback(length === 1);
    }
  });

  OLCS.conditionalButton({
    container: tableSelector,
    label: "Delete",
    predicate: function (length, callback) {
      callback(length >= 1);
    }
  });

  OLCS.conditionalButton({
    container: tableSelector,
    label: "Transfer",
    predicate: function (length, callback) {
      callback(length >= 1);
    }
  });

  OLCS.cascadeForm({
    cascade: false,
    rulesets: {
      "small": showTables,
      "medium": showTables,
      "large": showTables
    }
  });
});
