$(function () {
    "use strict";
    OLCS.cascadeForm({
        cascade: false,
        rulesets: {
            "content": {
                "selector:#surrender-licence-possession": function () {
                    return OLCS.formHelper.selectRadio("OperatorLicenceDocument", "licenceDocumentOptions", "possession");
                },
                "selector:#surrender-licence-lost": function () {
                    return OLCS.formHelper.selectRadio("OperatorLicenceDocument", "licenceDocumentOptions", "lost");
                },
                "selector:#surrender-licence-stolen": function () {
                    return OLCS.formHelper.selectRadio("OperatorLicenceDocument", "licenceDocumentOptions", "stolen");
                }
            }
        }
    });
});
