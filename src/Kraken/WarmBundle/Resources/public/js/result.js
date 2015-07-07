$(function () {

    Highcharts.setOptions({
        lang: {
            decimalPoint: ',',
            thousandsSep: ' '
        }
    });

    breakdownOptions = {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: ''
        },
        tooltip: {
            formatter: function() {
                return '<b>'+ this.series.name +'</b>: '+ Math.round(this.percentage) +' %';
            },
            percentageDecimals: 1
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    formatter: function() {
                        return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %';
                    }
                }
            }
        },
        series: []
    };

    $.getJSON(Routing.generate('details_breakdown', {id: window.calculationId}), function(data) {
            breakdownOptions.series.push(data);
            createBreakdownChart(breakdownOptions);
    });

    function createBreakdownChart(options) {
        $('#heat_loss_breakdown').highcharts(options);
    }
});


function openHeatingCostsTab() {
    $('#cost_charts_navbar').children().last().removeClass('active');
    $('#cost_charts_navbar').children().first().addClass('active');

    $('#setup_chart').hide();
    $('#fuel_chart').show();
}

function openSetupCostsTab() {
    $('#cost_charts_navbar').children().first().removeClass('active');
    $('#cost_charts_navbar').children().last().addClass('active');
    
    $('#fuel_chart').hide();
    $('#setup_chart').show();
}

var app = angular.module('warm', []).config(function($interpolateProvider){
        $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
    }
);

var FLOAT_REGEXP = /^\-?\d+((\.|\,)\d+)?$/;
app.directive('smartFloat', function() {
  return {
    require: 'ngModel',
    link: function(scope, elm, attrs, ctrl) {
      ctrl.$parsers.unshift(function(viewValue) {
        if (FLOAT_REGEXP.test(viewValue)) {
          ctrl.$setValidity('float', true);
          return parseFloat(viewValue.replace(',', '.'));
        } else {
          ctrl.$setValidity('float', false);
          return undefined;
        }
      });
    }
  };
});

app.controller('WarmCtrl', function($scope, $http) {
    $scope.fuelChart = null;
    $scope.workHourPrice = 10;
    $scope.includeWorkTime = true;
    
    $('#custom_fuel_prices').on('hide.bs.modal', function () {
        // restore unit price from human input
        for (var key in $scope.fuels) {
            $scope.fuels[key].price = $scope.fuels[key].human_price / $scope.fuels[key].trade_amount;
        }
        
        //store custom fuel prices
        $http.post(Routing.generate('details_custom_data', {id: window.calculationId}), {fuels: $scope.fuels}).
            success(function(data, status, headers, config) {
                // good.
                console.log('Custom data saved.' + data);
            }).
            error(function(data, status, headers, config) {
                console.log('Failed to save custom data');
            });
        
        $scope.recalculateCosts();
    });
    
    $scope.fuelChartOptions = {
        chart: {
            type: 'bar',
            renderTo: 'fuel_chart'
        },
        title: {
            text: 'Roczny koszt ogrzewania twojego domu'
        },
        xAxis: {
            categories: [],
            labels: {
              align: 'right',
              style: {
                  fontSize: '11px',
                  fontFamily: 'Verdana, sans-serif'
              }
            }
        },
        yAxis: {
            allowDecimals: false,
            min: 0,
            title: {
                text: 'Roczny koszt ogrzewania (zł)'
            },
            stackLabels: {
                enabled: true,
                formatter: function() {
                    return Highcharts.numberFormat(100 * Math.ceil(this.total / 100), 0) + 'zł';
                },
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                }
            }

        },
        tooltip: {
            headerFormat: '<span><b>{point.key}</b></span><table>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            series: {
                stacking: 'normal'
            },
            column: {
                dataLabels: {
                    enabled: false,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'black',
                    backgroundColor: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'ccc',
                    formatter: function() {
                        return Highcharts.numberFormat(this.y, 0) + 'zł';
                    }
                }
            }
        },
        series: []
    };
    
    $scope.setupChartOptions = {
        chart: {
            type: 'bar',
            renderTo: 'setup_chart'
        },
        title: {
            text: 'Koszty zmiany aktualnego sposobu ogrzewania'
        },
        xAxis: {
            categories: [],
            labels: {
              align: 'right',
              style: {
                  fontSize: '11px',
                  fontFamily: 'Verdana, sans-serif'
              }
            }
        },
        yAxis: {
            allowDecimals: false,
            min: 0,
            title: {
                text: 'Koszt instalacji (zł)'
            },
            stackLabels: {
                enabled: true,
                formatter: function() {
                    return Highcharts.numberFormat(100 * Math.ceil(this.total / 100), 0) + 'zł';
                },
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                }
            }

        },
        tooltip: {
            headerFormat: '<span><b>{point.key}</b></span><table>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            series: {
                stacking: 'normal'
            },
            column: {
                dataLabels: {
                    enabled: false,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'black',
                    backgroundColor: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'ccc',
                    formatter: function() {
                        return Highcharts.numberFormat(this.y, 0) + 'zł';
                    }
                }
            }
        },
        series: []
    };
  
    $http.get(Routing.generate('details_fuels', {id: window.calculationId})).
        success(function(data, status, headers, config) {
            $scope.heatingVariants = data.variants;
            $scope.fuels = data.fuels;
            $scope.currentVariant = data.currentVariant;
            
            //create fake variable with human-readable fuel price for settings modal
            for (var key in $scope.fuels) {
                var humanPrice = $scope.fuels[key].price * $scope.fuels[key].trade_amount;
                $scope.fuels[key].human_price = humanPrice;
            }
            
            $scope.recalculateCosts();
        }).
        error(function(data, status, headers, config) {
            // log error
        });
        
    $scope.recalculateCosts = function () {      
        for (var i = 0; i < $scope.heatingVariants.length; i++) {
            if ($scope.heatingVariants[i].type == 'bituminous_coal_manual_stove') {
                $scope.referenceVariant = $scope.heatingVariants[i];
                $scope.referenceVariant.setup_cost = $scope.calculateSetupCost($scope.referenceVariant.setup_costs);
                $scope.referenceVariant.cost = Math.round($scope.fuels[$scope.referenceVariant.fuel_type].price * $scope.referenceVariant.amount);
                break;
            }
        }

        var fuelConsumptionProvided = $scope.currentVariant.cost > 0 && $scope.currentVariant.time > 0;
        
        for (var i = 0; i < $scope.heatingVariants.length; i++) {
            if (fuelConsumptionProvided) {
                $scope.heatingVariants[i].setup_cost = $scope.calculateSetupCost($scope.heatingVariants[i].setup_costs);
                $scope.heatingVariants[i].cost = Math.round($scope.fuels[$scope.heatingVariants[i].fuel_type].price * $scope.heatingVariants[i].amount);
                $scope.heatingVariants[i].savedMoney = $scope.currentVariant.cost - $scope.heatingVariants[i].cost;
                $scope.heatingVariants[i].savedTime = Math.max(0, $scope.currentVariant.time - $scope.heatingVariants[i].maintenance_time);
                $scope.heatingVariants[i].savedTimeCost = $scope.heatingVariants[i].savedTime * $scope.workHourPrice;
                $scope.heatingVariants[i].roi = Math.round($scope.roiPeriod($scope.heatingVariants[i].savedMoney, $scope.heatingVariants[i].savedTime, $scope.heatingVariants[i].setup_cost)); 
            } else {
                $scope.heatingVariants[i].setup_cost = $scope.calculateSetupCost($scope.heatingVariants[i].setup_costs);
                $scope.heatingVariants[i].setup_cost_diff = $scope.calculateSetupCost($scope.heatingVariants[i].setup_costs) - $scope.calculateSetupCost($scope.referenceVariant.setup_costs);
                $scope.heatingVariants[i].cost = Math.round($scope.fuels[$scope.heatingVariants[i].fuel_type].price * $scope.heatingVariants[i].amount);
                $scope.heatingVariants[i].savedMoney = $scope.referenceVariant.cost - $scope.heatingVariants[i].cost;
                $scope.heatingVariants[i].savedTime = Math.max(0, $scope.referenceVariant.maintenance_time - $scope.heatingVariants[i].maintenance_time);
                $scope.heatingVariants[i].savedTimeCost = $scope.heatingVariants[i].savedTime * $scope.workHourPrice;
                $scope.heatingVariants[i].roi = Math.round($scope.roiPeriod($scope.heatingVariants[i].savedMoney, $scope.heatingVariants[i].savedTime, $scope.heatingVariants[i].setup_cost_diff)); 
            }
            
            $scope.heatingVariants[i].totalSavings = $scope.heatingVariants[i].savedMoney + $scope.heatingVariants[i].savedTimeCost;
        }

        $scope.createFuelChart();
        $scope.createSetupChart();
        
        $scope.heatingVariants.sort(function (a, b) { return a.roi - b.roi }); 
    }
        
    $scope.updateFuelCosts = function () {            
        $('#custom_fuel_prices').modal('hide');
        
        return false;
    }
        
    $scope.calculateSetupCost = function (data) {
        var sum = 0;

        for (var i = 0; i < data.length; i++) {
            sum += data[i][1];
        }
        
        return sum;
    }
        
    $scope.calculateFuelCosts = function (heatingVariants) {
        /*
         'price' => cena jednostkowa
         'amount' => ilosc jednostek paliwa
         'trade_amount' => mnoznik handlowy jednostek paliwa,
         'trade_unit' => nazwa jednostki handlowej,*/
        $scope.fuelChartOptions.xAxis.categories = [];
        
        heatingVariants.sort(function(a, b) { return ($scope.fuels[a.fuel_type].price * a.amount) - ($scope.fuels[b.fuel_type].price * b.amount) });
        
        var series = [];
        series[0] = {
            name: 'Koszt paliwa',
            heatingVariants: [],
            index: 1,
            showInLegend: false
        };
        series[0]['data'] = [];
        
        for (var i = 0; i < heatingVariants.length; i++) {
            $scope.fuelChartOptions.xAxis.categories.push(heatingVariants[i].label);

            series[0]['data'][i] = heatingVariants[i];
            series[0]['data'][i].y = heatingVariants[i].cost;
            series[0]['data'][i].trade_unit_price = $scope.fuels[heatingVariants[i].fuel_type].price * $scope.fuels[heatingVariants[i].fuel_type].trade_amount;
            series[0]['data'][i].price = $scope.fuels[heatingVariants[i].fuel_type].price;
            series[0]['data'][i].trade_unit = $scope.fuels[heatingVariants[i].fuel_type].trade_unit;
            series[0]['data'][i].trade_amount = $scope.fuels[heatingVariants[i].fuel_type].trade_amount;
        }

        return series;
    }
    
    $scope.calculateSetupCostsForChart = function (data) {
        $scope.setupChartOptions.xAxis.categories = [];
      
        data.sort(function(a, b) { return (a.setup_cost) - (b.setup_cost) });
        
        var series = [];
        series[0] = {
            name: 'Koszt inwestycji',
            data: [],
            color: '#FFB25A',
            index: 1,
            showInLegend: false
        };
        
        for (var i = 0; i < data.length; i++) {
            $scope.setupChartOptions.xAxis.categories.push(data[i].label);

            series[0]['data'][i] = data[i];
            series[0]['data'][i].y = data[i].setup_cost;
        }

        return series;
    }
    
    $scope.createFuelChart = function () {             
        $scope.fuelChartOptions.series = $scope.calculateFuelCosts($scope.heatingVariants);
        $scope.fuelChartOptions.series[0].tooltip = {};
        $scope.fuelChartOptions.series[0].tooltip.pointFormat = '<tr><td>{point.version}</td>' +
                              '<td style="padding:0">&nbsp;</td></tr>' +
                              '<tr><td style="color:{series.color};padding:0">Efektywność:</td>' +
                              '<td style="padding:0">&nbsp;<b>{point.efficiency}%</b></td></tr>' +
                              '<tr><td style="color:{series.color};padding:0">Cena:</td>' +
                              '<td style="padding:0">&nbsp;<b>{point.trade_unit_price}zł/{point.trade_unit}</b></td></tr>' +
                              '<tr><td style="color:{series.color};padding:0">Zużycie:</td>' +
                              '<td style="padding:0">&nbsp;<b>{point.consumption}{point.trade_unit}</b></td></tr>' +
                              '<tr><td style="color:{series.color};padding:0">Koszt:</td>' +
                              '<td style="padding:0">&nbsp;<b>{point.y}zł</b></td></tr>';
                              
        $scope.fuelChart = new Highcharts.Chart($scope.fuelChartOptions);
    };
    
    $scope.createSetupChart = function () {                              
        $scope.setupChartOptions.series = $scope.calculateSetupCostsForChart($scope.heatingVariants);
        $scope.setupChartOptions.series[0].tooltip = {};
        $scope.setupChartOptions.series[0].tooltip.pointFormat = '<tr><td>{point.version}</td>' +
                              '<td style="padding:0">&nbsp;</td></tr>' +
                              '<tr><td style="color:{series.color};padding:0">Koszty inwestycji:</td>' +
                              '<td style="padding:0">&nbsp;<b>{point.y}zł</b></td></tr>';
        $scope.setupChart = new Highcharts.Chart($scope.setupChartOptions);
    };
    
    $scope.roiPeriod = function (savedMoney, savedTime, setupCost) {
        var savedTimeEquivalent = savedTime * $scope.workHourPrice;
        var savings = $scope.includeWorkTime ? savedMoney + savedTimeEquivalent : savedMoney;

        return savings > 0 ? setupCost / savings: 0;
    };
    
    $scope.formatRoiPeriod = function (period) {
        var suffix = 'lat';
        
        if (period < 2) {
            suffix = 'rok';
        } else if (period >= 2 && period < 5) {
            suffix = 'lata';
        }
        
        if (period > 30) {
            return '> 30 lat';
        }
        
        return period < 1 ? 'poniżej roku' : Math.round(period) + " " + suffix;
    };
    
    $scope.greaterThan = function(prop, val){
        return function(item){
            return item[prop] > val;
        }
    }
    
    $scope.filterOutManualStoveVariants = function(variant) {
        return variant.type != 'brown_coal_manual_stove' 
            && variant.type != 'coke_manual_stove' 
            && variant.type != 'wood_manual_stove';
    }
});
