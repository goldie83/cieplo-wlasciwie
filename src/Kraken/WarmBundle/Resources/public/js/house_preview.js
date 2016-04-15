(function(window, $){

    var paper = null;

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

        //parameters
        var buildingType = window.buildingType;
        var totalFloors = window.hasBasement && window.roofType == 'steep' ? window.totalFloors-1 : window.totalFloors;
        var roofType = window.roofType;
        var hasBasement = window.hasBasement;
        var heatedFloors = window.heatedFloors;
        var center = paperWidth/2;

        console.log("Total floors: " + totalFloors);

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

            var roofLabel = paper.text(center, verticalMargin+0.55*floorHeight, "Poddasze").attr({fill: '#000'});
            roofLabel.attr({"font-size": 14, "font-weight": 'bold'});
            var roofLabel2 = paper.text(center, verticalMargin+0.55*floorHeight+10, heatedText).attr({fill: '#000'});
            roofLabel2.attr({"font-size": 12});
        } else if (roofType == 'oblique') {
            var roof = paper.path(
                "M " + center + " " + (verticalMargin+0.5*floorHeight) +
                " L " + (center-horizontalSpace*0.4) + " " + (verticalMargin+floorHeight) +
                " L " + (center+horizontalSpace*0.4) + " " + (verticalMargin+floorHeight) +
                " L " + center + " " + (verticalMargin+0.5*floorHeight)
            );
            roof.attr("fill", "#ff0000");
        } else {
             var flatRoof = paper.rect(center-horizontalSpace*0.4, verticalMargin-0.1*floorHeight, 0.8*horizontalSpace, floorHeight*0.1, 0).attr({fill: "#777", stroke: "none"});
             var flatRoof2 = paper.rect(center-horizontalSpace*0.35, verticalMargin-0.25*floorHeight, 0.7*horizontalSpace, floorHeight*0.15, 0).attr({fill: "#222", stroke: "none"});
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
        for (floorIndex = roofType == 'flat' ? 0 : 1; floorIndex < totalFloors; floorIndex++) {
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
