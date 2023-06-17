<template>
  <div class="row">
    <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
      <!-- Search for small screen-->
      <div class="container">
        <div class="row">
          <div class="col s10 m6 l6 breadcrumbs-left">
            <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down"><span>Configuración de Email</span></h5>
            <ol class="breadcrumbs mb-0">
              <li class="breadcrumb-item"><a :href=" this.url + '/home'">Home</a></li>
              <li class="breadcrumb-item"><a href="#">Configurar</a></li>
              <li class="breadcrumb-item active">Configuración de Email</li>
            </ol>
          </div>

        </div>
      </div>
    </div>
    <div class="col s12">
      <div class="container">
        <div class="seaction">
          <!--Line Chart-->
          <div class="card-alert card green lighten-5" v-show="alert">
            <div class="card-content green-text">
              <p>Éxito: Configuraciones guardadas correctamente.</p>
            </div>
            <button type="button" class="close green-text" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true" @click="alert = !alert">×</span>
            </button>
          </div>
          <div class="card-alert card red lighten-5" v-show="error">
            <div class="card-content red-text">
              <p>ERROR: La operación no pudo ser procesada.</p>
            </div>
            <button type="button" class="close red-text" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true" @click="error = !error">×</span>
            </button>
          </div>
          <div id="chartjs-line-chart" class="card">

            <div class="card-content">

              <h4 class="card-title">Configurar</h4>
              <p class="caption">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Distinctio eaque ex inventore magnam maiores numquam odio sed, tempora tenetur veritatis. Ad fuga fugiat iusto labore minima molestias optio unde veniam?
              </p>
              <div class="row">
                <form v-on:submit.prevent="save()">
                <div class="col s12 m6">
                  <br>
                  <h4 class="card-title">Listado de informes</h4>
                  <p class="mb-1">
                    <label>
                      <input type="checkbox" name="sale_of_month" v-model="form.sale_of_month"/>
                      <span>Informe de Ventas</span>
                    </label>
                  </p>
                  <p class="mb-1">
                    <label>
                      <input type="checkbox" name="sale_of_month" v-model="form.sale_of_product"/>
                      <span>Informe de Productos</span>
                    </label>
                  </p>
                  <p class="mb-1">
                    <label>
                      <input type="checkbox" name="sale_of_month" v-model="form.sale_of_customer"/>
                      <span>Informe de Clientes</span>
                    </label>
                  </p>
                  <p class="mb-1">
                    <label>
                      <input type="checkbox" name="sale_of_month" v-model="form.sale_of_seller"/>
                      <span>Informe de Vendedor</span>
                    </label>
                  </p>
                  <p class="mb-1">
                    <label>
                      <input type="checkbox" name="sale_of_month" v-model="form.sale_of_wallet"/>
                      <span>Informe de Cartera</span>
                    </label>
                  </p>
                </div>
                <div class="col s12 m6">
                  <div>
                    <label for="email">Correo Electornico</label>
                    <input type="email" name="email" class="validate" id="email" v-model="form.email" required>
                  </div>
                  <div>
                    <label>Tiempo</label>
                    <select class="browser-default valid" v-model="form.time" required>
                      <option value="0" disabled selected>Seleccione ...</option>
                      <option value="1">Dia</option>
                      <option value="2">Semana</option>
                      <option value="3">Mes</option>
                      <option value="4">Ninguna</option>
                    </select>
                  </div>
                  <br>
                  <div class="row">
                    <div class="col s12 m6" v-show="this.form.time == 3" required>
                      <label for="hours">Dia</label>
                      <select class="browser-default" v-model="form.day_number" id="day_number">
                        <option value="" disabled selected>Seleccione ...</option>
                        <option v-for="i of 30" >
                          <span v-if="String(i).length == 2 ">{{ i }}</span>
                          <span v-else>0{{ i }}</span>
                        </option>
                      </select>
                    </div>
                    <div class="col s12 m6" v-show="this.form.time == 2">
                      <label for="days">Días de la Semana</label>
                      <select class="browser-default" v-model="form.day" id="days" required>
                        <option   value="" disabled selected>Seleccione ...</option>
                        <option   value="Monday">Lunes</option>
                        <option   value="Tuesday">Martes</option>
                        <option   value="Wednesday">Miércoles</option>
                        <option   value="Thursday">Jueves</option>
                        <option   value="Viernes">Viernes</option>
                        <option   value="Saturday">Sábado</option>
                        <option   value="Sunday">Domingo</option>
                      </select>
                    </div>
                    <div class="col s12 m6" v-show="this.form.time == 1 || this.form.time == 2 || this.form.time == 3" >
                      <label for="hours">Hora</label>
                      <select class="browser-default" v-model="form.hours" id="hours" required>
                        <option value="" disabled selected>Seleccione ...</option>
                        <option  v-for="i of 24" >
                          <span v-if="String(i - 1).length == 2 ">{{ i - 1 }}:00{{ i.length}}</span>
                          <span v-else>0{{ i - 1 }}:00</span>
                        </option>
                      </select>
                    </div>
                    <div class="col s12">
                      <br>
                      <button class="btn pull-right" type="button" v-on:click="save()">Guardar</button>
                    </div>

                  </div>
                </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Axios from 'axios';
  export default {
    name: 'SettingComponent',
    created() {

      this.url = localStorage.getItem('url');
      this.baseUrl = `${localStorage.getItem('url')}/api/v1/graphic/setting`;
      Axios.get(this.baseUrl).then((res) => {
          this.form = res.data.data;
      });
    },
    data () {
      return {
        url: '',
        baseUrl: '',
        alert: false,
        error: false,
        form: {
          sale_of_month: false,
          sale_of_product: false,
          sale_of_customer: false,
          sale_of_seller: false,
          sale_of_wallet: false,
          time: 0,
          hours: '',
          day: '',
          day_number: '',
          email: ''
        },
      }
    },
    methods: {
      save() {
        if(this.form.email != '') {
          Axios.post(this.baseUrl, this.form).then((res) => {
            this.alert =  true;
            setTimeout(() => {
              this.alert =  false;
            }, 5000);
          }).catch((error) =>  {
            this.error = true;
          });
        }
      }
    }
  }
</script>