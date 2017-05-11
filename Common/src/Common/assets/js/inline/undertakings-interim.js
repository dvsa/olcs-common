$(function () {
  "use strict";

  OLCS.cascadeForm({
    cascade: false,
    rulesets: {
      "interim": {
        "label:applicationInterimReason": function () {
          return OLCS.formHelper.isChecked("interim", "goodsApplicationInterim");
        },
        "selector:.interimFee": function () {
          return OLCS.formHelper.isChecked("interim", "goodsApplicationInterim");
        },
      }
    }
  });
});
