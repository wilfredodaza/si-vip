<body>
    <?php
        $total = 0;
        $name = $invoices[0]->name_customer;
        foreach ($invoices as $key => $value) $total += $value->payable_amount;
    ?>
    <h4 class="text-center">Edades de <?= $type == 1 ? 'Clientes' : 'Proveedores' ?></h4>
    <table  style="width:100%;">
        <thead>
            <tr>
                <th><?= $type == 1 ? 'Cliente' : 'Proveedor' ?></th>
                <th>Fecha inicio</th>
                <th>Fecha fin</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= $name ?></td>
                <td><?= $date_init ?></td>
                <td><?= $date_end ?></td>
                <td>$ <?= number_format($total, '2', ',', '.') ?></td>
            </tr>
        </tbody>
    </table>
    <hr>
    <div class="row">
        <div class="col s12 m12 l12">
            <table   style="width:100%;">
                <thead>
                <tr>
                    <th class="text-right">#</th>
                    <th class="text-right">Fecha de creacion</th>
                    <th class="text-right">N° Factura</th>
                    <th class="text-right">Forma de pago</th>
                    <th class="text-right">Valor</th>
                    <th class="text-right">Total</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($invoices as $key => $item):?>
                    <tr>
                        <td class="text-right"><?= $key + 1 ?></td>
                        <td class="text-right"><?= $item->created_at ?></td>
                        <td class="text-right"><?= $item->resolution ?></td>
                        <td class="text-right"><?= $item->payment_form ?></td>
                        <td class="text-right">$ <?= number_format($item->line_extesion_amount, '2', ',', '.') ?></td>
                        <td class="text-right">$ <?= number_format($item->payable_amount, '2', ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
