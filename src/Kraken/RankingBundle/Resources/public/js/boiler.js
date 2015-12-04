$(document).ready(function () {
    if (window.location.hash != '') {
        $('#boilerTabs a[href="' + hash + '"]').tab('show');
    }

    $('#boilerTabs a').click(function (e) {
          e.preventDefault();
          $(this).tab('show');
    });
});
