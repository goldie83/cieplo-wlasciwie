(function(window, $){
    // that's event delegation
    $('body').on("change", "select", function () {
        if ($(this).attr('id').indexOf('consumptions') != -1) {
            selectedFuel = $(this).find('option:selected').text();
            unit = 't';

            if (selectedFuel == 'Prąd' || selectedFuel.indexOf('Gaz') != -1 || selectedFuel.indexOf('sieciowe') != -1) {
                unit = 'kWh';
            }
            if (selectedFuel == 'Drewno') {
                unit = 'mp';
            }
            if (selectedFuel.indexOf('LPG') != -1) {
                unit = 'l';
            }

            $(this).parent().parent().next().find('.input-group-addon').text(unit);
        }
    });

    $('#calculation_include_hot_water').prop('checked', $('#calculation_hot_water_persons').val());

    $('#hot_water').toggle($('#calculation_include_hot_water').is(':checked'));

    $('#calculation_include_hot_water').change(function () {
        var newVal = $('#calculation_include_hot_water').is(':checked');

        $('#hot_water').toggle(newVal);

        if (!newVal) {
            $('#calculation_hot_water_persons').val('');
        }
    });

})(window, jQuery);
