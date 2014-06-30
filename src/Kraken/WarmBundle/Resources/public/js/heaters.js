var app = angular.module('warm', []).config(function($interpolateProvider){
        $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
    }
);

app.controller('WarmCtrl', function($scope) {
    //TODO
    $scope.outdoor_temperature = -20;
    $scope.unheated_temperature = 10;
    $scope.floor_height = 2.6;
    $scope.standard_window_area = 1.5 * 1.4;
    $scope.standard_door_area = 2.2 * 1;
    $scope.floors = buildingFloors;

    $scope.getFirstHeatedFloor = function()
    {
        for (i = 0; i < $scope.floors.length; i++) {
            if ($scope.floors[i].heated == true) {
                return $scope.floors[i].name;
            }
        }
    }

    $scope.room_type = "standard";
    $scope.room_floor = $scope.getFirstHeatedFloor();
    $scope.room_width = Math.round(buildingWidth / 2);
    $scope.room_length = Math.round(buildingLength / 2);
    $scope.room_external_walls = "long";
    $scope.room_unheated_walls = 0;
    $scope.room_windows = 1;
    $scope.room_doors = 0;

    $scope.power = 0;

    $scope.getWindowsArea = function()
    {
        var windowsArea = $scope.room_windows * $scope.standard_window_area;

        if ($scope.room_has_balcony_door) {
            windowsArea += 0.8 * $scope.standard_window_area;
        }

        return Math.max(0, windowsArea);
    }

    $scope.getDoorsArea = function()
    {
        return Math.max(0, $scope.room_doors * $scope.standard_door_area);
    }

    $scope.getExternalWallArea = function()
    {
        var externalWallLength = 0;
        
        console.log("width: " + $scope.room_width + ", len: " + $scope.room_length);

        if ($scope.room_external_walls == "short") {
            console.log('short');
            externalWallLength = Math.min($scope.room_width, $scope.room_length);
        } else if ($scope.room_external_walls == "long") {
            console.log('long');
            externalWallLength = Math.max($scope.room_width, $scope.room_length);
        } else if ($scope.room_external_walls == 2) {
            console.log('2');
            externalWallLength = $scope.room_width + $scope.room_length;
        } else if ($scope.room_external_walls == 3) {
            console.log('3');
            externalWallLength = 2 * $scope.room_width + $scope.room_length;
        } else if ($scope.room_external_walls == 4) {
            console.log('4');
            externalWallLength = 2 * $scope.room_width + 2 * $scope.room_length;
        }
        
        console.log("external:" + externalWallLength);
        
        return $scope.floor_height * externalWallLength - $scope.getWindowsArea() - $scope.getDoorsArea();
    }

    $scope.getUnheatedWallArea = function()
    {
        var unheatedWallLength = 0;

        if ($scope.room_unheated_walls == "short") {
            unheatedWallLength = Math.min($scope.room_width, $scope.room_length);
        } else if ($scope.room_unheated_walls == "long") {
            unheatedWallLength = Math.max($scope.room_width, $scope.room_length);
        } else if ($scope.room_unheated_walls == 2) {
            unheatedWallLength = $scope.room_width + $scope.room_length;
        } else if ($scope.room_unheated_walls == 3) {
            unheatedWallLength = 2 * $scope.room_width + $scope.room_length;
        } else if ($scope.room_unheated_walls == 4) {
            unheatedWallLength = 2 * $scope.room_width + 2 * $scope.room_length;
        }

        console.log("unheated:" + unheatedWallLength);

        return $scope.floor_height * unheatedWallLength;
    }

    $scope.getIndoorTemperature = function()
    {
        return $scope.room_type == "bathroom" ? 24 : 20;
    }

    $scope.getBelowFloorName = function()
    {
        for (i = 0; i < $scope.floors.length; i++) {
            if ($scope.floors[i].name == $scope.room_floor) {
                return i > 0 ? $scope.floors[i-1].name : false;
            }
        }

        return false;
    }

    $scope.isBelowFloorHeated = function()
    {
        for (i = 0; i < $scope.floors.length; i++) {
            if ($scope.floors[i].name == $scope.room_floor) {
                return i > 0 ? $scope.floors[i-1].heated : false;
            }
        }

        return false;
    }

    $scope.getAboveFloorName = function()
    {
        for (i = 0; i < $scope.floors.length; i++) {
            if ($scope.floors[i].name == $scope.room_floor) {
                return i < $scope.floors.length-1 ? $scope.floors[i+1].name : false;
            }
        }

        return false;
    }

    $scope.isAboveFloorHeated = function()
    {
        for (i = 0; i < $scope.floors.length; i++) {
            if ($scope.floors[i].name == $scope.room_floor) {
                return i < $scope.floors.length-1 ? $scope.floors[i+1].heated : false;
            }
        }

        return false;
    }

    $scope.getCeilingArea = function()
    {
        // close enough
        return $scope.room_length * $scope.room_width;
    }
    
    $scope.getCeilingHeatLoss = function()
    {
        var aboveFloorName = $scope.getAboveFloorName();
        var isAboveFloorHeated = $scope.isAboveFloorHeated();
        
        console.log("above: " + aboveFloorName);
        console.log("above heated: " + isAboveFloorHeated);
        
        if ($scope.room_floor == "attic") {
            return $scope.getCeilingArea() * buildingRoofConductance * ($scope.getIndoorTemperature() - $scope.outdoor_temperature);
        }
        
        if (aboveFloorName == 'attic' && !isAboveFloorHeated) {
            return $scope.getCeilingArea() * buildingHighestCeilingConductance * ($scope.getIndoorTemperature() - $scope.unheated_temperature);
        }

        if (!isAboveFloorHeated) {
            return $scope.getCeilingArea() * buildingInternalCeilingConductance * ($scope.getIndoorTemperature() - $scope.unheated_temperature);
        }
        
        return 0;
    }

    $scope.getFloorHeatLoss = function()
    {
        var belowFloorName = $scope.getBelowFloorName();
        var isBelowFloorHeated = $scope.isBelowFloorHeated();
        
        console.log("below: " + belowFloorName);
        console.log("below heated: " + isBelowFloorHeated);
        
        if ($scope.room_floor == "ground_floor" && belowFloorName == false) {
            return $scope.getCeilingArea() * buildingGroundFloorConductance * ($scope.getIndoorTemperature() - $scope.outdoor_temperature);
        }
        
        if ($scope.room_floor == "basement") {
            return $scope.getCeilingArea() * buildingUndergroundConductance * ($scope.getIndoorTemperature() - $scope.outdoor_temperature);
        }

        if (!isBelowFloorHeated) {
            return $scope.getCeilingArea() * buildingInternalCeilingConductance  * ($scope.getIndoorTemperature() - $scope.unheated_temperature);
        }

        return 0;
    }
    
    $scope.calculatePower = function()
    {
        var temperatureDiff = $scope.getIndoorTemperature() - $scope.outdoor_temperature;
        var power = 0;
        
        if ($scope.room_floor != 'basement') {
            power = $scope.getExternalWallArea() * buildingExternalWallConductance * temperatureDiff;

            if ($scope.room_has_balcony_door == true) {
                power *= 1.15;
            }
        }
            
        power += $scope.getWindowsArea() * buildingWindowsConductance * temperatureDiff;
        power += $scope.getDoorsArea() * buildingDoorsConductance * temperatureDiff;

        power += $scope.getUnheatedWallArea() * buildingInternalWallConductance * temperatureDiff * 0.5;
        power += $scope.getCeilingHeatLoss();
        power += $scope.getFloorHeatLoss();

        power *= 1.1;
        
        if (power) {
            $scope.power = 50 * Math.ceil(Math.round(power) / 50);
        }

        return $scope.power;
    }
});