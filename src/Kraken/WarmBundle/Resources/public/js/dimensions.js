(function(window, $){

    function refreshAreaPreview() {
        var enoughData = $('#calculation_area').val() > 0 || ($('#calculation_building_width').val() > 0 && $('#calculation_building_length').val() > 0)

        var floorArea = 0;
        if ($('#calculation_area').val() > 0) {
            fakeEdge = Math.sqrt($('#calculation_area').val()) - 0.8; // assume 40cm thick wall
            floorArea = fakeEdge * fakeEdge;
        } else {
            floorArea = ($('#calculation_building_width').val() - 0.8) * ($('#calculation_building_length').val() - 0.8) - $('#calculation_building_contour_free_area').val();
        }
        var heatedArea = $('#heated_floors input:checked').size() * floorArea;
        var totalArea = ($('#heated_floors label:visible').size() - 1) * floorArea;

        $('#heated_area').text(parseInt(heatedArea));
        $('#total_area').text(parseInt(totalArea));

        $('#area_preview').toggle(enoughData);
    }

    function refreshHeatedFloors() {
        floors = parseInt($('#calculation_building_floors').val());
        if ($('#calculation_building_roof').val() == 'steep') {
            floors +=1;
        }

        $('#calculation_building_heated_floors_0').parents('.checkbox').toggle($('#calculation_has_basement').is(':checked'));
        $('#calculation_building_heated_floors_1').parents('.checkbox').toggle(floors >= 1);

        $('#calculation_building_heated_floors_2').parents('.checkbox').toggle(floors >= 2);
        $('#heated_floors label').eq(3).contents().last()[0].textContent = ' 1. piętro';
        $('#calculation_building_heated_floors_3').parents('.checkbox').toggle(floors >= 3);
        $('#heated_floors label').eq(4).contents().last()[0].textContent = ' 2. piętro';
        $('#calculation_building_heated_floors_4').parents('.checkbox').toggle(floors >= 4);
        $('#heated_floors label').eq(5).contents().last()[0].textContent = ' 3. piętro';
        $('#calculation_building_heated_floors_5').parents('.checkbox').toggle(floors >= 5);
        $('#heated_floors label').eq(6).contents().last()[0].textContent = ' 4. piętro';
        $('#calculation_building_heated_floors_6').parents('.checkbox').toggle(floors >= 6);
        $('#heated_floors label').eq(7).contents().last()[0].textContent = ' 5. piętro';

        if ($('#calculation_building_roof').val() == 'steep') {
            $('#heated_floors label:visible').last().contents().last()[0].textContent = ' Poddasze';
        }

        $('#heated_floors label:hidden').each(function() {
            $(this).children('input').prop('checked', false);
        });
    }


    $('#calculation_area').change(function () {
        refreshAreaPreview();
    });
    $('#calculation_building_width').change(function () {
        refreshAreaPreview();
    });
    $('#calculation_building_length').change(function () {
        refreshAreaPreview();
    });
    $('#calculation_building_length').change(function () {
        refreshAreaPreview();
    });
    $('#calculation_building_contour_free_area').change(function () {
        refreshAreaPreview();
    });

    $('#calculation_building_shape').change(function () {
        $('#contour_free_area').toggle($(this).val() != 'regular');
    });

    $('#calculation_has_area_0').on('click', function() {
        $('#has_area_no').hide();
        $('#contour_explanation').hide();

        $('#calculation_building_shape').val('regular');
        $('#calculation_building_length').val('');
        $('#calculation_building_width').val('');
        $('#calculation_building_contour_free_area').val('');
        $('#contour_free_area').hide();

        $('#has_area_yes').show();
    });

    $('#calculation_has_area_1').on('click', function() {
        $('#has_area_yes').hide();

        $('#calculation_area').val('');

        $('#contour_explanation').show();
        $('#has_area_no').show();
    });

    refreshHeatedFloors();
    refreshAreaPreview();

    var paper = null;

    $('#calculation_building_floors').on('change', function(paper) {
        refreshHeatedFloors();
        refreshAreaPreview();

        drawPreview(paper);
    });
    $('#calculation_building_roof').on('change', function(paper) {
        refreshHeatedFloors();
        refreshAreaPreview();

        drawPreview(paper);
    });
    $('#calculation_has_basement').on('change', function(paper) {
        refreshHeatedFloors();
        refreshAreaPreview();

        drawPreview(paper);
    });
    $('#heated_floors').on('change', function(paper) {
        refreshAreaPreview();
        drawPreview(paper);
    });

    function drawPreview(paper) {
        var paperWidth = $('#house_preview').width();
        var paperHeight = $('#house_preview').height();

        if (paper) {
            $('#house_preview').empty();
        }

        paper = Raphael("house_preview", paperWidth, paperHeight);
        paper.setViewBox(0, 0, paperWidth, paperHeight, true);
        paper.setSize('100%', '100%');

        var heatedColor = "#FFAC4D";
        var notHeatedColor = "#E8FFA7";
        var skyHeight = 0.75*paperHeight;
        var groundHeight = 0.25*paperHeight;
        var verticalSpace = 0.9*skyHeight;
        var horizontalSpace = 1.2*verticalSpace;
        var center = paperWidth/2;

        //parameters
        var totalFloors = parseInt($('#calculation_building_floors').val()) + 1;
        var roofType = $('#calculation_building_roof').val();
        var hasBasement = $('#calculation_has_basement').is(':checked');
        var heatedFloors = $('#heated_floors input:checked').map(function() {return parseInt(this.value);}).get();

        paper.rect(0, 0, paperWidth, skyHeight, 0).attr({fill: "#5ECAF1", stroke: "none"});
        paper.rect(0, skyHeight, paperWidth, groundHeight, 0).attr({fill: "#BE5052", stroke: "none"});

        var verticalMargin = totalFloors > 3 ? 0.05*skyHeight : 0.2*skyHeight;
        var floorHeight = (skyHeight-verticalMargin)/totalFloors;
        var floorWidth = 2*(center-(center-horizontalSpace*0.33));

        //roof
        if (roofType == 'steep') {
            isFloorHeated = heatedFloors.indexOf(totalFloors) != -1;
            heatedText = isFloorHeated ? "ogrzewane" : "nieogrzewane";
            floorFill = isFloorHeated ? heatedColor : notHeatedColor;

            var roof = paper.path(
                "M " + center + " " + (verticalMargin) +
                " L " + (center-horizontalSpace*0.4) + " " + (verticalMargin+floorHeight) +
                " L " + (center+horizontalSpace*0.4) + " " + (verticalMargin+floorHeight) +
                " L " + center + " " + (verticalMargin)
            );
            roof.attr("fill", "#ff0000");
            var roofFill = paper.path(
                "M " + center + " " + (verticalMargin+0.05*floorHeight) +
                " L " + (center-horizontalSpace*0.4+0.05*horizontalSpace) + " " + (verticalMargin+floorHeight*0.95) +
                " L " + (center+horizontalSpace*0.4-0.05*horizontalSpace) + " " + (verticalMargin+floorHeight*0.95) +
                " L " + center + " " + (verticalMargin+0.05*floorHeight)
            );
            roofFill.attr("fill", floorFill);

            var roofLabel = paper.text(center, verticalMargin+0.5*floorHeight, "Poddasze").attr({fill: '#000'});
            roofLabel.attr({"font-size": 14, "font-weight": 'bold'});
            var roofLabel2 = paper.text(center, verticalMargin+0.5*floorHeight+10, heatedText).attr({fill: '#000'});
            roofLabel2.attr({"font-size": 12});
        } else {
             var flatRoof = paper.rect(center-horizontalSpace*0.4, verticalMargin+0.9*floorHeight, 0.8*horizontalSpace, floorHeight*0.1, 0).attr({fill: "#777", stroke: "none"});
             var flatRoof2 = paper.rect(center-horizontalSpace*0.35, verticalMargin+0.8*floorHeight, 0.7*horizontalSpace, floorHeight*0.15, 0).attr({fill: "#222", stroke: "none"});
        }

        var floors = [
            'Piwnica',
            'Parter',
            '1. piętro',
            '2. piętro',
            '3. piętro',
            '4. piętro',
            '5. piętro',
        ];

        //floors
        for (floorIndex = 1; floorIndex < totalFloors; floorIndex++) {
            reversedFloorIndex = totalFloors - floorIndex;
            isFloorHeated = heatedFloors.indexOf(reversedFloorIndex) != -1;
            floorName = floors[reversedFloorIndex];

            if (floorName == 'Parter') {
                heatedText = isFloorHeated ? "ogrzewany" : "nieogrzewany";
            } else {
                heatedText = isFloorHeated ? "ogrzewane" : "nieogrzewane";
            }

            floorFill = isFloorHeated ? heatedColor : notHeatedColor;
            var floor = paper.rect(center-horizontalSpace*0.33, verticalMargin+floorIndex*floorHeight, floorWidth, floorHeight, 0).attr({fill: "#777", stroke: "none"});
            var floorFill = paper.rect(center-horizontalSpace*0.33+10, verticalMargin+floorIndex*floorHeight+5, floorWidth-20, floorHeight-10, 0).attr({fill: floorFill, stroke: "none"});
            var floorLabel = paper.text(center, verticalMargin+(floorIndex+0.4)*floorHeight, floorName).attr({fill: '#000'});
            floorLabel.attr({"font-size": 16, "font-weight": 'bold'});
            var floorLabel = paper.text(center, verticalMargin+(floorIndex+0.4)*floorHeight+15, heatedText).attr({fill: '#000'});
            floorLabel.attr({"font-size": 14});
        }

        if (hasBasement) {
            isFloorHeated = heatedFloors.indexOf(0) != -1;
            floorFill = isFloorHeated ? heatedColor : notHeatedColor;
            heatedText = isFloorHeated ? "ogrzewana" : "nieogrzewana";
            basementHeight = Math.min(floorHeight*1.5, 0.8*groundHeight);
            var basement = paper.rect(center-horizontalSpace*0.33, skyHeight, floorWidth, basementHeight, 0).attr({fill: "#777", stroke: "none"});
            var basementFill = paper.rect(center-horizontalSpace*0.33+10, skyHeight+5, floorWidth-20, basementHeight-15, 0).attr({fill: floorFill, stroke: "none"});
            var basementLabel = paper.text(center, skyHeight+groundHeight*0.3, "Piwnica").attr({fill: '#000'});
            basementLabel.attr({"font-size": 16, "font-weight": 'bold'});
            var basementLabel = paper.text(center, skyHeight+groundHeight*0.3+15, heatedText).attr({fill: '#000'});
            basementLabel.attr({"font-size": 14});
        } else {
            var noBasementLabel = paper.text(center, skyHeight+20, "bez podpiwniczenia").attr({fill: '#fff'});
            noBasementLabel.attr({"font-size": 14, "font-weight": 'bold'});
        }
    }

    drawPreview(paper);

})(window, jQuery);
