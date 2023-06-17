<div class="card-content">
    <div class="row">
        <div class="input-field col l4 m4 s12">
            <input id="titulo" name="titulo" type="number" value="1" class="validate"
                   required>
            <label class="active" for="titulo">Número de fila del encabezado hoja 1</label>
        </div>
        <div class="input-field col l4 m4 s12">
            <input id="titulo2" name="titulo2" type="number" value="1" class="validate"
                   required>
            <label class="active" for="titulo2">Número de fila del encabezado hoja 2</label>
        </div>
        <div class="input-field col l4 m4 s12">
            <input id="titulo3" name="titulo3" type="number" value="1" class="validate"
                   required>
            <label class="active" for="titulo3">Número de fila del encabezado hoja 3</label>
        </div>
    </div>
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
                <option value="110" >Desprendibles empleados</option>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="input-field col l6 m6 s12">
            <h6>
                Mes de liquidación
            </h6>
            <select class="select2 browser-default" id="month_cargue" name="month"
                    required>
                <option value="" disabled="" selected="">Seleccione una opción</option>
            </select>
        </div>
        <div class="input-field col l6 m6 s12">
            <h6>
                Periodo de liquidación
            </h6>
            <select class="select2 browser-default period" id="period_cargue" name="period" required>
                <option value="" disabled="" selected="">Seleccione una opción</option>
                <?php foreach ($payroll_periods as $periods): ?>
                    <option value="<?= $periods->id ?>"><?= $periods->name ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- cargue -->
        <div id="cargue1">
            <div id="fp1" class="input-field col l2 m2 s12">
                <input name="fp1[]" id="fp_cargue" type="date" class="validate" required>
                <label class="active" for="titulo">Fechas de pago</label>
            </div>
            <div id="file" class="col s12 m4 l4">
                <div class="file-field input-field">
                    <div class="btn indigo">
                        <span>Archivo</span>
                        <input type="file" id="files" name="file1">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text" required>
                    </div>
                </div>
            </div>
            <div id="fi1" class="input-field col l3 m3 s12">
                <input name="fi1" id="fi_cargue" type="date" class="validate" required>
                <label class="active" for="titulo">Fecha inicio de liquidación</label>
            </div>
            <div id="ff1" class="input-field col l3 m3 s12">
                <input name="ff1" id="ff_cargue" type="date" class="validate" required>
                <label class="active" for="titulo">Fechas fin de liquidación</label>
            </div>
        </div>
        <div class="col s6 m6 l6">
            <!--<a href="<?= base_url('upload/tyc.xlsx') ?>"
               class="waves-effect waves-light btn-large float-left btn-light-indigo"
               download="Plantilla" style="position: static;"><i class="material-icons right">file_download</i>Descargar
                PLantilla
            </a>-->
        </div>
        <div class="col s6 m6 l6">
            <button type="submit" id="btncarguetyc"
                    class="waves-effect waves-light btn-large float-right btn-light-indigo"
                    style="position: static;"><i class="material-icons right">file_upload</i>Cargar
            </button>
        </div>
    </div>
</div>