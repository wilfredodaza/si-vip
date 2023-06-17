<div class="card-content">
    <div class="row">
        <div class="input-field col l6 m6 s12">
            <h6>
                Año
            </h6>
            <?php
            $cont = date('Y');
            ?>
            <select class="select2 browser-default validate" id="year" name="year" required>
                <option value="" disabled="" selected="">Seleccione una opción</option>
                <?php while ($cont >= 2021) { ?>
                    <option value="<?php echo($cont); ?>"><?php echo($cont); ?></option>
                    <?php $cont = ($cont-1); } ?>
            </select>
        </div>
        <div class="input-field col l6 m6 s12">
            <h6>
                Tipo de documento a cargar
            </h6>
            <select class="select2 browser-default validate" id="document_cargue" name="document"
                    required>
                <option value="" selected disabled >Seleccione tipo de documento</option>
                <option value="9" >Nómina Individual</option>
                <option value="10" >Nómina de Ajuste</option>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="input-field col l6 m6 s12">
            <h6>
                Mes de liquidación
            </h6>
            <select class="select2 browser-default validate" id="month_cargue" name="month"
                    required>
                <option value="" disabled="" selected="">Seleccione una opción</option>
            </select>
        </div>
        <div class="input-field col l6 m6 s12">
            <h6>
                Periodo de liquidación
            </h6>
            <select class="select2 browser-default period validate" name="period" id="period_cargue" required>
                <option value="" disabled="" selected="">Seleccione una opción</option>
                <?php foreach ($payroll_periods as $periods): ?>
                    <option value="<?= $periods->id ?>"><?= $periods->name ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="row">
        <!-- cargue -->
        <div class="col s12 m6 l6 ">
            <div class="button">
                <button type="button" class="btn btn-samll indigo addDate right"
                        style="padding-left: 10px;padding-right:10px; margin-top:20px;"> +
                </button>
            </div>
            <div class="input input-field">
                <input type="date" id="payment_dates" class="validate" name="payments" placeholder="Fechas de pago">
                <label for="payment_dates" class="active">Fechas de pago</label>
                <input type="hidden" name="payment_dates[]" class="payment_dates">
            </div>

        </div>
        <div class="col s12 m6 l6 ">
            <h6 class="red-text date-info">No se encuentran fechas agregadas</h6>
            <ul class="collection addDates">
            </ul>
        </div>
        <div class="col s12 m12 l12">
            <label for="files" class="active black-text">Cargar Documentos</label>
            <input type="file" name="file[]" class="dropify" id="files" data-allowed-file-extensions="xls xlsx" required multiple>
        </div>
        <!-- end cargue -->
    </div>
    <div class="row">
        <br>
        <div class="col s12 m12 l12">
            <button type="submit" id="btncargue"
                    class="waves-effect waves-light btn-large float-right btn-light-indigo"
                    style="position: static;"><i class="material-icons right">file_upload</i>Cargar
            </button>
        </div>
    </div>
</div>
