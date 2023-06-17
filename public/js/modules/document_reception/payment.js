$(document).ready(function () {
    $('.payment_upload').click(function() {
        var id = $(this).data('document_id');
        $('#payment_upload_files').attr('action', localStorage.getItem('url') + '/documents/payment_upload/'  + id)
    });
})