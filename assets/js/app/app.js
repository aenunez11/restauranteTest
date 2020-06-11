/**
 * Entrypoint for JS and Styles from Delectatech (Webpack)
 */

$(document).ready(function () {

    // Auto toggle
    $(document).on('click', '.toggle-button', function () {
        let groupName = $(this).data('collapseTarget');
        $('*[data-collapse-group="' + groupName + '"]').each(function () {
            $(this).toggle('fast');
        })
    });


    // Resize window on collapse (for highcharts) or changing TABs
    $(document).on('click', '#button-collapse-left-menu,a[data-toggle="tab"]', function (e) {
        window.dispatchEvent(new Event('resize'));
    });

    /**
     * Toggle icon in click with collapse
     */
    $(document).on('click', '.collapse-button', function (e) {
        $(this).find('i').toggle();
    });

});