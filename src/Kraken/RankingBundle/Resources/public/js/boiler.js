$(document).ready(function () {
    if (window.location.hash != '') {
        $('#boilerTabs a[href="' + window.location.hash + '"]').tab('show');
    }

    $('#boilerTabs a').click(function (e) {
          e.preventDefault();
          $(this).tab('show');
    });
});
