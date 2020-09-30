<template>
  <apexchart
    type="area"
    height="150"
    :options="chart.options"
    :series="chart.series"
  />
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
            zoom: {
              enabled: false,
            },
            toolbar: {
              show: false,
            },
          },
          grid: {
            show: false,
          },
          dataLabels: {
            enabled: false,
          },
          stroke: {
            curve: "smooth",
          },
          xaxis: {
            type: "datetime",
          },
          yaxis: {
            show: false,
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
          data: value,
        },
      ];
    },
  },
};
</script>