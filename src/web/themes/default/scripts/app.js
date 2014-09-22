var $body = $('body');

$body.on('contentcreated', function (event) {
    // Fire any other events on this element
    // But don't bubble
    event.stopPropagation();
    var $el = $(event.target);

    $el.find('[data-widget~="buttonGroup"]').buttonFlyout();
    $el.find('[data-widget~="entitySearch"]').entitySearch();
    $el.find('[data-widget~="collection"]').collection();
    $el.find('[data-widget~="flyoutTrigger"]').flyoutTrigger();
    $el.find('[data-widget~="togglable"]').togglable();
    $el.find('[data-widget~="dataGrid"]').dataGrid();

}).trigger('contentcreated');

if ($.support.pjax) {
    $.pjax.defaults.timeout = false;
    $('body').on('click', 'a[data-pjax]', function(event) {
        $page = $(".Page");
        $.pjax.click(event, {
            container: $page,
            fragment: "#Page"
        });
    });
    $(document).on('pjax:end', function(event) {
        $(event.target).trigger('contentcreated');
    });
}

$(document).bind("ajaxSend", function(){
    $("#loading").show();
}).bind("ajaxComplete", function(){
    $("#loading").hide();
});
