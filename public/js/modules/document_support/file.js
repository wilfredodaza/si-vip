$(document).ready(function () {
    $('.send').click(function () {
        let id = $(this).data('id');
        const url = window.localStorage.getItem('url');
        $('#form-resolution').attr('action', url + '/document_support/send/' + id);
    });
});