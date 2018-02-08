$(function () {
    function initResponsiveTables() {
        var $desktopTable = $('.list-table');
        if ($(window).width() < 1001 && $desktopTable.length && $('.responsive-table').length == 0) {
            /* create responsive table */
            $desktopTable.responsiveTable({tableClass: 'responsive-table'});
        }
    }

    initResponsiveTables();
    var resizeTimeoutId;
    $(window).resize(function () {
        clearTimeout(resizeTimeoutId);
        resizeTimeoutId = setTimeout(function () {
            initResponsiveTables();
        }, 100);
    });
});

$.fn.responsiveTable = function(options) {
    var defaults = {},
        settings = $.extend({}, defaults, options);

    var $table = $(this);
    var tableClass = (typeof settings.tableClass !== 'undefined') ? settings.tableClass : '';
    var $topRow, body, tr_class;

    $table.addClass('desktop-table');
    $topRow = $table.find('tr').eq(0);
    
    body = '';
    var i = 0;
    $table.find('tbody tr').each(function() {
        tr_class = $(this).prop('class');
        tr_class += (i % 2 == 0 ? ' even' : '');
        i++;

        $(this).find('td,th').each(function(cellIndex) {
            if ($(this).html() !== ''){
                body += '<tr class="' + tr_class +'">';
                if ($topRow.find('td,th').eq(cellIndex).html()){
                    body += '<td>'+$topRow.find('td,th').eq(cellIndex).html()+'</td>';
                } else {
                    body += '<td></td>';
                }
                body += '<td class="'+$(this).prop('class')  +'">'+$(this).html()+'</td>';
                body += '</tr>';
            }
        });
    });

    var content = '</i><table class="table table-bordered ' + tableClass + '"><tbody>' + body + '</tbody></table>';
    $table.before(content);
};