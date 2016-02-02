(function(window, $){
    $('#calculation_has_isolation_inside').prop('checked', $('#calculation_internal_isolation_layer_size').val());
    $('#calculation_has_isolation_outside').prop('checked', $('#calculation_external_isolation_layer_size').val());

    $('#wall_isolation_layer').toggle($('#calculation_has_isolation_inside').is(':checked'));
    $('#wall_extra_isolation_layer').toggle($('#calculation_has_isolation_outside').is(':checked'));

    $('#calculation_has_isolation_inside').change(function () {
        var newVal = $('#calculation_has_isolation_inside').is(':checked');

        $('#wall_isolation_layer').toggle(newVal);

        if (!newVal) {
            $('#calculation_isolation_layer_material').val('');
            $('#calculation_isolation_layer_size').val('');
        }
    });

    $('#calculation_has_isolation_outside').change(function () {
        var newVal = $('#calculation_has_isolation_outside').is(':checked');

        $('#wall_extra_isolation_layer').toggle(newVal);

        if (!newVal) {
            $('#calculation_extra_isolation_layer_material').val('');
            $('#calculation_extra_isolation_layer_size').val('');
        }
    });

    $('#calculation_construction_type_0').on('click', function() {
        $('#traditional_materials').show();
        $('#calculation_has_isolation_inside').prop('checked', false);
        $('#wall_isolation_layer').toggle(false);
    });

    $('#calculation_construction_type_1').on('click', function() {
        $('#traditional_materials').hide();
        $('#calculation_has_isolation_inside').prop('checked', true);
        $('#wall_isolation_layer').toggle(true);
    });
})(window, jQuery);
