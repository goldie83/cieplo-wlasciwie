(function(window, $){

    $('#calculation_has_top_isolation_0').prop('checked', $('#calculation_top_isolation_layer_size').val());
    $('#calculation_has_bottom_isolation_0').prop('checked', $('#calculation_bottom_isolation_layer_size').val());
    $('#calculation_has_top_isolation_1').prop('checked', $('#calculation_top_isolation_layer_size').val() == 0);
    $('#calculation_has_bottom_isolation_1').prop('checked', $('#calculation_bottom_isolation_layer_size').val() == 0);

    $('#top_isolation_layer').toggle($('#calculation_has_top_isolation_0').is(':checked'));
    $('#bottom_isolation_layer').toggle($('#calculation_has_bottom_isolation_0').is(':checked'));

    $('#calculation_has_top_isolation_0').change(function () {
        $('#top_isolation_layer').toggle($(this).is(':checked'));
    });

    $('#calculation_has_bottom_isolation_0').change(function () {
        $('#bottom_isolation_layer').toggle($(this).is(':checked'));
    });

    $('#calculation_has_top_isolation_1').change(function () {
        $('#top_isolation_layer').toggle(!$(this).is(':checked'));

        $('#calculation_top_isolation_layer_material').val('');
        $('#calculation_top_isolation_layer_size').val('');
    });

    $('#calculation_has_bottom_isolation_1').change(function () {
        $('#bottom_isolation_layer').toggle(!$(this).is(':checked'));

        $('#calculation_bottom_isolation_layer_material').val('');
        $('#calculation_bottom_isolation_layer_size').val('');
    });

})(window, jQuery);
