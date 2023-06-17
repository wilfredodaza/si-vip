<template>
  <div class="row" >
    <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
      <div class="container">
        <div class="row">
          <div class="col s10 m6 l6 breadcrumbs-left">
            <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down">
              <span>Informe de Productos</span>
            </h5>
            <ol class="breadcrumbs mb-0">
              <li class="breadcrumb-item"><a :href=" this.url + '/home'">Home</a></li>
              <li class="breadcrumb-item"><a href="#">Informes</a></li>
              <li class="breadcrumb-item active">Informe de Productos</li>
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
      <div class="section">
        <div class="card">
          <div class="card-content">
            <a class="btn-floating btn-move-up waves-effect waves-light red accent-2 z-depth-4 right">
              <i class="material-icons activator">grid_on</i>
            </a>
            <h4 class="card-title">Informe de Productos</h4>
            <p class="caption">Estos son los 5 productos o servicios con mayor participación en el ingreso de la compañía.</p>
            <GraphicComponent  :chartData="grafic" :options="options" :height="300" :width="500" :typeGraphic="typeGraphic" />
          </div>

          <div class="card-reveal">
             <span class="card-title grey-text text-darken-4">Tabla <i
                 class="material-icons right">close</i>
                </span>
            <table class="responsive-table">
              <thead>
              <tr>
               <th class="center" v-for="item of labels"> {{ item }}</th>
              </tr>
                </thead>
              <tbody>
              <tr>
                <td class="center" v-for="item of values"> {{ item }} %</td>
              </tr>
              </tbody>
            </table>
          </div>
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
  name: "ProductComponent",
  components: {
    GraphicComponent
  },
  mounted() {
    this.url = localStorage.getItem('url');
    this.information();
    this.typeGraphics(2);
  },
  data() {
    return {
      url: '',
      info: [],
      name: [],
      typeGraphic: '1',
      totals: [],
      graphics: [],
      year: new Date().getFullYear().toString().slice(2, 4),
      months: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
      grafic: {},
      util: [],
      options: {},
      labels: [],
      values: []
    }
  },
  methods: {

    information() {
      Axios.get(`${this.url}/api/v1/graphic/sales_of_product`).then((response) => {
        this.labels   = response.data.data.labels;
        this.values   = response.data.data.values;

        this.grafic = {
          labels: this.labels,
          datasets: [{
            label: 'Informe de productos',
            data: this.values,
            backgroundColor: util.colors,
          }]
        };

        this.options = {
          responsive: true,
          showTooltips: false,
          animation: {
            onComplete: function () {
              var chartInstance = this.chart;
              var ctx = chartInstance.ctx;
              console.log(chartInstance);
              var height = chartInstance.controller.boxes[0].bottom;
              ctx.textAlign = "center";
              Chart.helpers.each(this.data.datasets.forEach(function (dataset, i) {
                var meta = chartInstance.controller.getDatasetMeta(i);
                Chart.helpers.each(meta.data.forEach(function (bar, index) {
                  ctx.fillText(dataset.data[index]  + '%', bar._model.x, height - ((height - bar._model.y) / 2));
                }), this)
              }), this);
            }
          }
        }
      }).catch((error) => {
        console.log(error);
      });


    },

    month(item) {
      return this.months[item - 1];
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
  }, computed: {
    getMonth() {
      const month = [];
      let l = 0;
      for(let i of this.months) {
        month[l] = i+ ' - ' + this.year;
        l++;
      }
      return month;
    }
  }

}
</script>

<style scoped>
.text-center {
  text-align: center;
}

</style>