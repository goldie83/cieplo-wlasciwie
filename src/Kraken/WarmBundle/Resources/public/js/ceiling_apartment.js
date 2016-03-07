(function(window, $){

    $('#top_isolation_details').toggle($('#calculation_whats_over').val() != 'heated_room');
    $('#bottom_isolation_details').toggle($('#calculation_whats_under').val() != 'heated_room');

    $('#calculation_has_top_isolation').prop('checked', $('#calculation_top_isolation_layer_size').val());
    $('#calculation_has_bottom_isolation').prop('checked', $('#calculation_bottom_isolation_layer_size').val());

    $('#top_isolation_layer').toggle($('#calculation_has_top_isolation').is(':checked'));
    $('#bottom_isolation_layer').toggle($('#calculation_has_bottom_isolation').is(':checked'));

    $('#calculation_has_top_isolation').change(function () {
        if (!$(this).is(':checked')) {
            $('#calculation_top_isolation_layer_material').val('');
            $('#calculation_top_isolation_layer_size').val('');
        }
    });

    $('#calculation_has_bottom_isolation').change(function () {
        if (!$(this).is(':checked')) {
            $('#calculation_bottom_isolation_layer_material').val('');
            $('#calculation_bottom_isolation_layer_size').val('');
        }
    });

    $('#calculation_whats_over').change(function () {
        $('#top_isolation_details').toggle($(this).val() != 'heated_room');
    });

    $('#calculation_has_top_isolation').change(function () {
        $('#top_isolation_layer').toggle($(this).is(':checked'));
    });

    $('#calculation_whats_under').change(function () {
        $('#bottom_isolation_details').toggle($(this).val() != 'heated_room');
    });

    $('#calculation_has_bottom_isolation').change(function () {
        $('#bottom_isolation_layer').toggle($(this).is(':checked'));
    });

})(window, jQuery);
