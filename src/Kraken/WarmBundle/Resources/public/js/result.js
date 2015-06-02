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

    $('#update_fuels').bind('click', function() {
        if (!fuelChart) {
            return;  
        }
        
        var fuelIndex = 1;
        var costsIndex = 0;
        
        var newData = fuelChart.series[fuelIndex].options.data;

        var dataChanged = false;

        for (var i = 0; i < newData.length; i++) {
            var fuelType = newData[i].fuel_type;

            var fuelField = fuelType == 'coal_cleaner'
                ? 'coal_dirty'
                : fuelType;

            var newPrice = parseFloat($('#fuel_' + fuelField).val().replace(',','.').replace(' ',''));
            if (newPrice < 0) {
                continue;
            }

            var newUnitPrice = newPrice / newData[i].trade_amount;
            if (newUnitPrice == newData[i].price) {
                continue;
            }

            dataChanged = true;
            newData[i].price = newUnitPrice;
            newData[i].y = Math.round(newData[i].price * newData[i].amount);
            newData[i].trade_unit_price = newData[i].price * newData[i].trade_amount;
        }

        if (dataChanged) {
            fuelChart.series[fuelIndex].setVisible(false);
            fuelChart.series[fuelIndex].setData(newData);
            fuelChart.series[fuelIndex].setVisible(true, true);
        }

        var newData = fuelChart.series[costsIndex].options.data;
        var dataChanged = false;

        for (var i = 0; i < newData.length; i++) {
            var fuelType = newData[i].fuel_type;

            var newWorkPrice = parseFloat($('#work_hour_cost').val().replace(',','.').replace(' ',''));
            if (newWorkPrice < 0) {
                continue;
            }

            var newWorkCost = newWorkPrice * newData[i].hours;
            if (newWorkCost == newData[i].y) {
                continue;
            }

            dataChanged = true;
            newData[i].y = Math.round(newWorkPrice * newData[i].hours);
        }

        if (dataChanged) {
            fuelChart.series[costsIndex].setVisible(false);
            fuelChart.series[costsIndex].setData(newData);
            fuelChart.series[costsIndex].setVisible(true, true);
        }

        $('#custom_fuel_prices').modal('hide');

        return false;
    });

    function createBreakdownChart(options) {
        $('#heat_loss_breakdown').highcharts(options);
    }
});


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
  
    $http.get(Routing.generate('details_fuels', {id: window.calculationId})).
        success(function(data, status, headers, config) {
            $scope.heatingVariants = data.variants;
            $scope.currentVariant = data.currentVariant;
            
            for (var i = 0; i < $scope.heatingVariants.length; i++) {
                $scope.heatingVariants[i].cost = Math.round($scope.heatingVariants[i].price * $scope.heatingVariants[i].amount);
                $scope.heatingVariants[i].savedMoney = $scope.currentVariant.cost - $scope.heatingVariants[i].cost;
                $scope.heatingVariants[i].savedTime = $scope.currentVariant.time - $scope.heatingVariants[i].maintenance_time;
                $scope.heatingVariants[i].roi = Math.round($scope.roiPeriod($scope.heatingVariants[i].savedMoney, $scope.heatingVariants[i].savedTime, $scope.heatingVariants[i].setup_cost)); 
            }
            
            console.log($scope.heatingVariants);
            console.log($scope.currentVariant);
            
            $scope.createFuelChart();
            
            $scope.heatingVariants.sort(function (a, b) { return a.roi - b.roi });
        }).
        error(function(data, status, headers, config) {
            // log error
        });
        
    $scope.calculateFuelCosts = function (data)
    {
        /*
         'price' => cena jednostkowa
         'amount' => ilosc jednostek paliwa
         'trade_amount' => mnoznik handlowy jednostek paliwa,
         'trade_unit' => nazwa jednostki handlowej,*/
        
        data.sort(function(a, b) { return (a.price * a.amount) - (b.price * b.amount) });
        
        var series = [];
        series[0] = {
            name: 'Koszt paliwa',
            data: [],
            index: 1,
            showInLegend: false
        };
        
        for (var i = 0; i < data.length; i++) {
            $scope.fuelChartOptions.xAxis.categories.push(data[i].label);

            series[0]['data'][i] = data[i];
            series[0]['data'][i].y = data[i].cost;
            series[0]['data'][i].trade_unit_price = data[i].price * data[i].trade_amount;
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
    
    $scope.roiPeriod = function (savedMoney, savedTime, setupCost) {
        var savedTimeEquivalent = savedTime * $scope.workHourPrice;
        var savings = $scope.includeWorkTime ? savedMoney + savedTimeEquivalent : savedMoney;

        return savings > 0 ? setupCost / savings: 0;
    };
    
    $scope.formatRoiPeriod = function (period) {
        var suffix = 'lat';
        
        if (period < 2) {
            suffix = 'rok';
        }
        
        if (period % 10 >= 2 && period % 10 < 5) {
            suffix = 'lata';
        }
        
        return period <= 20 ? Math.round(period) + " " + suffix : 'nigdy';
    };
    
    $scope.greaterThan = function(prop, val){
        return function(item){
            return item[prop] > val;
        }
    }
});
