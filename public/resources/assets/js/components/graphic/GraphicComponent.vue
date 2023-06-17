<template>
  <div>
    <BarGraphicComponent :chartData="chartData"   :options="options"  :height="150"   :width="500" v-show="typeGraphic == 1"/>
    <LineGraphicComponent :chartData="chartData"  :options="options" :height="150"   :width="500" v-show="typeGraphic == 2"/>
    <PieGraphicComponent :chartData="chartData"   :options="options"  :height="150"   :width="500" v-show="typeGraphic == 3" />
  </div>
</template>

<script>
import BarGraphicComponent from "./BarGraphicComponent";
import LineGraphicComponent from "./LineGraphicComponent";
import PieGraphicComponent from "./PieGraphicComponent";

export default {
  name:'GraphicComponent',
  props: [
      'typeGraphic',
      'chartData',
      'height',
      'width',
      'options'
  ],
  components:{
    BarGraphicComponent,
    LineGraphicComponent,
    PieGraphicComponent
  },
  mounted() {
  }
};
</script>