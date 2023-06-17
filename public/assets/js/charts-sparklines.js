$(".select2").select2({
    dropdownAutoWidth: true,
    width: '100%'
});

var dataFirma = $('#firm').data('info');
if (!dataFirma == '') {
    $('#firm').hide();
}
var dataRUT = $('#RUT').data('info');
if (!dataRUT == '') {
    $('#RUT').hide();
}

var dataBankCertificate = $('#bank_certificate').data('info');
if (!dataBankCertificate == '') {
    $('#bank_certificate').hide();
}



$('#firm_button').click(function() {
    $('#firm').show();
    $('#firm_download').hide();
});

$('#rut_button').click(function() {
    $('#RUT').show();
    $('#rut_download').hide();
});

$('#bank_certificate_button').click(function() {
    $('#bank_certificate').show();
    $('#bank_certificate_download').hide();
});





$('#bank_certificate').dropzone({
    url: null,
    addRemoveLinks: true,
    enqueueForUpload: false,
    maxFilesize: 1,
    acceptedFiles: 'application/pdf',
    uploadMultiple: false,
    dictRemoveFile: "Quitar Archivo",
    dictInvalidFileType: 'El formato del archivo no es válido solo se admiten PDF',
    dictDefaultMessage: `
        <h5 style="color: #e0e0e0;">Anexar certificado bancario</h5>
        <small style="color: #a53394;">Por favor arrastre el documento o de clic aquí</small>`,
    init: function() {
        var myDropzone = this; // closure
        this.on("success", function(file) {
            location.reload();
        });
    }
});

$('#document_supports').dropzone({
    url: null,
    addRemoveLinks: true,
    enqueueForUpload: false,
    uploadMultiple: true,
    acceptedFiles: 'application/pdf',
    dictRemoveFile: "Quitar Archivo",
    dictInvalidFileType: 'El formato del archivo no es válido solo se admiten PDF',
    dictDefaultMessage: `
        <h5 style="color: #e0e0e0;">Anexar documentos adjuntos</h5>
        <small style="color: #a53394;">Por favor arrastre el documento o de clic aquí</small>`,
    init: function() {
        var myDropzone = this; // closure
        this.on("success", function(file) {
            location.reload();
        });
    }
});





$('#RUT').dropzone({
    url: null,
    addRemoveLinks: true,
    enqueueForUpload: false,
    maxFilesize: 1,
    uploadMultiple: false,
    acceptedFiles: 'application/pdf',
    dictRemoveFile: "Quitar Archivo",
    dictInvalidFileType: 'El formato del archivo no es válido solo se admiten PDF',
    dictDefaultMessage: `
        <h5 style="color: #e0e0e0;">Anexar RUT</h5>
        <small style="color: #a53394;">Por favor arrastre el documento o de clic aquí</small>`,
    init: function() {
        var myDropzone = this; // closure
        this.on("success", function(file) {
            location.reload();
        });
    }
});

$('#firm').dropzone({
    url: null,
    addRemoveLinks: true,
    enqueueForUpload: false,
    maxFilesize: 1,
    uploadMultiple: false,
    acceptedFiles: 'image/jpeg,image/png,image/gif',
    dictRemoveFile: "Quitar Archivo",
    dictInvalidFileType: 'El formato del archivo no es válido solo se admiten jpg, png jpeg',
    dictDefaultMessage: `
        <h5 style="color: #e0e0e0;">Anexar firma</h5>
        <small style="color: #a53394;">Por favor arrastre el documento o de clic aquí</small>`,
    init: function() {
        var myDropzone = this; // closure
        this.on("success", function(file) {
            location.reload();
        });
    }
});

const URL = localStorage.getItem('url');

fetch(`${URL}/home/products`)
    .then(function(response) {
        return response.json();
    })
    .then(function(myJson) {
        var dates = myJson;

        let labels = [];
        let values = [];

        for (const i of dates) {
            labels.push(i.name);
            values.push(i.cant);
        }


        var ctx = document.getElementById('products').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Productos mas vendidos',
                    data: values,
                    backgroundColor: [
                        '#039be5',
                        '#fdd835',
                        '#e53935',
                        '#d81b60',
                        '#8e24aa',
                    ],

                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    });

fetch(`${URL}/home/customers`)
    .then(function(response) {
        return response.json();
    })
    .then(function(myJson) {
        var dates = myJson;

        let labels = [];
        let values = [];

        for (const i of dates) {
            labels.push(i.name);
            values.push(i.cant);
        }


        var ctx = document.getElementById('customers').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Clientes mas frecuentes',
                    data: values,
                    backgroundColor: [
                        '#039be5',
                        '#fdd835',
                        '#e53935',
                        '#d81b60',
                        '#8e24aa',
                    ],

                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    });