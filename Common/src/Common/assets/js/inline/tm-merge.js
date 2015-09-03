$(function() {
    "use strict";

    $('#toTmId').focusout(function() {
        $('#toTmName').text('');
        if ($.isNumeric($(this).val())) {
            $.getJSON('/transport-manager/'+ $(this).val() +'/lookup', null ,function(e) {
                if ($('#toTmName').length == 0) {
                    var element = $('<span id="toTmName"></span');
                    $('#toTmId').after(element);
                }
                $('#toTmName').text(e.name);
            });
        }
    });
});
