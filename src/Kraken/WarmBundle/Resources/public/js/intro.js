$(document).ready(function () {
    initialize();

    bindEvents();
});

function initialize() {
    heatingDeviceChanged();
    
    $('select[id*="fuel"]').each(function(){
        updateFuelType(this);
    });    
}

function bindEvents() {
    $('#calculation_heating_device').change(function() {
        heatingDeviceChanged();
    });

    $('#wants_email').prop('checked', $('#give_email').val());

    $('#wants_email').change(function() {
        $('#give_email').toggle($('#wants_email').is(':checked'));
    });
    
    $(document).on('change', 'select[id*="fuel"]', function(){
        updateFuelType(this);
    });    
}

function heatingDeviceChanged() {
    var newVal = $('#calculation_heating_device option:selected').text();

    $('#calculation_stove_power').parents('.form-group').toggle(newVal != '');
}

function updateFuelType(fuelSelect) {
    var newVal = $('option:selected', fuelSelect).text();
    var unitSpan = $(fuelSelect).parent().parent().next().find('span.input-group-addon');
    
    unitSpan.text('t');
    
    if (newVal.indexOf("Gaz") !== -1) {
        unitSpan.text('kWh');
    }
    
    if (newVal.indexOf("LPG") !== -1) {
        unitSpan.text('l');
    }

    if (newVal.indexOf("Drewno") !== -1) {
        unitSpan.text('mp');
    }

    if (newVal.indexOf("Prąd") !== -1) {
        unitSpan.text('kWh');
        $('label[for="calculation_fuel_consumption"]').text('Zużycie energii ostatniej zimy');
        $('label[for="calculation_fuel_cost"]').text('Koszt zużytej energii');
        $('#calculation_stove_power').parents('.form-group').hide();
    } 
}
