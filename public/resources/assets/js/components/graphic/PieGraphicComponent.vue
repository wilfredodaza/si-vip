<script>
import { Pie, mixins } from "vue-chartjs";

export default {
  extends: Pie,
  props: ['grafic'],
  mixins: [mixins.reactiveProp],

  mounted() {
    this.renderChart(this.chartData, this.options);
  }
};
</script>