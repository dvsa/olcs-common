$(function() {
    "use strict";

    $('#toOperatorId').focusout(function() {
        $('#toOperatorName').text('');
        if ($.isNumeric($(this).val())) {
            $.getJSON('../lookup/'+ $(this).val(), null ,function(e) {
                if ($('#toOperatorName').length == 0) {
                    var orgNameElement = $('<span id="toOperatorName"></span');
                    $('#toOperatorId').after(orgNameElement);
                }
                $('#toOperatorName').text(e.name);
            });
        }
    });
});
