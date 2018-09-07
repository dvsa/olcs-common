$(function () {
    "use strict";
    OLCS.cascadeForm({
        cascade: false,
        rulesets: {
            "form-actions": {
                "selector:#sign": function () {
                    return OLCS.formHelper.isChecked("declarationsAndUndertakings", "signatureOptions", "Y");
                },
                "selector:#submitAndPay": function () {
                    return OLCS.formHelper.isChecked("declarationsAndUndertakings", "signatureOptions", "N");
                },
                "selector:#submit": function () {
                    return OLCS.formHelper.isChecked("declarationsAndUndertakings", "signatureOptions", "N");
                },
                "selector:#change": function () {
                    return OLCS.formHelper.isChecked("declarationsAndUndertakings", "signatureOptions", "Y") ||
                        OLCS.formHelper.isChecked("declarationsAndUndertakings", "signatureOptions", "N");
                }
            },
            "declarationsAndUndertakings": {
                "selector:.download": function () {
                    return OLCS.formHelper.isChecked("declarationsAndUndertakings", "signatureOptions", "N");
                },
                "selector:#label-declarationConfirmation": function () {
                    return OLCS.formHelper.isChecked("declarationsAndUndertakings", "signatureOptions", "Y");
                },
                "selector:.declarationForVerify": function () {
                    return OLCS.formHelper.isChecked("declarationsAndUndertakings", "signatureOptions", "Y");
                },
            }
        }
    });
});
