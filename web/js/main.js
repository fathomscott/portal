$('.confirm-delete').on('click', function (e) {
    e.preventDefault();
    var modal = $('#confirm-delete');
    var url = $(this).attr('href');
    var title = $(this).data('title');
    var content = $(this).data('content');

    if (!url) {
        return;
    }

    $('.submit', modal).attr('href', url);

    if (title) {
        $('.modal-title', modal).text(title);
    }

    if (content) {
        $('.confirm-body' ,modal).text(content);
    }

    modal.modal();
});

$('th.sorting, th.sorting_asc, th.sorting_desc').on('click', function (e) {
    if (e.target.tagName.toUpperCase() === 'TH') {
        window.location = $(this).find('a').attr('href');
    }
});
