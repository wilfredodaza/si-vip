<script>
import { Bar,mixins } from "vue-chartjs";



export default {
  extends: Bar,
  mixins: [mixins.reactiveProp],
  props: [
      'chartData',
      'options'
  ],

  mounted() {
    this.renderChart(this.chartData, this.options);
  },
  methods: {
    createColors(quantity) {
      let util = [];
      for (let i = 0; i < quantity; i++) {
        util[i] = `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 1)`;
      }
      return util;
    }
  }
};
</script>