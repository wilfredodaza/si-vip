$(document).ready(function () {


    const created = CKEDITOR.replace('editorCreate', {
        extraPlugins: 'notification'
    });

    created.on('required', function (evt) {
        edit.showNotification('El campo Observación es obligatorio.', 'warning');
        evt.cancel();
    });

    const edit = CKEDITOR.replace('editorEdit', {
        extraPlugins: 'notification'
    });

    edit.on('required', function (evt) {
        edit.showNotification('El campo Observación es obligatorio.', 'warning');
        evt.cancel();
    });

    var url = localStorage.getItem('url');
    $("#notification").change(function () {
        if ($('#notification').prop('checked')) {
            $('#created_at').show();
        } else {
            $('#created_at').hide();

        }
    });

    $('.quotation_edit').click(function () {
        const id = $(this).data('id');
        const quotationId = $(this).data('quotation-id');
        $('#formEdit').attr('action', `${url}/tracking/update/${quotationId}/quotation/${id}`)
        $.get(`${url}/tracking/edit/${id}`, function (data) {
            const info = JSON.parse(data);
            edit.setData(info.message);
        });
    });
});