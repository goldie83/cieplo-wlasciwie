$(document).ready(function () {
    initialize();

    bindEvents();
});

function initialize() {
    updateConstructionType();
    updateWallStuff();
    updateFloorsStuff();
    updateRoofType();
    updateBasementThings();
    analyzeWallSize();
    setInitialWallSize();

    if ($('#calculation_wall_size').val() == '' && $('#calculation_walls_0_construction_layer_size').val() > 0) {
        $('#calculation_wall_size').val($('#calculation_walls_0_construction_layer_size').val());
    }
}

function bindEvents() {
    $('#calculation_roof_type').change(function() {
        updateRoofType();
    });

    $('#calculation_has_basement').change(function () {
        updateBasementThings();
    });

    $('#calculation_is_basement_heated').change(function () {
        updateBasementThings();
    });

    $('#calculation_is_ground_floor_heated').change(function () {
        updateBasementThings();
    });

    $('#calculation_number_floors').change(function () {
        updateFloorsStuff();
    });

    $('#calculation_number_heated_floors').change(function () {
        updateFloorsStuff();
    });
    
    $('#calculation_construction_type').change(function() {
        updateConstructionType();
    });
    
    $('#calculation_wall_size').change(function () {
        calculateWallSize();
        analyzeWallSize();
    });

    $('#calculation_walls_0_construction_layer_size').change(function () {
        analyzeWallSize();
    });

    $('#calculation_walls_0_isolation_layer_size').change(function () {
        calculateWallSize();
        analyzeWallSize();
    });

    $('#calculation_walls_0_outside_layer_size').change(function () {
        calculateWallSize();
        analyzeWallSize();
    });

    $('#calculation_walls_0_extra_isolation_layer_size').change(function () {
        calculateWallSize();
        analyzeWallSize();
    });
    
    $('#calculation_walls_0_has_another_layer').change(function () {
        var newVal = $('#calculation_walls_0_has_another_layer').is(':checked');
        
        $('#wall_outside_layer').toggle(newVal);
        
        if (!newVal) {
            $('#calculation_walls_0_outside_layer_material').val('');
            $('#calculation_walls_0_outside_layer_size').val('');
        }
    });

    $('#calculation_walls_0_has_isolation_inside').change(function () {
        var newVal = $('#calculation_walls_0_has_isolation_inside').is(':checked');
        
        $('#wall_isolation_layer').toggle(newVal);
        
        if (!newVal) {
            $('#calculation_walls_0_isolation_layer_material').val('');
            $('#calculation_walls_0_isolation_layer_size').val('');
        }
    });

    $('#calculation_walls_0_has_isolation_outside').change(function () {
        var newVal = $('#calculation_walls_0_has_isolation_outside').is(':checked');
        
        $('#wall_extra_isolation_layer').toggle(newVal);
        
        if (!newVal) {
            $('#calculation_walls_0_extra_isolation_layer_material').val('');
            $('#calculation_walls_0_extra_isolation_layer_size').val('');
        }
    });
}

function analyzeWallSize()
{
    var wallSize = makeInteger($('#calculation_walls_0_construction_layer_size').val())
        + makeInteger($('#calculation_walls_0_isolation_layer_size').val())
        + makeInteger($('#calculation_walls_0_outside_layer_size').val())
        + makeInteger($('#calculation_walls_0_extra_isolation_layer_size').val());

    $('#wall_may_be_too_thin').toggle(wallSize > 0 && wallSize < 20 && $('#calculation_construction_type').val() == 'traditional');
    $('#wall_may_be_too_thin span').text(wallSize);
    $('#wall_may_have_isolation').toggle(wallSize > 30 && $('#calculation_walls_0_isolation_layer_size').val() == 0);
}

function makeInteger(text) {
    var val = parseInt(text);

    return isNaN(val) ? 0 : val;
}

function updateFloorsStuff() {
    var heatedFloorsCount = $('#calculation_number_heated_floors').val();
    if (heatedFloorsCount == 0) {
        return;
    }
    $('#whats_unheated').toggle($('#calculation_number_floors').val()-heatedFloorsCount == 1);
}

function setInitialWallSize() {
    var isCanadian = $('#calculation_construction_type').val() == 'canadian';

    var constructionLayerSize = makeInteger($('#calculation_walls_0_construction_layer_size').val());
    var isolationLayerSize = makeInteger($('#calculation_walls_0_isolation_layer_size').val());
    var outsideLayerSize = makeInteger($('#calculation_walls_0_outside_layer_size').val());
    var extraIsolationLayerSize = makeInteger($('#calculation_walls_0_extra_isolation_layer_size').val());
    
    if (isCanadian) {
        $('#calculation_wall_size').val(isolationLayerSize + extraIsolationLayerSize);
    } else if (isolationLayerSize + outsideLayerSize + extraIsolationLayerSize > 0) {
        $('#calculation_wall_size').val(constructionLayerSize + isolationLayerSize + outsideLayerSize + extraIsolationLayerSize);
    }
}

function calculateWallSize() {
    var totalSize = makeInteger($('#calculation_wall_size').val());
    var isCanadian = $('#calculation_construction_type').val() == 'canadian';

    var constructionLayerSize = makeInteger($('#calculation_walls_0_construction_layer_size').val());
    var isolationLayerSize = makeInteger($('#calculation_walls_0_isolation_layer_size').val());
    var outsideLayerSize = makeInteger($('#calculation_walls_0_outside_layer_size').val());
    var extraIsolationLayerSize = makeInteger($('#calculation_walls_0_extra_isolation_layer_size').val());
    
    if (isCanadian) {
        $('#calculation_walls_0_construction_layer_size').val(6);
    } else {      
        var sizeLeft = totalSize - (isolationLayerSize + outsideLayerSize + extraIsolationLayerSize);

        if (sizeLeft < 6) {
            sizeLeft = 6;
            $('#calculation_wall_size').val(totalSize + 6)
        }

        $('#calculation_walls_0_construction_layer_size').val(sizeLeft);
    }
}

function updateWallStuff() {
    $('#calculation_walls_0_has_another_layer').prop('checked', $('#calculation_walls_0_outside_layer_size').val());
    $('#calculation_walls_0_has_isolation_inside').prop('checked', $('#calculation_walls_0_isolation_layer_size').val());
    $('#calculation_walls_0_has_isolation_outside').prop('checked', $('#calculation_walls_0_extra_isolation_layer_size').val());

    $('#wall_outside_layer').toggle($('#calculation_walls_0_has_another_layer').is(':checked'));
    $('#wall_isolation_layer').toggle($('#calculation_walls_0_has_isolation_inside').is(':checked'));
    $('#wall_extra_isolation_layer').toggle($('#calculation_walls_0_has_isolation_outside').is(':checked'));
}

function updateRoofType() {
    var newVal = $('#calculation_roof_type').val();

    $('#calculation_is_attic_heated').parents('.control-group').toggle(newVal != 'flat');
    $('#roof_isolation_layer').toggle(newVal != 'flat');
    if (newVal == 'flat') {
        $('#calculation_highest_ceiling_isolation_layer').parent().prev().text('Izolacja dachu');
    } else {
        $('#calculation_highest_ceiling_isolation_layer').parent().prev().text('Izolacja najwyższego stropu');
    }
}

function updateConstructionType() {
    var newVal = $('#calculation_construction_type').val();

    var isCanadian = newVal == 'canadian';
    
    $('#wall_isolation_layer').toggle(isCanadian);
    $('#calculation_walls_0_has_isolation_inside').parent().parent().toggle(!isCanadian);
    $('#calculation_walls_0_has_another_layer').parent().parent().toggle(!isCanadian);
    $('#calculation_walls_0_construction_layer_material').parent().parent().toggle(!isCanadian);
    $('#calculation_walls_0_construction_layer_size').parent().parent().parent().toggle(!isCanadian);
    $('#calculation_walls_0_outside_layer_material').parent().parent().toggle(!isCanadian);
    $('#calculation_walls_0_outside_layer_size').parent().parent().parent().toggle(!isCanadian);

    if (newVal == 'traditional') {
        $('#calculation_walls_0_construction_layer_material').parent().prev().text('Główny materiał ścian zewnętrznych');
        $('#calculation_walls_0_construction_layer_size').parent().parent().prev().text('Grubość ściany');
        
        $('#calculation_walls_0_outside_layer_material').parent().prev().text('Materiał drugiej warstwy ścian zewnętrznych');
        $('#calculation_walls_0_isolation_layer_material').parent().prev().text('Izolacja wewnątrz ścian zewnętrznych');
        $('#calculation_walls_0_extra_isolation_layer_material').parent().prev().text('Materiał ocieplenia');
        
        $('#calculation_walls_0_isolation_layer_size').parent().parent().prev().text('Grubość');
    } else {
        var additionalMaterial = 'Drewno liściaste';
        $('#calculation_walls_0_construction_layer_material option:contains(' + additionalMaterial + ')').prop({selected: true});
        $('#calculation_walls_0_construction_layer_size').val(6);

        $('#calculation_walls_0_outside_layer_material').parent().prev().text('Materiał wykończeniowy');
        $('#calculation_walls_0_outside_layer_size').parent().parent().prev().text('Grubość');
        
        $('#calculation_walls_0_isolation_layer_material').parent().prev().text('Izolacja wypełniająca ściany');
        $('#calculation_walls_0_isolation_layer_size').parent().parent().prev().text('Grubość');
        
        $('#calculation_walls_0_extra_isolation_layer_material').parent().prev().text('Docieplenie od zewnątrz');
        $('#calculation_walls_0_extra_isolation_layer_size').parent().parent().prev().text('Grubość');
    }
}

function updateBasementThings() {
    var hasBasement = $('#calculation_has_basement').is(':checked');
    var basementIsHeated = $('#calculation_is_basement_heated').is(':checked');
    var groundFloorIsHeated = $('#calculation_is_ground_floor_heated').is(':checked');

    $('#calculation_is_basement_heated').parents('.control-group').toggle(hasBasement);
    $('#basement_floor_isolation_layer').toggle(hasBasement);
    $('#lowest_ceiling_isolation_layer').toggle(!groundFloorIsHeated);
}