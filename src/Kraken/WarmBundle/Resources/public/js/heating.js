(function(window, $){
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
