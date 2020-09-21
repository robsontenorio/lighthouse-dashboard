<template>
  <div>
    <apexchart type="bar" :height="height" :options="chart.options" :series="chart.series" />
  </div>
</template>

<script>
export default {
  props: ["series"],
  data() {
    return {
      chart: {
        series: [
          {
            name: "Requests",
            data: this.series || [],
          },
        ],
        options: {
          chart: {
            toolbar: {
              show: false,
            },
          },
          grid: {
            show: false,
          },
          plotOptions: {
            bar: {
              horizontal: true,
            },
          },
          dataLabels: {
            enabled: false,
          },
          tooltip: {
            y: {
              formatter: function (val) {
                return new Intl.NumberFormat("pt-BR").format(val);
              },
            },
          },
        },
      },
    };
  },
  watch: {
    series(value) {
      this.chart.series = [
        {
          name: "Requests",
          data: value,
        },
      ];
    },
  },
  computed: {
    height() {
      return this.series.length > 2 ? this.series.length * 40 : 140;
    },
  },
};
</script>
