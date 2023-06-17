$(document).ready(function() {
    $('.send').click(function () {
        $('#form-resolution').attr('action', `${localStorage.getItem('url')}/payrolls/send/${$(this).data('send')}`)
    });
});