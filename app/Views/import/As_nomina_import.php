<div class="card-content">
    <div class="row">
        <div class="input-field col l12 m12 s12">
            <input id="titulo" name="titulo" type="number" class="validate"
                   required>
            <label class="active" for="titulo">Número de fila del encabezado</label>
        </div>
        <div class="input-field col l6 m6 s12">
            <h6>
                Mes de liquidación
            </h6>
            <select class="select2 browser-default" name="month"
                    required>
                <option value="" disabled="" selected="">Seleccione una opción</option>
                <?= (in_array('1', $periods)) ? '' : '<option value="1">Enero</option>'; ?>
                <?= (in_array('2', $periods)) ? '' : '<option  value="2">Febrero</option>'; ?>
                <?= (in_array('3', $periods)) ? '' : '<option  value="3">Marzo</option>'; ?>
                <?= (in_array('4', $periods)) ? '' : '<option  value="4">Abril</option>'; ?>
                <?= (in_array('5', $periods)) ? '' : '<option  value="5">Mayo</option>'; ?>
                <?= (in_array('6', $periods)) ? '' : '<option  value="6">Junio</option>'; ?>
                <?= (in_array('7', $periods)) ? '' : '<option  value="7">Julio</option>'; ?>
                <?= (in_array('8', $periods)) ? '' : '<option  value="8">Agosto</option>'; ?>
                <?= (in_array('9', $periods)) ? '' : '<option  value="9">Septiembre</option>'; ?>
                <?= (in_array('10', $periods)) ? '' : '<option  value="10">Octubre</option>'; ?>
                <?= (in_array('11', $periods)) ? '' : '<option  value="11">Noviembre</option>'; ?>
                <?= (in_array('12', $periods)) ? '' : '<option  value="12">Diciembre</option>'; ?>
            </select>
        </div>
        <div class="input-field col l6 m6 s12">
            <h6>
                Periodo de liquidación
            </h6>
            <select class="select2 browser-default period" name="period" required>
                <option value="" disabled="" selected="">Seleccione una opción</option>
                <?php foreach ($payroll_periods as $periods): ?>
                    <option value="<?= $periods->id ?>"><?= $periods->name ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- cargue -->
        <div id="fp1" class="input-field col l2 m2 s12">
            <input name="fp1" type="date" class="validate" required>
            <label class="active" for="titulo">Fechas de pago</label>
        </div>
        <div id="file" class="col s12 m4 l4">
            <div class="file-field input-field">
                <div class="btn indigo">
                    <span>Archivo</span>
                    <input type="file" name="file1">
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text" required>
                </div>
            </div>
        </div>
        <div id="fp2" class="input-field col l2 m2 s12">
            <input id="fpago2" name="fp2" type="date" class="validate">
            <label class="active" for="titulo">Fechas de pago</label>
        </div>
        <div id="file2" class="col s12 m4 l4">
            <div class="file-field input-field">
                <div class="btn indigo">
                    <span>Archivo 2</span>
                    <input type="file" name="file2">
                </div>
                <div class="file-path-wrapper">
                    <input id="f2" class="file-path validate" type="text">
                </div>
            </div>
        </div>
        <div id="fp3" class="input-field col l2 m2 s12">
            <input id="fpago3" name="fp3" type="date" class="validate">
            <label class="active" for="titulo">Fechas de pago</label>
        </div>
        <div id="file3" class="col s12 m4 l4">
            <div class="file-field input-field">
                <div class="btn indigo">
                    <span>Archivo 3</span>
                    <input type="file" name="file3">
                </div>
                <div class="file-path-wrapper">
                    <input id="f3" class="file-path validate" type="text">
                </div>
            </div>
        </div>
        <div id="fp4" class="input-field col l2 m2 s12">
            <input id="fpago4" name="fp4" type="date" class="validate">
            <label class="active" for="titulo">Fechas de pago</label>
        </div>
        <div id="file4" class="col s12 m4 l4">
            <div class="file-field input-field">
                <div class="btn indigo">
                    <span>Archivo 4</span>
                    <input type="file" name="file4">
                </div>
                <div class="file-path-wrapper">
                    <input id="f4" class="file-path validate" type="text">
                </div>
            </div>
        </div>
        <!-- end cargue -->
        <div class="col s12 m12 l12">
            <button type="submit"
                    class="waves-effect waves-light btn-large float-right btn-light-indigo"
                    style="position: static;"><i class="material-icons right">file_upload</i>Cargar
            </button>
        </div>
    </div>
</div>
