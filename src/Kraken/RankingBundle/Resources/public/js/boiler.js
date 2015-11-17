$(function () {
    $('#boilerTabs a').click(function (e) {
          e.preventDefault();
          $(this).tab('show');
    });
});
