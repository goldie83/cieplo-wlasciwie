$(document).ready(function () {
    initialize();

    bindEvents();
});

function initialize() {
    heatingDeviceChanged();
    updateFuelType();
}

function bindEvents() {
    $('#calculation_fuel').change(function() {
        updateFuelType();
    });

    $('#calculation_heating_device').change(function() {
        heatingDeviceChanged();
    });

    $('#wants_email').prop('checked', $('#give_email').val());

    $('#wants_email').change(function() {
        $('#give_email').toggle($('#wants_email').is(':checked'));
    });
}

function heatingDeviceChanged() {
    var newVal = $('#calculation_heating_device option:selected').text();

    var showFuels = newVal.indexOf("podajnikowy") !== -1 || newVal.indexOf("zasypowy") !== -1 || newVal.indexOf("ceramiczny") !== -1;
    
    $('#calculation_fuel').parents('.form-group').toggle(showFuels);
    $('#calculation_stove_power').parents('.form-group').toggle(newVal != '');
    $('#calculation_fuel_consumption').parents('.form-group').toggle(newVal != '');
    $('#calculation_fuel_cost').parents('.form-group').toggle(newVal != '');
    
}

function updateFuelType() {
    $('#calculation_fuel_consumption').next().text('t');
    $('label[for="calculation_fuel_consumption"]').text('Zużycie opału ostatniej zimy');
    $('label[for="calculation_fuel_cost"]').text('Koszt zużytego opału');

    var newVal = $('#calculation_fuel option:selected').text();

    if (newVal.indexOf("Gaz") !== -1) {
        $('#calculation_fuel_consumption').next().text('m3');
    }
    
    if (newVal.indexOf("LPG") !== -1) {
        $('#calculation_fuel_consumption').next().text('l');
    }

    if (newVal.indexOf("Drewno") !== -1) {
        $('#calculation_fuel_consumption').next().text('mp');
    }

    if (newVal.indexOf("Prąd") !== -1) {
        $('#calculation_fuel_consumption').next().text('kWh');
        $('label[for="calculation_fuel_consumption"]').text('Zużycie energii ostatniej zimy');
        $('label[for="calculation_fuel_cost"]').text('Koszt zużytej energii');
        $('#calculation_stove_power').parents('.form-group').hide();
    }
}