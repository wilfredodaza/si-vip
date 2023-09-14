const addBono = [];
const formatter = new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 2
});

function viewGastos(id) {
    var gasto = gastos.find(gasto => parseInt(gasto.id) === id);
    console.log(gasto);
    var count_lines = gasto.line_invoice.length;
    var body = `
        <thead>
          <tr>
            <th>Gasto</th>
            <th>Descripción</th>
            <th>Valor gasto</th>
            <th>Valor pagado</th>
            <th>Valor a pagar</th>
          </tr>
        </thead>
        <tbody>`;
    gasto.line_invoice.forEach(detail => {
        var max = (parseInt(detail.price_amount) - parseInt(detail.line_invoice_payment));
        body += `
      <tr>
        <td>${detail.name}</td>
        <td>${detail.description}</td>
        <td>${formatter.format(detail.price_amount)}</td>
        <td>${formatter.format(detail.line_invoice_payment)}</td>
        <td>
          <div class="input-field col s12">
            <input max="${max}" id="line_invoice_${detail.id}" value="${detail.value_payroll ? detail.value_payroll : ''}" placeholder="Valor máximo ${formatter.format(max)}" type="number" class="validate">
          </div>
        </td>
      </tr> 
      `;
    });
    body += `</tbody>`;
    $('#viewGastos table').html(body);
    $('#viewGastos #btnSavePayroll').attr('onclick', `saveLineGasto(${id})`);
    $('#viewGastos').modal('open');
}

function saveLineGasto(id) {
    var total = 0;
    var valid = true;
    gastos.map((gasto, index) => {
        gasto.line_invoice.map((line_invoice, idx) => {
            if (line_invoice.invoices_id == id) {
                $(`.trGasto_${line_invoice.id}`).remove();
                var value = $(`#line_invoice_${line_invoice.id}`).val();
                var valid_aux = parseInt(value ? value : 0) <= (parseInt(line_invoice.price_amount) - parseInt(line_invoice.line_invoice_payment)) ? true : false;
                if (valid_aux) {
                    line_invoice.value_payroll = parseInt(value);
                    console.log(line_invoice);
                    $('.table-salary tbody').find("tr:eq(0)").after(`
                  <tr class="trGasto_${line_invoice.id}">
                    <td>${line_invoice.start_date}</td>
                    <td>${line_invoice.name} - [${line_invoice.description}]</td>
                    <td>${formatter.format(-line_invoice.value_payroll)}</td>
                  </tr>
                `);
                    total += parseInt(line_invoice.value_payroll);
                } else {
                    valid = false;
                    alert(
                        `<span class="red-text">
                      ${line_invoice.name} [${line_invoice.description}]
                      supera el máximo de
                      ${formatter.format(parseInt(line_invoice.price_amount) - parseInt(line_invoice.line_invoice_payment))}
                    </span>`,
                        'red lighten-5', 4000);
                }
            }
        });

    });
    if (valid) {
        $('#viewGastos').modal('close');
    }
}

async function bono(idx = null) {
    if (idx != null)
        var line_invoice = addBono.find((bonoAux, index) => idx == index);
    const { value: data } = await Swal.fire({
        title: 'Añadir detalle',
        html: `
        <div>
            <p style="
            display: flex;
            align-items: center;
            justify-content: space-around;
        ">
                <label>
                    <input class="with-gap" name="type_concepto" value="1" type="radio" ${line_invoice ? (line_invoice.payroll ? 'checked' : '') : 'checked'} />
                    <span style="display:flex"><i class="material-icons">attach_money</i> Devengado</span>
                </label>
                <label>
                    <input class="with-gap" name="type_concepto" value="0" type="radio" ${line_invoice ? (!line_invoice.payroll ? 'checked' : '') : ''}/>
                    <span style="display:flex"><i class="material-icons">money_off</i> Deducción</span>
                </label>
            </p>
        </div>
      <p>
        <label>
          <textarea id="description_detail" class="materialize-textarea">${line_invoice ? line_invoice.description : ''}</textarea>
          <label for="description_detail">Descripción</label>
        </label>
      </div>
      <p>
          <label>
              <input value="${line_invoice ? line_invoice.price_amount : ''}" id="value_modal" type="number" class="validate">
              <label for="value_modal">Valor añadir</label>
          </label>
      </p>`,
        preConfirm: () => {
            const value = $('#value_modal').val();
            const description = $('#description_detail').val();
            const type_concepto = $('input[name="type_concepto"]:checked').val();
            if (!value) {
                Swal.showValidationMessage('El valor a añadir es necesario.'); // Mostrar mensaje de error
            } else if (description == '') {
                Swal.showValidationMessage('La descripción es necesaria.'); // Mostrar mensaje de error
            } else {
                return { 'value': parseInt(value), 'description': description, 'type_concepto': type_concepto == 1 ? true : false };
            }
        }
    });
    if (data) {
        if (idx == null) {
            var line_invoice = {
                products_id: productOtros.id,
                name: productOtros.name,
                quantity: 1,
                line_extension_amount: data.value,
                price_amount: data.value,
                description: data.description,
                payroll: data.type_concepto,
                value_payroll: data.value,
            }
            addBono.push(line_invoice);
        } else {
            addBono.map((bonoA, index) => {
                if (idx == index) {
                    bonoA.line_extension_amount = data.value;
                    bonoA.price_amount = data.value;
                    bonoA.value_payroll = data.value;
                    bonoA.description = data.description;
                    bonoA.payroll = data.type_concepto;
                }
            })
            var line_invoice = addBono.find((bonoAux, index) => idx == index);
        }
        updateTable();
    }
}

function deleteBono(idx) {
    addBono.splice(idx, 1);
    updateTable();
}

function updateTable() {
    $('.tr_detail').remove();
    // var total = 0;
    // var expense = 0;
    addBono.forEach((bonoA, index) => {
        var today = new Date();
        var date = formatDate(today);
        $('.table-salary tbody').find("tr:eq(0)").after(`
        <tr class="tr_detail">
          <td>${date}</td>
          <td>${bonoA.name ? bonoA.name +" - ": ''}[${bonoA.description}]</td>
          <td>${bonoA.payroll ? '': '-'}${formatter.format(bonoA.price_amount)}</td>
        </tr>
      `);

        $('#gastos tbody').append(`
        <tr class="tr_detail">
          <td>${date}</td>
          <td>Concepto nómina</td>
          <td>${bonoA.description}</td>
          <td>${bonoA.payroll ? '': '-'}${formatter.format(bonoA.price_amount)}</td>
          <td>${bonoA.payroll ? '': '-'}${formatter.format(bonoA.price_amount)}</td>
          <td>
            <a class="blue-text" href="javascript:void(0);" onclick="bono(${index})"><i class="material-icons">attach_money</i></a>
            <a class="red-text" href="javascript:void(0);" onclick="deleteBono(${index})"><i class="material-icons">delete</i></a>
          </td>
        </tr>
      `);
        // console.log(bonoA);
        // bonoA.payroll ? total += bonoA.price_amount : expense += bonoA.price_amount;
    });
    // var salary = parseInt(user.salary ? user.salary : 0);
    // $('.salaryParcial').html(formatter.format(salary + total));
    // $('.expense').html(formatter.format(expense));
}

function changeValues() {
    var totalExpense = 0;
    var totalAbono = 0;
    gastos.map(gasto => {
        gasto.line_invoice.forEach(line => {
            totalExpense += line.value_payroll ? line.value_payroll : 0;
        })
    });
    addBono.forEach(bono => {
        bono.payroll ? totalAbono += bono.price_amount : totalExpense += bono.price_amount
    })
    var total = totalAbono - totalExpense;
    console.log(totalExpense, totalAbono, total);
    $('.salary').html(formatter.format(parseInt(user.salary ? user.salary : 0) + total));
    // $('#expense').val(total);
    $('.expense').html(formatter.format(totalExpense));
    $('.salaryParcial').html(formatter.format(parseInt(user.salary ? user.salary : 0) + totalAbono));
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

function pagoNomina() {
    var aux_gastos = [];
    gastos.forEach(gasto => gasto.line_invoice.forEach(line_invoice => line_invoice.value_payroll ? aux_gastos.push(line_invoice) : null));
    addBono.forEach(aux_bono => aux_gastos.push(aux_bono));
    var expense = 0;
    var bono = 0;
    aux_gastos.forEach(gasto => {
        console.log(gasto.payroll);
        gasto.payroll == true ? bono += gasto.value_payroll : expense += gasto.value_payroll;
    });
    console.log([gastos, aux_gastos, addBono]);
    var data = {
        payment_method_id: $('#payment_method_id').val(),
        sede_id: $('#sede_id').val(),
        description: $('#description').val(),
        salary: parseInt(user.salary ? user.salary : 0),
        expense: expense,
        bono: bono,
        detail: aux_gastos,
        user: $('#user').val(),
        year: $('#year').val(),
        date: $('#date').val()
    };
    $('#pagoNomina').modal('close');
    // return console.log(data);
    var url = base_url(['payrolls', 'payment']);
    var process = proceso_fetch(url, JSON.stringify(data));
    process.then(response => {
        window.location.reload();
        // console.log(response);
    });
}