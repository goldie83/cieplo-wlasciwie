(function(window, $){

    function refreshHeatedFloors() {
        floors = parseInt($('#calculation_building_floors').val());

        $('#calculation_building_heated_floors_0').parents('.checkbox').toggle(floors >= 1);
        $('#calculation_building_heated_floors_1').parents('.checkbox').toggle(floors >= 2);
        $('#calculation_building_heated_floors_2').parents('.checkbox').toggle(floors >= 3);

        $('#heated_floors label:hidden').each(function() {
            $(this).children('input').prop('checked', false);
        });
    }

    refreshHeatedFloors();

    $('#calculation_building_floors').on('change', function(paper) {
        refreshHeatedFloors();
    });

})(window, jQuery);
