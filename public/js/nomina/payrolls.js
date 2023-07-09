function viewGastos(id) {
    const formatter = new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 2
    });
    var gasto = gastos.find(gasto => parseInt(gasto.id) === id);
    var count_lines = gasto.line_invoice.length;
    var body = `
        <thead>
          <tr>
            <th>Gasto</th>
            <th>Descripción</th>
            <th>Valor gasto</th>
            ${count_lines == 1 ? '<th>Valor a pagar</th>' : '' }
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>`;
    gasto.line_invoice.forEach(detail => {
                body += `

            <tr>
              <td>${detail.name}</td>
              <td>${detail.description}</td>
              <td>${formatter.format(detail.price_amount)}</td>
              ${count_lines == 1 ? `<td><div class="input-field col s12">
                  <input placeholder="Valor a pagar" onkeyup="changeValue(this.value, ${id})" type="number" class="validate">
                </div></td>` : '' }
              <td>
                <p>
                  <label>
                    <input ${detail.payroll ? 'checked="checked"' : ''} id="line_invoice_${detail.id}" name="line_invoice[]" value="${detail.id}" type="checkbox" />
                    <span></span>
                  </label>
                </p>
              </td>
            </tr> 

          `;
    });
    body += `</tbody>`;
    $('#viewGastos table').html(body);
    $('#viewGastos #btnSavePayroll').attr('onclick', `saveLineGasto(${id})`);
    $('#viewGastos').modal('open');
}

function saveLineGasto(id){
  var total = 0;
  const formatter = new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 2
  });
  gastos.forEach((gasto, index) => {
    if(parseInt(gasto.id) === id){
      gasto.line_invoice.forEach((line_invoice, idx) => {
        var checked = $(`#line_invoice_${line_invoice.id}`).is(':checked');
        line_invoice.payroll = checked;
        console.log(line_invoice);
        if(checked){
          $('.table-salary tbody').find("tr:eq(0)").after(`
            <tr class="trGasto_${line_invoice.id}">
              <td>${line_invoice.start_date}</td>
              <td>${line_invoice.name} - [${line_invoice.description}]</td>
              <td>${formatter.format(line_invoice.value_payroll)}</td>
            </tr>
          `);
        }else{
          $(`.trGasto_${line_invoice.id}`).remove();
        }
      })
    }
  });
  gastos.forEach((gasto, index) => {
      gasto.line_invoice.forEach((line_invoice, idx) => {
        if(line_invoice.payroll){
            total += parseInt(line_invoice.value_payroll);
        }
      })
  });
  $('.salary').html(formatter.format(parseInt(customer.salary) + total));
  $('#expense').val(total);
}

function changeValue(value, id){
  gastos.forEach((gasto, index) => {
    if(parseInt(gasto.id) === id){
        gastos[index].line_invoice[0].value_payroll = parseInt(value);
    }
  });
}

function changeFile(event) {
  const url = event.target.files[0];
  $(`#filename`).val(event.target.files[0].name);
  const reader = new FileReader();
  reader.readAsDataURL(url);
  const data = reader.onload = () => {
      const base64 = reader.result;
      $(`#file`).val(base64);
  }
}

function pagoNomina(){
  var aux_gastos = [];
  gastos.forEach(gasto => gasto.line_invoice.forEach(line_invoice => line_invoice.payroll ? aux_gastos.push(line_invoice) : null ));
  var data = {
    payment_method_id:  $('#payment_method_id').val(),
    sede_id:            $('#sede_id').val(),
    description:        $('#description').val(),
    salary:             $('#salary').val(),
    expense:            $('#expense').val(),
    detail:             aux_gastos,
    customer:           $('#customer').val(),
    year:               $('#year').val(),
    date:               $('#date').val()
  };
  var url = base_url(['payrolls', 'payment']);
  var process = proceso_fetch(url, JSON.stringify(data));
  process.then(response => {
    console.log(response);
  });
}