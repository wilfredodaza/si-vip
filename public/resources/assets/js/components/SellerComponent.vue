<template>
  <div class="row">
    <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
      <div class="container">
        <div class="row">
          <div class="col s10 m6 l6 breadcrumbs-left">
            <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down">
              <span>Informe de Vendedores</span>
            </h5>
            <ol class="breadcrumbs mb-0">
              <li class="breadcrumb-item"><a :href=" this.url + '/home'">Home</a></li>
              <li class="breadcrumb-item"><a href="#">Informes</a></li>
              <li class="breadcrumb-item active">Informe de Vendedores</li>
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
          <h4 class="card-title">Informe de Vendedores</h4>
          <p class="caption">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquam architecto assumenda
            at, cum deleniti dignissimos dolores enim eum eveniet ex exercitationem fugit laborum laudantium porro
            quidem quo sed? Ad, molestias!</p>
          <GraphicComponent :chartData="grafic" :height="300" :width="500" :options="options" :typeGraphic="typeGraphic"/>
        </div>

        <div class="card-reveal">
          <span class="card-title grey-text text-darken-4">Tabla <i
              class="material-icons right">close</i>
                </span>
          <table class="table">
            <thead>
            <tr>
              <th>Mes</th>
              <th v-for="(item, index) of info" style="text-align: center;">
                {{ month(index + 1) }} - {{ year }}
              </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item, index) of name" style="text-align: center;">
              <th>{{ item }}</th>
              <td v-for="item2 of info" style="text-align: center;">$
                {{ new Intl.NumberFormat("es-Es").format(item2[index].totals) }}
              </td>
            </tr>
            <tr>
              <th>TOTAL</th>
              <th class="text-center" v-for="item of totals">$ {{
                  new Intl.NumberFormat("es-Es").format(item)
                }}
              </th>
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
  name: "SellerComponent",
  components: {
    GraphicComponent
  },
  mounted() {
    this.url = localStorage.getItem('url');
    this.information();
    this.typeGraphics(4);
  },
  data() {
    return {
      url: '',
      info: [],
      name: [],
      totals: [],
      typeGraphic: '1',
      graphics: [],
      grafic: {},
      year: new Date().getFullYear().toString().slice(2, 4),
      months: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
      util: [],
      options: {}
    }
  },
  methods: {
    information() {
      Axios.get(`${this.url}/api/v1/graphic/sales_of_seller`).then((response) => {
        this.info = response.data.data;
        for (let i of this.info[0]) {
          this.name.push(i.name);
        }

        for (let i of this.info) {
          let total = 0;
          for (let l of i) {
            total += parseInt(l.totals);
          }
          this.totals.push(total);
        }

        let m = 0;
        const data = [];

        this.util = this.createColors(this.info[0].length);

        for (let l = 0; l < this.info[0].length; l++) {
          const objeto = {
            data: []
          };
          for (let i of this.info) {
            objeto.label = i[l].name;
            objeto.stack = i[l].name;
            objeto.backgroundColor = util.colors[m];
            objeto.data.push(parseFloat(i[l].totals));
          }
          m++;
          data.push(objeto);
        }

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

        };

        this.grafic = {
          labels: this.getMonth,
          datasets: data
        };

      }).catch((error) => {
        console.log(error);
      })
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
        console.log(data);
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