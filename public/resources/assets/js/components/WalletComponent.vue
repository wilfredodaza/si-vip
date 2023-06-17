<template>
  <div class="row">
    <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
      <div class="container">
        <div class="row">
          <div class="col s10 m6 l6 breadcrumbs-left">
            <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down">
              <span>Informe de Cartera</span>
            </h5>
            <ol class="breadcrumbs mb-0">
              <li class="breadcrumb-item"><a :href=" this.url + '/home'">Home</a></li>
              <li class="breadcrumb-item"><a href="#">Informes</a></li>
              <li class="breadcrumb-item active">Informe de Cartera</li>
            </ol>
          </div>
          <div class="col s2 m6 l6"><a class="btn btn-floating dropdown-settings waves-effect waves-light breadcrumbs-btn right" href="#!" data-target="dropdown1"><i class="material-icons">expand_more </i><i class="material-icons right">arrow_drop_down</i></a>
            <ul class="dropdown-content" id="dropdown1" tabindex="0">
              <li tabindex="0" v-for="i of graphics">
                <a class="grey-text text-darken-2" href="#" @click="typeGraphic = i.graphic_type_id">{{ i.graphic_type_name }}</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="col s12">
      <div class="card">
        <div class="card-content">
          <a class="btn-floating btn-move-up waves-effect waves-light red accent-2 z-depth-4 right">
            <i class="material-icons activator">grid_on</i>
          </a>
          <h4 class="card-title">Informe de Cartera</h4>
          <p class="caption">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquam architecto assumenda
            at, cum deleniti dignissimos dolores enim eum eveniet ex exercitationem fugit laborum laudantium porro
            quidem quo sed? Ad, molestias!</p>
          <GraphicComponent :chartData="grafic" :height="300" :options="options" :width="500" :typeGraphic="typeGraphic"/>
        </div>
        <div class="card-reveal">
            <span class="card-title grey-text text-darken-4">Tabla <i
                class="material-icons right">close</i>
                </span>
          <table class="table">
            <thead>
            <tr>
              <th>Cliente</th>
              <th class="text-center">{{ date }}</th>
              <th class="text-center">Cantidad de facturas</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="item of info">
              <th>{{ item.customer }}</th>
              <td class="text-center">$ {{ new Intl.NumberFormat("es-Es").format(item.total) }}</td>
              <td class="text-center">{{ item.invoices }}</td>
            </tr>
            <tr>
              <th>TOTAL</th>
              <th class="text-center">$ {{ total }}</th>
              <th class="text-center">{{ invoices }}</th>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
<script>

import Axios from 'axios';
import GraphicComponent from "./graphic/GraphicComponent";
import util from '../util';


export default {
  name: 'wallet-component',
  components: {
    GraphicComponent,
  },
  mounted() {
    this.url = localStorage.getItem('url');
    this.information();
    this.typeGraphics(5);
  },
  data() {
    return {
      info: [],
      customers: [],
      date: new Date().toLocaleDateString(),
      total: 0,
      invoices: 0,
      typeGraphic: 1,
      graphics: [],
      util: [],
      grafic: {},
      url: '',
      options:{}
    }
  },
  methods: {
    information() {

      Axios.get(`${localStorage.getItem('url')}/api/v1/graphic/sales_of_wallet`).then((response) => {
        this.info = response.data.data;
        const data = [];
        for (let i of this.info) {
          data.push(parseFloat(i.total));
          this.customers.push(i.customer);
          this.total += parseInt(i.total);
          this.invoices += parseInt(i.invoices);
        }
        this.util =  util.colors;
        this.options = {
          responsive: true,
          maintainAspectRatio: false,
          tooltips: {
            enabled: false
          },
          scales:{
            yAxes:[
              {
                ticks: {
                  beginAtZero: true,
                  callback: (value) => {
                    return "$" + (new Intl.NumberFormat("de-DE").format(value));
                  }
                }
              }
            ]
          },
          animation: {
            onComplete: function ()  {
              const chart =  this.chart;
              const ctx =     chart.ctx;
              ctx.textAlign = "center";
              ctx.textBaseline = "bottom";
              var height = chart.controller.boxes[0].bottom;
              Chart.helpers.each(this.data.datasets.forEach(function (dataset, i) {
                var meta = chart.controller.getDatasetMeta(i);
                Chart.helpers.each(meta.data.forEach(function (bar, index) {
                  ctx.fillText("$" + (new Intl.NumberFormat("de-DE").format(dataset.data[index])), bar._model.x, height - ((height - bar._model.y) / 2));
                }),this)
              }),this);
            }
          }
        };

        this.grafic = {
          labels: this.customers,
          datasets: [{
            label: 'Informe de cartera',
            data: data,
            backgroundColor: this.util,
            borderColor: this.util,
            borderWidth: 1
          }]
        }

      }).catch((error) => {
        console.log(error);
      })
    },
    createColors(quantity) {
      let util = [];
      for (let i = 0; i < quantity; i++) {
        util[i] = `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 1)`;
      }
      return util;
    },
    typeGraphics(id) {
      Axios.get(`${localStorage.getItem('url')}/api/v1/graphic/graphic_type/${id}`).then((res) => {
        const data =  res.data.data;
        this.graphics =  data;
        if(data.length > 0) {
          this.typeGraphic = data[0].graphic_type_id;
        }
      }).catch((error) => {

      });
    }
  }
}
</script>
<style scoped>
.text-center {
  text-align: center;
}

</style>